# 🛍️ EasyBuy Marketplace

### A Second-Hand Buying and Selling Platform

EasyBuy is a web-based marketplace that allows users to buy and sell second-hand items within their community. The platform provides a simple and convenient way to discover affordable products, manage listings, and connect with local buyers and sellers.

## 📌 About the Project

EasyBuy was developed to make buying and selling pre-owned items easier, more accessible, and more affordable. Users can explore available products, search for items, browse categories, save favorites, and manage their own listings.

The project is built using PHP and MySQL, with a focus on usability, affordability, and community-based trading.

## ✨ Features

- 🏠 **Home Page:** Browse available second-hand products.
- 🔍 **Product Search:** Find items by searching for their names or keywords.
- 🗂️ **Product Categories:** Browse electronics, furniture, clothing, vehicles, books, home appliances, sports equipment, and other items.
- 👤 **User Authentication:** Register and log in to an account.
- 📦 **My Listings:** View and manage your posted products.
- ❤️ **Favorites:** Save products you are interested in.
- ➕ **Post an Item:** Create listings for second-hand products.
- ✏️ **Edit Listings:** Update product information.
- 💰 **Pricing in Kenyan Shillings:** Display product prices in KES.
- 💳 **Payment Page:** A dedicated page for payment-related functionality.

## 🛠️ Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Local Development Environment:** XAMPP
- **Version Control:** Git and GitHub

## 📂 Project Structure

```text
Easybuy/
├── config.php
├── index.php
├── login.php
├── register.php
├── edit_item.php
├── favorites.php
├── my_listings.php
├── payment.php
└── README.md
```

- `config.php` - Database connection and configuration.
- `index.php` - Main marketplace homepage.
- `login.php` - User login page.
- `register.php` - User registration page.
- `edit_item.php` - Edit an existing product listing.
- `favorites.php` - Manage favorite products.
- `my_listings.php` - View the user's listings.
- `payment.php` - Payment-related page.

## ⚙️ Installation and Setup

Follow these steps to run EasyBuy on your local computer.

### Prerequisites

Install the following software:

- [XAMPP](https://www.apachefriends.org/)
- A web browser
- Git (optional)

### Step 1: Clone the Repository

Open your terminal and run:

```bash
git clone https://github.com/herod-prog/Easybuy.git
```

### Step 2: Move the Project

Copy or move the project folder into your XAMPP `htdocs` directory.

For example:

```text
C:\xampp\htdocs\Easybuy
```

### Step 3: Start XAMPP

Open the XAMPP Control Panel and start:

- Apache
- MySQL

### Step 4: Configure the Database

1. Open [phpMyAdmin](http://localhost/phpmyadmin/).
2. Create a MySQL database for EasyBuy.
3. Import the project's database SQL file if one is available.
4. Update `config.php` with the correct database name, username, password, and host.

**Note:** The exact database name and table structure depend on your project's configuration.

### Step 5: Launch EasyBuy

Open your browser and visit:

```text
http://localhost/Easybuy/
```

The homepage should load if the application and database are configured correctly.

## 🎯 Project Objectives

- Provide an accessible platform for second-hand trading.
- Help users discover affordable products.
- Enable sellers to publish and manage product listings.
- Encourage reuse of goods and reduce unnecessary waste.
- Develop practical skills in web development and database management.

## 🔮 Future Improvements

Potential enhancements include:

- 📱 Responsive design for mobile devices.
- 💬 Buyer-seller messaging.
- ⭐ Product ratings and reviews.
- 🔔 Notifications for listing and transaction updates.
- 🛡️ Improved account security and listing moderation.
- 💳 Secure payment gateway integration.
- 📍 Location-based product discovery.

## 🤝 Contributing

Contributions and suggestions are welcome.

1. Fork the repository.
2. Create a feature branch.
3. Make your changes.
4. Commit your changes.
5. Open a pull request.

## 👨‍💻 Developer

**HEROD**

GitHub: [@herod-prog](https://github.com/herod-prog)

## 📄 License

No license has been specified yet. All rights and permissions remain with the copyright holder unless a license is added to the repository.

---

*EasyBuy Marketplace: Making second-hand shopping easier, more affordable, and more accessible.*
