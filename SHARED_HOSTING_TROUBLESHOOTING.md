# Shared Hosting Troubleshooting Guide - Hotel Insight

## 🚨 **Error 500 - Solusi untuk Shared Hosting**

### **1. Masalah yang Sudah Diperbaiki**

✅ **Type Hints**: Dihapus semua `string`, `array`, `?float` yang tidak support di PHP lama
✅ **Return Types**: Dihapus semua `: array`, `: void` yang tidak support di PHP lama
✅ **Null Coalescing**: Ganti `??` dengan `isset() ? : ` yang kompatibel
✅ **Facade Checks**: Tambah `class_exists()` untuk Log dan Cache
✅ **String Functions**: Ganti `str_contains()` dengan `strpos() !== false`

### **2. File yang Sudah Diperbaiki**

- `app/Services/SerpApiService.php` ✅
- `app/Services/HotelDataAggregatorService.php` ✅
- `app/Services/TravelokaApiService.php` ✅
- `app/Http/Controllers/HotelController.php` ✅
- `public/.htaccess` ✅

### **3. Langkah Deployment di Shared Hosting**

#### **Step 1: Upload File**
```bash
# Upload semua file ke hosting
# Pastikan struktur folder tetap sama
```

#### **Step 2: Set Document Root**
- Di cPanel, set Document Root ke folder `public/`
- Atau jika tidak bisa, pindahkan semua file dari `public/*` ke root

#### **Step 3: Set Permission**
```bash
# Set permission folder
storage/ → 755
bootstrap/cache/ → 755
public/ → 755
```

#### **Step 4: Buat .env File**
```env
APP_NAME="Hotel Insight"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SERPAPI_KEY=4e8aa76b5aed65e0d0e558589264c1becb21e374464353cf70663289c19935b5
```

### **4. Generate Application Key**

Jika tidak bisa akses terminal, generate manual:

```php
// Buat file generate_key.php di root
<?php
echo 'base64:' . base64_encode(random_bytes(32));
```

### **5. Jalankan Migration**

#### **Option A: Via cPanel Terminal**
```bash
cd /home/username/public_html
php artisan migrate:fresh --seed
```

#### **Option B: Via phpMyAdmin**
1. Import file `database/sample_data.sql`
2. Atau jalankan migration manual

### **6. Troubleshooting Error 500**

#### **Check Error Log**
- cPanel → Error Log
- File `error_log` di root
- File `storage/logs/laravel.log`

#### **Common Issues & Solutions**

**Issue 1: Class Not Found**
```
Fatal error: Class 'App\Services\SerpApiService' not found
```
**Solution**: Pastikan semua file service ada di folder `app/Services/`

**Issue 2: Database Connection**
```
SQLSTATE[HY000] [1045] Access denied for user
```
**Solution**: Periksa kredensial database di `.env`

**Issue 3: Permission Denied**
```
Permission denied: storage/framework/cache
```
**Solution**: Set permission folder storage ke 755

**Issue 4: Memory Limit**
```
Fatal error: Allowed memory size exhausted
```
**Solution**: Tambah di `.htaccess`:
```apache
php_value memory_limit 256M
```

### **7. Test Implementation**

#### **Test Basic Route**
```
https://yourdomain.com/
https://yourdomain.com/hotels
```

#### **Test OTA Integration**
```
https://yourdomain.com/hotels/1
```

### **8. Alternative Setup untuk Shared Hosting**

Jika masih error, gunakan setup sederhana:

#### **File Structure Sederhana**
```
public_html/
├── index.php (Laravel public/index.php)
├── .htaccess
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
└── vendor/
```

#### **Modified index.php**
```php
<?php
define('LARAVEL_START', microtime(true));

// Check maintenance mode
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require __DIR__.'/vendor/autoload.php';

// Bootstrap
$app = require_once __DIR__.'/bootstrap/app.php';

// Handle request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

### **9. Performance Optimization**

#### **Enable Caching**
```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'file'),
```

#### **Database Optimization**
```sql
-- Add indexes
ALTER TABLE hotels ADD INDEX idx_location (location);
ALTER TABLE hotel_prices ADD INDEX idx_hotel_dates (hotel_id, check_in_date, check_out_date);
```

### **10. Monitoring & Debugging**

#### **Enable Debug Mode (Sementara)**
```env
APP_DEBUG=true
```

#### **Check System Requirements**
```php
// Buat file check.php
<?php
echo "PHP Version: " . phpversion() . "\n";
echo "Extensions: " . implode(', ', get_loaded_extensions()) . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";
```

### **11. Support & Backup**

#### **Backup Strategy**
- Backup database setiap hari
- Backup file setiap minggu
- Test restore procedure

#### **Rollback Plan**
- Simpan versi sebelumnya
- Document semua perubahan
- Test di environment staging

## 🎯 **Expected Result**

Setelah semua perbaikan, sistem seharusnya:
- ✅ Tidak ada error 500
- ✅ Bisa akses halaman utama
- ✅ Bisa fetch data dari SerpAPI
- ✅ Bisa aggregate data dari multiple OTAs
- ✅ Bisa display harga dan review comparison

## 🆘 **Jika Masih Error**

1. **Check error log** di cPanel
2. **Enable debug mode** sementara
3. **Test step by step** dari basic route
4. **Contact hosting support** untuk PHP version
5. **Use alternative setup** jika diperlukan

## 📞 **Support**

Untuk bantuan lebih lanjut:
- Check error log hosting
- Test dengan file sederhana dulu
- Pastikan semua dependency terinstall
- Verify database connection
