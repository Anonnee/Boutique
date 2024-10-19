# Boutique Store Project

## Overview
Welcome to the Boutique Store project! This is a fully functional web application where you can explore a range of products, add items to your cart, and finalize your purchases. Built using PHP, Bootstrap, and MySQL, it offers features like user registration, login, product management, and order history to enhance your shopping experience.

### Key Features
- **User Registration & Login**: Create an account and log in to manage your orders.
- **Product Management**: Browse products and perform CRUD operations (Create, Read, Update, Delete).
- **Shopping Cart & Checkout**: Add items to your cart and complete your purchase smoothly.
- **Order History**: Keep track of your past orders.
- **Responsive Design**: A mobile-friendly interface using Bootstrap for seamless navigation.

---

## Project Setup

### Prerequisites
Make sure you have the following installed:
- **PHP**: Version 7.4 or higher.
- **MySQL**: Installed and running.
- **Web Server**: XAMPP, WAMP, MAMP, or any standalone Apache server.

### Database Setup
1. Create a new MySQL database (name it `boutique_store`).
2. Import the provided SQL file (`products.sql`) to set up the necessary tables and sample data:
   mysql -u yourusername -p yourpassword boutique_store < products.sql

### Configuration
1. Open the `config.php` file and update it with your database credentials:
   <?php
   $conn = mysqli_connect("localhost", "yourusername", "yourpassword", "boutique_store");
   ?>

### Running the Project
1. Clone the repository to your local machine:
   git clone https://github.com/Anonnee/Boutique.git
   
2. Move the project folder into your web server's document root (e.g., `htdocs` for XAMPP).
3. Open your browser and navigate to:
    http://localhost/Boutique/index.php

---

## File Structure
Here's a quick overview of the main files in the project:
- **index.php**: The homepage displaying featured products.
- **product.php**: Displays all available products.
- **add_product.php**: A form to add new products.
- **delete_product.php**: Removes a product from the database.
- **fetch_cart.php**: Displays items in the user's shopping cart.
- **checkout.php**: Handles the checkout process.
- **login.php**: User login page.
- **register.php**: User registration page.
- **profile.php**: Manage user profile details.
- **logout.php**: Logs out the user.
- **orders.php**: View order history.
- **header.php** & **footer.php**: Common elements for all pages.
- **config.php**: Database configuration settings.

---

## CRUD Operations
- **Create**: `add_product.php`, `register.php`
- **Read**: `product.php`, `orders.php`, `profile.php`
- **Update**: `profile.php`
- **Delete**: `delete_product.php`

---

## Technologies Used
- **Backend**: PHP
- **Frontend**: HTML, CSS, Bootstrap
- **Database**: MySQL
- **Version Control**: Git

---

## License
This project is open-source and available under the [MIT License](LICENSE). Feel free to use, modify, and share!