# Panduan Instalasi Project Laravel KPU-SMP

## 📋 Ringkasan Project
- **Framework**: Laravel 10.x
- **PHP Requirement**: ^8.0 (Current: PHP 8.5.8 ✅)
- **Database**: MySQL (default config)
- **Frontend**: Vite + vanilla JS/CSS
- **Dependencies**: Composer + NPM

---

## ✅ Prasyarat Sistem (Sudah Terpenuhi)
| Tool | Version Dibutuhkan | Version Terinstall | Status |
|------|-------------------|-------------------|--------|
| PHP | ^8.0 | 8.5.8 | ✅ |
| Composer | 2.x | 2.10.2 | ✅ |
| Node.js | 18+ | v26.10.0 | ✅ |
| NPM | 9+ | 11.19.1 | ✅ |
| MySQL/MariaDB | 5.7+/8.0+ | Perlu dicek | ⚠️ |

---

## 🚀 Langkah-Langkah Instalasi

### 1. Clone & Masuk ke Directory
```bash
cd /Users/andri.dev.code/Coding/laravel/kpu-smp
```

### 2. Install Dependensi PHP (Composer)
```bash
composer install
```
> **Catatan**: Akan otomatis generate `APP_KEY` dan copy `.env.example` ke `.env` (sudah ada tapi key kosong)

### 3. Generate Application Key (jika belum)
```bash
php artisan key:generate
```

### 4. Setup Database MySQL
Buat database baru di MySQL:
```sql
CREATE DATABASE kpu_smp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Konfigurasi `.env` Database
Edit file `.env` dan sesuaikan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kpu_smp       # Ganti dari 'laravel' ke 'kpu_smp'
DB_USERNAME=root          # Sesuaikan dengan user MySQL Anda
DB_PASSWORD=              # Isi password MySQL jika ada
```

### 6. Jalankan Migrasi Database
```bash
php artisan migrate
```

### 7. (Opsional) Jalankan Seeder untuk Data Awal
```bash
# Seed admin user (cek AdminUserSeeder.php dulu untuk kredensial)
php artisan db:seed --class=AdminUserSeeder

# Atau semua seeder
php artisan db:seed
```

### 8. Install Dependensi Frontend (NPM)
```bash
npm install
```

### 9. Build Asset Frontend
```bash
# Development (dengan hot reload)
npm run dev

# Atau production build
npm run build
```

### 10. Jalankan Server Development
```bash
php artisan serve
```
Akses di: **http://localhost:8000**

---

## 🔧 Troubleshooting Umum

### Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Error: "Access denied for user 'root'@'localhost'"
- Periksa kredensial MySQL di `.env`
- Pastikan user `root` memiliki akses ke database `kpu_smp`
- Atau buat user baru: `CREATE USER 'kpu_user'@'localhost' IDENTIFIED BY 'password'; GRANT ALL ON kpu_smp.* TO 'kpu_user'@'localhost';`

### Error: "Class 'Maaatwebsite\Excel\ExcelServiceProvider' not found"
```bash
composer install --optimize-autoloader
```

### Error: Vite manifest not found
```bash
npm run build
# Atau untuk development
npm run dev
```

### Permission Error (Storage/Log)
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

---

## 📁 Struktur Project Penting

```
kpu-smp/
├── app/
│   ├── Http/Controllers/     # Controller (Admin, Auth, Kandidat, Votes, KPU)
│   ├── Models/               # Model (AdminUser, Kandidat, User, Votes, Exsel)
│   └── Providers/            # Service Providers
├── database/
│   ├── migrations/           # 7 migration files
│   └── seeders/              # AdminUserSeeder, DatabaseSeeder
├── public/                   # Asset publik (CSS, JS, images, fonts)
├── resources/                # View, JS, CSS source
├── routes/
│   ├── web.php               # Route utama
│   └── api.php               # Route API (jika ada)
└── config/                   # Konfigurasi Laravel
```

---

## 🎯 Verifikasi Instalasi Berhasil

1. **Halaman utama** terbuka di http://localhost:8000
2. **Database** terbuat tabel: `admin_users`, `kandidat`, `users`, `votes`, `personal_access_tokens`, `password_reset_tokens`, `failed_jobs`
3. **Login admin** berfungsi (cek `AdminUserSeeder` untuk kredensial default)
4. **Asset CSS/JS** termuat dengan benar (cek Network tab browser)

---

## 📝 Catatan Khusus Versi Lama

Project ini menggunakan:
- **Laravel 10** (bukan Laravel 11 terbaru)
- **Vite 4.x** (bukan Vite 5/6)
- **PHP 8.0+** requirement (kompatibel dengan PHP 8.5.8)
- **Manual asset management** di `public/assets/vendor/` (FontAwesome, Chart.js, jQuery, dll)

Jika ada error compatibilitas PHP 8.5, coba:
```bash
composer update --with-all-dependencies
```

---

## 🆘 Butuh Bantuan?

Jika mengalami error yang tidak terdaftar di atas, jalankan:
```bash
php artisan optimize:clear
composer dump-autoload
npm run build
```

Lalu cek log di: `storage/logs/laravel.log`