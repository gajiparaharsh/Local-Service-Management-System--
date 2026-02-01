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
git clone [https://github.com/gajiparaharsh/Local-Service-Management-System.git](https://github.com/gajiparaharsh/Local-Service-Management-System.git)
