<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Kijabe POS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
            --success-500: #10b981;
            --success-600: #059669;
            --warning-500: #f59e0b;
            --warning-600: #d97706;
            --danger-500: #ef4444;
            --danger-600: #dc2626;
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
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            color: var(--gray-800);
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            color: white;
            padding: var(--space-6);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: var(--space-2);
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--space-6);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }

        .stat-card {
            background: white;
            padding: var(--space-6);
            border-radius: var(--radius-xl);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary-500);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-card.warning {
            border-left-color: var(--warning-500);
        }

        .stat-card.danger {
            border-left-color: var(--danger-500);
        }

        .stat-card.success {
            border-left-color: var(--success-500);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: var(--space-2);
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }

        .inventory-table-container {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: var(--gray-50);
            padding: var(--space-6);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .search-box {
            width: 300px;
            padding: var(--space-3);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
        }

        .search-box:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table th {
            background: var(--gray-50);
            padding: var(--space-4);
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .inventory-table td {
            padding: var(--space-4);
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.875rem;
        }

        .inventory-table tr:hover {
            background: var(--gray-50);
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-badge.in-stock {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-600);
        }

        .status-badge.low-stock {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-600);
        }

        .status-badge.out-of-stock {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-600);
        }

        .status-badge.inactive {
            background: rgba(107, 114, 128, 0.1);
            color: var(--gray-600);
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 500;
            margin-right: 0.25rem;
            transition: all 0.2s ease;
        }

        .action-btn.edit {
            background: var(--primary-100);
            color: var(--primary-600);
        }

        .action-btn.edit:hover {
            background: var(--primary-200);
        }

        .action-btn.toggle {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .action-btn.toggle:hover {
            background: var(--gray-200);
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: var(--space-6);
        }

        .sidebar-card {
            background: white;
            padding: var(--space-6);
            border-radius: var(--radius-xl);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .sidebar-card h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: var(--space-4);
        }

        .alert-item {
            padding: var(--space-3);
            background: var(--gray-50);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-3);
            border-left: 3px solid var(--warning-500);
        }

        .alert-item:last-child {
            margin-bottom: 0;
        }

        .alert-item.danger {
            border-left-color: var(--danger-500);
        }

        .alert-item h4 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: var(--space-2);
        }

        .alert-item p {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
        }

        .quick-action-btn {
            padding: var(--space-3);
            background: var(--primary-500);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background 0.2s ease;
        }

        .quick-action-btn:hover {
            background: var(--primary-600);
        }

        .quick-action-btn.secondary {
            background: var(--gray-500);
        }

        .quick-action-btn.secondary:hover {
            background: var(--gray-600);
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal {
            background: white;
            border-radius: var(--radius-xl);
            padding: var(--space-6);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: var(--space-4);
        }

        .form-group {
            margin-bottom: var(--space-4);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--space-2);
        }

        .form-input {
            width: 100%;
            padding: var(--space-3);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
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
        }

        .btn {
            padding: var(--space-3) var(--space-6);
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-500);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-600);
        }

        .btn-secondary {
            background: var(--gray-300);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-400);
        }

        .status-message {
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-4);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-message.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-600);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-message.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-600);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .status-message.info {
            background: rgba(14, 165, 233, 0.1);
            color: var(--primary-600);
            border: 1px solid rgba(14, 165, 233, 0.2);
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr 1fr;
            }

            .main-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                gap: var(--space-4);
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-boxes"></i> Inventory Management</h1>
            <p>Manage stock levels, monitor low inventory alerts, and control product availability</p>
        </div>
    </div>

    <div class="container">
        <!-- Status Message -->
        <div id="statusMessage" style="display: none;"></div>

        <!-- Dashboard Cards -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $products->count() }}</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $products->where('is_active', true)->count() }}</div>
                <div class="stat-label">Active Products</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $lowStockProducts->count() }}</div>
                <div class="stat-label">Low Stock Alerts</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-value">{{ $outOfStockProducts->count() }}</div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Inventory Table -->
            <div class="inventory-table-container">
                <div class="table-header">
                    <h2>Product Inventory</h2>
                    <input type="text" class="search-box" placeholder="Search products..." id="searchProducts">
                </div>
                
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
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <br>
                                    <small class="text-gray-500">Ksh {{ number_format($product->price, 2) }}</small>
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
                                <button class="action-btn edit" onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }})">
                                    <i class="fas fa-edit"></i> Edit Stock
                                </button>
                                <button class="action-btn toggle" onclick="toggleAvailability({{ $product->id }})">
                                    <i class="fas fa-toggle-{{ $product->is_active ? 'on' : 'off' }}"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Low Stock Alerts -->
                <div class="sidebar-card">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Low Stock Alerts</h3>
                    @forelse($lowStockProducts as $product)
                    <div class="alert-item {{ $product->stock_quantity == 0 ? 'danger' : '' }}">
                        <h4>{{ $product->name }}</h4>
                        <p>{{ $product->stock_quantity }} left (threshold: {{ $product->low_stock_threshold }})</p>
                    </div>
                    @empty
                    <p class="text-gray-500">No low stock alerts</p>
                    @endforelse
                </div>

                <!-- Quick Actions -->
                <div class="sidebar-card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="quick-actions">
                        <button class="quick-action-btn" onclick="refreshInventory()">
                            <i class="fas fa-sync"></i> Refresh Inventory
                        </button>
                        <button class="quick-action-btn secondary" onclick="exportInventory()">
                            <i class="fas fa-download"></i> Export Report
                        </button>
                        <button class="quick-action-btn secondary" onclick="bulkUpdateModal()">
                            <i class="fas fa-edit"></i> Bulk Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div class="modal-overlay" id="stockModal">
        <div class="modal">
            <h3>Update Stock</h3>
            <form id="stockUpdateForm">
                <input type="hidden" id="productId">
                
                <div class="form-group">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-input" id="productName" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Current Stock</label>
                    <input type="number" class="form-input" id="currentStock" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Action</label>
                    <select class="form-input" id="stockAction">
                        <option value="add">Add Stock</option>
                        <option value="subtract">Remove Stock</option>
                        <option value="set">Set Exact Amount</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-input" id="stockQuantity" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Reason (Optional)</label>
                    <input type="text" class="form-input" id="stockReason" placeholder="e.g., New delivery, Damaged goods, etc.">
                </div>

                <div class="form-group">
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" class="form-input" id="stockThreshold" min="0">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('stockModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Show status messages
        function showStatus(message, type = 'info') {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.className = `status-message ${type}`;
            statusDiv.textContent = message;
            statusDiv.style.display = 'block';
            
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        }

        // Open stock update modal
        function openStockModal(productId, productName, currentStock) {
            document.getElementById('productId').value = productId;
            document.getElementById('productName').value = productName;
            document.getElementById('currentStock').value = currentStock;
            document.getElementById('stockQuantity').value = '';
            document.getElementById('stockReason').value = '';
            document.getElementById('stockModal').style.display = 'flex';
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Handle stock update form submission
        document.getElementById('stockUpdateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                product_id: document.getElementById('productId').value,
                action: document.getElementById('stockAction').value,
                quantity: parseInt(document.getElementById('stockQuantity').value),
                reason: document.getElementById('stockReason').value
            };

            try {
                const response = await fetch('/inventory/update-stock', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    showStatus(result.message, 'success');
                    closeModal('stockModal');
                    
                    // Update threshold if provided
                    const threshold = document.getElementById('stockThreshold').value;
                    if (threshold) {
                        await updateThreshold(formData.product_id, parseInt(threshold));
                    }
                    
                    refreshInventory();
                } else {
                    showStatus(result.message, 'error');
                }
            } catch (error) {
                showStatus('Error updating stock: ' + error.message, 'error');
            }
        });

        // Update threshold
        async function updateThreshold(productId, threshold) {
            try {
                const response = await fetch('/inventory/update-threshold', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        threshold: threshold
                    })
                });

                const result = await response.json();
                if (!result.success) {
                    showStatus('Warning: ' + result.message, 'error');
                }
            } catch (error) {
                showStatus('Error updating threshold: ' + error.message, 'error');
            }
        }

        // Toggle product availability
        async function toggleAvailability(productId) {
            try {
                const response = await fetch('/inventory/toggle-availability', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showStatus(result.message, 'success');
                    refreshInventory();
                } else {
                    showStatus(result.message, 'error');
                }
            } catch (error) {
                showStatus('Error toggling availability: ' + error.message, 'error');
            }
        }

        // Refresh inventory data
        async function refreshInventory() {
            showStatus('Refreshing inventory...', 'info');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        // Export inventory (placeholder)
        function exportInventory() {
            showStatus('Export functionality coming soon...', 'info');
        }

        // Bulk update modal (placeholder)
        function bulkUpdateModal() {
            showStatus('Bulk update functionality coming soon...', 'info');
        }

        // Search products
        document.getElementById('searchProducts').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryTableBody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('td strong').textContent.toLowerCase();
                const category = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || category.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Close modal when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>