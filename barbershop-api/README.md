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

Jalankan server development:
```bash
php -S localhost:8000 -t public
```

## Fase Pengembangan

- [x] **FASE 1 - Langkah 1**: Setup Lumen, .env, dan Helper Response
- [x] **FASE 1 - Langkah 2**: UUID Setup & Trait
- [ ] **FASE 1 - Langkah 3**: Migration User
- [ ] **FASE 1 - Langkah 4**: Auth System

## Tech Stack

- **Framework**: Lumen v10
- **Database**: MySQL
- **Primary Key**: UUID (String 36 char)
- **PHP**: ^8.1

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
