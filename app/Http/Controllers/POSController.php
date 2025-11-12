<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\MpesaPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display the POS interface
     */
    public function index()
    {
        $categories = Category::active()
            ->with(['activeProducts' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get all active products for the products grid with stock information
        $products = Product::active()
            ->with('category')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category_id' => $product->category_id,
                    'category' => $product->category,
                    'stock_quantity' => $product->stock_quantity,
                    'low_stock_threshold' => $product->low_stock_threshold,
                    'is_low_stock' => $product->isLowStock(),
                    'is_out_of_stock' => $product->stock_quantity == 0,
                    'stock_status' => $product->stock_quantity == 0 ? 'out_of_stock' : 
                                    ($product->isLowStock() ? 'low_stock' : 'in_stock')
                ];
            });

        return view('pos.index', compact('categories', 'products'));
    }

    /**
     * Create a new sale
     */
    public function createSale(Request $request)
    {
        // Log the incoming request data for debugging
        \Log::info('POS Create Sale Request:', $request->all());

        // Validate the request
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            \Log::error('POS Create Sale Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create the sale
            $sale = Sale::create([
                'sale_number' => Sale::generateSaleNumber(),
                'cashier_id' => Auth::id(),
                'subtotal' => 0,
                'total' => 0,
                'status' => 'draft',
                'customer_phone' => $request->customer_phone,
            ]);

            $subtotal = 0;

            // Add items to the sale
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $subtotal += $saleItem->total_price;

                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Update sale totals
            $sale->update([
                'subtotal' => $subtotal,
                'total' => $subtotal, // No VAT for now
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load('saleItems.product'),
                'message' => 'Sale created successfully. Awaiting M-Pesa payment.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Search for M-Pesa payments
     */
    public function searchMpesaPayments(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'show_all' => 'nullable|boolean',
        ]);

        $query = MpesaPayment::pending()
            ->orderByRaw('transaction_time IS NULL, transaction_time DESC, created_at DESC');

        // Search by name, phone, or transaction code
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by amount if provided (with tolerance for small differences)
        if ($request->filled('amount')) {
            $amount = $request->amount;
            $query->whereBetween('amount', [$amount - 0.50, $amount + 0.50]);
        }

        // Time filtering - more lenient for showing previous payments
        if (!$request->filled('show_all')) {
            // Show last 7 days OR payments without transaction_time (pending Lipa na M-Pesa)
            $query->where(function ($q) {
                $q->where('transaction_time', '>=', now()->subDays(7))
                  ->orWhereNull('transaction_time');
            });
        }
        // If show_all is true, don't apply time filter

        $payments = $query->take(50)->get();

        return response()->json([
            'success' => true,
            'total_found' => $payments->count(),
            'payments' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'transaction_code' => $payment->transaction_code ?: 'Awaiting confirmation',
                    'customer_name' => $payment->customer_name ?: 'Lipa na M-Pesa payment',
                    'phone_number' => $payment->phone_number ? $payment->masked_phone : 'Not provided',
                    'amount' => $payment->formatted_amount,
                    'time' => $payment->transaction_time ? $payment->transaction_time->format('Y-m-d H:i:s') : $payment->created_at->format('Y-m-d H:i:s'),
                    'payment_method' => $payment->payment_method,
                    'days_ago' => $payment->transaction_time ? 
                        $payment->transaction_time->diffForHumans() : 
                        $payment->created_at->diffForHumans(),
                    'is_available' => $payment->isAvailable(),
                ];
            }),
        ]);
    }

    /**
     * Link M-Pesa payment to sale
     */
    public function linkMpesaPayment(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'payment_id' => 'required|exists:mpesa_payments,id',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($request->sale_id);
            $payment = MpesaPayment::findOrFail($request->payment_id);

            // Check if sale is already paid
            if ($sale->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This sale has already been paid for.',
                ], 400);
            }

            // Check if sale has items (total > 0)
            if ($sale->total <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot process payment for a sale with no items. Please add items to the sale first.',
                ], 400);
            }

            // Check if payment is available
            if (!$payment->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This M-Pesa payment has already been used.',
                ], 400);
            }

            // Check if payment amount matches sale total
            if ($payment->amount != $sale->total) {
                return response()->json([
                    'success' => false,
                    'message' => "Payment amount ({$payment->formatted_amount}) does not match sale total ({$sale->formatted_total}).",
                ], 400);
            }

            // Link payment to sale
            $payment->linkToSale($sale, Auth::user());

            // Update sale status
            $sale->update([
                'status' => 'completed',
                'mpesa_transaction_id' => $payment->transaction_code,
                'mpesa_receipt_number' => $payment->transaction_code,
                'payment_confirmed_at' => $payment->transaction_time,
            ]);

            DB::commit();

            $updatedSale = $sale->fresh()->load('saleItems.product');
            
            \Log::info('M-Pesa payment linked', [
                'sale_id' => $sale->id,
                'payment_id' => $payment->id,
                'new_status' => $updatedSale->status,
                'mpesa_transaction_id' => $updatedSale->mpesa_transaction_id,
                'payment_confirmed_at' => $updatedSale->payment_confirmed_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'M-Pesa payment linked successfully! Sale completed.',
                'sale' => $updatedSale,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Confirm M-Pesa payment
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'mpesa_transaction_id' => 'required|string',
            'mpesa_receipt_number' => 'required|string',
        ]);

        try {
            $sale = Sale::findOrFail($request->sale_id);
            
            $sale->update([
                'status' => 'completed', // Mark as completed instead of paid
                'mpesa_transaction_id' => $request->mpesa_transaction_id,
                'mpesa_receipt_number' => $request->mpesa_receipt_number,
                'payment_confirmed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully!',
                'sale' => $sale,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Add M-Pesa payment manually
     */
    public function manualMpesaPayment(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'mpesa_transaction_id' => 'required|string',
            'mpesa_receipt_number' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'transaction_time' => 'required|date',
        ]);

        try {
            $sale = Sale::findOrFail($request->sale_id);
            
            // Check if sale is already paid
            if ($sale->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This sale has already been paid for.',
                ], 400);
            }

            // Check if sale has items (total > 0)
            if ($sale->total <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot process payment for a sale with no items. Please add items to the sale first.',
                ], 400);
            }
            
            $sale->update([
                'status' => 'completed',
                'mpesa_transaction_id' => $request->mpesa_transaction_id,
                'mpesa_receipt_number' => $request->mpesa_receipt_number,
                'payment_confirmed_at' => $request->transaction_time,
            ]);

            $updatedSale = $sale->fresh()->load('saleItems.product');
            
            \Log::info('Manual M-Pesa payment processed', [
                'sale_id' => $sale->id,
                'original_status' => $sale->getOriginal('status'),
                'new_status' => $updatedSale->status,
                'mpesa_transaction_id' => $updatedSale->mpesa_transaction_id,
                'payment_confirmed_at' => $updatedSale->payment_confirmed_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manual M-Pesa payment recorded successfully!',
                'sale' => $updatedSale,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get sales for today
     */
    public function todaySales()
    {
        $sales = Sale::whereDate('created_at', today())
            ->with(['saleItems.product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($sales);
    }

    /**
     * Sales Dashboard - shows only active sales (not completed)
     */
    public function salesDashboard()
    {
        $sales = Sale::active() // Only draft and awaiting_payment
            ->with(['saleItems.product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sales.dashboard', compact('sales'));
    }

    /**
     * Work on a specific sale - opens POS for that sale
     */
    public function workOnSale(Sale $sale)
    {
        // Check if this sale is completed
        if ($sale->isCompleted()) {
            return redirect()->route('sales.dashboard')->with('error', 'This sale has already been completed.');
        }

        // Load relationships for the sale
        $sale->load(['saleItems.product', 'cashier']);

        $categories = Category::active()
            ->with(['activeProducts' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get all active products for the products grid
        $products = Product::active()
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('categories', 'sale', 'products'));
    }

    /**
     * Create a blank sale for workflow
     */
    public function createBlankSale(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_phone' => 'nullable|string|max:15',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed in createBlankSale', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            \Log::info('Creating sale with data', [
                'customer_phone' => $request->customer_phone ?? 'Walk-in',
                'user_id' => auth()->id(),
                'authenticated' => auth()->check()
            ]);

            $sale = Sale::create([
                'sale_number' => Sale::generateSaleNumber(),
                'cashier_id' => auth()->id(),
                'customer_phone' => $request->customer_phone ?? 'Walk-in',
                'subtotal' => 0,
                'discount' => 0,
                'vat' => 0,
                'total' => 0,
                'status' => 'draft' // Start as draft
            ]);

            \Log::info('Sale created successfully', ['sale_id' => $sale->id, 'sale_number' => $sale->sale_number]);

            return response()->json([
                'success' => true,
                'sale' => $sale,
                'message' => 'Sale created successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error creating blank sale', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a sample sale with items for testing
     */
    public function createSampleSale(Request $request)
    {
        try {
            // Get first few products for the sample
            $products = Product::take(3)->get();
            
            if ($products->count() < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough products in database to create sample sale'
                ], 400);
            }

            DB::beginTransaction();

            // Create the sale
            $sale = Sale::create([
                'sale_number' => Sale::generateSaleNumber(),
                'cashier_id' => auth()->id(),
                'customer_phone' => $request->customer_phone ?? 'Walk-in',
                'subtotal' => 0,
                'discount' => 0,
                'vat' => 0,
                'total' => 0,
                'status' => 'draft'
            ]);

            $subtotal = 0;

            // Add sample items to the sale
            foreach ($products as $index => $product) {
                $quantity = $index + 1; // 1, 2, 3 quantities
                $totalPrice = $product->price * $quantity;
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $totalPrice,
                ]);

                $subtotal += $totalPrice;
            }

            // Update sale totals and status
            $sale->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => 'awaiting_payment' // Change to awaiting payment since it has items
            ]);

            DB::commit();

            \Log::info('Sample sale created successfully', ['sale_id' => $sale->id, 'sale_number' => $sale->sale_number, 'total' => $sale->total]);

            return response()->json([
                'success' => true,
                'sale' => $sale,
                'message' => 'Sample sale created successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating sample sale', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating sample sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to existing sale
     */
    public function addItemToSale(Request $request, Sale $sale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Check if sale can have items added (not completed)
            if ($sale->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add items to a completed sale.',
                ], 400);
            }

            $product = Product::findOrFail($request->product_id);
            
            // Check stock availability
            if ($product->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}",
                ], 400);
            }

            // Check if item already exists in sale, if so update quantity
            $existingItem = $sale->saleItems()->where('product_id', $product->id)->first();
            
            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $request->quantity;
                
                // Check total stock for new quantity
                if ($product->stock_quantity < ($newQuantity - $existingItem->quantity)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}",
                    ], 400);
                }
                
                $existingItem->update([
                    'quantity' => $newQuantity,
                    'total_price' => $newQuantity * $product->price,
                ]);
                
                $saleItem = $existingItem;
            } else {
                // Create new sale item
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'unit_price' => $product->price,
                    'total_price' => $request->quantity * $product->price,
                ]);
            }

            // Update product stock
            $product->decrement('stock_quantity', $request->quantity);

            // Recalculate sale totals
            $subtotal = $sale->saleItems()->sum('total_price');
            $sale->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => $subtotal > 0 ? 'awaiting_payment' : 'draft', // Update status based on whether there are items
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load('saleItems.product'),
                'sale_item' => $saleItem->load('product'),
                'message' => 'Item added to sale successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding item to sale', [
                'sale_id' => $sale->id,
                'product_id' => $request->product_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search payments by various criteria
     */
    public function searchPayments(Request $request)
    {
        try {
            $searchTerm = $request->input('search_term');
            
            if (empty($searchTerm)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term is required'
                ], 400);
            }

            // Search in MpesaPayment model
            $payments = MpesaPayment::where(function ($query) use ($searchTerm) {
                $query->where('phone_number', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('mpesa_receipt_number', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('transaction_id', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('checkout_request_id', 'LIKE', '%' . $searchTerm . '%');
            })
            ->with(['sale' => function ($query) {
                $query->select('id', 'sale_number', 'total', 'status');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

            return response()->json([
                'success' => true,
                'payments' => $payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'phone_number' => $payment->phone_number,
                        'mpesa_receipt_number' => $payment->mpesa_receipt_number,
                        'transaction_id' => $payment->transaction_id,
                        'status' => $payment->status,
                        'created_at' => $payment->created_at,
                        'sale' => $payment->sale ? [
                            'id' => $payment->sale->id,
                            'sale_number' => $payment->sale->sale_number,
                            'total' => $payment->sale->total,
                            'status' => $payment->sale->status
                        ] : null
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Search payments error:', [
                'search_term' => $request->input('search_term'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch available M-Pesa payments from Safaricom API
     */
    public function fetchAvailablePayments(Request $request)
    {
        try {
            // First, let's get unlinked M-Pesa payments from our database
            // These are payments that have been received but not yet linked to any sale
            $availablePayments = MpesaPayment::whereNull('sale_id')
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            // If you want to fetch fresh data from Safaricom API, 
            // you can integrate with MpesaService here
            // For now, we'll return the unlinked payments from our database

            return response()->json([
                'success' => true,
                'payments' => $availablePayments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'phone_number' => $payment->phone_number,
                        'mpesa_receipt_number' => $payment->mpesa_receipt_number,
                        'first_name' => $payment->first_name,
                        'transaction_id' => $payment->transaction_id,
                        'status' => $payment->status,
                        'created_at' => $payment->created_at,
                        'transaction_time' => $payment->transaction_time
                    ];
                }),
                'message' => 'Found ' . $availablePayments->count() . ' available payments'
            ]);

        } catch (\Exception $e) {
            \Log::error('Fetch available payments error:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch available payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Link an M-Pesa payment to the current sale
     */
    public function linkPaymentToSale(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_id' => 'required|exists:mpesa_payments,id',
                'amount' => 'required|numeric|min:0',
                'transaction_code' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Find the payment
            $payment = MpesaPayment::find($request->payment_id);
            
            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            // Check if payment is already linked to a sale
            if ($payment->sale_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is already linked to a sale'
                ], 400);
            }

            // For now, create a new sale with this payment
            // In a real implementation, you might want to link to an existing draft sale
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'customer_phone' => $payment->phone_number,
                'subtotal' => $payment->amount,
                'tax' => $payment->amount * 0.16, // 16% VAT
                'discount' => 0,
                'total' => $payment->amount * 1.16,
                'status' => 'completed',
                'payment_method' => 'mpesa',
                'mpesa_checkout_request_id' => $payment->checkout_request_id
            ]);

            // Link the payment to the sale
            $payment->update(['sale_id' => $sale->id]);

            \Log::info('Payment linked to sale:', [
                'payment_id' => $payment->id,
                'sale_id' => $sale->id,
                'amount' => $payment->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment linked to sale successfully',
                'sale' => [
                    'id' => $sale->id,
                    'sale_number' => $sale->sale_number,
                    'total' => $sale->total,
                    'status' => $sale->status
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Link payment to sale error:', [
                'payment_id' => $request->payment_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to link payment to sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product stock when sale is made
     */
    /**
     * Get real-time inventory status for cashier
     */
    public function getInventoryStatus()
    {
        try {
            $products = Product::active()
                ->with('category')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'stock_quantity' => $product->stock_quantity,
                        'low_stock_threshold' => $product->low_stock_threshold,
                        'is_low_stock' => $product->isLowStock(),
                        'is_out_of_stock' => $product->stock_quantity == 0,
                        'stock_status' => $product->stock_quantity == 0 ? 'out_of_stock' : 
                                        ($product->isLowStock() ? 'low_stock' : 'in_stock')
                    ];
                });

            $stats = [
                'total_products' => Product::active()->count(),
                'low_stock_alerts' => Product::active()->where(function($query) {
                    $query->whereRaw('stock_quantity <= low_stock_threshold')
                          ->where('stock_quantity', '>', 0);
                })->count(),
                'out_of_stock' => Product::active()->where('stock_quantity', 0)->count(),
            ];

            return response()->json([
                'success' => true,
                'products' => $products,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get inventory status', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get inventory status'
            ], 500);
        }
    }

    private function updateProductStock($productId, $quantity)
    {
        try {
            $product = Product::findOrFail($productId);
            $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
            $product->save();

            Log::info('Product stock updated after sale', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity_sold' => $quantity,
                'new_stock_quantity' => $product->stock_quantity
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update product stock', [
                'product_id' => $productId,
                'quantity' => $quantity,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
