<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index()
    {
        try {
            $products = Product::with('category')->get();
            
            return response()->json([
                'success' => true,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'low_stock_threshold' => $request->low_stock_threshold,
                'description' => $request->description,
                'is_active' => true
            ]);

            $product->load('category');

            Log::info('Product created', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $product->category->name,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'product_name' => $request->name
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $oldData = [
                'name' => $product->name,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'stock_quantity' => $product->stock_quantity
            ];
            
            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'stock_quantity' => $request->stock_quantity,
                'low_stock_threshold' => $request->low_stock_threshold,
                'description' => $request->description
            ]);

            $product->load('category');

            Log::info('Product updated', [
                'product_id' => $product->id,
                'old_data' => $oldData,
                'new_data' => [
                    'name' => $product->name,
                    'category_id' => $product->category_id,
                    'price' => $product->price,
                    'stock_quantity' => $product->stock_quantity
                ],
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $product
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update product', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product has been sold (has sale items)
            if ($product->saleItems()->count() > 0) {
                // Instead of deleting, we'll deactivate the product
                $product->update(['is_active' => false]);
                
                Log::info('Product deactivated (has sales history)', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'deactivated_by' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Product deactivated successfully (product has sales history)',
                    'action' => 'deactivated'
                ]);
            }

            $productName = $product->name;
            $product->delete();

            Log::info('Product deleted', [
                'product_name' => $productName,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
                'action' => 'deleted'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all categories for dropdown in product forms
     */
    public function getCategories()
    {
        try {
            $categories = Category::select('id', 'name')->get();
            
            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories: ' . $e->getMessage()
            ], 500);
        }
    }
}