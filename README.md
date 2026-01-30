# 💈 Barbershop API

[![Lumen](https://img.shields.io/badge/Lumen-10.0-F05340?style=for-the-badge&logo=laravel&logoColor=white)](https://lumen.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

> RESTful API untuk sistem booking barbershop dengan fitur manajemen layanan, kapster, dan booking dengan deteksi konflik jadwal otomatis.

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Running the Application](#-running-the-application)
- [API Documentation](#-api-documentation)
- [Project Structure](#-project-structure)
- [Business Logic](#-business-logic)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

- 🔐 **Authentication System** - JWT-like token authentication
- 👥 **Role-Based Access Control** - Admin & Customer roles
- 💇 **Barber Management** - CRUD operations with photo upload
- 🛠️ **Service Management** - Manage barbershop services
- 📅 **Smart Booking System** - Automatic schedule conflict detection
- 🔍 **Advanced Filtering** - Search & filter by multiple parameters
- 📷 **File Upload** - Image upload for barber profiles
- 🆔 **UUID Primary Keys** - All tables use UUID instead of auto-increment
- 📊 **Standardized Response** - Consistent JSON response format

## 🛠️ Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **Lumen** | v10.0 | PHP Micro-framework |
| **PHP** | ^8.1 | Programming Language |
| **MySQL** | 8.0+ | Database |
| **Composer** | 2.x | Dependency Manager |
| **UUID** | ramsey/uuid | Unique Identifiers |

## 📦 Installation

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 8.0
- Git

### Step 1: Clone Repository

```bash
git clone https://github.com/adhmubtadi/UAS_Interoperability.git
cd UAS_Interoperability/barbershop-api
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` file:

```env
APP_NAME="Barbershop API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barbershop_db
DB_USERNAME=root
DB_PASSWORD=
```

## 🗄️ Database Setup

### Create Database

```bash
# MySQL CLI
mysql -u root -p
CREATE DATABASE barbershop_db;
exit;
```

### Run Migrations

```bash
php artisan migrate
```

### Seed Database (Optional)

```bash
php artisan db:seed
```

**Seeder includes:**
- 8 Barbershop Services (Gentleman Cut, Premium Cut, Hair Coloring, etc.)
- 6 Barbers with different statuses

## 🚀 Running the Application

### Development Server

```bash
php -S localhost:8000 -t public
```

API will be available at: `http://localhost:8000`

### Verify Installation

```bash
curl http://localhost:8000
```

Expected response:
```json
{
  "sukses": true,
  "pesan": "Barbershop API - Lumen",
  "data": {
    "version": "10.0.0",
    "timestamp": "2026-01-30 12:00:00"
  }
}
```

## 📚 API Documentation

**Complete API documentation is available in:** [`API_DOCUMENTATION.md`](API_DOCUMENTATION.md)

### Quick Overview

| Category | Endpoints | Access Level |
|----------|-----------|--------------|
| **Authentication** | 4 endpoints | Public / Customer |
| **Services** | 5 endpoints | Customer / Admin |
| **Barbers** | 6 endpoints | Customer / Admin |
| **Bookings** | 5 endpoints | Customer / Admin |

### Authentication

All protected endpoints require `Authorization` header:

```bash
Authorization: Bearer {your_api_token}
```

### Quick Start Example

**1. Register:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secret123",
    "role": "customer"
  }'
```

**2. Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "secret123"
  }'
```

**3. Create Booking:**
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "barber_id": "uuid-barber-id",
    "service_id": "uuid-service-id",
    "booking_date": "2026-02-01",
    "booking_time": "10:00"
  }'
```

## 📁 Project Structure

```
barbershop-api/
├── app/
│   ├── Helpers/
│   │   └── ResponseHelper.php      # Standardized JSON responses
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── ServiceController.php
│   │   │       ├── BarberController.php
│   │   │       └── BookingController.php
│   │   └── Middleware/
│   │       ├── Authenticate.php
│   │       └── AdminMiddleware.php
│   ├── Models/
│   │   ├── BaseModel.php           # UUID support
│   │   ├── User.php
│   │   ├── Service.php
│   │   ├── Barber.php
│   │   └── Booking.php
│   └── Traits/
│       └── UsesUuid.php            # Auto-generate UUID
├── database/
│   ├── migrations/                 # Database schema
│   └── seeders/                    # Test data
├── public/
│   └── uploads/barbers/            # Uploaded photos
├── routes/
│   ├── web.php
│   └── api.php                     # API routes
├── .env                            # Environment config
├── API_DOCUMENTATION.md            # Complete API docs
└── README.md
```

## 🧠 Business Logic

### UUID System

All models use UUID as primary keys:

```php
// Automatic UUID generation via UsesUuid trait
$service = Service::create([
    'name' => 'Gentleman Cut',
    'price' => 50000
]);
// ID automatically generated: "550e8400-e29b-41d4-a716-446655440000"
```

### Booking Conflict Detection

System automatically prevents double-booking:

```php
// Checks if requested time overlaps with existing bookings
// Algorithm: (requestedStart < existingEnd) AND (requestedEnd > existingStart)
```

### Available Slots

Get available time slots (09:00-21:00, 30-min intervals):

```bash
GET /api/bookings/available-slots?barber_id={uuid}&service_id={uuid}&booking_date=2026-02-01
```

### File Upload

Barber photos are stored with unique filenames:

```
Pattern: {timestamp}_{uniqid}.{extension}
Path: public/uploads/barbers/
Max Size: 2MB
Formats: JPEG, PNG, JPG
```

## 🧪 Testing

### Test with cURL

```bash
# Test Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@test.com","password":"123456"}'

# Test Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"123456"}'

# Test Get Services (with token)
curl -X GET http://localhost:8000/api/services \
  -H "Authorization: Bearer {your_token}"
```

### Test with Postman

Import endpoints from `API_DOCUMENTATION.md` into Postman collection.

## 🎯 Development Roadmap

- [x] **FASE 1** - Setup & Authentication System
- [x] **FASE 2** - Services & Barbers Management
- [x] **FASE 3** - Booking System with Conflict Detection
- [x] **FASE 4** - Admin Security & File Upload
- [ ] **FASE 5** - Payment Integration (Future)
- [ ] **FASE 6** - Notification System (Future)

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Adhitia Rian Permana & Muhammad Ahyadi Yuduf**
- GitHub: [@adhmubtadi](https://github.com/adhmubtadi)
- GitHub: [@yusuuuff](https://github.com/yusuuuff)
- Repository: [UAS_Interoperability](https://github.com/adhmubtadi/UAS_Interoperability)

## 🙏 Acknowledgments

- [Lumen Framework](https://lumen.laravel.com)
- [Laravel Documentation](https://laravel.com/docs)
- Course: UAS Interoperability 2026

---

⭐ **If you find this project useful, please give it a star!** ⭐
