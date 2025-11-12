<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        try {
            $categories = Category::withCount('products')->get();
            
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

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            Log::info('Category created', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category->loadCount('products')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create category', [
                'error' => $e->getMessage(),
                'category_name' => $request->name
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $oldName = $category->name;
            
            $category->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            Log::info('Category updated', [
                'category_id' => $category->id,
                'old_name' => $oldName,
                'new_name' => $category->name,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category->loadCount('products')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update category', [
                'error' => $e->getMessage(),
                'category_id' => $category->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has products. Please move or delete all products first.'
                ], 400);
            }

            $categoryName = $category->name;
            $category->delete();

            Log::info('Category deleted', [
                'category_name' => $categoryName,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete category', [
                'error' => $e->getMessage(),
                'category_id' => $category->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }
}