# 🛒 Simple Shop Billing System (POS)

A lightweight, easy-to-use **Point of Sale (POS)** system built specifically for small shops, grocery stores, and cafes. This system allows cashiers to quickly add items, calculate totals, apply discounts, and generate printable invoices.

![PHP](https://img.shields.io/badge/PHP-8.1-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![Status](https://img.shields.io/badge/Status-Working-brightgreen)

---

## ✨ Features

- 🔐 **Secure Login System** (Password hashed with bcrypt)
- 🛍️ **Product Search** (Smart `datalist` autocomplete – no heavy libraries!)
- 🛒 **Dynamic Cart Management** (Add, Remove, Clear items instantly via AJAX)
- 💰 **Smart Payment Summary**
  - Auto-calculates Total, Discount (%), Net Amount, Paid, and Change
  - Real-time updates as you type
- 🧾 **Invoice Generation**
  - Saves bills to MySQL database with unique Invoice Numbers (`INV-YYYYMMDD-XXXX`)
  - Stores customer details and itemized lists
- 🖨️ **Print Ready** (Clean print CSS to hide buttons and forms)
- 📜 **Bill History** (View all past invoices)
- 👤 **Customer Management** (Supports walk-in customers)

---

## 🏗️ Technologies Used

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript (Vanilla JS with Fetch API)
- **Backend:** PHP 8.1 (Session-based cart management)
- **Database:** MySQL (via phpMyAdmin)
- **Server:** Apache (XAMPP)

---

## 🚀 Installation & Setup Guide

Follow these steps to run the project on your local machine.

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (with Apache & MySQL)
- Web Browser (Chrome/Firefox)

### Step 1: Clone or Download
Clone this repository to your XAMPP `htdocs` folder:
```bash
(https://github.com/dilshankumara001001/simple-billing-system)
