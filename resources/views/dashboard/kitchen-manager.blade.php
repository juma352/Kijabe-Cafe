@extends('layouts.dashboard')

@section('title', 'Kitchen Manager Dashboard')

@section('sidebar')
<nav>
    <a href="{{ route('dashboard') }}" class="nav-item active">
        <i class="fas fa-chart-bar"></i> Dashboard
    </a>
    <a href="#inventory-section" class="nav-item" onclick="scrollToSection('inventory-section')">
        <i class="fas fa-boxes"></i> Inventory Management
    </a>
    <a href="#low-stock-section" class="nav-item" onclick="scrollToSection('low-stock-section')">
        <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
    </a>
    <a href="#categories-section" class="nav-item" onclick="scrollToSection('categories-section')">
        <i class="fas fa-tags"></i> Category Management
    </a>
    <a href="#products-section" class="nav-item" onclick="scrollToSection('products-section')">
        <i class="fas fa-box"></i> Product Management
    </a>
    <a href="#" class="nav-item">
        <i class="fas fa-clipboard-list"></i> Order Queue
    </a>
    <a href="#" class="nav-item">
        <i class="fas fa-chart-line"></i> Kitchen Reports
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
    <h3><i class="fas fa-rocket"></i> Quick Actions</h3>
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
                            <button class="action-btn-small edit" onclick="openStockModal({{ $product->id }}, {{ json_encode($product->name) }}, {{ $product->stock_quantity }}, {{ $product->low_stock_threshold }})" title="Update Stock">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button class="action-btn-small toggle" onclick="toggleAvailability({{ $product->id }})" title="Toggle Availability">
                                <i class="fas fa-toggle-{{ $product->is_active ? 'on' : 'off' }}"></i>
                                <span>{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
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
                <button class="action-btn-small primary" onclick="openStockModal({{ $product->id }}, {{ json_encode($product->name) }}, {{ $product->stock_quantity }}, {{ $product->low_stock_threshold }})">>
                    <i class="fas fa-plus"></i> Update Stock
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

<!-- Category Management Section -->
<div id="categories-section" class="content-section">
    <div class="section-header">
        <h3><i class="fas fa-tags"></i> Category Management</h3>
        <button class="action-btn primary" onclick="openCategoryModal()" style="background: var(--primary-600); color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>
    
    @if($categories->count() > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Product Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTableBody">
                @foreach($categories as $category)
                <tr data-category-id="{{ $category->id }}">
                    <td class="category-name">{{ $category->name }}</td>
                    <td class="category-description">{{ $category->description ?? 'No description' }}</td>
                    <td class="product-count">{{ $category->products_count }}</td>
                    <td class="actions">
                        <button class="action-btn-small edit" onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}')" title="Edit Category">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn-small danger" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" title="Delete Category">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-tags fa-3x" style="color: var(--gray-400); margin-bottom: 1rem;"></i>
        <h4>No Categories Found</h4>
        <p>Start by creating your first category to organize your products.</p>
        <button class="action-btn primary" onclick="openCategoryModal()">
            <i class="fas fa-plus"></i> Create First Category
        </button>
    </div>
    @endif
</div>

<!-- Product Management Section -->
{{-- <div id="products-section" class="content-section">
    <div class="section-header">
        <h3><i class="fas fa-box"></i> Product Management</h3>
        <button class="action-btn primary" onclick="openProductModal()">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>
    
    <div class="products-overview">
        <div class="products-stats">
            <div class="stat-item">
                <span class="stat-number">{{ $products->count() }}</span>
                <span class="stat-label">Total Products</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $products->where('is_active', true)->count() }}</span>
                <span class="stat-label">Active Products</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $categories->count() }}</span>
                <span class="stat-label">Categories</span>
            </div>
        </div>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                @foreach($products as $product)
                <tr data-product-id="{{ $product->id }}">
                    <td class="product-name">{{ $product->name }}</td>
                    <td class="product-category">{{ $product->category->name }}</td>
                    <td class="product-price">KSh {{ number_format($product->price, 2) }}</td>
                    <td class="product-active">
                        @if($product->is_active)
                            <span class="status-badge active">Active</span>
                        @else
                            <span class="status-badge inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="actions">
                        <button class="action-btn-small edit" onclick="editProduct({{ $product->id }})" title="Edit Product">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn-small danger" onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')" title="Delete Product">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div> --}}

<!-- Update Stock Modal -->
<div id="stockModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Update Stock</h3>
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
                    <button type="submit" class="btn primary"><i class="fas fa-save"></i> Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div id="bulkUpdateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-boxes"></i> Bulk Update Stock</h3>
            <button class="close-btn" onclick="closeBulkModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="bulkUpdateForm">
                @csrf
                <div class="bulk-options">
                    <label class="checkbox-label">
                        <input type="radio" name="operation" value="add">
                        <i class="fas fa-plus"></i> Add to existing stock
                    </label>
                    <label class="checkbox-label">
                        <input type="radio" name="operation" value="set">
                        <i class="fas fa-edit"></i> Set stock quantity
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
                    <button type="submit" class="btn primary"><i class="fas fa-save"></i> Update Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-tags"></i> <span id="categoryModalTitle">Add Category</span></h3>
            <button class="close-btn" onclick="closeCategoryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="category_id">
                
                <div class="form-group">
                    <label for="categoryName">Category Name <span class="required">*</span></label>
                    <input type="text" id="categoryName" name="name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="categoryDescription">Description</label>
                    <textarea id="categoryDescription" name="description" class="form-input" rows="3" placeholder="Optional description for this category"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn secondary" onclick="closeCategoryModal()">Cancel</button>
                    <button type="submit" class="btn primary"><i class="fas fa-save"></i> <span id="categorySubmitText">Add Category</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content product-modal">
        <div class="modal-header">
            <h3><i class="fas fa-box"></i> <span id="productModalTitle">Add Product</span></h3>
            <button class="close-btn" onclick="closeProductModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="productForm">
                @csrf
                <input type="hidden" id="productId" name="product_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productName">Product Name <span class="required">*</span></label>
                        <input type="text" id="productName" name="name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="productCategory">Category <span class="required">*</span></label>
                        <select id="productCategory" name="category_id" class="form-input" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productPrice">Price (KSh) <span class="required">*</span></label>
                        <input type="number" id="productPrice" name="price" class="form-input" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="productStock">Initial Stock <span class="required">*</span></label>
                        <input type="number" id="productStock" name="stock_quantity" class="form-input" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="productThreshold">Low Stock Threshold <span class="required">*</span></label>
                        <input type="number" id="productThreshold" name="low_stock_threshold" class="form-input" min="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="productDescription">Description</label>
                    <textarea id="productDescription" name="description" class="form-input" rows="3" placeholder="Optional product description"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn secondary" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" class="btn primary"><i class="fas fa-save"></i> <span id="productSubmitText">Add Product</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS and JavaScript now loaded via Vite assets -->
<!-- All styles moved to resources/css/kitchen-manager.css -->
<!-- All scripts moved to resources/js/kitchen-manager.js -->



@vite(['resources/css/kitchen-manager.css', 'resources/js/kitchen-manager.js'])
@endsection