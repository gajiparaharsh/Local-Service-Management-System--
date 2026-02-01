# 🔧 Local-Service-Management-System

A full-stack PHP and MySQL online application for scheduling local services, such as electricians and plumbers. It's called the Local Service Management System. It includes an admin dashboard, booking management, service provider profiles, and user authentication.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

## 📋 Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Installation](#-installation)
- [Default Credentials](#-default-credentials)
- [Service Categories](#-service-categories)
- [Configuration](#-configuration)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 👤 User Features
- ✅ User registration and authentication
- ✅ Browse services by category
- ✅ Search for service providers by location
- ✅ View provider profiles with ratings & reviews
- ✅ Book services with date/time selection
- ✅ Track booking status in real-time
- ✅ Rate and review service providers
- ✅ User dashboard with booking history
- ✅ Profile management

### 🛠️ Service Provider Features
- ✅ Provider registration with approval workflow
- ✅ Business profile management
- ✅ Service listing and pricing management
- ✅ Booking management (Accept/Reject/Complete)
- ✅ Availability schedule configuration
- ✅ View customer reviews and ratings
- ✅ Provider dashboard with analytics
- ✅ Verified badge system

### 👨‍💼 Admin Features
- ✅ Comprehensive admin dashboard with statistics
- ✅ User management (activate/deactivate)
- ✅ Provider approval and management
- ✅ Service category management (CRUD)
- ✅ Services management (CRUD)
- ✅ Booking oversight and monitoring
- ✅ Review moderation

### 🌟 Additional Features
- ✅ Fully responsive design (Mobile-friendly)
- ✅ Modern UI with smooth animations
- ✅ Real-time price estimator calculator
- ✅ Location-based provider search
- ✅ Featured providers section
- ✅ Live reviews ticker
- ✅ Contact form with message management
- ✅ 24/7 Support system

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| **PHP 7.4+** | Backend logic & server-side processing |
| **MySQL 5.7+** | Relational database |
| **HTML5/CSS3** | Frontend structure & styling |
| **JavaScript** | Client-side interactivity |
| **Bootstrap 5** | Responsive UI framework |
| **Font Awesome** | Icon library |
| **AOS Library** | Scroll animations |
| **PDO** | Secure database connectivity |

---

## 📁 Project Structure

```
Local-Service-Management-System/
│
├── 📂 admin/                    # Admin Panel
│   ├── index.php               # Admin dashboard
│   ├── bookings.php            # Manage all bookings
│   ├── categories.php          # Manage service categories
│   ├── providers.php           # Manage service providers
│   ├── services.php            # Manage services
│   └── users.php               # User management
│
├── 📂 assets/                   # Static Assets
│   ├── 📂 css/                 # Stylesheets
│   ├── 📂 js/                  # JavaScript files
│   └── 📂 images/              # Image assets
│
├── 📂 auth/                     # Authentication
│   ├── login.php               # User login
│   ├── register.php            # User/Provider registration
│   └── logout.php              # Session logout
│
├── 📂 classes/                  # PHP Classes
│   └── Database.php            # Database connection singleton
│
├── 📂 config/                   # Configuration Files
│   ├── config.php              # Application settings
│   └── database.php            # Database credentials
│
├── 📂 database/                 # Database Files
│   ├── schema.sql              # Complete database schema
│   └── sample_orders.sql       # Sample data for testing
│
├── 📂 includes/                 # Common Includes
│   ├── header.php              # Global header
│   └── footer.php              # Global footer
│
├── 📂 provider/                 # Provider Panel
│   ├── dashboard.php           # Provider dashboard
│   ├── bookings.php            # Manage bookings
│   ├── profile.php             # Profile settings
│   └── services.php            # Manage offered services
│
├── 📂 user/                     # User Panel
│   ├── dashboard.php           # User dashboard
│   ├── bookings.php            # Booking history
│   └── profile.php             # Profile settings
│
├── 📂 uploads/                  # User Uploads Directory
│
├── 📄 index.php                 # Home page
├── 📄 services.php              # Services listing page
├── 📄 providers.php             # Providers listing page
├── 📄 provider-profile.php      # Individual provider profile
├── 📄 book-service.php          # Service booking page
├── 📄 about.php                 # About us page
├── 📄 contact.php               # Contact page
├── 📄 .htaccess                 # Apache configuration
└── 📄 DEPLOYMENT.md             # Deployment guide
```

---

## 🗄️ Database Schema

The application uses the following database tables:

| Table | Description |
|-------|-------------|
| `users` | Stores all user accounts (customers, providers, admins) |
| `categories` | Service categories (Plumbing, Electrical, etc.) |
| `services` | Individual services under each category |
| `provider_profiles` | Detailed provider business profiles |
| `provider_services` | Services offered by each provider with pricing |
| `provider_availability` | Weekly availability schedule |
| `bookings` | All service bookings with status tracking |
| `reviews` | Customer reviews and ratings |
| `notifications` | In-app notifications |
| `contact_messages` | Contact form submissions |
| `settings` | System-wide configuration |

### Entity Relationship

```
users ──────┬──> provider_profiles ──> provider_services
            │                      └──> provider_availability
            │
            ├──> bookings ──────────────> reviews
            │
            └──> notifications
```

---

## 🚀 Installation

### Prerequisites
- ✅ XAMPP / WAMP / MAMP / LAMP Stack
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher
- ✅ Web browser (Chrome, Firefox, Edge)

### Step-by-Step Setup

#### 1️⃣ Clone the Repository
```bash
git clone https://github.com/gajiparaharsh/Local-Service-Management-System.git
```

#### 2️⃣ Move to Web Server Directory
```bash
# For XAMPP on Windows
copy Local-Service-Management-System C:\xampp\htdocs\localservice

# For XAMPP on Mac/Linux
cp -r Local-Service-Management-System /opt/lampp/htdocs/localservice
```

#### 3️⃣ Start Services
- Open **XAMPP Control Panel**
- Start **Apache** ✅
- Start **MySQL** ✅

#### 4️⃣ Create Database
- Open browser: `http://localhost/phpmyadmin`
- Click **"New"** on the left sidebar
- Database name: `local_service_finder`
- Click **"Create"**

#### 5️⃣ Import Database Schema
- Select `local_service_finder` database
- Click **"Import"** tab
- Choose file: `database/schema.sql`
- Click **"Go"**

*Optional: Import sample data*
- Import: `database/sample_orders.sql`

#### 6️⃣ Configure Database Connection
Edit `config/database.php`:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'local_service_finder');
define('DB_USER', 'root');
define('DB_PASS', '');  // Leave empty for XAMPP default
?>
```

#### 7️⃣ Access the Application
```
http://localhost/localservice/
```

---

## 🔑 Default Credentials

### 👨‍💼 Admin Login
| Field | Value |
|-------|-------|
| **URL** | `http://localhost/localservice/auth/login.php` |
| **Email** | `admin@localservice.com` |
| **Password** | `Admin@123` |

---

## 🏷️ Service Categories

The system includes 8 pre-configured service categories with 30+ services:

| # | Category | Icon | Services Included |
|---|----------|------|-------------------|
| 1 | 🔧 **Plumbing** | fa-wrench | Pipe Repair, Drain Cleaning, Faucet Installation, Water Heater Repair |
| 2 | ⚡ **Electrical** | fa-bolt | Wiring Repair, Switch Installation, Fan Installation, Electrical Inspection |
| 3 | 🔨 **Carpentry** | fa-hammer | Furniture Repair, Door Installation, Cabinet Making, Wood Polishing |
| 4 | ❄️ **AC Repair** | fa-snowflake | AC Installation, AC Servicing, Gas Refill, AC Repair |
| 5 | 🧹 **Cleaning** | fa-broom | Home Deep Cleaning, Kitchen Cleaning, Bathroom Cleaning, Sofa Cleaning |
| 6 | 🚗 **Mechanics** | fa-car | Car Service, Oil Change, Brake Repair, Battery Replacement |
| 7 | 🎨 **Painting** | fa-paint-roller | Interior Painting, Exterior Painting, Texture Painting |
| 8 | 🐛 **Pest Control** | fa-bug | General Pest Control, Termite Control, Cockroach Control |

---

## ⚙️ Configuration

### Application Settings

Edit `config/config.php`:

```php
<?php
// Base URL (change for production)
define('BASE_URL', 'http://localhost/localservice/');

// Environment: 'development' or 'production'
define('ENVIRONMENT', 'development');

// Uploads directory
define('UPLOADS_PATH', __DIR__ . '/../uploads/');
define('UPLOADS_URL', BASE_URL . 'uploads/');
?>
```

### Database Settings

| Setting Key | Default Value | Description |
|-------------|---------------|-------------|
| `site_name` | Local Service Finder | Website name |
| `site_email` | info@localservice.com | Contact email |
| `site_phone` | +91 9876543210 | Contact phone |
| `currency` | ₹ | Currency symbol |
| `booking_advance_days` | 30 | Max days ahead for booking |
| `min_booking_hours` | 2 | Minimum hours before booking |
| `cancellation_hours` | 24 | Hours before cancellation allowed |

---

## 🚀 Deployment

For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md)

### Quick Deploy Options

| Platform | Difficulty | Cost |
|----------|------------|------|
| Shared Hosting (Hostinger, GoDaddy) | Easy | $3-10/month |
| VPS (DigitalOcean, Linode) | Medium | $5-20/month |
| Cloud (AWS, Google Cloud) | Advanced | Pay-as-you-go |

---

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork** the repository
2. **Create** your feature branch
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. **Commit** your changes
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. **Push** to the branch
   ```bash
   git push origin feature/AmazingFeature
   ```
5. **Open** a Pull Request

### Contribution Ideas
- [ ] Add payment gateway integration
- [ ] Implement real-time chat
- [ ] Add SMS notifications
- [ ] Multi-language support
- [ ] Mobile app (React Native/Flutter)

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Harsh Gajipara**

[![GitHub](https://img.shields.io/badge/GitHub-gajiparaharsh-181717?style=for-the-badge&logo=github)](https://github.com/gajiparaharsh)

---

## 🙏 Acknowledgments

- Bootstrap Team for the amazing UI framework
- Font Awesome for beautiful icons
- AOS Library for smooth animations
- All contributors and testers

---

<p align="center">
  <b>⭐ If you find this project helpful, please give it a star! ⭐</b>
</p>
