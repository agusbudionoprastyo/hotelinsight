# Troubleshooting Database Kosong

## 🔍 **Diagnosis Masalah**

### 1. **Cek Status Migrasi**
```bash
php artisan migrate:status
```
Pastikan semua tabel sudah terbuat (hotels, ota_sources, hotel_prices, hotel_reviews)

### 2. **Cek Tabel di Database**
```sql
SHOW TABLES;
USE dafm5634_hotelinsight;
SHOW TABLES;
```

### 3. **Cek Data di Tabel**
```sql
SELECT COUNT(*) FROM ota_sources;
SELECT COUNT(*) FROM hotels;
SELECT COUNT(*) FROM hotel_prices;
SELECT COUNT(*) FROM hotel_reviews;
```

## 🚨 **Penyebab Database Kosong**

### **A. Seeder Belum Dijalankan**
Setelah `migrate`, harus jalankan:
```bash
php artisan db:seed --force
```

### **B. Seeder Error**
Cek error dengan:
```bash
php artisan db:seed --force -v
```

### **C. Permission Folder**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### **D. APP_KEY Kosong**
Generate APP_KEY:
```bash
php artisan key:generate
```

## ✅ **Langkah Lengkap Deployment**

### **Step 1: Upload & Extract**
```bash
# Upload hotelinsight.zip ke hosting
# Extract file
# Set Document Root ke folder public/
```

### **Step 2: Konfigurasi Database**
```bash
# Edit .env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dafm5634_hotelinsight
DB_USERNAME=dafm5634_hotelinsight
DB_PASSWORD=password_anda_disini
```

### **Step 3: Generate Key**
```bash
php artisan key:generate
```

### **Step 4: Jalankan Migrasi**
```bash
php artisan migrate --force
```

### **Step 5: Jalankan Seeder (PENTING!)**
```bash
php artisan db:seed --force
```

### **Step 6: Set Permission**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

## 🧪 **Test Database**

### **Test 1: Cek Tabel**
```bash
php artisan tinker
>>> Schema::hasTable('hotels')
>>> Schema::hasTable('ota_sources')
>>> Schema::hasTable('hotel_prices')
>>> Schema::hasTable('hotel_reviews')
```

### **Test 2: Cek Data**
```bash
php artisan tinker
>>> App\Models\OtaSource::count()
>>> App\Models\Hotel::count()
>>> App\Models\HotelPrice::count()
>>> App\Models\HotelReview::count()
```

### **Test 3: Cek Seeder**
```bash
php artisan db:seed --force -v
```

## 🔧 **Manual Insert Data (Jika Seeder Gagal)**

### **Insert OTA Sources**
```sql
INSERT INTO ota_sources (name, slug, website_url, is_active, created_at, updated_at) VALUES
('Booking.com', 'booking-com', 'https://www.booking.com', 1, NOW(), NOW()),
('Agoda', 'agoda', 'https://www.agoda.com', 1, NOW(), NOW()),
('Expedia', 'expedia', 'https://www.expedia.com', 1, NOW(), NOW());
```

### **Insert Hotel Sample**
```sql
INSERT INTO hotels (name, description, city, country, star_rating, is_active, created_at, updated_at) VALUES
('Grand Hotel Jakarta', 'Hotel bintang 5 di pusat Jakarta', 'Jakarta', 'Indonesia', 5, 1, NOW(), NOW());
```

## 📋 **Checklist Lengkap**

- [ ] File terupload ke hosting
- [ ] Document Root set ke folder public/
- [ ] .env sudah dikonfigurasi dengan benar
- [ ] APP_KEY sudah di-generate
- [ ] Migrasi berhasil (tabel terbuat)
- [ ] Seeder berhasil (data terisi)
- [ ] Permission folder sudah benar
- [ ] Aplikasi bisa diakses

## 🆘 **Jika Masih Bermasalah**

### **Cek Error Log**
```bash
tail -f storage/logs/laravel.log
```

### **Cek Database Connection**
```bash
php artisan tinker
>>> DB::connection()->getPdo()
```

### **Test Query Sederhana**
```bash
php artisan tinker
>>> DB::select('SELECT 1 as test')
```

## 📞 **Support**

Jika masih bermasalah, cek:
1. Error log di `storage/logs/laravel.log`
2. Permission folder storage dan bootstrap/cache
3. Konfigurasi database di .env
4. APP_KEY sudah diisi
5. Seeder berhasil dijalankan
