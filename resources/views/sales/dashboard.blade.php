<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kijabe Hospital POS - Sales Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            height: 100vh;
        }
        
        /* Header */
        .header {
            background: #1e40af;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .logo { font-size: 1.5rem; font-weight: bold; }
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .logout-btn {
            background: #0891b2;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            background: #0e7490;
        }
        
        /* Main Container */
        .main-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .dashboard-title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
        }
        
        .new-sale-btn {
            background: #1e40af;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            font-size: 1.125rem;
            transition: all 0.2s;
        }
        
        .new-sale-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
        
        /* Sales Grid */
        .sales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .sale-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .sale-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            border-color: #1e40af;
        }
        
        /* Special styling for empty sales */
        .sale-card.empty-sale {
            border-color: #f59e0b;
            border-style: dashed;
        }
        
        .sale-card.empty-sale:hover {
            border-color: #d97706;
            background: #fffbeb;
        }
        
        .sale-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .sale-number {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1e40af;
        }
        
        .sale-status {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-pending-payment {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .sale-details {
            margin-bottom: 1rem;
        }
        
        .sale-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .sale-detail-label {
            color: #6b7280;
        }
        
        .sale-detail-value {
            font-weight: 500;
            color: #111827;
        }
        
        .sale-items {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f3f4f6;
        }
        
        .sale-items-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .sale-item {
            font-size: 0.75rem;
            color: #4b5563;
            margin-bottom: 0.25rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .empty-state-description {
            font-size: 1rem;
        }
        
        /* Loading State */
        .loading {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">Kijabe Hospital - Cafeteria POS</div>
        <div class="user-info">
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Sales Dashboard</h1>
            <div style="display: flex; gap: 0.5rem;">
                <button class="new-sale-btn" onclick="createNewSale()">+ New Sale</button>
                <button class="sample-sale-btn" onclick="createSampleSale()" style="background: #059669; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; cursor: pointer; font-weight: 500; transition: all 0.2s;">🧪 Sample Sale</button>
            </div>
        </div>

        @if($sales->count() > 0)
            <div class="sales-grid">
                @foreach($sales as $sale)
                    <div class="sale-card {{ $sale->saleItems->count() === 0 ? 'empty-sale' : '' }}" onclick="openSale({{ $sale->id }})">
                        <div class="sale-header">
                            <div class="sale-number">Sale {{ $sale->sale_number }}</div>
                            <div class="sale-status" style="background-color: {{ $sale->status_color }}; color: white;">
                                {{ $sale->status_display }}
                            </div>
                        </div>
                        
                        <div class="sale-details">
                            <div class="sale-detail">
                                <span class="sale-detail-label">Customer:</span>
                                <span class="sale-detail-value">{{ $sale->customer_phone ?: 'Walk-in' }}</span>
                            </div>
                            <div class="sale-detail">
                                <span class="sale-detail-label">Total:</span>
                                <span class="sale-detail-value">KSh {{ number_format($sale->total, 2) }}</span>
                            </div>
                            <div class="sale-detail">
                                <span class="sale-detail-label">Items:</span>
                                <span class="sale-detail-value">{{ $sale->saleItems->count() }} item(s)</span>
                            </div>
                            <div class="sale-detail">
                                <span class="sale-detail-label">Created:</span>
                                <span class="sale-detail-value">{{ $sale->created_at->format('M j, Y H:i') }}</span>
                            </div>
                            <div class="sale-detail">
                                <span class="sale-detail-label">Cashier:</span>
                                <span class="sale-detail-value">{{ $sale->cashier->name }}</span>
                            </div>
                        </div>

                        @if($sale->saleItems->count() > 0)
                            <div class="sale-items">
                                <div class="sale-items-title">Items ({{ $sale->saleItems->count() }}):</div>
                                @foreach($sale->saleItems->take(3) as $item)
                                    <div class="sale-item">
                                        {{ $item->quantity }}x {{ $item->product->name }} - KSh {{ number_format($item->subtotal, 2) }}
                                    </div>
                                @endforeach
                                @if($sale->saleItems->count() > 3)
                                    <div class="sale-item">... and {{ $sale->saleItems->count() - 3 }} more</div>
                                @endif
                            </div>
                        @else
                            <div class="sale-items">
                                <div class="sale-items-title">No items added yet</div>
                                <div style="margin-top: 0.5rem; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem; text-align: center; color: #92400e; font-size: 0.875rem; font-weight: 500;">
                                    <div style="margin-bottom: 0.25rem;">👆 Click this card to add items</div>
                                    <div style="font-size: 0.75rem; opacity: 0.8;">Cannot process payments until items are added</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🛒</div>
                <h2 class="empty-state-title">No Active Sales</h2>
                <p class="empty-state-description">Click "New Sale" to create your first sale</p>
            </div>
        @endif
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        async function createNewSale() {
            try {
                const response = await fetch('/sales/create-blank', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_phone: 'Walk-in'
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        // Redirect to work on the new sale
                        window.location.href = `/sales/${result.sale.id}`;
                    } else {
                        alert('Failed to create sale: ' + result.message);
                    }
                } else {
                    alert('Failed to create sale. Please try again.');
                }
            } catch (error) {
                console.error('Error creating sale:', error);
                alert('Error creating sale. Please try again.');
            }
        }
        
        async function createSampleSale() {
            if (!confirm('Create a sample sale with 3 products for testing?\n\nThis will help you test the complete payment simulation workflow.')) {
                return;
            }

            try {
                const response = await fetch('/sales/create-sample', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_phone: 'Walk-in'
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        // Redirect to work on the new sample sale
                        window.location.href = `/sales/${result.sale.id}`;
                    } else {
                        alert('Failed to create sample sale: ' + result.message);
                    }
                } else {
                    alert('Failed to create sample sale. Please try again.');
                }
            } catch (error) {
                console.error('Error creating sample sale:', error);
                alert('Error creating sample sale. Please try again.');
            }
        }
        
        function openSale(saleId) {
            window.location.href = `/sales/${saleId}`;
        }
    </script>
</body>
</html>