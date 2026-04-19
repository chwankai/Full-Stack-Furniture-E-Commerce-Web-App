# Full-Stack Furniture E-Commerce Web App

## Overview
This project is a PHP + MySQL furniture e-commerce application with:

- a customer-facing storefront
- an admin panel for product and order management
- shared assets and reusable PHP includes

The codebase is largely built with core PHP, Bootstrap, jQuery, and MySQL.

## Main Applications
- Storefront: `index.php`
- Admin panel: `admin/index.php`
- Customer order tracking page: `track-order.php`

## Tech Stack
- Backend: PHP
- Database: MySQL
- Frontend: HTML, CSS, JavaScript, jQuery, Bootstrap
- Mail/config support: Composer + `vlucas/phpdotenv`

## Setup
1. Place the project inside your local web root, for example XAMPP `htdocs/`.
2. Create a MySQL database named `shopping`.
3. Import `shopping.sql`.
4. Update database credentials if needed:
   - storefront/shared config: `includes/config.php`
   - admin config: `admin/include/config.php`
5. Install PHP dependencies if needed:

```bash
composer install
```

6. Add mail credentials to `.env` if you use the email features.

## Current Folder Structure
```text
Full-Stack-Furniture-E-Commerce-Web-App/
├── admin/
│   ├── bootstrap/
│   ├── css/
│   ├── images/
│   ├── include/
│   ├── phpmailer/
│   ├── productimages/
│   ├── scripts/
│   └── tcpdf/
├── assets/
│   ├── css/
│   ├── fonts/
│   ├── images/
│   ├── js/
│   └── less/
├── datepicker164/
├── includes/
├── layouts/
├── sendemail/
├── vendor/
├── *.php
├── composer.json
├── composer.lock
├── shopping.sql
└── .env
```

## Folder Explanations

### `admin/`
Contains the full admin-side application.

### `admin/bootstrap/`
Legacy Bootstrap assets still used by the live admin pages.

### `admin/css/`
Admin-specific stylesheet bundle used by the runtime admin UI.

Files:
- `styles.css`: admin login/entry screen styles
- `theme.css`: main admin layout/theme styles
- `updateorder.css`: order update/order detail page styles
- `loader.css`: admin loading/overlay styles

### `admin/images/`
Images used by the admin UI.

Examples:
- `Main_logo.png`: admin logo
- `user.png`: admin user avatar
- `favicon.ico`: admin favicon
- `icons/`: admin icon font assets
- `jquery-ui/`: small supporting images referenced by admin CSS

### `admin/include/`
Shared admin PHP includes.

Files:
- `admin_session.php`: starts the admin-only named PHP session
- `config.php`: admin database connection
- `header.php`: top navbar/header
- `sidebar.php`: admin sidebar navigation
- `footer.php`: footer partial
- `record.php`: shared record helper include

### `admin/phpmailer/`
Mailer library used by some admin email flows.

### `admin/productimages/`
Uploaded product image storage, grouped by product id.

### `admin/scripts/`
Admin JavaScript dependencies still used by the admin pages.

Examples:
- jQuery
- jQuery UI
- DataTables
- Flot

### `admin/tcpdf/`
PDF generation library used for admin reporting/export flows.

### `assets/`
Main storefront asset folder.

### `assets/css/`
Live customer-facing CSS bundle.

Files:
- `bootstrap.min.css`: storefront Bootstrap
- `main.css`: main storefront styles
- `orange.css`: storefront color theme
- `trackorder.css`: customer order-tracking page styles
- `wallet.css`: wallet page styles
- `sort.css`: category/search sorting styles
- `password-validation-message.css`: password validation UI
- `card_payment.css`: payment card UI styles
- `loader.css`: loader styles
- third-party support CSS such as `lightbox.css`, `owl.carousel.css`, `rateit.css`

### `assets/fonts/`
Font files used by storefront CSS.

### `assets/images/`
Storefront images and organized image groups.

Subfolders:
- `banners/`: category and promotional banners
- `brands/`: brand logos used by the storefront brand carousel
- `logo/`: primary storefront logo
- `owl-carousel/`: carousel helper images
- `sliders/`: homepage/hero slider images

### `assets/js/`
Storefront JavaScript bundle.

Examples:
- `scripts.js`: main storefront JS behavior
- `bootstrap.min.js`
- `owl.carousel.min.js`
- `lightbox.min.js`
- `wow.min.js`
- `statecitylist.js`
- validation helpers such as `password-validation-my-acc.js`

### `assets/less/`
LESS source files that mirror much of the storefront styling structure.

### `datepicker164/`
Standalone Bootstrap datepicker package used by parts of the project.

### `includes/`
Shared storefront PHP includes.

Files:
- `customer_session.php`: starts the customer-only named PHP session
- `config.php`: storefront/shared database connection
- `top-header.php`: top account/contact header
- `main-header.php`: main header and cart/search section
- `menu-bar.php`: main navigation bar
- `footer.php`: storefront footer
- `side-menu.php`: category side navigation
- `myaccount-sidebar.php`: customer account sidebar
- `brands-slider.php`: shared brand-carousel partial

### `layouts/`
Layout-related supporting files kept from the project structure. This folder is small and not the main runtime source of templates.

### `sendemail/`
Mailer library/package used by storefront email flows.

### `vendor/`
Composer-managed PHP dependencies. (for auto-load mailer username and password)

### Root `*.php` files
Most customer-facing pages live at the project root.

Examples:
- `index.php`: homepage
- `login.php`: customer login/register
- `my-cart.php`: shopping cart
- `product-details.php`: product detail page
- `track-order.php`: customer order detail/tracking page
- `wallet.php`: wallet page

### `shopping.sql`
Main SQL dump for the application database.

### `.env`
Environment values used by mail-related features.

## Session Separation
Customer and admin logins now use different PHP session names, so both can stay logged in in the same browser:

- customer session bootstrap: `includes/customer_session.php`
- admin session bootstrap: `admin/include/admin_session.php`

## Notes
- The storefront and admin panel intentionally use different asset systems.
- `admin/` still relies on its own older Bootstrap/CSS/script bundle.
- `assets/` is the canonical asset tree for the storefront.
- Product images are runtime content and should be treated carefully during cleanup or deployment.

## Recommended Entry URLs
- Customer site: `http://localhost/Full-Stack-Furniture-E-Commerce-Web-App/index.php`
- Admin site: `http://localhost/Full-Stack-Furniture-E-Commerce-Web-App/admin/index.php`

