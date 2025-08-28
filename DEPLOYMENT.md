# Instruksi Deployment Hotel Insight

## File yang Sudah Disiapkan
- `hotelinsight.zip` - File siap upload ke hosting
- `.env` - Konfigurasi database sudah disesuaikan

## Langkah Deployment ke Shared Hosting

### 1. Upload File
1. Login ke cPanel hosting Anda
2. Buka File Manager
3. Upload file `hotelinsight.zip` ke root domain
4. Extract file ZIP tersebut

### 2. Set Document Root
**Opsi A: Set Document Root ke folder public (Direkomendasikan)**
1. Di cPanel, buka "Domains" atau "Domain Manager"
2. Klik "Manage" pada domain Anda
3. Set Document Root ke: `public_html/public`

**Opsi B: Jika tidak bisa ubah Document Root**
1. Pindahkan semua file dari folder `public/*` ke root web (`public_html/`)
2. Edit file `index.php` yang dipindahkan:
   ```php
   // Ubah baris ini:
   require __DIR__.'/../vendor/autoload.php';
   require_once __DIR__.'/../bootstrap/app.php';
   
   // Menjadi:
   require __DIR__.'/vendor/autoload.php';
   require_once __DIR__.'/bootstrap/app.php';
   ```

### 3. Konfigurasi Database
1. Buat database MySQL baru di cPanel
2. Edit file `.env` dan update:
   ```
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_database_anda  
   DB_PASSWORD=password_database_anda
   ```

### 4. Generate Application Key
**Via SSH (jika tersedia):**
```bash
cd /path/to/your/domain
php artisan key:generate
```

**Manual (jika tidak ada SSH):**
1. Generate key di lokal: `php artisan key:generate`
2. Copy APP_KEY dari `.env` lokal
3. Paste ke `.env` di server

### 5. Set Permission
Via File Manager, set permission:
- Folder `storage/` → 755
- Folder `bootstrap/cache/` → 755
- File `.env` → 644

### 6. Jalankan Migrasi
**Via SSH:**
```bash
php artisan migrate --force
php artisan db:seed --force
```

**Via cPanel Terminal (jika tersedia):**
```bash
cd /path/to/your/domain
php artisan migrate --force
php artisan db:seed --force
```

### 7. Test Aplikasi
1. Akses website Anda
2. Pastikan halaman utama tampil
3. Coba tambah hotel baru
4. Cek detail hotel

## Troubleshooting

### Error 500
1. Cek file `storage/logs/laravel.log`
2. Pastikan APP_KEY sudah diisi
3. Pastikan permission folder storage benar
4. Pastikan vendor/ sudah terupload

### Error Database
1. Cek kredensial database di `.env`
2. Pastikan database sudah dibuat
3. Pastikan user database punya akses

### Error Permission
1. Set permission folder storage dan bootstrap/cache ke 755
2. Set permission file .env ke 644

## Fitur Aplikasi

### Halaman Utama
- Daftar hotel dengan rating dan harga terbaru
- Tampilan card yang responsif
- Link ke detail hotel

### Detail Hotel
- Informasi lengkap hotel
- Perbandingan harga dari berbagai OTA
- Daftar review dengan pagination
- Link booking langsung

### Tambah Hotel
- Form input data hotel
- Validasi input
- Redirect ke detail setelah simpan

### Data OTA
- Booking.com, Agoda, Expedia, dll
- Sudah di-seed otomatis

## Struktur Database

### Tabel hotels
- id, name, description, address, city, country
- phone, email, website, star_rating
- latitude, longitude, image_url, is_active

### Tabel ota_sources  
- id, name, slug, website_url, is_active

### Tabel hotel_prices
- hotel_id, ota_source_id, price, currency
- check_in_date, check_out_date, room_type
- booking_url, is_available, last_updated

### Tabel hotel_reviews
- hotel_id, ota_source_id, reviewer_name
- rating, review_text, review_date
- review_url, is_verified

## Support
Jika ada masalah, cek:
1. Error log di `storage/logs/laravel.log`
2. Permission folder dan file
3. Konfigurasi database
4. APP_KEY sudah diisi
