# StockPilot

StockPilot is a PHP-based supermarket management system designed to support inventory control, sales operations, employee management, supplier onboarding, and AI-driven business insights.

This project includes a manager dashboard, cashier interface, product and supplier workflows, and a live AI assistant called StockSense for real-time recommendations.

## Key Features

- Secure login for managers and cashiers
- Inventory overview with stock levels and expiry tracking
- New stock entry and supplier registration workflows
- Employee registration and role-based access control
- Sales analysis and performance dashboards
- AI-powered command center for business questions
- Modular backend with reusable PHP classes and MySQL database access

## Project Structure

- `api/` - backend API endpoints for sale completion and AJAX-style data operations
- `assets/css/` - visual styles for each page and interface component
- `assets/js/` - client-side logic for dashboard charts, AI chat, cashier operations, and more
- `classes/` - core PHP classes for database access and business logic
- `config/` - database configuration constants
- `public/` - user-facing pages, login screen, dashboards, and process endpoints
- `templates/` - shared header/footer and authentication control

## Core Components

- `public/index.php` - login page for StockPilot
- `public/login_process.php` - authenticates users and redirects managers or cashiers
- `public/MainDashBoard.php` - manager dashboard with live stats and AI insights
- `public/Cashier.php` - cashier interface for processing sales
- `public/AIchat.php` - StockSense chat interface for intelligent business queries
- `classes/Database.php` - PDO singleton database connector
- `classes/Product.php` - product registration and stock batch handling
- `classes/Employee.php` - employee operations and performance queries
- `classes/Supplier.php` - supplier registration and relationship logic
- `classes/AIService.php` - AI request handler for StockSense integration
- `templates/auth_check.php` - session and role-based access control

## Installation

1. Install a local PHP environment (XAMPP, WAMP, Laragon, or similar).
2. Create a MySQL database named `supermarket_inventory`.
3. Configure database credentials in `config/config.php`.
4. Place the project folder in your web server's document root.
5. Access `public/index.php` through your browser.

## Database Notes

The system expects a MySQL database with tables such as:

- `employees`
- `roles`
- `products`
- `stock_batches`
- `sales_log`
- `suppliers`
- `supplier_categories`
- `categories`

Ensure the schema includes a product `barcode` field used as the primary identifier for inventory and sales operations.

## AI Integration

StockPilot includes an AI assistant called StockSense.

- `classes/AIService.php` sends business-context data to a Google Gemini API endpoint.
- `public/process_ai.php` builds live store context and forwards the query.
- `public/AIchat.php` provides the user interface for asking questions.

> Note: The AI integration requires a valid API key and internet access.

## User Roles

- Manager: access to the full dashboard, inventory management, employee and supplier tools, and AI assistant.
- Cashier: access to sales processing and cashier-specific screens only.

## How to Use

1. Open the login page at `public/index.php`.
2. Enter valid credentials for a Manager or Cashier account.
3. If Manager, use the dashboard, AI tools, and management pages.
4. If Cashier, process sales and manage account settings on the cashier portal.

## Customization

- Update `config/config.php` for database credentials.
- Modify CSS files in `assets/css/` for branding and page layout.
- Extend `classes/` with new business logic as needed.
- Add new pages in `public/` and reuse templates from `templates/`.

## Support

If you need help, inspect `assets/js/` and `classes/` for logic flow, and verify the MySQL schema matches the code references.

---

StockPilot is built to streamline supermarket operations with a modern interface and practical AI insights for smarter inventory and sales decisions.