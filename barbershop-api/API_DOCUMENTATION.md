# 📚 API DOCUMENTATION - BARBERSHOP API

**Base URL:** `http://localhost:8000/api`

**Response Format:** Semua endpoint mengembalikan JSON dengan format standar:
```json
{
  "sukses": true,
  "pesan": "Pesan sukses/error",
  "data": { ... }
}
```

**Authentication:** Gunakan header `Authorization: Bearer {api_token}` untuk endpoint yang memerlukan autentikasi.

---

## 🔐 AUTHENTICATION ENDPOINTS

| Method | Endpoint | Deskripsi | Akses | Body Request |
|--------|----------|-----------|-------|--------------|
| `POST` | `/api/auth/register` | Registrasi user baru | Public | `name` (string, required)<br>`email` (string, required, email format)<br>`password` (string, required, min: 6)<br>`role` (string, optional, values: admin/customer, default: customer) |
| `POST` | `/api/auth/login` | Login user dan dapatkan token | Public | `email` (string, required, email format)<br>`password` (string, required) |
| `POST` | `/api/auth/logout` | Logout dan hapus token | Customer | - |
| `GET` | `/api/auth/profile` | Ambil data profile user yang sedang login | Customer | - |

---

## 🛠️ SERVICES MANAGEMENT ENDPOINTS

| Method | Endpoint | Deskripsi | Akses | Body Request |
|--------|----------|-----------|-------|--------------|
| `GET` | `/api/services` | Ambil daftar semua layanan (support filter) | Customer | **Query Params:**<br>`name` (string, optional) - Filter by name<br>`min_price` (numeric, optional) - Filter harga minimum<br>`max_price` (numeric, optional) - Filter harga maksimum |
| `GET` | `/api/services/{id}` | Ambil detail layanan berdasarkan ID | Customer | - |
| `POST` | `/api/services` | Tambah layanan baru | Admin | `name` (string, required, max: 255)<br>`price` (numeric, required, min: 0)<br>`duration_minutes` (integer, required, min: 1)<br>`description` (string, optional) |
| `PUT` | `/api/services/{id}` | Update data layanan | Admin | `name` (string, optional, max: 255)<br>`price` (numeric, optional, min: 0)<br>`duration_minutes` (integer, optional, min: 1)<br>`description` (string, optional) |
| `DELETE` | `/api/services/{id}` | Hapus layanan | Admin | - |

---

## 💇 BARBERS MANAGEMENT ENDPOINTS

| Method | Endpoint | Deskripsi | Akses | Body Request |
|--------|----------|-----------|-------|--------------|
| `GET` | `/api/barbers` | Ambil daftar semua kapster (support filter) | Customer | **Query Params:**<br>`name` (string, optional) - Filter by name<br>`status` (string, optional) - Filter by status (available/busy/off) |
| `GET` | `/api/barbers/{id}` | Ambil detail kapster berdasarkan ID | Customer | - |
| `POST` | `/api/barbers` | Tambah kapster baru dengan foto | Admin | `name` (string, required, max: 255)<br>`status` (string, optional, values: available/busy/off, default: available)<br>`photo` (file, required, format: jpeg/png/jpg, max: 2MB) |
| `PUT` | `/api/barbers/{id}` | Update data kapster | Admin | `name` (string, optional, max: 255)<br>`status` (string, optional, values: available/busy/off)<br>`photo` (file, optional, format: jpeg/png/jpg, max: 2MB) |
| `PATCH` | `/api/barbers/{id}/status` | Update status kapster saja | Admin | `status` (string, required, values: available/busy/off) |
| `DELETE` | `/api/barbers/{id}` | Hapus kapster | Admin | - |

---

## 📅 BOOKINGS MANAGEMENT ENDPOINTS

| Method | Endpoint | Deskripsi | Akses | Body Request |
|--------|----------|-----------|-------|--------------|
| `GET` | `/api/bookings` | Ambil riwayat booking user yang sedang login | Customer | - |
| `GET` | `/api/bookings/{id}` | Ambil detail booking berdasarkan ID | Customer | - |
| `POST` | `/api/bookings` | Buat booking baru (dengan validasi bentrok jadwal) | Customer | `barber_id` (uuid, required, exists in barbers)<br>`service_id` (uuid, required, exists in services)<br>`booking_date` (date, required, format: YYYY-MM-DD, today or future)<br>`booking_time` (time, required, format: HH:MM) |
| `GET` | `/api/bookings/available-slots` | Ambil slot waktu yang tersedia untuk booking | Customer | **Query Params:**<br>`barber_id` (uuid, required)<br>`service_id` (uuid, required)<br>`booking_date` (date, required, format: YYYY-MM-DD) |
| `PATCH` | `/api/bookings/{id}/status` | Update status booking (untuk admin) | Admin | `status` (string, required, values: pending/confirmed/completed/canceled) |

---

## 📝 NOTES & BUSINESS RULES

### 🔒 Authentication
- Token digenerate otomatis saat **Register** dan **Login**
- Token disimpan di kolom `api_token` di tabel `users`
- Token harus dikirim di header: `Authorization: Bearer {api_token}`
- Token dihapus saat **Logout**

### 👤 User Roles
- **Admin:** Dapat CRUD semua data (services, barbers, bookings)
- **Customer:** Dapat melihat services & barbers, membuat & melihat booking sendiri

### ⏰ Booking Rules
1. Hanya bisa booking kapster dengan status `available`
2. Sistem otomatis cek **bentrok jadwal** sebelum menyimpan booking
3. Booking yang bentrok akan ditolak dengan HTTP 400
4. Jam operasional: 09:00 - 21:00 (dapat dikustomisasi)
5. Slot waktu tersedia dengan interval 30 menit

### 📁 File Upload
- Upload foto kapster menggunakan `multipart/form-data`
- File disimpan di: `public/uploads/barbers/`
- Format diterima: JPEG, PNG, JPG
- Ukuran maksimal: 2MB
- Nama file: `{timestamp}_{uniqid}.{extension}`

### 🔍 Search & Filter
- **Services:** Filter by `name`, `min_price`, `max_price`
- **Barbers:** Filter by `name`, `status`
- Semua filter bersifat **optional** dan dapat dikombinasikan

### 🆔 Primary Key
- Semua tabel menggunakan **UUID** (String 36 char)
- Auto-generate menggunakan trait `UsesUuid`

---

## 🧪 EXAMPLE REQUESTS

### 1. Register
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

### 2. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "secret123"
  }'
```

### 3. Create Booking
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "barber_id": "uuid-barber-id",
    "service_id": "uuid-service-id",
    "booking_date": "2026-02-01",
    "booking_time": "10:00"
  }'
```

### 4. Upload Barber Photo
```bash
curl -X POST http://localhost:8000/api/barbers \
  -H "Authorization: Bearer {admin_api_token}" \
  -F "name=Rudi Haircut" \
  -F "status=available" \
  -F "photo=@/path/to/photo.jpg"
```

### 5. Get Available Slots
```bash
curl -X GET "http://localhost:8000/api/bookings/available-slots?barber_id=uuid-barber&service_id=uuid-service&booking_date=2026-02-01" \
  -H "Authorization: Bearer {your_api_token}"
```

---

## ⚠️ ERROR RESPONSES

| HTTP Code | Deskripsi |
|-----------|-----------|
| `200` | OK - Request berhasil |
| `201` | Created - Resource berhasil dibuat |
| `400` | Bad Request - Input tidak valid atau business rule dilanggar |
| `401` | Unauthorized - Token tidak ada atau tidak valid |
| `403` | Forbidden - User tidak punya akses (bukan admin) |
| `404` | Not Found - Resource tidak ditemukan |
| `422` | Unprocessable Entity - Validasi input gagal |
| `500` | Internal Server Error - Error di server |

**Error Response Format:**
```json
{
  "sukses": false,
  "pesan": "Deskripsi error",
  "data": null
}
```

---

## 📊 DATABASE SCHEMA

### Users
- `id` (UUID, PK)
- `name` (String)
- `email` (String, Unique)
- `password` (String, Hashed)
- `role` (Enum: admin/customer)
- `api_token` (String, Nullable)

### Services
- `id` (UUID, PK)
- `name` (String)
- `price` (Decimal)
- `duration_minutes` (Integer)
- `description` (Text, Nullable)

### Barbers
- `id` (UUID, PK)
- `name` (String)
- `status` (Enum: available/busy/off)
- `photo_url` (String, Nullable)

### Bookings
- `id` (UUID, PK)
- `user_id` (UUID, FK → users)
- `barber_id` (UUID, FK → barbers)
- `service_id` (UUID, FK → services)
- `booking_date` (Date)
- `booking_time` (Time)
- `status` (Enum: pending/confirmed/completed/canceled)

---

**📅 Last Updated:** January 30, 2026  
**🔧 Framework:** Lumen v10.0  
**💾 Database:** MySQL with UUID Primary Keys
