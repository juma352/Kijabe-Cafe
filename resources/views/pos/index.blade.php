<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kijabe Hospital Cafeteria - Smart POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--primary-50) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            font-size: 14px;
            overflow: hidden;
            height: 100vh;
        }
        
        /* Modern Header with Gradient */
        .header {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 50%, var(--primary-900) 100%);
            color: white;
            padding: var(--space-4) var(--space-6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="medical" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23medical)"/></svg>');
            pointer-events: none;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: var(--space-4);
            position: relative;
            z-index: 1;
        }
        
        .hospital-logo {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-600);
            font-size: 20px;
            box-shadow: var(--shadow-md);
        }
        
        .hospital-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        
        .hospital-info p {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: var(--space-4);
            position: relative;
            z-index: 1;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            background: rgba(255, 255, 255, 0.1);
            padding: var(--space-2) var(--space-4);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(10px);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--medical-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .user-details h3 {
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .user-details p {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: var(--space-2) var(--space-4);
            border-radius: var(--radius-md);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }
        
        /* Modern Main Layout */
        .main-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            height: calc(100vh - 80px);
            gap: var(--space-6);
            padding: var(--space-6);
            max-width: 100vw;
            overflow: hidden;
        }
        
        /* Left Panel - Products */
        .products-panel {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .products-header {
            background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
            padding: var(--space-6);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .products-title {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-4);
        }
        
        .products-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
        }
        
        .products-title .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        
        /* Modern Search and Controls */
        .search-controls {
            display: flex;
            gap: var(--space-4);
            margin-bottom: var(--space-4);
        }
        
        .search-input-wrapper {
            position: relative;
            flex: 1;
        }
        
        .search-input {
            width: 100%;
            padding: var(--space-3) var(--space-4) var(--space-3) 3rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-500);
            background: white;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: var(--space-3);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1rem;
        }
        
        .filter-select {
            padding: var(--space-3) var(--space-4);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            background: var(--gray-50);
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 200px;
        }
        
        .filter-select:focus {
            outline: none;
            border-color: var(--primary-500);
            background: white;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        /* Sale Controls */
        .sale-controls {
            display: flex;
            gap: var(--space-3);
            align-items: center;
        }
        
        .modern-btn {
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-lg);
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: var(--space-2);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .modern-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .modern-btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--medical-green), #059669);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--medical-red), #dc2626);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        /* Products Grid */
        .products-grid-container {
            flex: 1;
            overflow-y: auto;
            padding: var(--space-6);
            padding-top: 0;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: var(--space-4);
            padding-bottom: var(--space-6);
        }
        
        /* Modern Product Cards */
        .product-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-xl);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            height: 240px;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-300);
        }

        .product-card.out-of-stock {
            background: var(--gray-100);
            border-color: var(--gray-300);
            opacity: 0.6;
        }

        .product-card.out-of-stock:hover {
            transform: none;
            cursor: not-allowed;
        }

        .product-card.low-stock {
            border-color: var(--warning-400);
            background: linear-gradient(135deg, white 0%, #fef3c7 100%);
        }
        
        .product-image {
            height: 120px;
            background: linear-gradient(135deg, var(--gray-100), var(--gray-200));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="rgba(0,0,0,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            opacity: 0.3;
        }
        
        .product-icon {
            font-size: 2.5rem;
            color: var(--primary-500);
            z-index: 1;
            position: relative;
        }
        
        .product-info {
            padding: var(--space-4);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .product-category {
            font-size: 0.75rem;
            color: var(--primary-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-1);
        }
        
        .product-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
            line-height: 1.4;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--medical-green);
        }
        
        .add-btn {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
        }
        
        .add-btn:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        /* Stock Indicator Styles */
        .stock-indicator {
            position: absolute;
            top: var(--space-2);
            right: var(--space-2);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            z-index: 2;
            letter-spacing: 0.05em;
        }

        .stock-indicator.in-stock {
            background: rgba(16, 185, 129, 0.9);
            color: white;
        }

        .stock-indicator.low-stock {
            background: rgba(245, 158, 11, 0.9);
            color: white;
        }

        .stock-indicator.out-of-stock {
            background: rgba(239, 68, 68, 0.9);
            color: white;
        }

        .stock-quantity {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: var(--space-1);
            font-weight: 500;
        }

        .product-card.out-of-stock .add-btn {
            display: none;
        }
        
        /* Right Panel - Cart & Payment */
        .right-panel {
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
            height: 100%;
            overflow-y: auto; /* Allow scrolling to see all sections */
            overflow-x: hidden;
            padding-right: var(--space-2); /* Add padding for scrollbar */
            /* Custom scrollbar styling */
            scrollbar-width: thin;
            scrollbar-color: var(--gray-300) transparent;
        }
        
        /* Custom scrollbar for webkit browsers */
        .right-panel::-webkit-scrollbar {
            width: 8px;
        }
        
        .right-panel::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: var(--radius-sm);
        }
        
        .right-panel::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: var(--radius-sm);
            transition: background 0.2s ease;
        }
        
        .right-panel::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* Sale Summary Card */
        .sale-summary-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            flex-shrink: 0; /* Prevent shrinking below content size */
        }
        
        .sale-summary-header {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            padding: var(--space-4) var(--space-6);
            display: flex;
            align-items: center;
            justify-content: between;
        }
        
        .sale-summary-title {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .sale-summary-title h3 {
            font-size: 1.125rem;
            font-weight: 700;
        }
        
        .sale-summary-title .icon {
            background: rgba(255, 255, 255, 0.2);
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sale-number {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .sale-summary-content {
            padding: var(--space-6);
        }
        
        /* Shopping Cart */
        .shopping-cart {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            flex: 1; /* Reduced from 2 to 1 to give space for other sections */
            display: flex;
            flex-direction: column;
            min-height: 250px; /* Reduced minimum height */
            max-height: 400px; /* Add maximum height to prevent it from taking too much space */
        }
        
        .cart-header {
            background: linear-gradient(135deg, var(--gray-50), white);
            padding: var(--space-4) var(--space-6);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .cart-title {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .cart-title h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }
        
        .cart-count {
            background: var(--primary-500);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .cart-items {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: var(--space-4);
            min-height: 200px;
            border-top: 1px solid var(--gray-100);
            border-bottom: 1px solid var(--gray-100);
            scroll-behavior: smooth; /* Smooth scrolling */
            /* Custom scrollbar styling */
            scrollbar-width: thin;
            scrollbar-color: var(--gray-300) transparent;
        }
        
        /* Custom scrollbar for webkit browsers */
        .cart-items::-webkit-scrollbar {
            width: 8px;
        }
        
        .cart-items::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: var(--radius-sm);
            margin: var(--space-2);
        }
        
        .cart-items::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: var(--radius-sm);
            transition: background 0.2s ease;
        }
        
        .cart-items::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* Scrollbar track styling for better visibility */
        .cart-items::-webkit-scrollbar-corner {
            background: transparent;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-2);
            transition: all 0.3s ease;
            background: var(--gray-50);
        }
        
        .cart-item:hover {
            background: var(--primary-50);
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-1);
        }
        
        .cart-item-price {
            font-size: 0.75rem;
            color: var(--gray-600);
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .qty-btn {
            background: var(--primary-500);
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .qty-btn:hover {
            background: var(--primary-600);
            transform: scale(1.1);
        }
        
        .qty-display {
            min-width: 32px;
            text-align: center;
            font-weight: 600;
            color: var(--gray-800);
        }
        
        .cart-total {
            background: var(--gray-50);
            padding: var(--space-4) var(--space-6);
            border-top: 1px solid var(--gray-200);
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-2);
        }
        
        .total-row:last-child {
            margin-bottom: 0;
            padding-top: var(--space-2);
            border-top: 1px solid var(--gray-300);
        }
        
        .total-label {
            font-size: 0.875rem;
            color: var(--gray-600);
        }
        
        .total-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
        }
        
        .grand-total .total-label,
        .grand-total .total-value {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--primary-600);
        }
        
        /* Payment Options Section */
        .payment-options-panel {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .payment-options-header {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            padding: var(--space-4) var(--space-6);
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .payment-options-header h3 {
            font-size: 1.125rem;
            font-weight: 700;
        }
        
        .payment-options-header .icon {
            background: rgba(255, 255, 255, 0.2);
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .payment-options-content {
            padding: var(--space-6);
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
        }
        
        .btn-add-mpesa {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
        }
        
        .btn-add-mpesa:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.2);
        }
        
        .btn-search-payments {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            border: none;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            box-shadow: 0 4px 6px -1px rgba(6, 182, 212, 0.1);
        }
        
        .btn-search-payments:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(6, 182, 212, 0.2);
        }
        
        .btn-print-receipt {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            border: none;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1);
        }
        
        .btn-print-receipt:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.2);
        }
        
        .btn-generate-mpesa {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
        }
        
        .btn-generate-mpesa:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
        }
        
        .btn-simulate-payment {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1);
        }
        
        .btn-simulate-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.2);
        }
        
        .customer-input-group {
            margin-top: var(--space-2);
        }
        
        .customer-input-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: var(--space-2);
        }
        
        .customer-phone-input {
            width: 100%;
            padding: var(--space-3) var(--space-4);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .customer-phone-input:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        /* Payment Interface */
        .payment-panel {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            flex-shrink: 0; /* Prevent shrinking below content size */
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--medical-green), #059669);
            color: white;
            padding: var(--space-4) var(--space-6);
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .payment-header h3 {
            font-size: 1.125rem;
            font-weight: 700;
        }
        
        .payment-header .icon {
            background: rgba(255, 255, 255, 0.2);
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .payment-content {
            padding: var(--space-6);
        }
        
        .payment-status {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-4);
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: var(--primary-50);
            color: var(--primary-700);
            border: 1px solid var(--primary-200);
        }
        
        .status-completed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--medical-green);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .payment-actions {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
        }
        
        .mpesa-btn {
            background: linear-gradient(135deg, var(--medical-green), #059669);
            color: white;
            border: none;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            box-shadow: var(--shadow-md);
        }
        
        .mpesa-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .simulate-btn {
            background: linear-gradient(135deg, var(--medical-orange), #d97706);
            color: white;
            border: none;
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
        }
        
        .simulate-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .print-btn {
            background: linear-gradient(135deg, var(--medical-purple), #7c3aed);
            color: white;
            border: none;
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
        }
        
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .search-btn {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            border: none;
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
            width: 100%;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .search-payments {
            border-top: 1px solid var(--gray-200);
            padding-top: var(--space-3);
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none; /* Hidden by default */
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            max-width: 90vw;
            max-height: 90vh;
            overflow: hidden;
            position: relative;
            z-index: 1001;
        }
        
        /* Search Results Modal (different from overlay pattern) */
        .modal.search-results {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1002; /* Higher than other modals */
        }
        
        .modal-content {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            max-width: 90vw;
            max-height: 90vh;
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            padding: var(--space-4) var(--space-6);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            transition: background 0.2s ease;
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .modal-title {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: white;
        }
        
        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-sm);
        }
        
        .modal-body {
            padding: var(--space-6);
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-footer {
            background: var(--gray-50);
            padding: var(--space-4) var(--space-6);
            display: flex;
            justify-content: flex-end;
            gap: var(--space-3);
        }
        
        /* Form Styling */
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
            padding: var(--space-3) var(--space-4);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .form-input:read-only {
            background: var(--gray-100);
            color: var(--gray-600);
        }
        
        .modal-actions {
            display: flex;
            gap: var(--space-3);
            justify-content: flex-end;
            margin-top: var(--space-6);
            padding-top: var(--space-4);
            border-top: 1px solid var(--gray-200);
        }
        
        .btn-secondary {
            background: var(--gray-500);
            color: white;
            border: none;
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: var(--gray-600);
            transform: translateY(-1px);
        }
        
        .btn-modal-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            border: none;
            padding: var(--space-3) var(--space-6);
            border-radius: var(--radius-lg);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.1);
        }
        
        .btn-modal-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.2);
        }
        
        /* Status Messages */
        .status-message {
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-4);
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .status-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--medical-green);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .status-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--medical-red);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .status-info {
            background: var(--primary-50);
            color: var(--primary-700);
            border: 1px solid var(--primary-200);
        }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid var(--primary-200);
            border-top: 2px solid var(--primary-500);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-container {
                grid-template-columns: 1fr 320px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
                gap: var(--space-4);
                padding: var(--space-4);
            }
            
            .right-panel {
                order: -1;
                max-height: 300px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
            
            .hospital-info h1 {
                font-size: 1.25rem;
            }
            
            .hospital-info p {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Header -->
    <header class="header">
        <div class="header-left">
            <div class="hospital-logo">
                <div class="logo-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="hospital-info">
                    <h1>Kijabe Hospital</h1>
                    <p>Cafeteria Point of Sale</p>
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-details">
                    <h3>{{ auth()->user()->name }}</h3>
                    <p>Cashier</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Left Panel - Products -->
        <div class="products-panel">
            <div class="products-header">
                <div class="products-title">
                    <div class="icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h2>Products</h2>
                </div>
                
                <!-- Search and Filter Controls -->
                <div class="search-controls">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Search products..." id="searchProducts">
                    </div>
                    <select class="filter-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sale Controls -->
                <div class="sale-controls">
                    <a href="/sales" class="modern-btn btn-secondary" style="background: linear-gradient(135deg, var(--gray-500), var(--gray-600)); color: white; text-decoration: none;">
                        <i class="fas fa-arrow-left"></i>
                        Back to Sales
                    </a>
                    @if(isset($sale))
                        <a href="/sales" class="modern-btn btn-success">
                            <i class="fas fa-check-circle"></i>
                            Finish & New Sale
                        </a>
                    @else
                        <button class="modern-btn btn-primary" onclick="createBlankSale()">
                            <i class="fas fa-plus"></i>
                            New Sale
                        </button>
                    @endif
                    <span id="currentSaleNumber" style="color: var(--gray-600); font-weight: 600; font-size: 0.875rem;"></span>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid-container">
                <div class="products-grid" id="productGrid">
                    @foreach($products as $product)
                        <div class="product-card {{ $product['stock_status'] }}" onclick="addToCart({{ $product['id'] }})" data-product-id="{{ $product['id'] }}">
                            <!-- Stock Indicator -->
                            @if($product['is_out_of_stock'])
                                <div class="stock-indicator out-of-stock">Out</div>
                            @elseif($product['is_low_stock'])
                                <div class="stock-indicator low-stock">Low</div>
                            @else
                                <div class="stock-indicator in-stock">{{ $product['stock_quantity'] }}</div>
                            @endif
                            
                            <div class="product-image">
                                <i class="product-icon fas fa-utensils"></i>
                            </div>
                            <div class="product-info">
                                <div class="product-category">{{ $product['category']['name'] ?? 'General' }}</div>
                                <div class="product-name">{{ $product['name'] }}</div>
                                <div class="stock-quantity">Stock: {{ $product['stock_quantity'] }} available</div>
                                <div class="product-footer">
                                    <div class="product-price">Ksh {{ number_format($product['price'], 2) }}</div>
                                    @if(!$product['is_out_of_stock'])
                                        <button class="add-btn" onclick="event.stopPropagation(); addToCart({{ $product['id'] }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <!-- Sale Summary -->
            <div class="sale-summary-card">
                <div class="sale-summary-header">
                    <div class="sale-summary-title">
                        <div class="icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h3>Sale Summary</h3>
                            <div class="sale-number" id="saleNumber">
                                @if(isset($sale))
                                    Sale #{{ $sale->sale_number }}
                                @else
                                    New Sale
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="sale-summary-content">
                    <div class="status-message" id="statusMessage">
                        <!-- Status messages will appear here -->
                    </div>
                    
                    <!-- Sale Status Display -->
                    @if(isset($sale) && $sale->status === 'completed')
                        <div class="payment-status status-completed">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <div><strong>Status:</strong> <span id="sale-status-display">{{ $sale->status_display }}</span></div>
                                <div><strong>Total:</strong> Ksh {{ number_format($sale->total, 2) }}</div>
                            </div>
                        </div>
                    @elseif(isset($sale) && $sale->status === 'draft')
                        <div class="payment-status status-pending">
                            <i class="fas fa-clock"></i>
                            <div>
                                <div><strong>Status:</strong> <span id="sale-status-display">Awaiting Payment</span></div>
                                <div><strong>Total:</strong> Ksh {{ number_format($sale->total, 2) }}</div>
                            </div>
                        </div>
                    @else
                        <div class="payment-status status-pending">
                            <i class="fas fa-shopping-cart"></i>
                            <div>
                                <div><strong>Status:</strong> <span id="sale-status-display">New Sale</span></div>
                                <div><strong>Total:</strong> <span id="reference-total">Ksh 0.00</span></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="shopping-cart">
                <div class="cart-header">
                    <div class="cart-title">
                        <h3>Shopping Cart</h3>
                        <div class="cart-count" id="cartCount">0</div>
                    </div>
                    <button class="modern-btn" style="background: var(--medical-red); color: white; padding: var(--space-2) var(--space-3); font-size: 0.75rem;" onclick="clearCart()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="cart-items" id="cartItems">
                    <div style="text-align: center; color: var(--gray-500); padding: var(--space-6); font-style: italic;">
                        Cart is empty
                    </div>
                </div>
                
                <div class="cart-total">
                    <div class="total-row">
                        <span class="total-label">Subtotal:</span>
                        <span class="total-value" id="subtotal">Ksh 0.00</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Tax (0%):</span>
                        <span class="total-value" id="tax">Ksh 0.00</span>
                    </div>
                    <div class="total-row grand-total">
                        <span class="total-label">Total:</span>
                        <span class="total-value" id="grandTotal">Ksh 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Payment Options Section -->
            <div class="payment-options-panel">
                <div class="payment-options-header">
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3>Payment Options</h3>
                </div>
                
                <div class="payment-options-content">
                    <!-- Core Payment Buttons (Always Available) -->
                    <button class="btn-add-mpesa" onclick="openManualMpesaModal()">
                        <i class="fas fa-mobile-alt"></i>
                        Add M-Pesa Transaction Manually
                    </button>
                    
                    <button class="btn-search-payments" onclick="fetchAvailablePayments()">
                        <i class="fas fa-search"></i>
                        Search Payments
                    </button>
                    
                    <!-- Conditional Buttons Based on Sale Status -->
                    @if(isset($sale) && $sale->status === 'completed')
                        <button class="btn-print-receipt" onclick="printReceipt()">
                            <i class="fas fa-print"></i>
                            Print Receipt
                        </button>
                    @elseif(isset($sale) && $sale->status === 'draft')
                        <button class="btn-generate-mpesa" onclick="generateLipaNaMpesa()">
                            <i class="fas fa-qrcode"></i>
                            Generate Lipa na M-Pesa
                        </button>
                        
                        <button class="btn-simulate-payment" onclick="simulatePayment()">
                            <i class="fas fa-play"></i>
                            Simulate Payment
                        </button>
                    @endif
                    
                    <!-- Customer Phone Input -->
                    <div class="customer-input-group">
                        <label for="customerPhoneMain">Customer Phone Number (Optional)</label>
                        <input type="tel" id="customerPhoneMain" class="customer-phone-input" placeholder="254712345678">
                    </div>
                </div>
            </div>
        </div>
    </div>
</head>
<body>
    <!-- Modern Header -->
    <header class="header">
        <div class="header-left">
            <div class="hospital-logo">
                <div class="logo-icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <div class="hospital-info">
                    <h1>Kijabe Hospital</h1>
                    <p>Cafeteria Point of Sale</p>
                </div>
            </div>
        </div>
        
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-details">
                    <h3>{{ auth()->user()->name }}</h3>
                    <p>Cashier</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Product Area -->
        <div class="product-area">
            <!-- Sale Controls -->
            <div class="sale-controls">
                <a href="/sales" class="btn btn-secondary" style="background: #6b7280; color: white; text-decoration: none; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-right: 0.5rem;">← Back to Sales</a>
                @if(isset($sale))
                    <button class="btn btn-success" onclick="window.location.href='/sales'" style="background: #059669; color: white; border: none; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-right: 0.5rem;">✅ Finish & New Sale</button>
                @else
                    <button class="btn btn-primary" onclick="createBlankSale()">+ New Sale</button>
                @endif
                <span id="currentSaleNumber"></span>
            </div>

            <!-- Search and Filter -->
            <div class="search-filter">
                <input type="text" class="search-input" placeholder="Search products..." id="searchProducts">
                <select class="filter-select" id="categoryFilter">
                    <option value="">Filter by Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Grid -->
            <div class="product-grid" id="productGrid">
                @foreach($categories as $category)
                    @foreach($category->activeProducts as $product)
                        <div class="product-card" data-category="{{ $category->id }}" data-name="{{ strtolower($product->name) }}">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-category">{{ $category->name }}</div>
                            <div class="product-price">Ksh {{ number_format($product->price, 2) }}</div>
                            <button class="add-to-sale-btn" onclick="addToSale({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                                Add to Sale
                            </button>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        <!-- Sale Summary Panel -->
        <div class="sale-summary">
            <div class="summary-header">
                <span class="summary-title">Sale Summary</span>
                <button class="clear-btn" onclick="clearCart()">🗑️</button>
            </div>

            <div class="summary-content">
                <!-- Totals -->
                <div class="summary-totals">
                    <div class="total-row">
                        <span>Total</span>
                        <span id="totalAmount">0</span>
                    </div>
                    <div class="total-row">
                        <span>Discount</span>
                        <span>0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Vat (exclusive)</span>
                        <span>0.00</span>
                    </div>
                    <div class="total-row main">
                        <span>Total Sale</span>
                        <span id="finalTotal">0.00</span>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="cart-items" id="cartItems">
                    <!-- Cart items will be added here dynamically -->
                </div>

                <!-- Reference Section -->
                <div class="reference-section">
                    <div class="reference-title">Payment Information</div>
                    @if(isset($sale) && $sale->status === 'completed')
                        <div class="payment-details">
                            <table class="reference-table">
                                <tbody>
                                    <tr>
                                        <td><strong>Sale Number:</strong></td>
                                        <td>{{ $sale->sale_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>M-Pesa Reference:</strong></td>
                                        <td>{{ $sale->mpesa_receipt_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transaction ID:</strong></td>
                                        <td>{{ $sale->mpesa_transaction_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount Paid:</strong></td>
                                        <td><strong>Ksh {{ number_format($sale->total, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer Phone:</strong></td>
                                        <td>{{ $sale->customer_phone ?? 'Walk-in' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Date:</strong></td>
                                        <td>{{ $sale->payment_confirmed_at ? $sale->payment_confirmed_at->format('d/m/Y H:i') : $sale->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Print Receipt Button -->
                            <button onclick="printReceipt()" class="print-receipt-btn" style="background: #059669; color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 500; font-size: 0.875rem; width: 100%; margin-top: 1rem; transition: all 0.2s;">
                                🖨️ Print Receipt
                            </button>
                        </div>
                    @elseif(isset($sale) && $sale->status === 'awaiting_payment')
                        <div class="payment-pending">
                            <table class="reference-table">
                                <tbody>
                                    <tr>
                                        <td><strong>Sale Number:</strong></td>
                                        <td>{{ $sale->sale_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td><strong>Ksh {{ number_format($sale->total, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td><span id="sale-status-display" style="color: #3b82f6; font-weight: 500;">{{ $sale->status_display }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @elseif(isset($sale) && $sale->status === 'draft')
                        <div class="payment-pending">
                            <table class="reference-table">
                                <tbody>
                                    <tr>
                                        <td><strong>Sale Number:</strong></td>
                                        <td>{{ $sale->sale_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td><strong>Ksh {{ number_format($sale->total, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td><span id="sale-status-display" style="color: #f59e0b; font-weight: 500;">Awaiting Payment</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <table class="reference-table">
                            <tbody>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><span id="reference-total">Ksh 0.00</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span id="sale-status-display">New Sale</span></td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Payment Options Section -->
            <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                <div style="margin-bottom: 1rem;">
                    <h4 style="margin: 0 0 0.75rem 0; font-size: 0.875rem; font-weight: 500; color: #1f2937;">Payment Options</h4>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @if(isset($sale) && $sale->status === 'awaiting_payment' && $sale->total > 0)
                            <button class="simulate-payment-btn" onclick="simulatePayment()" style="background: #059669; color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 500; font-size: 0.875rem; width: 100%; transition: all 0.2s; margin-bottom: 0.5rem;">🧪 Simulate M-Pesa Payment</button>
                        @endif
                        <button class="manual-mpesa-btn" onclick="openManualMpesaModal()" style="background: #0891b2; color: white; padding: 0.75rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 500; font-size: 0.875rem; width: 100%; transition: all 0.2s;">Add M-Pesa Transaction Manually</button>
                        <button class="btn" style="background: #1e40af; color: white; padding: 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 500; font-size: 1rem; width: 100%; transition: all 0.2s;" onclick="generateLipaNaMpesa()">Generate Lipa na M-Pesa</button>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="payment-section">
                <input type="tel" class="customer-phone" placeholder="Customer Phone Number (Optional)" id="customerPhone">
                <div class="payment-buttons">
                    <button class="search-payments-btn" onclick="fetchAvailablePayments()">Search Payments</button>
                </div>
                
                <div id="statusMessage"></div>
            </div>
        </div>
    </div>

    <!-- Manual M-Pesa Modal -->
    <div class="modal-overlay" id="mpesaModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add M-Pesa Transaction Manually</h3>
                <button class="modal-close" onclick="closeManualMpesaModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <form id="manualMpesaForm">
                    <div class="form-group">
                        <label class="form-label">Enter Mpesa Code</label>
                        <input type="text" class="form-input" id="mpesaCode" placeholder="e.g., QEI2AK4J0M" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enter Amount</label>
                        <input type="number" class="form-input" id="mpesaAmount" placeholder="0.00" step="0.01" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enter Customer Phone Number</label>
                        <input type="tel" class="form-input" id="mpesaPhone" placeholder="254712345678" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enter Customer First Name</label>
                        <input type="text" class="form-input" id="customerFirstName" placeholder="Customer Name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Enter Transaction Date/Time</label>
                        <input type="datetime-local" class="form-input" id="transactionDateTime" required>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeManualMpesaModal()">Close</button>
                        <button type="submit" class="btn-modal-primary">ADD MPESA PAYMENT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Payments Modal -->
    <div class="modal-overlay" id="searchPaymentsModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Search Payments</h3>
                <button class="modal-close" onclick="closeModal('searchPaymentsModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="paymentSearchNew">Search Term</label>
                    <input class="form-input" type="text" id="paymentSearchNew" placeholder="Enter phone number, transaction ID, or reference number...">
                </div>
                <div class="modal-actions">
                    <button class="btn-secondary" onclick="closeModal('searchPaymentsModal')">Cancel</button>
                    <button class="btn-modal-primary" onclick="performPaymentSearch()">Search</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lipa na M-Pesa Instructions Modal -->
    <div class="modal-overlay" id="lipaNaMpesaModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Lipa na M-Pesa Payment Instructions</h3>
                <button class="modal-close" onclick="closeLipaNaMpesaModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <div style="margin-bottom: 1rem; padding: 1rem; background: #f0f9ff; border: 1px solid #0284c7; border-radius: 0.5rem;">
                    <h4 style="color: #0369a1; margin: 0 0 0.5rem 0; font-weight: 600;">Payment Details</h4>
                    <div style="display: grid; grid-template-columns: 120px 1fr; gap: 0.5rem; font-size: 0.875rem;">
                        <span style="font-weight: 500;">Paybill Number:</span>
                        <span id="paybillNumber" style="font-weight: bold; color: #0369a1;"></span>
                        <span style="font-weight: 500;">Account Number:</span>
                        <span id="accountNumber" style="font-weight: bold; color: #0369a1;"></span>
                        <span style="font-weight: 500;">Amount:</span>
                        <span id="paymentAmount" style="font-weight: bold; color: #0369a1;"></span>
                    </div>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <h4 style="color: #059669; margin: 0 0 0.75rem 0; font-weight: 600;">Follow these steps:</h4>
                    <ol id="paymentInstructions" style="margin: 0; padding-left: 1.5rem; color: #374151; line-height: 1.6;">
                        <!-- Instructions will be populated by JavaScript -->
                    </ol>
                </div>
                
                <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
                    <p style="margin: 0; color: #92400e; font-size: 0.875rem; font-weight: 500;">
                        ⚠️ <strong>Important:</strong> Please use the exact Account Number shown above to ensure your payment is properly tracked.
                    </p>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeLipaNaMpesaModal()">Close</button>
                    <button type="button" class="btn-modal-primary" onclick="startPaymentTracking()">I've Made Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let cart = [];
        let currentSale = null;
        
        // CSRF token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Products data for cart functionality
        const productsData = @json($products->keyBy('id'));
        const products = @json($products->values()); // Array of all products for find/filter operations
        
        // Check if we're working on an existing sale
        @if(isset($sale))
            const existingSale = @json($sale);
            const existingSaleItems = @json($sale->saleItems->load('product'));
        @else
            const existingSale = null;
            const existingSaleItems = [];
        @endif
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            if (existingSale) {
                loadExistingSale();
            } else {
                newSale();
            }
            setupEventListeners();
        });
        
        function loadExistingSale() {
            currentSale = existingSale;
            
            // Load existing items into cart
            cart = [];
            existingSaleItems.forEach(item => {
                cart.push({
                    product_id: item.product.id,
                    name: item.product.name,
                    price: parseFloat(item.unit_price),
                    quantity: item.quantity
                });
            });
            
            // Update display
            document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - ${currentSale.status_display || currentSale.status}`;
            updateCartDisplay();
            updateTotals();
            
            // Check if sale is completed and update UI accordingly
            if (currentSale.status === 'completed') {
                updateUIForCompletedSale();
            }
        }
        
        function setupEventListeners() {
            // Search functionality
            document.getElementById('searchProducts').addEventListener('input', filterProducts);
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);
            
            // Manual M-Pesa form
            document.getElementById('manualMpesaForm').addEventListener('submit', handleManualMpesaSubmit);
            
            // Modal click outside to close - use event delegation since modals are dynamically created
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    closeAllModals();
                }
            });
            
            // Keyboard support
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAllModals();
                }
            });
        }
        
        function newSale() {
            cart = [];
            currentSale = null;
            updateCartDisplay();
            
            // Safely clear form fields
            const customerPhone = document.getElementById('customerPhone');
            if (customerPhone) customerPhone.value = '';
            
            const statusMessage = document.getElementById('statusMessage');
            if (statusMessage) statusMessage.innerHTML = '';
            
            const mpesaTransactions = document.getElementById('mpesaTransactions');
            if (mpesaTransactions) mpesaTransactions.innerHTML = '';
            
            const currentSaleNumber = document.getElementById('currentSaleNumber');
            if (currentSaleNumber) currentSaleNumber.textContent = 'Ready for new sale';
        }

        // Calculate cart total
        function calculateTotal() {
            return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        }

        // Update totals display
        function updateTotals() {
            const subtotal = calculateTotal();
            const total = subtotal; // No VAT for now
            
            // Update subtotal display
            const subtotalElement = document.getElementById('subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = 'Ksh ' + subtotal.toFixed(2);
            }
            
            // Update total display
            const totalElement = document.getElementById('total');
            if (totalElement) {
                totalElement.textContent = 'Ksh ' + total.toFixed(2);
            }
            
            // Update reference total
            updateReferenceTotal();
            
            // If we have a current sale and items, and it's still draft, update status automatically
            @if(isset($sale) && $sale->status === 'draft')
            if (currentSale && cart.length > 0 && total > 0) {
                // Automatically update sale status to awaiting_payment
                updateSaleStatus('awaiting_payment');
            }
            @endif
        }
        
        // Update sale status (helper function)
        async function updateSaleStatus(newStatus) {
            if (!currentSale || !currentSale.id) return;
            
            try {
                // This would need a backend endpoint, but for now we'll just update locally
                if (currentSale.status !== newStatus) {
                    currentSale.status = newStatus;
                    console.log(`Sale status updated to: ${newStatus}`);
                }
            } catch (error) {
                console.error('Error updating sale status:', error);
            }
        }

        // Create a blank sale (for workflow: New Sale -> Add Items -> Payment)
        async function createBlankSale() {
            try {
                showStatus('Creating new sale...', 'info');
                
                const response = await fetch('/sales/create-blank', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_phone: document.getElementById('customerPhone').value || 'Walk-in'
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        // Clear current cart and set new sale
                        cart = [];
                        currentSale = result.sale;
                        
                        // Update display
                        document.getElementById('currentSaleNumber').textContent = `Sale ${result.sale.sale_number} - Ready`;
                        updateCartDisplay();
                        updateTotals();
                        
                        showStatus(`✅ Sale ${result.sale.sale_number} created successfully! Start adding items.`, 'success');
                    } else {
                        showStatus('Failed to create sale: ' + result.message, 'error');
                    }
                } else {
                    const errorText = await response.text();
                    console.error('Create blank sale error:', response.status, errorText);
                    showStatus('Failed to create sale. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error creating blank sale:', error);
                showStatus('Error creating sale. Please try again.', 'error');
            }
        }
        
        async function addToSale(productId, productName, price) {
            const existingItem = cart.find(item => item.product_id === productId);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    product_id: productId,
                    name: productName,
                    price: price,
                    quantity: 1
                });
            }
            
            // Create sale if it doesn't exist and cart has items
            if (!currentSale && cart.length > 0) {
                await createSale();
            } 
            // If working on existing sale, save item to database
            else if (currentSale) {
                await addItemToExistingSale(productId, 1);
            }
            
            updateCartDisplay();
        }

        // Add product to cart (wrapper for addToSale with product lookup and stock check)
        async function addToCart(productId) {
            // Check if product is out of stock
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (productCard && productCard.classList.contains('out-of-stock')) {
                showStatus('❌ This item is currently out of stock', 'error');
                return;
            }

            const product = productsData[productId];
            if (product) {
                // Check stock availability from the enhanced product data
                const productData = products.find(p => p.id == productId);
                if (productData && productData.stock_quantity <= 0) {
                    showStatus(`❌ ${product.name} is out of stock`, 'error');
                    return;
                }

                await addToSale(productId, product.name, parseFloat(product.price));
                
                // Show stock warning if running low
                if (productData && productData.is_low_stock) {
                    showStatus(`⚠️ ${product.name} is running low on stock (${productData.stock_quantity} remaining)`, 'warning');
                }
            } else {
                console.error('Product not found:', productId);
                showStatus('❌ Product not found', 'error');
            }
        }

        // Add item to existing sale via API
        async function addItemToExistingSale(productId, quantity) {
            try {
                const response = await fetch(`/sales/${currentSale.id}/add-item`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        // Update current sale data
                        currentSale = result.sale;
                        
                        // Update the Payment Information section
                        updatePaymentInformation();
                        
                        showStatus(`✅ Item added to sale successfully!`, 'success');
                    } else {
                        showStatus('❌ Failed to add item: ' + result.message, 'error');
                    }
                } else {
                    const errorText = await response.text();
                    console.error('Add item error:', response.status, errorText);
                    showStatus('❌ Failed to add item to sale. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error adding item to sale:', error);
                showStatus('❌ Error adding item to sale. Please try again.', 'error');
            }
        }

        // Update Payment Information section with current sale data
        function updatePaymentInformation() {
            // Find and update total amount display
            const totalCells = document.querySelectorAll('table.reference-table td');
            totalCells.forEach((cell, index) => {
                if (cell.textContent.includes('Total Amount:')) {
                    const nextCell = totalCells[index + 1];
                    if (nextCell) {
                        nextCell.innerHTML = `<strong>Ksh ${Number(currentSale.total).toFixed(2)}</strong>`;
                    }
                }
            });
        }

        async function createSale() {
            try {
                const response = await fetch('/pos/create-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        items: cart,
                        customer_phone: document.getElementById('customerPhone').value || ''
                    })
                });

                const responseText = await response.text();
                console.log('Create sale raw response:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    showStatus('❌ Server returned invalid response. Check console for details.', 'error');
                    console.error('Parse error:', parseError);
                    console.error('Response text:', responseText);
                    return;
                }
                
                if (result.success) {
                    currentSale = result.sale;
                    const saleNumberElement = document.getElementById('currentSaleNumber');
                    if (saleNumberElement) {
                        saleNumberElement.textContent = `Sale: ${result.sale.sale_number}`;
                    }
                    showStatus(`Sale ${result.sale.sale_number} created successfully`, 'success');
                } else {
                    showStatus('Failed to create sale: ' + result.message, 'error');
                }
            } catch (error) {
                showStatus('Error creating sale: ' + error.message, 'error');
                console.error('Sale creation error:', error);
            }
        }
        
        function updateQuantity(productId, change) {
            const item = cart.find(item => item.product_id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCartDisplay();
                    updateTotals();
                }
            }
        }

        // Set quantity manually from input field
        function setQuantity(productId, newQuantity) {
            const quantity = parseInt(newQuantity);
            
            // Validate quantity
            if (isNaN(quantity) || quantity < 1) {
                // Reset to current quantity if invalid
                updateCartDisplay();
                return;
            }
            
            const item = cart.find(item => item.product_id === productId);
            if (item) {
                item.quantity = quantity;
                updateCartDisplay();
                updateTotals();
            }
        }

        // Handle Enter key press in quantity input
        function handleQuantityKeypress(event, productId) {
            if (event.key === 'Enter') {
                event.target.blur(); // Remove focus to trigger onchange
            }
        }
        
        function removeFromCart(productId) {
            cart = cart.filter(item => item.product_id !== productId);
            updateCartDisplay();
        }
        
        function clearCart() {
            cart = [];
            updateCartDisplay();
        }
        
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartCountElement = document.getElementById('cartCount');
            const subtotalElement = document.getElementById('subtotal');
            const grandTotalElement = document.getElementById('grandTotal');
            
            // Update cart count
            if (cartCountElement) {
                cartCountElement.textContent = cart.length;
            }
            
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<div style="text-align: center; color: var(--gray-500); padding: var(--space-6); font-style: italic;">Cart is empty</div>';
                if (subtotalElement) subtotalElement.textContent = 'Ksh 0.00';
                if (grandTotalElement) grandTotalElement.textContent = 'Ksh 0.00';
                return;
            }
            
            let total = 0;
            cartItemsContainer.innerHTML = '';
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                
                const cartItemHtml = `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">Ksh ${item.price.toFixed(2)} each</div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.product_id}, -1)">-</button>
                            <input type="number" class="quantity-input" value="${item.quantity}" min="1" 
                                   onchange="setQuantity(${item.product_id}, this.value)"
                                   onkeypress="handleQuantityKeypress(event, ${item.product_id})">
                            <button class="qty-btn" onclick="updateQuantity(${item.product_id}, 1)">+</button>
                            <button class="remove-item" onclick="removeFromCart(${item.product_id})">×</button>
                        </div>
                    </div>
                `;
                cartItemsContainer.innerHTML += cartItemHtml;
            });
            
            // Update totals
            if (subtotalElement) subtotalElement.textContent = 'Ksh ' + total.toFixed(2);
            if (grandTotalElement) grandTotalElement.textContent = 'Ksh ' + total.toFixed(2);
            
            // Update modal amount field if modal is open
            const mpesaAmountField = document.getElementById('mpesaAmount');
            if (mpesaAmountField) {
                mpesaAmountField.value = total.toFixed(2);
            }
            
            // Update totals in other parts of the UI
            updateTotals();
        }
        
        // Modal Functions
        function openManualMpesaModal() {
            const total = parseFloat(document.getElementById('finalTotal').textContent);
            const phone = document.getElementById('customerPhone').value;
            
            if (cart.length === 0) {
                showStatus('Please add items to cart first', 'error');
                return;
            }
            
            if (total <= 0) {
                showStatus('Cart total must be greater than 0', 'error');
                return;
            }
            
            // Pre-fill modal fields
            document.getElementById('mpesaAmount').value = total.toFixed(2);
            document.getElementById('mpesaPhone').value = phone;
            
            // Set current date/time
            const now = new Date();
            const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            document.getElementById('transactionDateTime').value = localDateTime;
            
            document.getElementById('mpesaModal').classList.add('active');
        }
        
        function closeManualMpesaModal() {
            document.getElementById('mpesaModal').classList.remove('active');
            document.getElementById('manualMpesaForm').reset();
        }
        
        async function handleManualMpesaSubmit(e) {
            e.preventDefault();
            
            const formData = {
                mpesa_code: document.getElementById('mpesaCode').value,
                amount: parseFloat(document.getElementById('mpesaAmount').value),
                phone: document.getElementById('mpesaPhone').value,
                customer_name: document.getElementById('customerFirstName').value,
                transaction_time: document.getElementById('transactionDateTime').value
            };
            
            // Validate form
            if (!formData.mpesa_code || !formData.amount || !formData.phone || !formData.customer_name) {
                showStatus('Please fill in all required fields', 'error');
                return;
            }
            
            try {
                // First create the sale if not already created
                if (!currentSale) {
                    const saleResponse = await fetch('/pos/create-sale', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            items: cart,
                            customer_phone: formData.phone
                        })
                    });
                    
                    const saleResult = await saleResponse.json();
                    
                    if (!saleResult.success) {
                        showStatus(saleResult.message, 'error');
                        return;
                    }
                    
                    currentSale = saleResult.sale;
                    document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - Processing Payment`;
                }
                
                // Process manual M-Pesa payment
                const paymentResponse = await fetch('/pos/manual-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        sale_id: currentSale.id,
                        mpesa_transaction_id: formData.mpesa_code,
                        mpesa_receipt_number: formData.mpesa_code,
                        customer_name: formData.customer_name,
                        transaction_time: formData.transaction_time
                    })
                });
                
                const result = await paymentResponse.json();
                
                console.log('Manual M-Pesa response:', result);
                
                if (result.success) {
                    closeManualMpesaModal();
                    showStatus('M-Pesa payment added successfully! Sale completed.', 'success');
                    
                    // Update currentSale with the complete response data from server
                    if (result.sale) {
                        console.log('Sale data from server:', result.sale);
                        
                        // Use the server data directly - don't override it
                        currentSale = result.sale;
                        
                        // Only set status_display if it's not already set
                        if (!currentSale.status_display) {
                            currentSale.status_display = 'Completed';
                        }
                        
                        console.log('Updated currentSale after manual M-Pesa:', currentSale);
                        console.log('currentSale status:', currentSale.status);
                        console.log('currentSale mpesa_transaction_id:', currentSale.mpesa_transaction_id);
                        console.log('currentSale payment_confirmed_at:', currentSale.payment_confirmed_at);
                    }
                    
                    // Update UI for completed sale
                    updateUIForCompletedSale();
                    
                    // Show print receipt option
                    setTimeout(() => {
                        if (confirm('Payment completed successfully! Would you like to print the receipt?')) {
                            printReceipt();
                        }
                    }, 500);
                    
                    // Auto-start new sale after 5 seconds (give time for print)
                    setTimeout(() => {
                        newSale();
                    }, 5000);
                } else {
                    showStatus(result.message, 'error');
                }
                
            } catch (error) {
                showStatus('Error processing manual M-Pesa payment: ' + error.message, 'error');
            }
        }
        
        async function searchPayments() {
            const phone = document.getElementById('customerPhone').value;
            const total = parseFloat(document.getElementById('finalTotal').textContent);
            
            if (cart.length === 0) {
                showStatus('Please add items to cart first', 'error');
                return;
            }
            
            if (total <= 0) {
                showStatus('Cart total must be greater than 0', 'error');
                return;
            }
            
            try {
                // First create the sale if not already created
                if (!currentSale) {
                    const saleResponse = await fetch('/pos/create-sale', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            items: cart,
                            customer_phone: phone || 'Unknown'
                        })
                    });
                    
                    const saleResult = await saleResponse.json();
                    
                    if (!saleResult.success) {
                        showStatus(saleResult.message, 'error');
                        return;
                    }
                    
                    currentSale = saleResult.sale;
                    document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - Searching Payments`;
                }
                
                // Search for M-Pesa payments
                const paymentResponse = await fetch('/pos/search-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        search: phone,
                        amount: total,
                        show_all: false  // Start with recent payments
                    })
                });
                
                const paymentResult = await paymentResponse.json();
                
                if (paymentResult.success) {
                    showMpesaPaymentsModal(paymentResult.payments, total, paymentResult.total_found);
                } else {
                    showStatus('Error searching for payments', 'error');
                }
                
            } catch (error) {
                showStatus('Error processing request: ' + error.message, 'error');
            }
        }
        
        function showMpesaPaymentsModal(payments, expectedAmount, totalFound) {
            // Create modal HTML
            const modalHtml = `
                <div class="modal-overlay" id="mpesaPaymentsModal">
                    <div class="modal" style="max-width: 900px; width: 95%;">
                        <div class="modal-header">
                            <h3 class="modal-title">M-PESA Payments (${totalFound || payments.length} found)</h3>
                            <button class="modal-close" onclick="closeMpesaPaymentsModal()">&times;</button>
                        </div>
                        
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <input type="text" id="paymentSearch" placeholder="Search by Name/Phone number or Transaction Code" 
                                       style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.375rem;"
                                       oninput="filterPayments()">
                                <button onclick="showAllPayments()" style="background: #059669; color: white; border: none; padding: 0.75rem 1rem; border-radius: 0.375rem; cursor: pointer; white-space: nowrap;">
                                    Show All Payments
                                </button>
                                <button onclick="refreshPayments()" style="background: #0891b2; color: white; border: none; padding: 0.75rem 1rem; border-radius: 0.375rem; cursor: pointer;">
                                    🔄 Refresh
                                </button>
                            </div>
                            
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                                    <thead>
                                        <tr style="background: #f8fafc; border-bottom: 1px solid #e5e7eb;">
                                            <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Name</th>
                                            <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Transaction Code</th>
                                            <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Phone</th>
                                            <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Amount</th>
                                            <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Time</th>
                                            <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentsTableBody">
                                        ${generatePaymentsTableRows(payments, expectedAmount)}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div style="padding: 1.5rem 2rem; border-top: 1px solid #e5e7eb; text-align: right;">
                            <button onclick="closeMpesaPaymentsModal()" style="background: #6b7280; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 0.375rem; cursor: pointer;">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('mpesaPaymentsModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to document
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Store payments for filtering
            window.currentPayments = payments;
            window.expectedAmount = expectedAmount;
            
            // Show the modal using proper management
            openModal('mpesaPaymentsModal');
        }
        
        function generatePaymentsTableRows(payments, expectedAmount) {
            if (payments.length === 0) {
                return `<tr><td colspan="6" style="padding: 2rem; text-align: center; color: #6b7280;">No M-Pesa payments found</td></tr>`;
            }
            
            return payments.map(payment => {
                const isMatching = parseFloat(payment.amount) === expectedAmount;
                const rowStyle = isMatching ? 'background: #f0f9ff; border: 1px solid #0ea5e9;' : '';
                const amountStyle = isMatching ? 'color: #0ea5e9; font-weight: 600;' : '';
                
                return `
                    <tr data-payment='${JSON.stringify(payment)}' style="${rowStyle}">
                        <td style="padding: 0.75rem; border-bottom: 1px solid #f1f5f9;">${payment.customer_name}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #f1f5f9;">${payment.transaction_code}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #f1f5f9;">${payment.phone_number}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #f1f5f9; ${amountStyle}">${payment.amount}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #f1f5f9;">${new Date(payment.time).toLocaleString()}</td>
                        <td style="padding: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <button onclick="linkPayment(${payment.id})" 
                                    style="background: #f59e0b; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem;">
                                Add
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        function filterPayments() {
            const search = document.getElementById('paymentSearch').value.toLowerCase();
            const tbody = document.getElementById('paymentsTableBody');
            
            if (!window.currentPayments) return;
            
            const filteredPayments = window.currentPayments.filter(payment => 
                payment.customer_name.toLowerCase().includes(search) ||
                payment.phone_number.toLowerCase().includes(search) ||
                payment.transaction_code.toLowerCase().includes(search)
            );
            
            tbody.innerHTML = generatePaymentsTableRows(filteredPayments, window.expectedAmount);
        }
        
        async function linkPayment(paymentId) {
            if (!currentSale) {
                showStatus('No active sale to link payment to', 'error');
                return;
            }
            
            try {
                const response = await fetch('/pos/link-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        sale_id: currentSale.id,
                        payment_id: paymentId
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeMpesaPaymentsModal();
                    showStatus('Payment linked successfully! Sale completed.', 'success');
                    
                    // Update currentSale with the complete response data from server
                    if (result.sale) {
                        console.log('Sale data from server (link payment):', result.sale);
                        
                        // Use the server data directly - don't override it
                        currentSale = result.sale;
                        
                        // Only set status_display if it's not already set
                        if (!currentSale.status_display) {
                            currentSale.status_display = 'Completed';
                        }
                        
                        console.log('Updated currentSale after linking payment:', currentSale);
                    }
                    
                    // Update UI for completed sale
                    updateUIForCompletedSale();
                    
                    // Show print receipt option
                    setTimeout(() => {
                        if (confirm('Payment linked successfully! Would you like to print the receipt?')) {
                            printReceipt();
                        }
                    }, 500);
                    
                    // Auto-start new sale after 5 seconds (give time for print)
                    setTimeout(() => {
                        newSale();
                    }, 5000);
                } else {
                    showStatus(result.message, 'error');
                }
                
            } catch (error) {
                showStatus('Error linking payment: ' + error.message, 'error');
            }
        }
        
        function closeMpesaPaymentsModal() {
            const modal = document.getElementById('mpesaPaymentsModal');
            if (modal) {
                modal.remove();
            }
        }
        
        // Real M-Pesa Integration Functions
        async function testMpesaConnection() {
            try {
                showStatus('Testing M-Pesa connection...', 'success');
                
                const response = await fetch('/mpesa/test-connection', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showStatus('✅ M-Pesa connection successful!', 'success');
                } else {
                    showStatus('❌ M-Pesa connection failed: ' + result.message, 'error');
                }
                
            } catch (error) {
                showStatus('Error testing M-Pesa connection: ' + error.message, 'error');
            }
        }
        
        async function generateLipaNaMpesa() {
            const total = calculateTotal(); // Use the function instead of DOM element
            
            if (cart.length === 0) {
                showStatus('Please add items to cart first', 'error');
                return;
            }
            
            if (total <= 0) {
                showStatus('Cart total must be greater than 0', 'error');
                return;
            }

            // Auto-create sale if not exists
            if (!currentSale) {
                // Set customer phone first
                const phoneInput = document.getElementById('customerPhone');
                if (phoneInput && !phoneInput.value) {
                    phoneInput.value = 'Walk-in';
                }
                await createSale();
            }

            if (!currentSale || !currentSale.id) {
                showStatus('Failed to create sale. Please try again.', 'error');
                return;
            }

            try {
                showStatus('Generating Lipa na M-Pesa instructions...', 'info');
                
                const response = await fetch('/mpesa/lipa-na-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        sale_id: currentSale.id,
                        amount: total
                    })
                });

                // Check if response is OK
                if (!response.ok) {
                    console.error('Response not OK:', response.status, response.statusText);
                    
                    if (response.status === 401 || response.status === 419) {
                        showStatus('Session expired. Please refresh the page and login again.', 'error');
                        return;
                    }
                    
                    showStatus(`Server error (${response.status}). Please try again or contact support.`, 'error');
                    return;
                }

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Response is not JSON. Content-Type:', contentType);
                    const responseText = await response.text();
                    console.error('Response body:', responseText.substring(0, 200) + '...');
                    showStatus('Server returned invalid response. Please refresh the page and try again.', 'error');
                    return;
                }

                const result = await response.json();
                
                if (result.success) {
                    showLipaNaMpesaInstructions(result);
                    showStatus('✅ Payment instructions generated successfully!', 'success');
                } else {
                    showStatus('❌ Failed to generate instructions: ' + result.message, 'error');
                }
                
            } catch (error) {
                console.error('Fetch error:', error);
                showStatus('Network error. Please check your internet connection and try again.', 'error');
            }
        }

        function showLipaNaMpesaInstructions(data) {
            // Populate modal with payment details
            document.getElementById('paybillNumber').textContent = data.instructions.paybill;
            document.getElementById('accountNumber').textContent = data.instructions.account_number;
            document.getElementById('paymentAmount').textContent = 'KSh ' + data.instructions.amount.toLocaleString();
            
            // Populate instructions list
            const instructionsList = document.getElementById('paymentInstructions');
            instructionsList.innerHTML = '';
            data.instructions.instructions.forEach(instruction => {
                const li = document.createElement('li');
                li.textContent = instruction;
                li.style.marginBottom = '0.5rem';
                instructionsList.appendChild(li);
            });
            
            // Show modal
            document.getElementById('lipaNaMpesaModal').style.display = 'flex';
        }

        function closeLipaNaMpesaModal() {
            document.getElementById('lipaNaMpesaModal').style.display = 'none';
        }

        function startPaymentTracking() {
            closeLipaNaMpesaModal();
            showStatus('Waiting for payment confirmation...', 'success');
            // Start polling for payment status
            startPaymentPolling();
        }

        async function initiateStkPush() {
            const phone = document.getElementById('customerPhone').value;
            const total = parseFloat(document.getElementById('finalTotal').textContent);
            
            if (!phone) {
                showStatus('Please enter customer phone number for STK Push', 'error');
                return;
            }
            
            if (cart.length === 0) {
                showStatus('Please add items to cart first', 'error');
                return;
            }
            
            if (total <= 0) {
                showStatus('Cart total must be greater than 0', 'error');
                return;
            }
            
            try {
                // First create the sale if not already created
                if (!currentSale) {
                    const saleResponse = await fetch('/pos/create-sale', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            items: cart,
                            customer_phone: phone
                        })
                    });
                    
                    const saleResult = await saleResponse.json();
                    
                    if (!saleResult.success) {
                        showStatus(saleResult.message, 'error');
                        return;
                    }
                    
                    currentSale = saleResult.sale;
                }
                
                showStatus('Sending STK Push to ' + phone + '...', 'success');
                document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - Sending STK Push`;
                
                // Send STK Push
                const stkResponse = await fetch('/mpesa/stk-push', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        phone: phone,
                        amount: total,
                        sale_id: currentSale.id
                    })
                });
                
                const stkResult = await stkResponse.json();
                
                if (stkResult.success) {
                    showStatus('📱 STK Push sent! Customer should check their phone and enter M-Pesa PIN', 'success');
                    document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - Waiting for Payment`;
                    
                    // Start polling for payment status
                    startPaymentPolling(stkResult.checkoutRequestId);
                } else {
                    showStatus('STK Push failed: ' + stkResult.message, 'error');
                    document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - STK Push Failed`;
                }
                
            } catch (error) {
                showStatus('Error initiating STK Push: ' + error.message, 'error');
            }
        }
        
        function startPaymentPolling(checkoutRequestId) {
            let pollCount = 0;
            const maxPolls = 30; // Poll for 5 minutes (30 * 10 seconds)
            
            const pollInterval = setInterval(async () => {
                pollCount++;
                
                try {
                    const response = await fetch('/mpesa/stk-query', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            checkout_request_id: checkoutRequestId
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.ResultCode === '0') {
                        // Payment successful
                        clearInterval(pollInterval);
                        showStatus('🎉 Payment successful! Sale completed.', 'success');
                        
                        // Update currentSale status
                        if (currentSale) {
                            currentSale.status = 'completed';
                            currentSale.status_display = 'Completed';
                        }
                        
                        // Update UI for completed sale
                        updateUIForCompletedSale();
                        
                        // Show print receipt option
                        setTimeout(() => {
                            if (confirm('Payment completed successfully! Would you like to print the receipt?')) {
                                printReceipt();
                            }
                        }, 1000);
                        
                        // Auto-start new sale after 5 seconds (give time for print)
                        setTimeout(() => {
                            newSale();
                        }, 5000);
                        
                    } else if (result.ResultCode && result.ResultCode !== '1037') {
                        // Payment failed (but not timeout)
                        clearInterval(pollInterval);
                        showStatus('❌ Payment failed: ' + (result.ResultDesc || 'Payment was cancelled or failed'), 'error');
                        document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - Payment Failed`;
                    }
                    
                    // Continue polling if result code is 1037 (in progress) or no result code yet
                    
                } catch (error) {
                    console.error('Polling error:', error);
                }
                
                // Stop polling after max attempts
                if (pollCount >= maxPolls) {
                    clearInterval(pollInterval);
                    showStatus('⏰ Payment timeout. Customer may still complete payment.', 'error');
                    document.getElementById('currentSaleNumber').textContent = `Sale ${currentSale.sale_number} - Payment Timeout`;
                }
                
            }, 10000); // Poll every 10 seconds
        }
        
        function showStatus(message, type) {
            const statusElement = document.getElementById('statusMessage');
            statusElement.innerHTML = `<div class="status-message status-${type}">${message}</div>`;
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    statusElement.innerHTML = '';
                }, 5000);
            }
        }
        
        function filterProducts() {
            const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const category = card.getAttribute('data-category');
                
                const matchesSearch = searchTerm === '' || name.includes(searchTerm);
                const matchesCategory = categoryFilter === '' || category === categoryFilter;
                
                card.style.display = matchesSearch && matchesCategory ? 'block' : 'none';
            });
        }

        // Fetch live M-Pesa transactions from paybill
        async function fetchLiveTransactions() {
            try {
                showStatus('Fetching live M-Pesa transactions...', 'info');
                
                const response = await fetch('/mpesa/fetch-live-transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showStatus('Live transaction fetch initiated. New payments will appear in search results within 30 seconds.', 'success');
                    
                    // Automatically refresh search results after a delay
                    setTimeout(() => {
                        searchPayments();
                    }, 5000);
                } else {
                    showStatus(`Failed to fetch live transactions: ${result.message}`, 'error');
                }
                
            } catch (error) {
                console.error('Live transaction fetch error:', error);
                showStatus('Error fetching live transactions. Check your internet connection.', 'error');
            }
        }

        // Register C2B URLs to automatically receive all paybill payments
        async function registerC2BUrls() {
            try {
                showStatus('Registering C2B URLs with Safaricom...', 'info');
                
                const response = await fetch('/mpesa/register-c2b', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showStatus('C2B URLs registered successfully! Your paybill will now automatically send all payments to this system.', 'success');
                } else {
                    showStatus(`Failed to register C2B URLs: ${result.message}`, 'error');
                }
                
            } catch (error) {
                console.error('C2B registration error:', error);
                showStatus('Error registering C2B URLs. Check your internet connection.', 'error');
            }
        }

        // Show all payments (not just recent ones)
        async function showAllPayments() {
            const phone = document.getElementById('customerPhone').value;
            const total = parseFloat(document.getElementById('finalTotal').textContent);
            
            try {
                showStatus('Loading all payments...', 'info');
                
                const paymentResponse = await fetch('/pos/search-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        search: phone,
                        amount: total,
                        show_all: true  // Show all payments regardless of date
                    })
                });
                
                const paymentResult = await paymentResponse.json();
                
                if (paymentResult.success) {
                    // Update the existing modal with all payments
                    document.getElementById('paymentsTableBody').innerHTML = generatePaymentsTableRows(paymentResult.payments, total);
                    document.querySelector('.modal-title').textContent = `M-PESA Payments (${paymentResult.total_found} found - All Time)`;
                    
                    // Store updated payments for filtering
                    window.currentPayments = paymentResult.payments;
                    
                    showStatus(`Loaded ${paymentResult.total_found} payments from all time`, 'success');
                } else {
                    showStatus('Error loading all payments', 'error');
                }
                
            } catch (error) {
                console.error('Show all payments error:', error);
                showStatus('Error loading all payments. Check your internet connection.', 'error');
            }
        }

        // Refresh current payment search
        async function refreshPayments() {
            const phone = document.getElementById('customerPhone').value;
            const total = parseFloat(document.getElementById('finalTotal').textContent);
            
            try {
                showStatus('Refreshing payments...', 'info');
                
                const paymentResponse = await fetch('/pos/search-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        search: phone,
                        amount: total,
                        show_all: false  // Recent payments
                    })
                });
                
                const paymentResult = await paymentResponse.json();
                
                if (paymentResult.success) {
                    // Update the existing modal
                    document.getElementById('paymentsTableBody').innerHTML = generatePaymentsTableRows(paymentResult.payments, total);
                    document.querySelector('.modal-title').textContent = `M-PESA Payments (${paymentResult.total_found} found)`;
                    
                    // Store updated payments for filtering
                    window.currentPayments = paymentResult.payments;
                    
                    showStatus(`Found ${paymentResult.total_found} recent payments`, 'success');
                } else {
                    showStatus('Error refreshing payments', 'error');
                }
                
            } catch (error) {
                console.error('Refresh payments error:', error);
                showStatus('Error refreshing payments. Check your internet connection.', 'error');
            }
        }

        // Simulate Payment Function
        async function simulatePayment() {
            @if(isset($sale) && $sale->status === 'awaiting_payment' && $sale->total > 0)
                if (!confirm('Simulate M-Pesa payment for Ksh {{ number_format($sale->total, 2) }}?\n\nThis will mark the sale as COMPLETED with fake transaction details.')) {
                    return;
                }

                try {
                    // Generate fake M-Pesa transaction details
                    const now = new Date();
                    const timestamp = now.getTime().toString().slice(-10);
                    const fakeTransactionId = 'SIM' + timestamp;
                    const fakeReceiptNumber = 'SIM' + Math.random().toString(36).substr(2, 6).toUpperCase() + timestamp.slice(-4);

                    showStatus('Simulating payment...', 'info');

                    const response = await fetch('/pos/confirm-payment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            sale_id: {{ $sale->id }},
                            mpesa_transaction_id: fakeTransactionId,
                            mpesa_receipt_number: fakeReceiptNumber
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        showStatus('✅ Payment simulated successfully! Sale completed.', 'success');
                        
                        // Update current sale object with the response data
                        if (result.sale) {
                            currentSale = result.sale;
                            // Ensure status is set to completed
                            currentSale.status = 'completed';
                            currentSale.status_display = 'Completed';
                        }
                        
                        // Update the UI to reflect completed status
                        updateUIForCompletedSale();
                        
                        // Show print receipt option
                        setTimeout(() => {
                            if (confirm('Payment completed successfully! Would you like to print the receipt?')) {
                                printReceipt();
                            }
                        }, 500);
                    } else {
                        showStatus('❌ Simulation failed: ' + result.message, 'error');
                    }

                } catch (error) {
                    console.error('Simulation error:', error);
                    showStatus('❌ Simulation failed. Please try again.', 'error');
                }
            @else
                alert('Cannot simulate payment. Sale must be awaiting payment with items.');
            @endif
        }

        // Update UI when sale is completed
        function updateUIForCompletedSale() {
            // Update sale number/status display
            const currentSaleNumberElement = document.getElementById('currentSaleNumber');
            if (currentSaleNumberElement && currentSale) {
                currentSaleNumberElement.textContent = `Sale ${currentSale.sale_number} - COMPLETED`;
                currentSaleNumberElement.style.color = '#059669';
                currentSaleNumberElement.style.fontWeight = 'bold';
            }
            
            // Update sidebar status display
            const statusDisplayElement = document.getElementById('sale-status-display');
            if (statusDisplayElement) {
                statusDisplayElement.textContent = 'Completed';
                statusDisplayElement.style.color = '#059669';
                statusDisplayElement.style.fontWeight = '600';
            }
            
            // Hide payment buttons and show completed message
            const paymentButtons = document.querySelectorAll('.simulate-payment-btn, .btn[onclick*="generateLipaNaMpesa"]');
            paymentButtons.forEach(btn => {
                btn.style.display = 'none';
            });
            
            // Show print receipt button if it exists
            const printButtons = document.querySelectorAll('.btn[onclick*="printReceipt"]');
            printButtons.forEach(btn => {
                btn.style.display = 'inline-block';
                btn.disabled = false;
            });
            
            // Update Payment Information section if it exists
            updatePaymentInformation();
            
            // Show completion message
            showStatus('🎉 Sale completed successfully! You can now print the receipt.', 'success');
        }

        // Print Receipt Function
        function printReceipt() {
            // Always use currentSale if available (it has the most up-to-date data)
            // Only fallback to server-side sale if currentSale doesn't exist
            const sale = currentSale || window.currentSale || @json($sale ?? null);
            
            if (!sale) {
                alert('Cannot print receipt. No sale data available.');
                return;
            }
            
            // Debug logging to see what we have
            console.log('Sale object for receipt:', sale);
            console.log('Sale status:', sale.status);
            console.log('Sale status_display:', sale.status_display);
            console.log('Has mpesa_transaction_id:', !!sale.mpesa_transaction_id);
            console.log('Has payment_confirmed_at:', !!sale.payment_confirmed_at);
            
            // Check if sale is completed (handle both lowercase and uppercase, and status_display)
            // Also check if payment has been made (has mpesa transaction or payment confirmed)
            const hasPayment = !!(sale.mpesa_transaction_id || sale.payment_confirmed_at);
            const statusCompleted = sale.status === 'completed' || 
                                   sale.status === 'Completed' || 
                                   sale.status_display === 'Completed' ||
                                   sale.status_display === 'completed';
            
            const isCompleted = statusCompleted || hasPayment;
            
            console.log('Receipt check details:');
            console.log('- sale.status:', sale.status);
            console.log('- sale.status_display:', sale.status_display);
            console.log('- sale.mpesa_transaction_id:', sale.mpesa_transaction_id);
            console.log('- sale.payment_confirmed_at:', sale.payment_confirmed_at);
            console.log('- hasPayment:', hasPayment);
            console.log('- statusCompleted:', statusCompleted);
            console.log('- isCompleted:', isCompleted);
            
            if (!isCompleted) {
                alert('Cannot print receipt. Sale must be completed and paid.');
                console.log('Receipt printing blocked - sale not completed');
                return;
            }

            const receiptWindow = window.open('', '_blank', 'width=300,height=600');
            
            // Build items HTML
            let itemsHTML = '';
            if (sale.sale_items || sale.saleItems) {
                const items = sale.sale_items || sale.saleItems;
                items.forEach(item => {
                    const productName = item.product ? item.product.name : (item.product_name || 'Unknown Item');
                    itemsHTML += `
                        <div class="item-row">
                            <div class="item-desc">${productName}</div>
                            <div class="item-qty">${item.quantity}</div>
                            <div class="item-price">${parseFloat(item.total_price || 0).toFixed(2)}</div>
                        </div>
                    `;
                });
            }

            // Format date
            const saleDate = sale.payment_confirmed_at || sale.updated_at || new Date().toISOString();
            const formattedDate = new Date(saleDate).toLocaleString('en-GB', {
                day: '2-digit',
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const receiptHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt - ${sale.sale_number}</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { 
                            font-family: monospace; 
                            font-size: 12px; 
                            line-height: 1.4; 
                            padding: 10px; 
                            width: 280px;
                        }
                        .header { text-align: center; margin-bottom: 15px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
                        .hospital-name { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
                        .department { font-size: 14px; margin-bottom: 5px; }
                        .receipt-title { font-size: 14px; font-weight: bold; margin-top: 10px; }
                        .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
                        .items { margin: 15px 0; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; }
                        .item-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
                        .item-desc { flex: 1; }
                        .item-qty { width: 30px; text-align: center; }
                        .item-price { width: 60px; text-align: right; }
                        .totals { margin-top: 10px; }
                        .total-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
                        .grand-total { font-weight: bold; font-size: 14px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; }
                        .payment-info { margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px; }
                        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="hospital-name">KIJABE HOSPITAL</div>
                        <div class="department">CAFETERIA</div>
                        <div class="receipt-title">SALES RECEIPT</div>
                    </div>
                    
                    <div class="info-row">
                        <span>Receipt No:</span>
                        <span>${sale.sale_number}</span>
                    </div>
                    <div class="info-row">
                        <span>Date:</span>
                        <span>${formattedDate}</span>
                    </div>
                    <div class="info-row">
                        <span>Cashier:</span>
                        <span>${sale.cashier ? sale.cashier.name : 'N/A'}</span>
                    </div>
                    <div class="info-row">
                        <span>Customer:</span>
                        <span>${sale.customer_phone || 'Walk-in'}</span>
                    </div>
                    
                    <div class="items">
                        ${itemsHTML}
                    </div>
                    
                    <div class="totals">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>Ksh ${parseFloat(sale.subtotal || 0).toFixed(2)}</span>
                        </div>
                        ${sale.discount > 0 ? `
                        <div class="total-row">
                            <span>Discount:</span>
                            <span>-Ksh ${parseFloat(sale.discount).toFixed(2)}</span>
                        </div>
                        ` : ''}
                        ${sale.vat > 0 ? `
                        <div class="total-row">
                            <span>VAT:</span>
                            <span>Ksh ${parseFloat(sale.vat).toFixed(2)}</span>
                        </div>
                        ` : ''}
                        <div class="total-row grand-total">
                            <span>TOTAL:</span>
                            <span>Ksh ${parseFloat(sale.total || 0).toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <div class="payment-info">
                        <div class="info-row">
                            <span>Payment Method:</span>
                            <span>M-PESA</span>
                        </div>
                        ${sale.mpesa_receipt_number ? `
                        <div class="info-row">
                            <span>M-Pesa Ref:</span>
                            <span>${sale.mpesa_receipt_number}</span>
                        </div>
                        ` : ''}
                        ${sale.mpesa_transaction_id ? `
                        <div class="info-row">
                            <span>Transaction ID:</span>
                            <span>${sale.mpesa_transaction_id}</span>
                        </div>
                        ` : ''}
                        <div class="info-row">
                            <span>Amount Paid:</span>
                            <span>Ksh ${parseFloat(sale.total || 0).toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p>Thank you for your business!</p>
                        <p>Kijabe Hospital Cafeteria</p>
                        <p>${new Date().toLocaleString('en-GB')}</p>
                    </div>
                </body>
                </html>
            `;
            
            receiptWindow.document.write(receiptHTML);
            receiptWindow.document.close();
            
            // Auto print after a short delay
            setTimeout(() => {
                receiptWindow.print();
            }, 500);
        }

        // Update reference total when cart changes
        function updateReferenceTotal() {
            const totalElement = document.getElementById('reference-total');
            if (totalElement && typeof calculateTotal === 'function') {
                totalElement.textContent = 'Ksh ' + calculateTotal().toFixed(2);
            }
        }

        // Search Payments Function
        async function searchPayments() {
            const searchInput = document.getElementById('paymentSearch') || 
                               document.getElementById('paymentSearchDraft') || 
                               document.getElementById('paymentSearchNew');
            
            if (!searchInput) {
                showStatus('Search input not found', 'error');
                return;
            }
            
            const searchTerm = searchInput.value.trim();
            
            if (!searchTerm) {
                showStatus('Please enter a search term (phone number, transaction ID, etc.)', 'info');
                return;
            }
            
            try {
                showStatus('Searching payments...', 'info');
                
                const response = await fetch('/pos/search-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        search_term: searchTerm
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    displaySearchResults(result.payments || []);
                    showStatus(`Found ${result.payments ? result.payments.length : 0} payment(s)`, 'success');
                } else {
                    showStatus('Error searching payments', 'error');
                }
            } catch (error) {
                console.error('Payment search error:', error);
                showStatus('Error searching payments', 'error');
            }
        }
        
        // Display search results
        function displaySearchResults(payments) {
            // Create a modal or update a results area
            let resultsModal = document.getElementById('searchResultsModal');
            if (!resultsModal) {
                // Create modal if it doesn't exist
                resultsModal = document.createElement('div');
                resultsModal.id = 'searchResultsModal';
                resultsModal.className = 'modal-overlay';
                document.body.appendChild(resultsModal);
            }
            
            let resultsHTML = `
                <div class="modal" style="max-width: 1200px; width: 95%;">
                    <div class="modal-header">
                        <h3 class="modal-title">M-PESA Payments</h3>
                        <button onclick="closeSearchResults()" class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div style="margin-bottom: 1rem;">
                            <input type="text" id="resultSearch" placeholder="Search by Name/Phone number or Transaction Code" 
                                   style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.375rem;"
                                   oninput="filterDisplayedResults()">
                        </div>
                        
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Name</th>
                                        <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Transaction Code</th>
                                        <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Phone</th>
                                        <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Amount</th>
                                        <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Time</th>
                                        <th style="padding: 0.75rem; text-align: left; font-weight: 500;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="searchResultsTableBody">
            `;
            
            if (payments.length === 0) {
                resultsHTML += '<tr><td colspan="6" style="padding: 2rem; text-align: center; color: #6b7280;">No payments found.</td></tr>';
            } else {
                payments.forEach(payment => {
                    const formattedDate = new Date(payment.created_at).toLocaleString();
                    const maskedPhone = payment.phone_number ? 
                        payment.phone_number.substring(0, 4) + ' ***** ' + payment.phone_number.substring(payment.phone_number.length - 3) : 
                        'N/A';
                    
                    resultsHTML += `
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 0.75rem;">${payment.first_name || 'N/A'}</td>
                            <td style="padding: 0.75rem;">${payment.mpesa_receipt_number || 'N/A'}</td>
                            <td style="padding: 0.75rem;">${maskedPhone}</td>
                            <td style="padding: 0.75rem;">${payment.amount}</td>
                            <td style="padding: 0.75rem;">${formattedDate}</td>
                            <td style="padding: 0.75rem;">
                                <button onclick="addPaymentToSale('${payment.id}', '${payment.amount}', '${payment.mpesa_receipt_number}')" 
                                        style="background: #f59e0b; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer; font-weight: 500;">
                                    Add
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            
            resultsHTML += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="closeSearchResults()" class="btn-secondary">Close</button>
                    </div>
                </div>
            `;
            
            resultsModal.innerHTML = resultsHTML;
            resultsModal.style.display = 'flex';
            
            // Close when clicking outside the modal content
            resultsModal.onclick = function(e) {
                if (e.target === resultsModal) {
                    closeSearchResults();
                }
            };
        }
        
        // Close search results modal
        function closeSearchResults() {
            const modal = document.getElementById('searchResultsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Filter displayed search results
        function filterDisplayedResults() {
            const searchInput = document.getElementById('resultSearch');
            const tableBody = document.getElementById('searchResultsTableBody');
            
            if (!searchInput || !tableBody) return;
            
            const searchTerm = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) { // Exclude Actions column
                    if (cells[j].textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            }
        }

        // Add payment to current sale
        async function addPaymentToSale(paymentId, amount, transactionCode) {
            if (!confirm(`Add payment of Ksh ${amount} (${transactionCode}) to current sale?`)) {
                return;
            }
            
            try {
                showStatus('Linking payment to sale...', 'info');
                
                const response = await fetch('/pos/link-payment-to-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_id: paymentId,
                        amount: amount,
                        transaction_code: transactionCode
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    showStatus(`Payment ${transactionCode} linked to sale successfully!`, 'success');
                    closeSearchResults();
                    
                    // Refresh the page to show updated sale information
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    const errorData = await response.json();
                    showStatus(errorData.message || 'Error linking payment to sale', 'error');
                }
            } catch (error) {
                console.error('Link payment error:', error);
                showStatus('Error linking payment to sale', 'error');
            }
        }

        // Function to perform payment search from the search modal
        function performPaymentSearch() {
            const searchInput = document.getElementById('paymentSearchNew');
            const searchTerm = searchInput ? searchInput.value.trim() : '';
            
            if (!searchTerm) {
                showStatus('Please enter a search term (phone number, transaction ID, etc.)', 'info');
                return;
            }
            
            closeModal('searchPaymentsModal');
            searchPayments();
        }

        // Fetch available payments directly from Safaricom API
        async function fetchAvailablePayments() {
            try {
                showStatus('Fetching available M-Pesa payments...', 'info');
                
                const response = await fetch('/pos/fetch-available-payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    displaySearchResults(result.payments || []);
                    showStatus(`Found ${result.payments ? result.payments.length : 0} available payment(s)`, 'success');
                } else {
                    const errorData = await response.json();
                    showStatus(errorData.message || 'Error fetching payments', 'error');
                }
            } catch (error) {
                console.error('Payment fetch error:', error);
                showStatus('Error fetching available payments', 'error');
            }
        }

        // Clear Cart Function
        function clearCart() {
            if (cart.length === 0) {
                showStatus('Cart is already empty', 'info');
                return;
            }
            
            if (confirm('Are you sure you want to clear the cart?')) {
                cart = [];
                updateCartDisplay();
                updateTotals();
                showStatus('Cart cleared', 'success');
            }
        }

        // Inventory Management Functions
        async function refreshInventoryStatus() {
            try {
                const response = await fetch('/api/pos-inventory-status', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        updateProductCards(result.products);
                    }
                }
            } catch (error) {
                console.error('Failed to refresh inventory status:', error);
            }
        }

        // Update product cards with new inventory data
        function updateProductCards(inventoryData) {
            inventoryData.forEach(product => {
                const productCard = document.querySelector(`[data-product-id="${product.id}"]`);
                if (productCard) {
                    // Update stock indicator
                    const stockIndicator = productCard.querySelector('.stock-indicator');
                    const stockQuantity = productCard.querySelector('.stock-quantity');
                    const addBtn = productCard.querySelector('.add-btn');

                    // Update stock classes
                    productCard.className = `product-card ${product.stock_status}`;

                    // Update stock indicator
                    if (stockIndicator) {
                        stockIndicator.className = `stock-indicator ${product.stock_status}`;
                        if (product.is_out_of_stock) {
                            stockIndicator.textContent = 'Out';
                        } else if (product.is_low_stock) {
                            stockIndicator.textContent = 'Low';
                        } else {
                            stockIndicator.textContent = product.stock_quantity;
                        }
                    }

                    // Update stock quantity text
                    if (stockQuantity) {
                        stockQuantity.textContent = `Stock: ${product.stock_quantity} available`;
                    }

                    // Show/hide add button based on stock
                    if (addBtn) {
                        addBtn.style.display = product.is_out_of_stock ? 'none' : 'flex';
                    }

                    // Update products data for cart functionality
                    const productIndex = products.findIndex(p => p.id === product.id);
                    if (productIndex !== -1) {
                        products[productIndex] = {...products[productIndex], ...product};
                    }
                }
            });
        }

        // Modal Management Functions
        function closeAllModals() {
            // Close all modal overlays
            const modals = document.querySelectorAll('.modal-overlay');
            modals.forEach(modal => {
                modal.style.display = 'none';
                modal.classList.remove('active');
            });
            
            // Close search results modal
            const searchModal = document.getElementById('searchResultsModal');
            if (searchModal) {
                searchModal.style.display = 'none';
            }
        }
        
        function openModal(modalId) {
            closeAllModals(); // Close any open modals first
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.add('active');
            }
        }
        
        // Updated modal functions to use proper management
        function openManualMpesaModal() {
            openModal('mpesaModal');
        }
        
        function closeManualMpesaModal() {
            closeAllModals();
        }
        
        function closeLipaNaMpesaModal() {
            closeAllModals();
        }
        
        function closeMpesaPaymentsModal() {
            closeAllModals();
        }

        // Call updateReferenceTotal when items are added/removed (if on new sale)
        @if(!isset($sale))
        const originalAddToCart = addToCart;
        window.addToCart = function(productId) {
            originalAddToCart(productId);
            setTimeout(updateReferenceTotal, 100);
        };

        const originalUpdateQuantity = updateQuantity;
        window.updateQuantity = function(productId, change) {
            originalUpdateQuantity(productId, change);
            setTimeout(updateReferenceTotal, 100);
        };

        const originalRemoveFromCart = removeFromCart;
        window.removeFromCart = function(productId) {
            originalRemoveFromCart(productId);
            setTimeout(updateReferenceTotal, 100);
        };
        @endif

        // Start periodic inventory status refresh for real-time updates
        setInterval(refreshInventoryStatus, 30000); // Refresh every 30 seconds
        
        // Initial refresh on page load
        document.addEventListener('DOMContentLoaded', function() {
            refreshInventoryStatus();
        });
    </script>
</body>
</html>