# 🎂 Mint Patisserie & Cake Shop

A complete, responsive web application and e-commerce ordering system for an artisan cake shop, handcrafted with **PHP** and an integrated **SQLite database**.

Zero external frameworks or composer installations required. Runs immediately using PHP's built-in web server!

---

## ✨ Features

### 🍰 Customer & Public Storefront
- **Delightful Home Page (`index.php`)**:
  - Hero banner showcasing the signature *Mint Choco-Drip Cake*.
  - Category navigation browser (Shortcakes, Cheesecakes, Chocolate, Fruit Tarts, Specialties).
  - Featured & Chef's Pick cake carousels.
  - Shop story, baking philosophy, opening hours, and location.
- **Cake Catalog & Menu (`menu.php`)**:
  - Filter cakes by category.
  - Search cakes by name or ingredient keyword.
  - Sort by Price (Low to High, High to Low), Popularity, or Name.
  - Direct "Quick Add" to shopping bag.
- **Cake Details Page (`cake.php`)**:
  - High-resolution imagery, slice servings, dimensions, and allergen information.
  - Dynamic quantity selector.
  - Related cakes suggestions.
- **Shopping Cart (`cart.php`)**:
  - Live item count in navbar badge.
  - Adjust item quantities or remove cakes.
  - Real-time price and order total calculation.
- **Checkout & Fulfillment Scheduling (`checkout.php`)**:
  - Customer contact details validation.
  - Choice between **In-Store Pickup** (with date & time scheduler) or **Refrigerated Courier Delivery** (with address).
  - Custom chocolate plaque inscription messages (e.g., *"Happy 21st Birthday Sarah!"*).
- **Order Confirmation Receipt (`order_success.php`)**:
  - Unique Order Reference ID (`#ORD-XXXXX`).
  - Itemized receipt, fulfillment schedule, and printable receipt support.

---

### 🛡️ Bakery Staff & Admin Portal (`admin/`)
- **Secure Authentication (`admin/login.php`)**:
  - Staff login with bcrypt password verification (`password_hash`).
- **Performance & Order Management (`admin/index.php`)**:
  - Live revenue tally, total orders count, and pending orders queue.
  - Filter orders by status: `Pending`, `Processing / Baking`, `Completed`, `Cancelled`.
  - Update order status with one click.
  - View customer contact details, delivery method, and custom cake messages.
- **Cake Inventory Management (`admin/products.php`)**:
  - Add, edit, or delete cakes from the shop menu.
  - Instant toggle for stock availability (`In Stock` / `Sold Out`).
  - Preset photography selector for easy catalog management.

---

## 🚀 How to Run

### 1. Start the PHP Built-in Server
From the project root directory, run:

```bash
php -S localhost:8000
```

### 2. Open in Your Browser
- **Public Cake Shop:** [http://localhost:8000](http://localhost:8000)
- **Staff Admin Portal:** [http://localhost:8000/admin/login.php](http://localhost:8000/admin/login.php)

> **Automatic Database Setup:**
> The SQLite database file (`database/cake_shop.sqlite`) will automatically create all tables and populate 10 delicious cakes across 5 categories on your very first visit!

---

## 🔐 Default Admin Credentials

| Field | Value |
|---|---|
| **Username / Email** | `admin` (or `admin@mintcakes.com`) |
| **Password** | `admin123` |

---

## 📂 Project Structure

```
mint/
├── admin/                     # Staff management portal
│   ├── auth.php               # Authentication middleware
│   ├── footer.php             # Admin layout footer
│   ├── header.php             # Admin layout navbar & session check
│   ├── index.php              # Orders management & sales metrics dashboard
│   ├── login.php              # Admin login page
│   ├── logout.php             # Admin session logout
│   ├── product_form.php       # Add / Edit cake form
│   └── products.php           # Cake inventory & availability toggle
├── database/
│   └── cake_shop.sqlite       # Auto-created SQLite database file
├── includes/
│   ├── db.php                 # PDO connection & automatic schema/seed initialization
│   ├── footer.php             # Customer site footer (hours, address, socials)
│   └── header.php             # Customer site navigation, cart badge & styling
├── cake.php                   # Cake product detail page
├── cart.php                   # Shopping cart page
├── checkout.php               # Checkout & order placement form
├── index.php                  # Customer home page
├── menu.php                   # Cake catalog with search & filters
├── order_success.php          # Order confirmation receipt
├── README.md                  # Project documentation
└── .gitignore
```

---

## 🛠️ Technology Stack

- **Backend:** Pure PHP (8.x compatible)
- **Database:** SQLite with PDO (Prepared statements to prevent SQL injection)
- **Frontend / UI:** Tailwind CSS (via CDN), Google Fonts (*Playfair Display* & *Inter*), Font Awesome Icons
- **Security:** CSRF-safe design, output escaping with `htmlspecialchars()`, password hashing via `password_hash()`