// Kitchen Manager Dashboard JavaScript
// Kijabe Hospital POS System - Kitchen Manager Functionality

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

// Search Functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchProducts');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
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
    }
}

// Status Message Helper
function showStatusMessage(message, type = 'success') {
    const statusDiv = document.getElementById('statusMessage');
    if (statusDiv) {
        statusDiv.className = `alert alert-${type}`;
        statusDiv.textContent = message;
        statusDiv.style.display = 'block';
        
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, 5000);
    } else {
        // Fallback to alert if no status div
        alert(message);
    }
}

// Stock Form Submission
function initializeStockForm() {
    const stockForm = document.getElementById('stockForm');
    if (stockForm) {
        stockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = formData.get('product_id');
            
            console.log('Submitting stock update for product:', productId);
            
            fetch(`/inventory/update-stock/${productId}`, {
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
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
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
                showStatusMessage('Error updating stock: ' + error.message, 'error');
            });
        });
    }
}

// Bulk Update Form Submission
function initializeBulkUpdateForm() {
    const bulkUpdateForm = document.getElementById('bulkUpdateForm');
    if (bulkUpdateForm) {
        bulkUpdateForm.addEventListener('submit', function(e) {
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
            
            fetch('/inventory/bulk-update', {
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
    }
}

// Toggle Availability
function toggleAvailability(productId) {
    fetch(`/inventory/toggle-availability/${productId}`, {
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
        const stockElement = row.querySelector('.stock-quantity');
        if (stockElement) stockElement.textContent = product.stock_quantity;
        
        // Update threshold
        const thresholdElement = row.querySelector('.threshold-value');
        if (thresholdElement) thresholdElement.textContent = product.low_stock_threshold;
        
        // Update status badge
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
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
        }
        
        // Update toggle button icon and text
        const toggleBtn = row.querySelector('.action-btn-small.toggle');
        if (toggleBtn) {
            const toggleIcon = toggleBtn.querySelector('i');
            const toggleText = toggleBtn.querySelector('span');
            
            if (toggleIcon) toggleIcon.className = `fas fa-toggle-${product.is_active ? 'on' : 'off'}`;
            if (toggleText) toggleText.textContent = product.is_active ? 'Active' : 'Inactive';
        }
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

// Category Management Functions
function openCategoryModal(categoryId = null, categoryName = '', categoryDescription = '') {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('categoryModalTitle');
    const submitText = document.getElementById('categorySubmitText');
    
    if (!modal || !form || !title || !submitText) return;
    
    if (categoryId) {
        // Edit mode
        document.getElementById('categoryId').value = categoryId;
        document.getElementById('categoryName').value = categoryName;
        document.getElementById('categoryDescription').value = categoryDescription;
        title.textContent = 'Edit Category';
        submitText.textContent = 'Update Category';
        form.action = 'PUT';
    } else {
        // Add mode
        form.reset();
        document.getElementById('categoryId').value = '';
        title.textContent = 'Add Category';
        submitText.textContent = 'Add Category';
        form.action = 'POST';
    }
    
    modal.style.display = 'block';
}

function closeCategoryModal() {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    if (modal) modal.style.display = 'none';
    if (form) form.reset();
}

function editCategory(categoryId, categoryName, categoryDescription) {
    openCategoryModal(categoryId, categoryName, categoryDescription);
}

function deleteCategory(categoryId, categoryName) {
    if (confirm(`Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`)) {
        fetch(`/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStatusMessage(data.message, 'success');
                // Remove the row from the table
                const row = document.querySelector(`tr[data-category-id="${categoryId}"]`);
                if (row) row.remove();
                
                // Update product category dropdown
                loadCategories();
            } else {
                showStatusMessage('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showStatusMessage('An error occurred while deleting the category.', 'error');
        });
    }
}

// Product Management Functions
function openProductModal(productId = null) {
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    const title = document.getElementById('productModalTitle');
    const submitText = document.getElementById('productSubmitText');
    
    if (!modal || !form || !title || !submitText) return;
    
    if (productId) {
        // Edit mode - fetch product data
        fetch(`/products`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.products.find(p => p.id === productId);
                if (product) {
                    document.getElementById('productId').value = product.id;
                    document.getElementById('productName').value = product.name;
                    document.getElementById('productCategory').value = product.category_id;
                    document.getElementById('productPrice').value = product.price;
                    document.getElementById('productStock').value = product.stock_quantity;
                    document.getElementById('productThreshold').value = product.low_stock_threshold;
                    document.getElementById('productDescription').value = product.description || '';
                    title.textContent = 'Edit Product';
                    submitText.textContent = 'Update Product';
                    form.action = 'PUT';
                }
            }
        });
    } else {
        // Add mode
        form.reset();
        document.getElementById('productId').value = '';
        title.textContent = 'Add Product';
        submitText.textContent = 'Add Product';
        form.action = 'POST';
    }
    
    modal.style.display = 'block';
}

function closeProductModal() {
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    if (modal) modal.style.display = 'none';
    if (form) form.reset();
}

function editProduct(productId) {
    openProductModal(productId);
}

function deleteProduct(productId, productName) {
    if (confirm(`Are you sure you want to delete the product "${productName}"? This action cannot be undone.`)) {
        fetch(`/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStatusMessage(data.message, 'success');
                // Remove the row from the table or update its status
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (data.action === 'deleted' && row) {
                    row.remove();
                } else if (data.action === 'deactivated' && row) {
                    // Update the status badge to show inactive
                    const statusBadge = row.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = 'status-badge inactive';
                        statusBadge.textContent = 'Inactive';
                    }
                }
                
                // Refresh inventory table
                loadInventory();
            } else {
                showStatusMessage('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showStatusMessage('An error occurred while deleting the product.', 'error');
        });
    }
}

function loadCategories() {
    fetch('/categories')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const categorySelect = document.getElementById('productCategory');
            if (categorySelect) {
                categorySelect.innerHTML = '<option value="">Select Category</option>';
                
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });
            }
        }
    })
    .catch(error => console.error('Error loading categories:', error));
}

function loadInventory() {
    // Placeholder for inventory refresh functionality
    location.reload();
}

// Form Submission Handlers
function initializeCategoryForm() {
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const categoryId = formData.get('category_id');
            const method = this.action === 'PUT' ? 'PUT' : 'POST';
            const url = categoryId ? `/categories/${categoryId}` : '/categories';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    description: formData.get('description')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatusMessage(data.message, 'success');
                    closeCategoryModal();
                    
                    // Update or add the category row in the table
                    if (categoryId) {
                        // Update existing row
                        const row = document.querySelector(`tr[data-category-id="${categoryId}"]`);
                        if (row) {
                            row.querySelector('.category-name').textContent = data.category.name;
                            row.querySelector('.category-description').textContent = data.category.description || 'No description';
                            row.querySelector('.product-count').textContent = data.category.products_count;
                        }
                    } else {
                        // Add new row - simple refresh for now
                        location.reload();
                    }
                    
                    // Update product category dropdown
                    loadCategories();
                } else {
                    showStatusMessage('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatusMessage('An error occurred while saving the category.', 'error');
            });
        });
    }
}

function initializeProductForm() {
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = formData.get('product_id');
            const method = this.action === 'PUT' ? 'PUT' : 'POST';
            const url = productId ? `/products/${productId}` : '/products';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    category_id: formData.get('category_id'),
                    price: formData.get('price'),
                    stock_quantity: formData.get('stock_quantity'),
                    low_stock_threshold: formData.get('low_stock_threshold'),
                    description: formData.get('description')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatusMessage(data.message, 'success');
                    closeProductModal();
                    
                    // Refresh the page to show updated data
                    location.reload();
                } else {
                    showStatusMessage('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatusMessage('An error occurred while saving the product.', 'error');
            });
        });
    }
}

// Initialize modal close functionality
function initializeModalHandlers() {
    // Close modals when clicking outside
    window.onclick = function(event) {
        const stockModal = document.getElementById('stockModal');
        const bulkModal = document.getElementById('bulkUpdateModal');
        const categoryModal = document.getElementById('categoryModal');
        const productModal = document.getElementById('productModal');
        
        if (event.target === stockModal) {
            closeStockModal();
        } else if (event.target === bulkModal) {
            closeBulkModal();
        } else if (event.target === categoryModal) {
            closeCategoryModal();
        } else if (event.target === productModal) {
            closeProductModal();
        }
    }
}

// Auto-refresh functionality
function initializeAutoRefresh() {
    // Auto-refresh every 30 seconds for real-time updates
    setInterval(() => {
        fetch('/api/inventory-status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stats without full page reload
                    const lowStockStat = document.querySelector('.stats-grid .stat-card:nth-child(3) .stat-value');
                    const outOfStockStat = document.querySelector('.stats-grid .stat-card:nth-child(4) .stat-value');
                    
                    if (lowStockStat) lowStockStat.textContent = data.stats.low_stock_alerts;
                    if (outOfStockStat) outOfStockStat.textContent = data.stats.out_of_stock;
                }
            })
            .catch(error => console.log('Auto-refresh failed:', error));
    }, 30000);
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeStockForm();
    initializeBulkUpdateForm();
    initializeCategoryForm();
    initializeProductForm();
    initializeModalHandlers();
    initializeAutoRefresh();
    
    console.log('Kitchen Manager Dashboard initialized');
});

// Make functions globally available
window.scrollToSection = scrollToSection;
window.openStockModal = openStockModal;
window.closeStockModal = closeStockModal;
window.bulkUpdateModal = bulkUpdateModal;
window.closeBulkModal = closeBulkModal;
window.toggleAvailability = toggleAvailability;
window.refreshInventory = refreshInventory;
window.exportInventory = exportInventory;
window.openCategoryModal = openCategoryModal;
window.closeCategoryModal = closeCategoryModal;
window.editCategory = editCategory;
window.deleteCategory = deleteCategory;
window.openProductModal = openProductModal;
window.closeProductModal = closeProductModal;
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;