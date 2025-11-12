<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

// M-Pesa API routes (no middleware for callbacks)
Route::prefix('api/mpesa')->group(function () {
    Route::post('/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
    Route::post('/validation', [MpesaController::class, 'validation'])->name('mpesa.validation');
    Route::post('/confirmation', [MpesaController::class, 'confirmation'])->name('mpesa.confirmation');
});

// Main dashboard route - redirects to role-specific dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Role-specific dashboard routes with middleware protection
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
});

Route::middleware(['auth', 'verified', 'role:kitchen_manager'])->group(function () {
    Route::get('/dashboard/kitchen', [DashboardController::class, 'kitchenDashboard'])->name('dashboard.kitchen');
    
    // Inventory Management Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/update-stock/{product}', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::post('/inventory/update-threshold/{product}', [InventoryController::class, 'updateThreshold'])->name('inventory.update-threshold');
    Route::post('/inventory/toggle-availability/{product}', [InventoryController::class, 'toggleAvailability'])->name('inventory.toggle-availability');
    Route::post('/inventory/bulk-update', [InventoryController::class, 'bulkUpdateStock'])->name('inventory.bulk-update');
    Route::get('/api/inventory-status', [InventoryController::class, 'getInventoryStatus'])->name('api.inventory-status');
    
    // Category Management Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Product Management Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/categories', [ProductController::class, 'getCategories'])->name('products.categories');
});

Route::middleware(['auth', 'verified', 'role:cashier'])->group(function () {
    Route::get('/dashboard/cashier', [DashboardController::class, 'cashierDashboard'])->name('dashboard.cashier');
    
    // Sales Dashboard Routes
    Route::get('/sales', [POSController::class, 'salesDashboard'])->name('sales.dashboard');
    Route::get('/sales/{sale}', [POSController::class, 'workOnSale'])->name('sales.work-on');
    Route::post('/sales/create-blank', [POSController::class, 'createBlankSale'])->name('sales.create-blank');
    Route::post('/sales/create-sample', [POSController::class, 'createSampleSale'])->name('sales.create-sample');
    Route::post('/sales/{sale}/add-item', [POSController::class, 'addItemToSale'])->name('sales.add-item');
    
    // POS Routes for Cashiers
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/create-sale', [POSController::class, 'createSale'])->name('pos.create-sale');
    Route::post('/pos/search-mpesa', [POSController::class, 'searchMpesaPayments'])->name('pos.search-mpesa');
    Route::post('/pos/link-mpesa', [POSController::class, 'linkMpesaPayment'])->name('pos.link-mpesa');
    Route::post('/pos/confirm-payment', [POSController::class, 'confirmPayment'])->name('pos.confirm-payment');
    Route::post('/pos/manual-mpesa', [POSController::class, 'manualMpesaPayment'])->name('pos.manual-mpesa');
    Route::get('/pos/today-sales', [POSController::class, 'todaySales'])->name('pos.today-sales');
    Route::post('/pos/search-payments', [POSController::class, 'searchPayments'])->name('pos.search-payments');
    Route::post('/pos/fetch-available-payments', [POSController::class, 'fetchAvailablePayments'])->name('pos.fetch-available-payments');
    Route::post('/pos/link-payment-to-sale', [POSController::class, 'linkPaymentToSale'])->name('pos.link-payment-to-sale');
    Route::get('/api/pos-inventory-status', [POSController::class, 'getInventoryStatus'])->name('api.pos-inventory-status');
    
    // M-Pesa Routes for Cashiers
    Route::post('/mpesa/test-connection', [MpesaController::class, 'testConnection'])->name('mpesa.test');
    Route::post('/mpesa/lipa-na-mpesa', [MpesaController::class, 'lipaNaMpesa'])->name('mpesa.lipa-na-mpesa');
    Route::post('/mpesa/stk-push', [MpesaController::class, 'stkPush'])->name('mpesa.stk-push');
    Route::post('/mpesa/stk-query', [MpesaController::class, 'stkQuery'])->name('mpesa.stk-query');
    Route::post('/mpesa/register-urls', [MpesaController::class, 'registerUrls'])->name('mpesa.register-urls');
    Route::post('/mpesa/register-c2b', [MpesaController::class, 'registerC2BUrls'])->name('mpesa.register-c2b');
    Route::post('/mpesa/fetch-live-transactions', [MpesaController::class, 'fetchLiveTransactions'])->name('mpesa.fetch-live');
    
    // M-Pesa Test Page
    Route::get('/mpesa-test', function () {
        return view('mpesa-test');
    })->name('mpesa.test-page');
    
    // Test route for creating test sales
    Route::post('/mpesa/create-test-sale', function () {
        $sale = \App\Models\Sale::create([
            'user_id' => auth()->id(),
            'customer_phone' => '254700000000',
            'subtotal' => 100,
            'tax' => 16,
            'discount' => 0,
            'total' => 116,
            'status' => 'draft'
        ]);
        
        return response()->json([
            'success' => true,
            'sale' => $sale
        ]);
    })->name('mpesa.create-test-sale');
});

// M-Pesa Callback Routes (No authentication required - Safaricom calls these)
Route::post('/api/payments/callback', [MpesaController::class, 'callback'])->name('mpesa.api.callback');
Route::post('/api/payments/confirmation', [MpesaController::class, 'confirmation'])->name('mpesa.api.confirmation');
Route::post('/api/payments/balance/result', [MpesaController::class, 'balanceResult'])->name('mpesa.balance-result');
Route::post('/api/payments/balance/timeout', [MpesaController::class, 'balanceTimeout'])->name('mpesa.balance-timeout');
Route::post('/api/payments/validation', [MpesaController::class, 'validation'])->name('mpesa.api.validation');

// Public M-Pesa test routes (no authentication required for testing)
Route::post('/api/payments/test-connection', [MpesaController::class, 'testConnection'])->name('mpesa.public-test');
Route::post('/api/payments/register-urls', [MpesaController::class, 'registerUrls'])->name('mpesa.register-urls-public');

// Public test sale creation for testing
Route::post('/api/payments/create-test-sale', function () {
    try {
        // Get the first available user or create a test user
        $cashier = \App\Models\User::first();
        if (!$cashier) {
            return response()->json([
                'success' => false,
                'message' => 'No users found in database. Please create a user first.'
            ], 400);
        }

        $sale = \App\Models\Sale::create([
            'sale_number' => \App\Models\Sale::generateSaleNumber(),
            'cashier_id' => $cashier->id,
            'customer_phone' => '254700000000',
            'subtotal' => 100,
            'discount' => 0,
            'vat' => 16,
            'total' => 116,
            'status' => 'draft'
        ]);
        
        return response()->json([
            'success' => true,
            'sale' => $sale
        ]);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Test sale creation error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create test sale: ' . $e->getMessage()
        ], 500);
    }
})->name('payments.create-test-sale-public');

// Public lipa na mpesa test route
Route::post('/api/payments/lipa-na-mpesa', function (\Illuminate\Http\Request $request) {
    try {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $sale = \App\Models\Sale::findOrFail($request->sale_id);
        
        // Check if sale is already paid
        if ($sale->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This sale has already been paid for.'
            ], 400);
        }

        // Create M-Pesa service instance
        $mpesaService = new \App\Services\MpesaService();
        
        // Generate payment reference
        $paymentReference = $mpesaService->generatePaymentReference($sale->id);
        
        // Debug the values being used
        \Log::info('Updating sale with values:', [
            'sale_id' => $sale->id,
            'payment_reference' => $paymentReference,
            'status' => 'pending_payment'
        ]);
        
        // Update sale with payment reference using DB query to ensure proper parameter binding
        \Illuminate\Support\Facades\DB::table('sales')
            ->where('id', $sale->id)
            ->update([
                'mpesa_checkout_request_id' => $paymentReference,
                'status' => 'pending_payment',
                'updated_at' => now()
            ]);

        // Create M-Pesa payment record
        \App\Models\MpesaPayment::create([
            'sale_id' => $sale->id,
            'phone_number' => null,
            'amount' => $request->amount,
            'checkout_request_id' => $paymentReference,
            'status' => 'pending',
            'payment_method' => 'lipa_na_mpesa'
        ]);

        // Get payment instructions
        $instructions = $mpesaService->getLipaNaMpesaInstructions($paymentReference, $request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Payment instructions generated successfully',
            'payment_reference' => $paymentReference,
            'instructions' => $instructions,
            'sale_id' => $sale->id
        ]);
        
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Public Lipa na M-Pesa error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate payment instructions: ' . $e->getMessage()
        ], 500);
    }
})->name('payments.lipa-na-mpesa-public');

// Test route to verify ngrok connectivity
Route::get('/api/payments/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'M-Pesa callback endpoints are accessible',
        'timestamp' => now(),
        'urls' => [
            'callback' => config('mpesa.callback_url'),
            'confirmation' => config('mpesa.confirmation_url'),
            'validation' => config('mpesa.validation_url')
        ]
    ]);
})->name('mpesa.test-connectivity');

// Debug route to test M-Pesa configuration
Route::get('/api/payments/debug', function () {
    $config = [
        'environment' => config('mpesa.environment'),
        'consumer_key' => substr(config('mpesa.consumer_key'), 0, 10) . '...',
        'consumer_secret' => substr(config('mpesa.consumer_secret'), 0, 10) . '...',
        'shortcode' => config('mpesa.shortcode'),
        'passkey' => substr(config('mpesa.passkey'), 0, 10) . '...',
        'callback_url' => config('mpesa.callback_url'),
    ];
    
    // Test direct HTTP call
    try {
        $credentials = base64_encode(config('mpesa.consumer_key') . ':' . config('mpesa.consumer_secret'));
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        
        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->withOptions([
                'verify' => false, // Disable SSL verification for testing
            ])
            ->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ])->get($url);
        
        return response()->json([
            'config' => $config,
            'api_test' => [
                'url' => $url,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'config' => $config,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('mpesa.debug');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
