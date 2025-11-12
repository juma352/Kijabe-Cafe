@extends('layouts.dashboard')

@section('title', 'Kitchen Manager Dashboard')

@section('sidebar')
<nav>
    <a href="{{ route('dashboard') }}" class="nav-item active">
        📊 Dashboard
    </a>
    <a href="#inventory-section" class="nav-item" onclick="scrollToSection('inventory-section')">
        � Inventory Management
    </a>
    <a href="#low-stock-section" class="nav-item" onclick="scrollToSection('low-stock-section')">
        ⚠️ Low Stock Alerts
    </a>
    <a href="#categories-section" class="nav-item" onclick="scrollToSection('categories-section')">
        🏷️ Categories
    </a>
    <a href="#" class="nav-item">
        � Order Queue
    </a>
    <a href="#" class="nav-item">
        📈 Kitchen Reports
    </a>
</nav>
@endsection

@section('content')
<div class="page-title">Kitchen Manager Dashboard</div>
<div class="page-subtitle">Welcome {{ $user->name }}! Manage inventory and monitor kitchen operations.</div>

<!-- Status Message -->
<div id="statusMessage" style="display: none;"></div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total_products'] }}</div>
        <div class="stat-label">Total Products</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value">{{ $stats['active_products'] }}</div>
        <div class="stat-label">Active Products</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-value">{{ $stats['low_stock_alerts'] }}</div>
        <div class="stat-label">Low Stock Alerts</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-value">{{ $stats['out_of_stock'] }}</div>
        <div class="stat-label">Out of Stock</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions-section">
    <h3>Quick Actions</h3>
    <div class="action-buttons">
        <button class="action-btn primary" onclick="refreshInventory()">
            <i class="fas fa-sync"></i> Refresh Inventory
        </button>
        <button class="action-btn secondary" onclick="bulkUpdateModal()">
            <i class="fas fa-edit"></i> Bulk Update Stock
        </button>
        <button class="action-btn secondary" onclick="exportInventory()">
            <i class="fas fa-download"></i> Export Report
        </button>
    </div>
</div>

<!-- Inventory Management Section -->
<div id="inventory-section" class="content-section">
    <div class="section-header">
        <h3><i class="fas fa-boxes"></i> Inventory Management</h3>
        <div class="search-container">
            <input type="text" id="searchProducts" placeholder="Search products..." class="search-input">
            <button class="search-btn"><i class="fas fa-search"></i></button>
        </div>
    </div>
    
    <div class="inventory-table-container">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Threshold</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="inventoryTableBody">
                @foreach($products as $product)
                <tr data-product-id="{{ $product->id }}">
                    <td>
                        <div class="product-info">
                            <strong>{{ $product->name }}</strong>
                            <br>
                            <small class="price">Ksh {{ number_format($product->price, 2) }}</small>
                        </div>
                    </td>
                    <td>{{ $product->category->name }}</td>
                    <td>
                        <span class="stock-quantity">{{ $product->stock_quantity }}</span>
                    </td>
                    <td>
                        <span class="threshold-value">{{ $product->low_stock_threshold }}</span>
                    </td>
                    <td>
                        @php
                            $status = 'in-stock';
                            $statusText = 'In Stock';
                            
                            if (!$product->is_active) {
                                $status = 'inactive';
                                $statusText = 'Inactive';
                            } elseif ($product->stock_quantity == 0) {
                                $status = 'out-of-stock';
                                $statusText = 'Out of Stock';
                            } elseif ($product->isLowStock()) {
                                $status = 'low-stock';
                                $statusText = 'Low Stock';
                            }
                        @endphp
                        <span class="status-badge {{ $status }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-small">
                            <button class="action-btn-small edit" onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }}, {{ $product->low_stock_threshold }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn-small toggle" onclick="toggleAvailability({{ $product->id }})">
                                <i class="fas fa-toggle-{{ $product->is_active ? 'on' : 'off' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Low Stock Alerts Section -->
<div id="low-stock-section" class="content-section">
    <div class="section-header">
        <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h3>
    </div>
    
    <div class="alerts-grid">
        @forelse($lowStockProducts as $product)
        <div class="alert-card {{ $product->stock_quantity == 0 ? 'danger' : 'warning' }}">
            <div class="alert-header">
                <h4>{{ $product->name }}</h4>
                <span class="alert-badge">{{ $product->stock_quantity == 0 ? 'Out of Stock' : 'Low Stock' }}</span>
            </div>
            <div class="alert-body">
                <p><strong>Current Stock:</strong> {{ $product->stock_quantity }}</p>
                <p><strong>Threshold:</strong> {{ $product->low_stock_threshold }}</p>
                <p><strong>Category:</strong> {{ $product->category->name }}</p>
            </div>
            <div class="alert-actions">
                <button class="action-btn-small primary" onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }}, {{ $product->low_stock_threshold }})">
                    Update Stock
                </button>
            </div>
        </div>
        @empty
        <div class="no-alerts">
            <i class="fas fa-check-circle"></i>
            <p>No low stock alerts - all products are well stocked!</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Categories Section -->
<div id="categories-section" class="content-section">
    <div class="section-header">
        <h3><i class="fas fa-tags"></i> Category Overview</h3>
    </div>
    
    <div class="categories-grid">
        @foreach($categories as $category)
        <div class="category-card">
            <div class="category-header">
                <h4>{{ $category->name }}</h4>
                <span class="product-count">{{ $category->products_count }} products</span>
            </div>
            <div class="category-stats">
                @php
                    $categoryStats = $category->products->groupBy(function($product) {
                        if (!$product->is_active) return 'inactive';
                        if ($product->stock_quantity == 0) return 'out_of_stock';
                        if ($product->isLowStock()) return 'low_stock';
                        return 'in_stock';
                    });
                @endphp
                <div class="stat-item success">
                    <span class="stat-number">{{ $categoryStats->get('in_stock', collect())->count() }}</span>
                    <span class="stat-label">In Stock</span>
                </div>
                <div class="stat-item warning">
                    <span class="stat-number">{{ $categoryStats->get('low_stock', collect())->count() }}</span>
                    <span class="stat-label">Low Stock</span>
                </div>
                <div class="stat-item danger">
                    <span class="stat-number">{{ $categoryStats->get('out_of_stock', collect())->count() }}</span>
                    <span class="stat-label">Out of Stock</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Update Stock Modal -->
<div id="stockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Stock</h3>
            <button class="close-btn" onclick="closeStockModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="stockForm">
                @csrf
                <input type="hidden" id="productId" name="product_id">
                
                <div class="form-group">
                    <label for="productName">Product:</label>
                    <input type="text" id="productName" readonly class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="stockQuantity">Current Stock:</label>
                    <input type="number" id="stockQuantity" name="stock_quantity" min="0" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="lowStockThreshold">Low Stock Threshold:</label>
                    <input type="number" id="lowStockThreshold" name="low_stock_threshold" min="1" class="form-input" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn secondary" onclick="closeStockModal()">Cancel</button>
                    <button type="submit" class="btn primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div id="bulkUpdateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Bulk Update Stock</h3>
            <button class="close-btn" onclick="closeBulkModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="bulkUpdateForm">
                @csrf
                <div class="bulk-options">
                    <label class="checkbox-label">
                        <input type="radio" name="operation" value="add">
                        Add to existing stock
                    </label>
                    <label class="checkbox-label">
                        <input type="radio" name="operation" value="set">
                        Set stock quantity
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="bulkQuantity">Quantity:</label>
                    <input type="number" id="bulkQuantity" name="quantity" min="0" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="selectedProducts">Select Products:</label>
                    <div class="product-selection">
                        @foreach($products as $product)
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}">
                            {{ $product->name }} ({{ $product->stock_quantity }})
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn secondary" onclick="closeBulkModal()">Cancel</button>
                    <button type="submit" class="btn primary">Update Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    /* Kijabe Hospital Brand Colors */
    --primary-50: #f0f9ff;
    --primary-100: #e0f2fe;
    --primary-500: #0ea5e9;
    --primary-600: #0284c7;
    --primary-700: #0369a1;
    --primary-900: #0c4a6e;
    
    /* Medical Theme Accents */
    --medical-green: #10b981;
    --medical-red: #ef4444;
    --medical-orange: #f59e0b;
    --medical-purple: #8b5cf6;
    
    /* Neutral Palette */
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    
    /* Spacing & Typography */
    --space-1: 0.25rem;
    --space-2: 0.5rem;
    --space-3: 0.75rem;
    --space-4: 1rem;
    --space-6: 1.5rem;
    --space-8: 2rem;
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    
    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

/* Override stat card styles with hospital theme */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: var(--space-6);
    margin-bottom: var(--space-8);
}

.stat-card {
    background: linear-gradient(135deg, white, var(--gray-50));
    border-radius: var(--radius-xl);
    padding: var(--space-6);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
    border-left: 4px solid var(--medical-green);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(16, 185, 129, 0.03) 100%);
    pointer-events: none;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-left-color: var(--primary-600);
}

.stat-card.success {
    border-left-color: var(--medical-green);
}

.stat-card.success::before {
    background: linear-gradient(135deg, transparent 0%, rgba(16, 185, 129, 0.05) 100%);
}

.stat-card.warning {
    border-left-color: var(--medical-orange);
}

.stat-card.warning::before {
    background: linear-gradient(135deg, transparent 0%, rgba(245, 158, 11, 0.05) 100%);
}

.stat-card.danger {
    border-left-color: var(--medical-red);
}

.stat-card.danger::before {
    background: linear-gradient(135deg, transparent 0%, rgba(239, 68, 68, 0.05) 100%);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: var(--space-2);
    position: relative;
    z-index: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    position: relative;
    z-index: 1;
}

/* Page title and subtitle with hospital theme */
.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-700);
    margin-bottom: var(--space-2);
}

.page-subtitle {
    color: var(--gray-600);
    font-size: 1.125rem;
    margin-bottom: var(--space-8);
    font-weight: 500;
}

/* Inventory Management Styles */
.content-section {
    margin-bottom: 2rem;
    background: white;
    border-radius: var(--radius-lg);
    padding: var(--space-6);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-6);
    border-bottom: 2px solid var(--primary-100);
    padding-bottom: var(--space-4);
}

.section-header h3 {
    color: var(--primary-700);
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.search-container {
    display: flex;
    gap: var(--space-2);
}

.search-input {
    padding: var(--space-2);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    width: 250px;
    font-size: 0.875rem;
    transition: border-color 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.search-btn {
    padding: var(--space-2) var(--space-4);
    background: var(--primary-600);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-btn:hover {
    background: var(--primary-700);
}

.inventory-table-container {
    overflow-x: auto;
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
}

.inventory-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0;
}

.inventory-table th,
.inventory-table td {
    text-align: left;
    padding: var(--space-3);
    border-bottom: 1px solid var(--gray-200);
}

.inventory-table th {
    background: var(--gray-50);
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.inventory-table tbody tr:hover {
    background: var(--primary-50);
}

.product-info strong {
    color: var(--primary-700);
    font-size: 0.95rem;
    font-weight: 600;
}

.price {
    color: var(--medical-green);
    font-weight: 600;
    font-size: 0.875rem;
}

.stock-quantity {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--gray-900);
}

.threshold-value {
    color: var(--gray-500);
    font-size: 0.875rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-xl);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-badge.in-stock {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border: 1px solid #10b981;
}

.status-badge.low-stock {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid var(--medical-orange);
}

.status-badge.out-of-stock {
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    color: #991b1b;
    border: 1px solid var(--medical-red);
}

.status-badge.inactive {
    background: linear-gradient(135deg, var(--gray-200), var(--gray-300));
    color: var(--gray-600);
    border: 1px solid var(--gray-400);
}

.action-buttons-small {
    display: flex;
    gap: var(--space-1);
}

.action-btn-small {
    padding: 0.375rem 0.5rem;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn-small.edit {
    background: var(--primary-600);
    color: white;
}

.action-btn-small.edit:hover {
    background: var(--primary-700);
    transform: translateY(-1px);
}

.action-btn-small.toggle {
    background: var(--medical-green);
    color: white;
}

.action-btn-small.toggle:hover {
    background: #059669;
    transform: translateY(-1px);
}

.alerts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--space-4);
    margin-top: var(--space-4);
}

.alert-card {
    border-radius: var(--radius-lg);
    padding: var(--space-6);
    border-left: 4px solid;
    box-shadow: var(--shadow-md);
    transition: transform 0.2s ease;
}

.alert-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.alert-card.warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-left-color: var(--medical-orange);
}

.alert-card.danger {
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    border-left-color: var(--medical-red);
}

.alert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-3);
}

.alert-header h4 {
    margin: 0;
    color: var(--gray-900);
    font-weight: 600;
}

.alert-badge {
    padding: 0.25rem 0.75rem;
    background: rgba(0,0,0,0.1);
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.alert-body p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.alert-actions {
    margin-top: var(--space-4);
}

.no-alerts {
    text-align: center;
    padding: var(--space-8);
    color: var(--gray-500);
    grid-column: 1 / -1;
}

.no-alerts i {
    font-size: 3rem;
    color: var(--medical-green);
    margin-bottom: var(--space-4);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--space-4);
    margin-top: var(--space-4);
}

.category-card {
    background: linear-gradient(135deg, var(--primary-50), white);
    border: 1px solid var(--primary-200);
    border-radius: var(--radius-lg);
    padding: var(--space-6);
    box-shadow: var(--shadow-md);
    transition: all 0.2s ease;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-300);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-4);
    border-bottom: 1px solid var(--primary-200);
    padding-bottom: var(--space-3);
}

.category-header h4 {
    margin: 0;
    color: var(--primary-700);
    font-weight: 600;
}

.product-count {
    background: var(--primary-100);
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    color: var(--primary-700);
    font-weight: 600;
}

.category-stats {
    display: flex;
    justify-content: space-between;
    gap: var(--space-3);
}

.stat-item {
    text-align: center;
    flex: 1;
    padding: var(--space-2);
    border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.5);
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}

.stat-item.success .stat-number {
    color: var(--medical-green);
}

.stat-item.warning .stat-number {
    color: var(--medical-orange);
}

.stat-item.danger .stat-number {
    color: var(--medical-red);
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.quick-actions-section {
    margin-bottom: var(--space-8);
    background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 50%, var(--primary-900) 100%);
    color: white;
    padding: var(--space-8);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.quick-actions-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="medical" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23medical)"/></svg>');
    pointer-events: none;
}

.quick-actions-section h3 {
    margin: 0 0 var(--space-6) 0;
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.action-buttons {
    display: flex;
    gap: var(--space-4);
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}

.action-btn {
    padding: var(--space-4) var(--space-6);
    border: none;
    border-radius: var(--radius-lg);
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: var(--space-2);
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.action-btn.primary {
    background: rgba(255,255,255,0.2);
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
}

.action-btn.secondary {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 2px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-xl);
    background: rgba(255,255,255,0.3);
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
    overflow-y: auto;
}

.modal-content {
    background: linear-gradient(135deg, white, var(--gray-50));
    margin: 5% auto;
    padding: 0;
    border-radius: var(--radius-xl);
    width: 90%;
    max-width: 500px;
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--gray-200);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-6);
    border-bottom: 1px solid var(--gray-200);
    background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
    border-radius: var(--radius-xl) var(--radius-xl) 0 0;
}

.modal-header h3 {
    margin: 0;
    color: var(--primary-700);
    font-weight: 600;
    font-size: 1.25rem;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-500);
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    transition: all 0.2s ease;
}

.close-btn:hover {
    color: var(--medical-red);
    background: rgba(239, 68, 68, 0.1);
}

.modal-body {
    padding: var(--space-6);
}

.form-group {
    margin-bottom: var(--space-4);
}

.form-group label {
    display: block;
    margin-bottom: var(--space-2);
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.875rem;
}

.form-input {
    width: 100%;
    padding: var(--space-3);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--space-3);
    margin-top: var(--space-6);
    padding-top: var(--space-4);
    border-top: 1px solid var(--gray-200);
}

.btn {
    padding: var(--space-3) var(--space-6);
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.btn.primary {
    background: var(--primary-600);
    color: white;
}

.btn.primary:hover {
    background: var(--primary-700);
    transform: translateY(-1px);
}

.btn.secondary {
    background: var(--gray-500);
    color: white;
}

.btn.secondary:hover {
    background: var(--gray-600);
    transform: translateY(-1px);
}

.bulk-options {
    margin-bottom: var(--space-4);
    padding: var(--space-4);
    background: var(--primary-50);
    border-radius: var(--radius-md);
    border: 1px solid var(--primary-200);
}

.checkbox-label {
    display: block;
    margin-bottom: var(--space-2);
    cursor: pointer;
    padding: var(--space-2);
    border-radius: var(--radius-sm);
    transition: background-color 0.2s ease;
}

.checkbox-label:hover {
    background: rgba(14, 165, 233, 0.05);
}

.checkbox-label input[type="checkbox"], 
.checkbox-label input[type="radio"] {
    margin-right: var(--space-2);
    accent-color: var(--primary-600);
}

.product-selection {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-md);
    padding: var(--space-2);
    background: var(--gray-50);
}

/* Alert styles for status messages */
.alert {
    padding: var(--space-4);
    margin-bottom: var(--space-4);
    border-radius: var(--radius-md);
    border: 1px solid transparent;
    font-weight: 500;
}

.alert-success {
    color: #065f46;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-color: var(--medical-green);
}

.alert-error {
    color: #991b1b;
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    border-color: var(--medical-red);
}

.alert-info {
    color: var(--primary-900);
    background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
    border-color: var(--primary-500);
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .alerts-grid,
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .section-header {
        flex-direction: column;
        gap: var(--space-4);
        align-items: stretch;
    }
    
    .search-container {
        width: 100%;
    }
    
    .search-input {
        width: 100%;
    }
    
    .inventory-table-container {
        overflow-x: scroll;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .quick-actions-section {
        padding: var(--space-6);
    }
}
</style>

<script>
// Navigation Helper
function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({
        behavior: 'smooth'
    });
}

// Modal Management
function openStockModal(productId, productName, stockQuantity, lowStockThreshold) {
    document.getElementById('productId').value = productId;
    document.getElementById('productName').value = productName;
    document.getElementById('stockQuantity').value = stockQuantity;
    document.getElementById('lowStockThreshold').value = lowStockThreshold;
    document.getElementById('stockModal').style.display = 'block';
}

function closeStockModal() {
    document.getElementById('stockModal').style.display = 'none';
}

function bulkUpdateModal() {
    document.getElementById('bulkUpdateModal').style.display = 'block';
}

function closeBulkModal() {
    document.getElementById('bulkUpdateModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    const stockModal = document.getElementById('stockModal');
    const bulkModal = document.getElementById('bulkUpdateModal');
    
    if (event.target == stockModal) {
        closeStockModal();
    } else if (event.target == bulkModal) {
        closeBulkModal();
    }
}

// Search Functionality
document.getElementById('searchProducts').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#inventoryTableBody tr');
    
    tableRows.forEach(row => {
        const productName = row.querySelector('.product-info strong').textContent.toLowerCase();
        const category = row.cells[1].textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || category.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Status Message Helper
function showStatusMessage(message, type = 'success') {
    const statusDiv = document.getElementById('statusMessage');
    statusDiv.className = `alert alert-${type}`;
    statusDiv.textContent = message;
    statusDiv.style.display = 'block';
    
    setTimeout(() => {
        statusDiv.style.display = 'none';
    }, 5000);
}

// Stock Form Submission
document.getElementById('stockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const productId = formData.get('product_id');
    
    fetch(`/api/inventory/update-stock/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            stock_quantity: formData.get('stock_quantity'),
            low_stock_threshold: formData.get('low_stock_threshold')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatusMessage('Stock updated successfully!', 'success');
            updateTableRow(productId, data.product);
            closeStockModal();
        } else {
            showStatusMessage(data.message || 'Error updating stock', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusMessage('Error updating stock', 'error');
    });
});

// Bulk Update Form Submission
document.getElementById('bulkUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const selectedProducts = Array.from(document.querySelectorAll('input[name="product_ids[]"]:checked')).map(input => input.value);
    const operation = document.querySelector('input[name="operation"]:checked')?.value;
    
    if (!selectedProducts.length) {
        showStatusMessage('Please select at least one product', 'error');
        return;
    }
    
    if (!operation) {
        showStatusMessage('Please select an operation type', 'error');
        return;
    }
    
    fetch('/api/inventory/bulk-update', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_ids: selectedProducts,
            operation: operation,
            quantity: formData.get('quantity')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatusMessage(`${data.updated_count} products updated successfully!`, 'success');
            closeBulkModal();
            // Refresh the page to show updated data
            setTimeout(() => location.reload(), 1000);
        } else {
            showStatusMessage(data.message || 'Error updating products', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusMessage('Error updating products', 'error');
    });
});

// Toggle Availability
function toggleAvailability(productId) {
    fetch(`/api/inventory/toggle-availability/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatusMessage(data.message, 'success');
            updateTableRow(productId, data.product);
        } else {
            showStatusMessage(data.message || 'Error toggling availability', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatusMessage('Error toggling availability', 'error');
    });
}

// Update Table Row Helper
function updateTableRow(productId, product) {
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (row) {
        // Update stock quantity
        row.querySelector('.stock-quantity').textContent = product.stock_quantity;
        
        // Update threshold
        row.querySelector('.threshold-value').textContent = product.low_stock_threshold;
        
        // Update status badge
        const statusBadge = row.querySelector('.status-badge');
        let status = 'in-stock';
        let statusText = 'In Stock';
        
        if (!product.is_active) {
            status = 'inactive';
            statusText = 'Inactive';
        } else if (product.stock_quantity == 0) {
            status = 'out-of-stock';
            statusText = 'Out of Stock';
        } else if (product.stock_quantity <= product.low_stock_threshold) {
            status = 'low-stock';
            statusText = 'Low Stock';
        }
        
        statusBadge.className = `status-badge ${status}`;
        statusBadge.textContent = statusText;
        
        // Update toggle button icon
        const toggleBtn = row.querySelector('.action-btn-small.toggle i');
        toggleBtn.className = `fas fa-toggle-${product.is_active ? 'on' : 'off'}`;
    }
}

// Refresh Inventory
function refreshInventory() {
    location.reload();
}

// Export Inventory (placeholder)
function exportInventory() {
    showStatusMessage('Export feature coming soon!', 'info');
}

// Auto-refresh every 30 seconds for real-time updates
setInterval(() => {
    fetch('/api/inventory/status')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats without full page reload
                document.querySelector('.stats-grid .stat-card:nth-child(3) .stat-value').textContent = data.stats.low_stock_alerts;
                document.querySelector('.stats-grid .stat-card:nth-child(4) .stat-value').textContent = data.stats.out_of_stock;
            }
        })
        .catch(error => console.log('Auto-refresh failed:', error));
}, 30000);
</script>
@endsection