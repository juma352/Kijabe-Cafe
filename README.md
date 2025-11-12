# Kijabe Hospital POS System

A comprehensive Point of Sale (POS) system built specifically for Kijabe Hospital, featuring modern Laravel architecture, M-Pesa integration, and role-based access control.

## 🏥 About

The Kijabe Hospital POS System is designed to streamline hospital retail operations with a focus on:
- **Medical Environment**: Hospital-themed UI with professional medical color schemes
- **Role-Based Access**: Different dashboards for Admin, Kitchen Manager, and Cashier roles
- **Payment Integration**: Seamless M-Pesa payment processing with manual entry options
- **Inventory Management**: Real-time stock tracking with low-stock alerts
- **Receipt Printing**: Professional receipt generation and printing

## ✨ Features

### 🎯 Current Features

#### Authentication & Authorization
- **Role-Based Dashboards**: Admin, Kitchen Manager, and Cashier interfaces
- **Secure Authentication**: Laravel Breeze with email verification
- **User Management**: Role assignment and user administration

#### Point of Sale (Cashier Dashboard)
- **Product Management**: Browse products by category with search functionality
- **Shopping Cart**: Add/remove items with quantity management
- **Multiple Payment Methods**:
  - M-Pesa STK Push integration
  - Manual M-Pesa transaction entry
  - Link existing M-Pesa payments
  - Payment simulation for testing
- **Receipt Generation**: Professional receipt printing with hospital branding
- **Sales Tracking**: Today's sales overview and transaction history

#### Inventory Management (Kitchen Manager Dashboard)
- **Real-Time Inventory**: Live stock quantity tracking
- **Stock Management**: Update stock levels and low-stock thresholds
- **Inventory Alerts**: Automatic low-stock and out-of-stock notifications
- **Category Management**: Full CRUD operations for product categories
- **Product Management**: Complete product lifecycle management
- **Bulk Operations**: Bulk stock updates and product management
- **Search & Filter**: Advanced product filtering and search capabilities

#### Admin Dashboard
- **System Overview**: Comprehensive system statistics
- **User Management**: Role assignment and user administration
- **Sales Analytics**: Revenue tracking and performance metrics
- **Inventory Oversight**: System-wide inventory monitoring

#### Technical Features
- **Hospital Branding**: Consistent Kijabe Hospital color scheme and styling
- **Responsive Design**: Mobile-friendly interface with modern CSS Grid
- **Asset Management**: Proper Laravel Vite asset compilation
- **API Integration**: M-Pesa Daraja API for payment processing
- **Database Optimization**: Efficient queries with eager loading
- **Error Handling**: Comprehensive error logging and user feedback

### 🚧 Upcoming Features (Production Roadmap)

#### Enhanced Payment Features
- [ ] **Split Payments**: Multiple payment methods for single transaction
- [ ] **Payment Analytics**: Detailed payment method analytics

#### Advanced Inventory Management
- [ ] **Inventory Alerts**: Email/SMS notifications for low stock
- [ ] **Supplier Management**: Vendor tracking and purchase orders
- [ ] **Inventory Forecasting**: Predictive stock level recommendations

#### Reporting & Analytics
- [ ] **Advanced Reports**: Sales, inventory, and financial reports
- [ ] **Export Functions**: PDF/Excel report generation
- [ ] **Dashboard Analytics**: Real-time charts and graphs
- [ ] **Audit Trail**: Complete transaction history tracking

#### System Enhancements
- [ ] **Multi-Location Support**: Support for multiple hospital locations
- [ ] **Backup & Recovery**: Automated database backups
- [ ] **API Documentation**: Complete API documentation with Swagger
- [ ] **Performance Monitoring**: System performance tracking
- [ ] **Security Enhancements**: Advanced security features

## 🛠️ Prerequisites

### System Requirements
- **PHP**: >= 8.1
- **Composer**: Latest version
- **Node.js**: >= 16.x
- **NPM**: >= 8.x
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Web Server**: Apache/Nginx (for production)

### Development Environment
- **XAMPP/WAMP**: For local development (includes Apache, MySQL, PHP)
- **VS Code**: Recommended IDE with PHP and Laravel extensions
- **Git**: For version control

### M-Pesa Integration Requirements
- **Safaricom Developer Account**: For M-Pesa Daraja API access
- **Consumer Key & Secret**: From Safaricom Developer Portal
- **Business Short Code**: M-Pesa paybill/till number
- **Passkey**: For STK Push authentication
- **SSL Certificate**: Required for production M-Pesa callbacks

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/kijabe-pos.git
cd kijabe-pos
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Configuration
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kijabe_pos
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. M-Pesa Configuration (Optional for Development)
Add M-Pesa credentials to `.env`:
```env
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_CALLBACK_URL=https://yourdomain.com/mpesa/callback
```

### 7. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 8. Build Assets
```bash
# For development
npm run dev

# For production
npm run build
```

### 9. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 👥 Default Users

After seeding, you can log in with these test accounts:

### Admin Account
- **Email**: admin@kijabepos.com
- **Password**: password

### Kitchen Manager Account
- **Email**: kitchen@kijabepos.com
- **Password**: password

### Cashier Account
- **Email**: cashier@kijabepos.com
- **Password**: password

## 🏗️ Project Structure

```
kijabe-pos/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php     # Role-based dashboard logic
│   │   ├── POSController.php           # POS and sales management
│   │   ├── CategoryController.php      # Category CRUD operations
│   │   ├── ProductController.php       # Product management
│   │   └── InventoryController.php     # Inventory operations
│   ├── Models/
│   │   ├── User.php                    # User model with roles
│   │   ├── Sale.php                    # Sales transactions
│   │   ├── Product.php                 # Product catalog
│   │   ├── Category.php               # Product categories
│   │   └── MpesaPayment.php           # M-Pesa transactions
│   └── Services/
│       └── MpesaService.php           # M-Pesa API integration
├── database/
│   ├── migrations/                     # Database schema
│   └── seeders/                       # Sample data
├── resources/
│   ├── views/
│   │   ├── dashboard/                 # Role-based dashboards
│   │   ├── pos/                       # POS interface
│   │   └── layouts/                   # Shared layouts
│   ├── css/
│   │   └── kitchen-manager.css        # Kitchen manager styles
│   └── js/
│       └── kitchen-manager.js         # Kitchen manager functionality
├── routes/
│   ├── web.php                        # Web routes
│   └── api.php                        # API routes
└── public/
    └── Images/                        # Hospital branding assets
```

## 🔧 Configuration

### Role Management
Users are assigned roles during registration or by admin:
- `admin`: Full system access
- `kitchen_manager`: Inventory and product management
- `cashier`: POS operations and sales

### M-Pesa Integration
1. Register at [Safaricom Developer Portal](https://developer.safaricom.co.ke/)
2. Create an app and get credentials
3. Configure callback URLs for your domain
4. Update `.env` with your credentials

### Asset Compilation
The system uses Laravel Vite for asset compilation:
```bash
# Watch for changes during development
npm run dev

# Build for production
npm run build
```

## 📚 Usage Guide

### For Cashiers
1. **Login** with cashier credentials
2. **Create New Sale** from the sales dashboard
3. **Add Products** to cart by clicking product cards
4. **Process Payment** via M-Pesa STK Push or manual entry
5. **Print Receipt** after successful payment
6. **Start New Sale** for next customer

### For Kitchen Managers
1. **Login** with kitchen manager credentials
2. **Monitor Inventory** on the main dashboard
3. **Update Stock Levels** using the inventory management section
4. **Manage Categories** - add, edit, or remove product categories
5. **Manage Products** - complete product lifecycle management
6. **View Alerts** for low stock and out-of-stock items

### For Administrators
1. **Login** with admin credentials
2. **Monitor System** via comprehensive dashboard
3. **Manage Users** and assign roles
4. **View Analytics** for sales and inventory performance
5. **System Configuration** and maintenance

## 🧪 Testing

### Test Accounts
Use the seeded accounts for different role testing:
- Admin: Full access to all features
- Kitchen Manager: Inventory and product management
- Cashier: POS operations

### M-Pesa Testing
- Use Safaricom sandbox credentials for testing
- Test phone number: 254708374149
- Use "Simulate Payment" feature for offline testing

### Sample Data
The system includes comprehensive seeders with:
- Sample products across different categories
- Test M-Pesa transactions
- Sample sales data

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Add comments for complex business logic
- Write tests for new features
- Follow Laravel best practices

### Asset Development
- CSS: Use the established hospital color scheme
- JavaScript: Follow ES6+ standards
- Components: Maintain responsive design principles

## 📄 License

This project is proprietary software developed for Kijabe Hospital. All rights reserved.

## 👨‍💻 Authors

- **Kijabe Hospital Development Team** - Initial work and ongoing development

## 🆘 Support

For support and questions:
- **Email**: jumaleon58@gmail.com
- **Documentation**: [Project Wiki](https://github.com/yourusername/kijabe-pos/wiki)
- **Issues**: [GitHub Issues](https://github.com/yourusername/kijabe-pos/issues)

## 📋 Changelog

### Version 1.0.0 (Current)
- ✅ Role-based authentication and dashboards
- ✅ Complete POS system with M-Pesa integration
- ✅ Comprehensive inventory management
- ✅ Category and product management
- ✅ Professional receipt printing
- ✅ Hospital-themed responsive UI
- ✅ Laravel best practices implementation

### Upcoming Version 1.1.0
- 🚧 Advanced reporting system
- 🚧 Enhanced payment options
- 🚧 Inventory forecasting
- 🚧 Email/SMS notifications

---

**Built with ❤️ for Kijabe Hospital**
