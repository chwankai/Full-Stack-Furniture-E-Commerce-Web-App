# 🛋️ Full-Stack Furniture E-Commerce Web Application

## 📌 Overview
This project is a **full-stack web application** designed for an online furniture store. It allows users to browse, search, and purchase furniture products, while administrators can manage products and orders through a backend system.

---

## 🚀 Features
### 👤 User Features
* User registration and login
* Browse furniture products
* Search and filter items
* Add to cart
* Place orders
### 🛠️ Admin Features
* Add, update, and delete products
* Manage inventory
* View and manage customer orders

---

## 🧠 Tech Stack
### Frontend
* HTML
* CSS
* JavaScript
### Backend
* PHP (Core PHP)
### Database
* MySQL

---

## ⚙️ Installation & Setup
### 1. Clone the repository
```bash
git clone https://github.com/chwankai/Full-Stack-Furniture-E-Commerce-Web-App.git
cd furniture-ecommerce
```
### 2. Setup Local Server
You can use XAMPP / WAMP / MAMP:
* Place project inside `htdocs` (XAMPP)
* Start Apache & MySQL

### 3. Database Setup
* Open phpMyAdmin
* Create database: `furniture_store`
* Import SQL file from `/database/furniture_store.sql`

### 4. Configure Database Connection
Edit `backend/config.php`:
```php
<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "furniture_store";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```
### 5. Run the Application
* Open browser
* Visit: `http://localhost/furniture-ecommerce/index.php/` for customer site
* Visit: `http://localhost/furniture-ecommerce/admin/index.php/` for admin management site

---

## 🗄️ Database
* Stores user data, products, and orders
* Supports CRUD operations
* Relational structure for efficient querying

---

## 📊 Key Functionalities
* Full CRUD operations for products
* Secure authentication system (PHP sessions)
* Shopping cart and checkout workflow
* Responsive UI using HTML, CSS, JavaScript

---

## 🔍 Future Improvements
* Payment gateway integration (e.g. Stripe / FPX)
* Product recommendation system
* Wishlist feature
* User reviews and ratings

---

## 📚 Conclusion
This project demonstrates a complete end-to-end e-commerce system using **PHP, MySQL, HTML, CSS, and JavaScript**, combining frontend, backend, and database technologies to deliver a functional online furniture store.
