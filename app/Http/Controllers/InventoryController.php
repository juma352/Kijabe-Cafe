<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Display the inventory management dashboard for kitchen managers
     */
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        $categories = Category::active()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $lowStockProducts = Product::whereRaw('stock_quantity <= low_stock_threshold')
            ->with('category')
            ->orderBy('stock_quantity', 'asc')
            ->get();

        $outOfStockProducts = Product::where('stock_quantity', 0)
            ->with('category')
            ->get();

        return view('inventory.index', compact(
            'products', 
            'categories', 
            'lowStockProducts', 
            'outOfStockProducts'
        ));
    }

    /**
     * Update stock quantity and threshold for a product
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $product = Product::findOrFail($request->route('product'));
                $oldQuantity = $product->stock_quantity;
                $oldThreshold = $product->low_stock_threshold;

                $product->stock_quantity = $request->stock_quantity;
                $product->low_stock_threshold = $request->low_stock_threshold;
                $product->save();

                // Log the inventory change
                Log::info('Inventory updated', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $product->stock_quantity,
                    'old_threshold' => $oldThreshold,
                    'new_threshold' => $product->low_stock_threshold,
                    'updated_by' => Auth::id()
                ]);
            });

            // Get updated product data for response
            $product = Product::findOrFail($request->route('product'));
            
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => $product->stock_quantity,
                    'low_stock_threshold' => $product->low_stock_threshold,
                    'is_active' => $product->is_active,
                    'status' => $this->getProductStatus($product)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Inventory update failed', [
                'error' => $e->getMessage(),
                'product_id' => $request->route('product'),
                'stock_quantity' => $request->stock_quantity,
                'low_stock_threshold' => $request->low_stock_threshold
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update low stock threshold for a product
     */
    public function updateThreshold(Request $request)
    {
        $request->validate([
            'threshold' => 'required|integer|min:0'
        ]);

        try {
            $product = Product::findOrFail($request->route('product'));
            $product->low_stock_threshold = $request->threshold;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Low stock threshold updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update threshold: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle product availability (activate/deactivate)
     */
    public function toggleAvailability(Request $request)
    {
        try {
            $product = Product::findOrFail($request->route('product'));
            $product->is_active = !$product->is_active;
            $product->save();

            $status = $product->is_active ? 'activated' : 'deactivated';

            Log::info('Product availability changed', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'status' => $status,
                'changed_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Product {$status} successfully",
                'is_active' => $product->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current inventory status for API calls
     */
    public function getInventoryStatus()
    {
        try {
            $products = Product::select('id', 'name', 'stock_quantity', 'low_stock_threshold', 'is_active')
                ->with('category:id,name')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'category' => $product->category->name,
                        'stock_quantity' => $product->stock_quantity,
                        'low_stock_threshold' => $product->low_stock_threshold,
                        'is_active' => $product->is_active,
                        'is_low_stock' => $product->isLowStock(),
                        'is_out_of_stock' => $product->stock_quantity == 0,
                        'status' => $this->getProductStatus($product)
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $products,
                'summary' => [
                    'total_products' => $products->count(),
                    'active_products' => $products->where('is_active', true)->count(),
                    'low_stock_count' => $products->where('is_low_stock', true)->count(),
                    'out_of_stock_count' => $products->where('is_out_of_stock', true)->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get inventory status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product status for display
     */
    private function getProductStatus($product)
    {
        if (!$product->is_active) {
            return 'inactive';
        }
        
        if ($product->stock_quantity == 0) {
            return 'out_of_stock';
        }
        
        if ($product->isLowStock()) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    /**
     * Bulk update stock quantities
     */
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.product_id' => 'required|exists:products,id',
            'updates.*.quantity' => 'required|integer|min:0'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->updates as $update) {
                    $product = Product::findOrFail($update['product_id']);
                    $oldQuantity = $product->stock_quantity;
                    $product->stock_quantity = $update['quantity'];
                    $product->save();

                    Log::info('Bulk inventory update', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'old_quantity' => $oldQuantity,
                        'new_quantity' => $product->stock_quantity,
                        'updated_by' => Auth::id()
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Stock quantities updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock quantities: ' . $e->getMessage()
            ], 500);
        }
    }
}