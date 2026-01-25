# Barbershop API - Lumen

API untuk sistem booking barbershop menggunakan Lumen Framework.

## Setup

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Setup Environment**
   - Copy `.env.example` ke `.env` (sudah dilakukan)
   - Sesuaikan konfigurasi database di `.env`:
     ```
     DB_DATABASE=barbershop_db
     DB_USERNAME=root
     DB_PASSWORD=
     ```

3. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

4. **Buat Database**
   - Buat database MySQL dengan nama `barbershop_db`

## Struktur Proyek

### Helper Functions
- **ResponseHelper**: Helper untuk standar JSON response
  - `ResponseHelper::success($data, $pesan, $statusCode)` - Response sukses
  - `ResponseHelper::error($pesan, $data, $statusCode)` - Response error

### UUID System
- **UsesUuid Trait**: Trait untuk auto-generate UUID pada model
- **BaseModel**: Base class untuk semua model yang otomatis menggunakan UUID
  - Primary key: String (36 char UUID)
  - Non-incrementing
  - Auto-generate saat create

#### Cara Menggunakan UUID di Model:
```php
use App\Models\BaseModel;

class YourModel extends BaseModel
{
    protected $fillable = ['name', 'description'];
}
```

### Format Response Standar
```json
{
  "sukses": true,
  "pesan": "Operasi berhasil",
  "data": { ... }
}
```

## Development

### 1. Setup Database
Buat database terlebih dahulu:
```bash
# Lewat MySQL CLI atau phpMyAdmin
CREATE DATABASE barbershop_db;
```

### 2. Jalankan Migration
```bash
php artisan migrate
```

### 3. Jalankan Server Development
```bash
php -S localhost:8000 -t public
```

## API Endpoints

### Auth Endpoints (Public)
- `POST /api/auth/register` - Register user baru
- `POST /api/auth/login` - Login user

### Auth Endpoints (Protected - Butuh Token)
- `POST /api/auth/logout` - Logout user
- `GET /api/auth/profile` - Get user profile

### Contoh Request:
**Register:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"123456","role":"customer"}'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"123456"}'
```

**Profile (dengan token):**
```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer {your-token-here}"
```

## Fase Pengembangan

- [x] **FASE 1 - Langkah 1**: Setup Lumen, .env, dan Helper Response
- [x] **FASE 1 - Langkah 2**: UUID Setup & Trait
- [x] **FASE 1 - Langkah 3**: Migration User
- [x] **FASE 1 - Langkah 4**: Auth System (Register, Login, Logout, Profile)

## Tech Stack

- **Framework**: Lumen v10
- **Database**: MySQL
- **Primary Key**: UUID (String 36 char)
- **PHP**: ^8.1

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
