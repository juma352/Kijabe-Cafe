<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the role-specific dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect to role-specific dashboard based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'kitchen_manager':
                return redirect()->route('dashboard.kitchen');
            case 'cashier':
                return redirect()->route('dashboard.cashier');
            default:
                return redirect()->route('login')->with('error', 'Invalid user role');
        }
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $data = [
            'user' => Auth::user(),
            'stats' => [
                'total_sales' => 0, // We'll implement this later
                'total_orders' => 0,
                'active_staff' => 0,
                'inventory_items' => 0,
            ]
        ];
        
        return view('dashboard.admin', $data);
    }

    /**
     * Kitchen Manager Dashboard
     */
    public function kitchenDashboard()
    {
        // Get inventory data for the kitchen manager
        $products = \App\Models\Product::with('category')->orderBy('name')->get();
        
        $lowStockProducts = \App\Models\Product::whereRaw('stock_quantity <= low_stock_threshold')
            ->with('category')
            ->orderBy('stock_quantity', 'asc')
            ->get();

        $outOfStockProducts = \App\Models\Product::where('stock_quantity', 0)
            ->with('category')
            ->get();

        $categories = \App\Models\Category::withCount('products')
            ->orderBy('name')
            ->get();

        $data = [
            'user' => Auth::user(),
            'products' => $products,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'categories' => $categories,
            'stats' => [
                'total_products' => $products->count(),
                'active_products' => $products->where('is_active', true)->count(),
                'low_stock_alerts' => $lowStockProducts->count(),
                'out_of_stock' => $outOfStockProducts->count(),
            ]
        ];
        
        return view('dashboard.kitchen-manager', $data);
    }

    /**
     * Cashier Dashboard - redirect to sales dashboard
     */
    public function cashierDashboard()
    {
        // Redirect cashiers directly to the sales dashboard
        return redirect()->route('sales.dashboard');
    }
}
