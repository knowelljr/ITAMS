# ITAMS - Asset Management System

## Overview

ITAMS is a comprehensive **IT Asset Management System** built with PHP and Tailwind CSS. It provides enterprise-grade asset tracking, issuance, requests, and inventory management with a modern, responsive interface.

## Current Status: PHASE 6 COMPLETE ✅

### Latest Enhancement: Store-Based Inventory Management
The system has been upgraded from simple direct asset issuance to a **comprehensive store-based inventory system** with:
- ✅ Centralized inventory stores/warehouses
- ✅ Per-location asset stock tracking
- ✅ Complete asset movement audit trail
- ✅ Condition-based asset handling on receipt
- ✅ Multi-store support and scalability

**Documentation:** See [STORE_IMPLEMENTATION_PHASE_6_COMPLETE.md](STORE_IMPLEMENTATION_PHASE_6_COMPLETE.md)

## Key Features

### Asset Management
- Comprehensive asset database with categorization
- Asset lifecycle tracking (purchase → issuance → return → disposal)
- Asset depreciation and value tracking
- Bulk asset import and management

### Request & Issuance Workflow
- **Asset Requests**: Departments request needed assets with justification
- **Dual Approval**: Two-level approval process for asset requests
- **Store-Based Issuance**: Assets issued from centralized inventory stores
- **Condition Assessment**: Receipt confirmation with condition evaluation
- **Damage Handling**: Automatic routing of damaged assets

### Multi-Store Support
- Multiple inventory locations (Main Store, Branch Store, Central Warehouse)
- Per-store stock levels and asset availability
- Store manager assignments and permissions
- Automatic movement tracking between locations

### Audit & Compliance
- Complete asset movement history
- Timestamped transaction records
- User action tracking and accountability
- Movement reason documentation

### User Management
- Role-based access control (Admin, IT Manager, Department Manager, Requester, Storekeeper)
- Department hierarchy
- User authentication with JWT
- Password management

### Reporting & Analytics
- Asset inventory reports
- Department asset allocations
- Store inventory status
- Movement history and audit trails

## Architecture

### Technology Stack
- **Backend**: PHP 7.4+
- **Database**: SQL Server
- **Frontend**: HTML5, CSS (Tailwind), JavaScript (Vanilla)
- **Authentication**: JWT-based session management
- **API**: RESTful JSON endpoints for dynamic UI

### Database Schema
- **Users & Departments**: User management and organizational structure
- **Assets**: Asset master data with categorization
- **Inventory Stores**: Warehouse/location definitions
- **Store Inventory**: Per-location asset stock levels
- **Asset Issuances**: Issue/receipt history with conditions
- **Asset Movements**: Complete audit trail
- **Asset Requests**: Approval workflow and tracking
- **Notifications**: System alerts and communications

### Directory Structure
```
app/
  ├── Controllers/           # Application controllers
  │   ├── AdminController.php
  │   ├── AssetController.php
  │   ├── AssetIssuanceController.php
  │   ├── AssetRequestController.php
  │   ├── AuthController.php
  │   ├── DashboardController.php
  │   └── DepartmentController.php
  ├── Models/               # Data models
  │   ├── Asset.php
  │   ├── AssetIssuance.php
  │   ├── AssetRequest.php
  │   ├── Store.php        # NEW: Store inventory management
  │   ├── User.php
  │   └── Notification.php
  ├── Database/            # Database connection
  ├── Helpers/             # Utilities (encryption, JWT)
  └── Middleware/          # Authentication middleware

config/                     # Configuration files
database/
  └── migrations/          # Database schema migrations
resources/
  └── views/              # HTML views
    ├── dashboard.php
    ├── layout.php
    └── [feature folders]
routes/                    # URL routing
public/                    # Public assets & entry point
vendor/                    # Composer dependencies
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- SQL Server database
- Composer (for dependencies)
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone Repository**
```bash
git clone [repository-url]
cd ITAMS
```

2. **Install Dependencies**
```bash
composer install
```

3. **Configure Database**
- Edit `config/database.php` with your SQL Server credentials
- Ensure database exists and is accessible

4. **Run Migrations**
```bash
php run_migrations.php
```

5. **Create Initial Users & Stores**
```bash
php create_test_users.php          # Create test users
php create_initial_stores.php      # Create initial inventory stores
```

6. **Seed Test Data** (Optional)
```bash
php setup_test_assets.php          # Create sample assets
php create_test_movements.php      # Create test movements
```

7. **Configure Web Server**
- Point DocumentRoot to `public/`
- Ensure `public/index.php` handles all requests
- Enable PHP execution in `public/` directory

8. **Start Using**
- Navigate to `http://localhost/` (or your configured URL)
- Login with test credentials from `create_test_users.php`

## Usage

### For Asset Requesters
1. **Request Asset**: Navigate to Assets → New Request
2. **Await Approval**: Submit request with justification
3. **Receive Asset**: Once approved/issued, confirm receipt at Assets → Receive with condition assessment
4. **Return Asset**: Return assets through request workflow

### For IT Managers
1. **Approve Requests**: Review and approve asset requests
2. **Issue Assets**: Issue from appropriate inventory store
3. **View Inventory**: Check store stock levels and asset locations
4. **Generate Reports**: View asset allocation by department

### For Administrators
1. **Manage Users**: Create/edit users and assign roles/departments
2. **Manage Assets**: Add/edit asset master data
3. **Manage Stores**: Create inventory stores and assign department managers
4. **System Configuration**: Configure system settings and permissions
5. **View Audit Trail**: Review all asset movements and transactions

## Testing

### Quick Start Testing
See [STORE_WORKFLOW_TESTING.md](STORE_WORKFLOW_TESTING.md) for comprehensive testing procedures

### Key Test Scenarios
1. ✅ Store inventory loads correctly
2. ✅ Assets deducted from store on issuance
3. ✅ Condition recorded on receipt
4. ✅ Damaged items handled appropriately
5. ✅ Audit trail captures all movements

### Running Tests
```bash
# Verify database schema
php verify_store_tables.php

# Check system configuration
php system_check.php

# Test specific features
php test_db.php
php test_route.php
```

## API Reference

### Store Inventory Endpoints
- `GET /api/stores/:id/inventory` - Get inventory for specific store (returns JSON)
- `POST /assets/issue/process` - Issue asset from store
- `POST /assets/receive/process` - Confirm receipt with condition

### Asset Request Endpoints
- `POST /api/asset-requests/get-by-number` - Get request details

## Key Files

### Views (Phase 6 Updated)
- [resources/views/assets/issue.php](resources/views/assets/issue.php) - Store selection + dynamic asset loading
- [resources/views/assets/receive.php](resources/views/assets/receive.php) - Condition assessment + receipt notes

### Controllers
- [app/Controllers/AssetIssuanceController.php](app/Controllers/AssetIssuanceController.php) - Store-based issuance logic
- [app/Controllers/AssetRequestController.php](app/Controllers/AssetRequestController.php) - Request workflows

### Models
- [app/Models/Store.php](app/Models/Store.php) - Store inventory management (12 methods)
- [app/Models/Asset.php](app/Models/Asset.php) - Asset master data
- [app/Models/AssetRequest.php](app/Models/AssetRequest.php) - Request tracking

### Database
- [database/migrations/009_create_inventory_stores_table.sql](database/migrations/009_create_inventory_stores_table.sql)
- [database/migrations/010_create_store_inventory_table.sql](database/migrations/010_create_store_inventory_table.sql)
- [database/migrations/011_create_asset_movements_table.sql](database/migrations/011_create_asset_movements_table.sql)
- [database/migrations/012_add_store_fields_to_issuances.sql](database/migrations/012_add_store_fields_to_issuances.sql)

## Documentation

- **[STORE_IMPLEMENTATION_PHASE_6_COMPLETE.md](STORE_IMPLEMENTATION_PHASE_6_COMPLETE.md)** - Complete Phase 6 implementation details
- **[STORE_WORKFLOW_TESTING.md](STORE_WORKFLOW_TESTING.md)** - Comprehensive testing guide
- **[STORE_INVENTORY_IMPLEMENTATION.md](STORE_INVENTORY_IMPLEMENTATION.md)** - Architecture overview
- **[LAYOUT_UPDATE_SUMMARY.md](LAYOUT_UPDATE_SUMMARY.md)** - UI/UX improvements
- **[VERIFICATION_CHECKLIST.md](VERIFICATION_CHECKLIST.md)** - Implementation checklist

## Troubleshooting

### Database Connection Issues
```
Error: "Could not connect to database"
Solution: Verify database.php config and SQL Server is running
```

### Asset Dropdown Not Loading
```
Error: Assets dropdown shows no items after store selection
Solution: Populate store_inventory via test data script
Check browser console (F12) for API errors
```

### Form Submission Failures
```
Error: "Invalid condition status" or similar validation errors
Solution: Check form field names match controller expectations
Verify enum values match database schema
```

### Permission Denied Errors
```
Error: "You do not have permission to perform this action"
Solution: Check user role and department assignments
Verify middleware allows user role for route
```

## Development

### Running Local Server
```bash
cd public
php -S localhost:8000
```

### Debugging
- Enable error logging in database.php
- Check `logs/` directory for error records
- Use browser DevTools (F12) for JavaScript debugging
- SQL Server profiler for query analysis

## Performance Optimization

### Database Indexes
- `asset_movements(asset_id, created_at)` - Quick movement history
- `store_inventory(store_id, asset_id)` - Fast stock lookups
- `asset_issuances(status, created_at)` - Quick issuance filtering

### Caching Recommendations
- Cache store list (rarely changes)
- Cache user permissions (checked per request)
- Cache asset categories (infrequent updates)

## Security

### Authentication
- JWT token-based session management
- Secure password hashing with bcrypt
- Session timeout after inactivity

### Authorization
- Role-based access control (RBAC)
- Resource-level permission checks
- SQL parameter binding to prevent injection

### Data Protection
- Input validation and sanitization
- CSRF protection tokens
- XSS prevention with proper encoding

## Roadmap

### Completed ✅
- [x] Core asset management system
- [x] User & department management
- [x] Request & approval workflow
- [x] Store-based inventory (Phase 6)
- [x] Asset condition tracking
- [x] Movement audit trail

### In Development
- [ ] Store management UI/CRUD
- [ ] Advanced inventory reports
- [ ] Asset reconciliation workflows
- [ ] Bulk operations and imports

### Planned
- [ ] Mobile app integration
- [ ] Advanced analytics dashboard
- [ ] Multi-company support
- [ ] API rate limiting & throttling
- [ ] Automated asset depreciation

## Support & Contribution

### Getting Help
- Check documentation files in root directory
- Review test scripts for usage examples
- Check database schema comments for field details

### Contributing
- Follow existing code style (PSR-12)
- Add migrations for database changes
- Write test cases for new features
- Update documentation

## License

[LICENSE - To be defined]

## Contact

For support or questions, contact the IT team or project administrator.

---

**Last Updated**: Phase 6 Complete - Store-Based Inventory System Operational
**Version**: 2.0 - Store-Based Architecture
**Status**: Ready for Testing & Deployment
