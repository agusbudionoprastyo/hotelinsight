# Hotel Insight

Platform perbandingan harga dan review hotel dari berbagai OTA (Online Travel Agency).

## Fitur

- Daftar hotel dengan rating dan harga terbaru
- Detail hotel lengkap dengan informasi kontak
- Perbandingan harga dari berbagai OTA
- Sistem review hotel
- Interface responsif dan modern

## Deployment untuk Shared Hosting

### Prasyarat
- PHP ≥ 8.1
- MySQL/MariaDB
- Ekstensi PHP: pdo_mysql, openssl, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo

### Langkah Deployment

1. **Upload File**
   - Upload seluruh folder project ke hosting
   - Pastikan folder `public/` menjadi Document Root

2. **Konfigurasi Database**
   - Buat database MySQL baru
   - Update file `.env` dengan kredensial database:
   ```
   DB_DATABASE=nama_database_anda
   DB_USERNAME=username_database_anda
   DB_PASSWORD=password_database_anda
   ```

3. **Generate Application Key**
   - Jalankan: `php artisan key:generate`
   - Atau generate manual dan update `APP_KEY` di `.env`

4. **Jalankan Migrasi**
   - Jalankan: `php artisan migrate`
   - Jalankan: `php artisan db:seed`

5. **Set Permission**
   - Folder `storage/` dan `bootstrap/cache/` harus writable (755 atau 775)

6. **Konfigurasi .env Produksi**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-anda.com
   ```

### Jika Tidak Bisa Set Document Root ke public/

1. Pindahkan semua file dari `public/*` ke root web (public_html/)
2. Edit `index.php` yang dipindahkan:
   ```php
   require __DIR__.'/vendor/autoload.php';
   require_once __DIR__.'/bootstrap/app.php';
   ```

### Struktur Database

- **hotels**: Informasi hotel
- **ota_sources**: Platform OTA (Booking.com, Agoda, dll)
- **hotel_prices**: Harga hotel per OTA
- **hotel_reviews**: Review hotel

### Penggunaan

1. Akses website
2. Lihat daftar hotel di halaman utama
3. Klik "Tambah Hotel" untuk menambah hotel baru
4. Klik "Lihat Detail" untuk melihat informasi lengkap hotel

## Development

### Install Dependencies
```bash
composer install
npm install
```

### Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### Run Migrations
```bash
php artisan migrate
php artisan db:seed
```

### Start Development Server
```bash
php artisan serve
```

## License

MIT License
