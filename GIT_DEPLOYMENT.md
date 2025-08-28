# Git Deployment untuk Shared Hosting

## 🚀 **Setup Git Repository**

### **1. Inisialisasi Git**
```bash
git init
git add .
git commit -m "Initial commit: Hotel Insight Laravel App"
```

### **2. Setup Remote Repository**
```bash
# GitHub/GitLab
git remote add origin https://github.com/username/hotelinsight.git

# Atau SSH
git remote add origin git@github.com:username/hotelinsight.git
```

### **3. Push ke Repository**
```bash
git branch -M main
git push -u origin main
```

## 🔧 **Setup cPanel Git Version Control**

### **1. Login ke cPanel**
- Buka cPanel hosting
- Cari **"Git Version Control"**

### **2. Setup Repository**
- **Repository URL**: `https://github.com/username/hotelinsight.git`
- **Branch**: `main`
- **Deploy Path**: `/home/dafm5634/public_html/hotelinsight`
- **Auto Deploy**: ✅ Enable

### **3. Deploy Path Structure**
```
/home/dafm5634/public_html/hotelinsight/
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── public/          ← Document Root
│   ├── index.php
│   ├── .htaccess
│   └── favicon.ico
├── .env
├── artisan
└── composer.json
```

## 📋 **Document Root Configuration**

### **1. Set Document Root**
- Buka **"Domains"** di cPanel
- Cari domain `hotelinsight.dafam.cloud`
- Set **Document Root** ke: `/public_html/hotelinsight/public`

### **2. Alternative: .htaccess Redirect**
Jika tidak bisa set Document Root, buat `.htaccess` di root:
```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

## 🔄 **Auto Deployment Workflow**

### **1. Development**
```bash
# Buat perubahan
git add .
git commit -m "Update: deskripsi perubahan"
git push origin main
```

### **2. Auto Deploy**
- cPanel akan otomatis pull dari Git
- File `.cpanel.yml` akan menjalankan deployment tasks
- Aplikasi akan ter-update otomatis

### **3. Deployment Tasks**
- ✅ Copy files ke deployment path
- ✅ Set permission yang benar
- ✅ Clear cache Laravel
- ✅ Run migrations (jika ada)
- ✅ Run seeders (jika ada)

## 🛠 **Manual Deployment Commands**

### **1. SSH ke Server**
```bash
ssh dafm5634@sangihe
cd public_html/hotelinsight
```

### **2. Pull dari Git**
```bash
git pull origin main
```

### **3. Update Dependencies**
```bash
composer install --no-dev --optimize-autoloader
```

### **4. Clear Cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **5. Run Migrations**
```bash
php artisan migrate --force
php artisan db:seed --force
```

## 📁 **File yang Di-deploy**

### **✅ Included:**
- `app/` - Application logic
- `bootstrap/` - Bootstrap files
- `config/` - Configuration files
- `database/` - Migrations & Seeders
- `resources/` - Views & Assets
- `routes/` - Route definitions
- `storage/` - Storage directory
- `public/` - Public assets
- `artisan` - Artisan CLI
- `composer.json` - Dependencies

### **❌ Excluded:**
- `vendor/` - Composer dependencies
- `.env` - Environment variables
- `storage/logs/` - Log files
- `node_modules/` - NPM dependencies

## 🚨 **Troubleshooting**

### **1. Permission Error**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### **2. Database Error**
```bash
php artisan migrate:status
php artisan db:seed --force
```

### **3. Cache Error**
```bash
php artisan config:clear
php artisan cache:clear
```

### **4. Document Root Error**
- Pastikan Document Root set ke folder `public/`
- Atau gunakan `.htaccess` redirect

## 🎯 **Keuntungan Git Deployment**

1. **Version Control** - Track semua perubahan
2. **Auto Deploy** - Update otomatis saat push
3. **Rollback** - Bisa kembali ke version sebelumnya
4. **Collaboration** - Multiple developer bisa kerja sama
5. **Backup** - Repository sebagai backup

## 📞 **Support**

Jika ada masalah:
1. Cek error log: `tail -f storage/logs/laravel.log`
2. Cek Git status: `git status`
3. Cek deployment path: `ls -la /home/dafm5634/public_html/hotelinsight/`
4. Cek Document Root di cPanel
