# 🚗 Dhyecast Backend

Backend RESTful API dan Admin Backpage untuk toko online Diecast, dibangun menggunakan **Laravel 11**. Proyek ini mengintegrasikan standar praktikum **BAB III REST API** (Laravel Sanctum) dan **Modul Backpage Laravel** (Blade + Tailwind CSS).

---

## 🌟 Fitur Utama

- **RESTful API Standar**: Endpoint seragam menggunakan API Resource (`ProductResource`, `CategoryResource`, `OrderResource`).
- **Autentikasi Token (Sanctum)**: Register, Login dengan Bearer Token, Profile, dan Logout.
- **Otorisasi Role**: Proteksi route untuk hak akses `admin` dan `customer`.
- **Manajemen File & Upload**: Upload gambar produk tersimpan di storage publik (`storage:link`).
- **Transaksi & Checkout Aman**: Penguncian stok dengan `DB::transaction()` dan pelacakan waktu transaksi (`created_at`).
- **Admin Backoffice (Blade)**:
  - Dashboard ringkasan (Total Revenue, Total Orders, Product Catalog).
  - CRUD Produk lengkap dengan upload gambar dan dropdown kategori.
  - Pencarian nama/brand (`?s=`) dan filter kategori dengan pagination tetap aktif (`withQueryString()`).
  - Pembaruan status pesanan (*pending, paid, shipped, completed, cancelled*).
- **Postman Ready**: Koleksi request lengkap di folder `postman/`.

---

## 🚀 Panduan Menjalankan Proyek

### 1. Masuk ke Folder Backend & Install Dependensi
```bash
cd dhyecast-backend
composer install
```

### 2. Setup File Environment
Salin `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi koneksi database di `.env` (misal MySQL Laragon/XAMPP):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_dhyecast
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate App Key
```bash
php artisan key:generate
```

### 4. Migrasi & Seeder Database
Jalankan migrasi tabel dan isi data awal kategori, produk, order, dan akun pengguna:
```bash
php artisan migrate --seed
```

### 5. Buat Symbolic Link Storage (Untuk Gambar Produk)
```bash
php artisan storage:link
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Server berjalan di: `http://127.0.0.1:8000`

---

## 👤 Akun Demo (Hasil Seeder)

| Role | Email | Password |
|---|---|---|
| **Administrator** | `admin@dhyecast.test` | `admin12345` |
| **Customer** | `customer@dhyecast.test` | `customer12345` |

---

## 📌 Ringkasan Endpoint API Utama

| Method | Endpoint | Keterangan | Akses |
|---|---|---|---|
| `POST` | `/api/register` | Pendaftaran akun customer | Publik |
| `POST` | `/api/login` | Login & dapatkan Bearer Token | Publik |
| `GET` | `/api/profile` | Ambil data user yang sedang login | Sanctum Auth |
| `POST` | `/api/logout` | Revoke token aktif | Sanctum Auth |
| `GET` | `/api/products` | Katalog produk (search, filter, sort, paging) | Publik |
| `GET` | `/api/products/{id}` | Detail produk | Publik |
| `GET` | `/api/categories` | Daftar semua kategori | Publik |
| `POST` | `/api/orders` | Checkout pesanan (guest / user) | Publik |
| `POST` | `/api/products` | Tambah produk baru (upload gambar) | Admin Only |
| `PUT` | `/api/products/{id}` | Update data produk | Admin Only |
| `DELETE` | `/api/products/{id}` | Hapus produk | Admin Only |
| `GET` | `/api/admin/stats` | Ringkasan statistik toko | Admin Only |
| `PATCH` | `/api/admin/orders/{id}/status` | Ubah status order | Admin Only |

---

## 🌐 Rute Halaman Web (Blade)

- **Portal Integrasi**: `http://127.0.0.1:8000/portal`
- **Login Admin**: `http://127.0.0.1:8000/login`
- **Dashboard Backoffice**: `http://127.0.0.1:8000/admin/dashboard`
- **Katalog Produk Admin**: `http://127.0.0.1:8000/admin/products`

---

## 📬 Koleksi Postman

File koleksi Postman berada di folder `postman/`:
- `Dhyecast_API.postman_collection.json` (Semua request API)
- `Dhyecast_Local.postman_environment.json` (Variabel environment `baseUrl` & `token`)

Tinggal import kedua file tersebut ke aplikasi **Postman** untuk pengujian langsung.
