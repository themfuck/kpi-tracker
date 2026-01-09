# 🌐 Panduan Port Forwarding - KPI Live Streaming Tracker

## ✅ Status Akses Online

Aplikasi KPI Tracker Anda sudah **ONLINE** dan bisa diakses dari internet!

### 🔗 URL Publik
```
https://2f91865a7cf1.ngrok-free.app/admin
```

### 👤 Login Credentials
```
Email: admin@kpi.com
Password: password
```

⚠️ **PENTING:** Ganti password default setelah login pertama!

---

## 🚀 Cara Kerja

Aplikasi menggunakan **ngrok** untuk membuat tunnel dari localhost ke internet:

```
Internet → ngrok (https://2f91865a7cf1.ngrok-free.app)
           ↓
       localhost:8000 (Laravel Server)
```

### Komponen yang Berjalan:
1. **Laravel Server** - `php artisan serve` di port 8000
2. **ngrok Tunnel** - Expose port 8000 ke internet dengan HTTPS

---

## 🔧 Konfigurasi yang Sudah Diterapkan

### 1. Environment Variables (.env)
```env
APP_URL=https://2f91865a7cf1.ngrok-free.app
ASSET_URL=https://2f91865a7cf1.ngrok-free.app
```

### 2. Force HTTPS (AppServiceProvider.php)
```php
public function boot(): void
{
    // Force HTTPS for URLs when using ngrok
    if (str_starts_with(config('app.url'), 'https://')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
```

Ini memastikan semua asset (CSS, JS, images) di-load via HTTPS untuk menghindari **Mixed Content errors**.

---

## 📊 Status Saat Ini

✅ **Server Laravel:** Running di `http://0.0.0.0:8000`
✅ **ngrok Tunnel:** Running dan aktif
✅ **HTTPS:** Enabled dan berfungsi
✅ **Mixed Content:** Fixed (semua asset via HTTPS)
✅ **Login:** Berfungsi normal
✅ **Dashboard:** Accessible

---

## 🛠️ Cara Mengelola Tunnel

### Melihat Status ngrok
```bash
# Cek apakah ngrok masih running
ps aux | grep ngrok

# Lihat log ngrok
# (sudah berjalan di terminal)
```

### Restart ngrok (jika diperlukan)
```bash
# Stop ngrok
pkill ngrok

# Start ulang
ngrok http 8000 --log=stdout
```

### Mendapatkan URL Baru
Setiap kali restart ngrok, URL akan berubah. Untuk mendapatkan URL tetap:

**Option 1: ngrok Free Account**
1. Daftar di https://ngrok.com
2. Dapatkan authtoken
3. Set authtoken: `ngrok config add-authtoken YOUR_TOKEN`
4. Gunakan domain statis (fitur berbayar)

**Option 2: Gunakan URL yang sama**
- URL saat ini: `https://2f91865a7cf1.ngrok-free.app`
- Akan tetap sama selama ngrok tidak di-restart

---

## 🔒 Keamanan

### ⚠️ Peringatan Penting

1. **Password Default**
   - ❌ JANGAN gunakan password default di production
   - ✅ Ganti password admin segera

2. **Data Sensitif**
   - ❌ Jangan share URL ke publik
   - ✅ Hanya share ke orang yang dipercaya

3. **Session Timeout**
   - Aplikasi akan auto-logout setelah idle
   - Login ulang jika diperlukan

4. **Database**
   - Database SQLite ada di `database/database.sqlite`
   - Backup secara berkala

### 🛡️ Best Practices

1. **Batasi Akses**
   ```bash
   # Tambahkan basic auth (opsional)
   # Edit .env
   NGROK_AUTH_USER=admin
   NGROK_AUTH_PASS=secretpassword
   ```

2. **Monitor Akses**
   - Cek log Laravel: `storage/logs/laravel.log`
   - Cek ngrok dashboard: http://localhost:4040

3. **Backup Data**
   ```bash
   # Backup database
   cp database/database.sqlite database/backup-$(date +%Y%m%d).sqlite
   ```

---

## 📱 Akses dari Device Lain

### Desktop/Laptop
Buka browser dan akses:
```
https://2f91865a7cf1.ngrok-free.app/admin
```

### Mobile (Android/iOS)
1. Buka browser (Chrome/Safari)
2. Ketik URL: `https://2f91865a7cf1.ngrok-free.app/admin`
3. Login dengan credentials yang sama

### Tablet
Sama seperti mobile, buka di browser.

---

## 🐛 Troubleshooting

### 1. "This site can't be reached"
**Penyebab:** ngrok tunnel mati atau server Laravel mati

**Solusi:**
```bash
# Cek Laravel server
ps aux | grep "php artisan serve"

# Cek ngrok
ps aux | grep ngrok

# Restart jika perlu
php artisan serve --host=0.0.0.0 --port=8000 &
ngrok http 8000 --log=stdout &
```

### 2. "Mixed Content" Errors
**Penyebab:** Asset di-load via HTTP bukan HTTPS

**Solusi:**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Pastikan .env sudah benar
grep APP_URL .env
grep ASSET_URL .env
```

### 3. "Too Many Requests" (ngrok)
**Penyebab:** Free tier ngrok ada limit

**Solusi:**
- Tunggu beberapa menit
- Atau upgrade ke ngrok paid plan

### 4. URL Berubah Setelah Restart
**Penyebab:** ngrok free tier memberikan random URL

**Solusi:**
```bash
# Update .env dengan URL baru
sed -i 's|APP_URL=.*|APP_URL=https://NEW_URL.ngrok-free.app|' .env
sed -i 's|ASSET_URL=.*|ASSET_URL=https://NEW_URL.ngrok-free.app|' .env

# Clear cache
php artisan config:clear
```

### 5. Login Tidak Berfungsi
**Penyebab:** Session issue atau CSRF token

**Solusi:**
```bash
# Clear session
php artisan session:flush

# Clear cache
php artisan cache:clear

# Refresh browser (Ctrl+F5)
```

---

## 🔄 Alternatif Port Forwarding

Jika ngrok tidak cocok, ada alternatif lain:

### 1. **localtunnel**
```bash
# Install
npm install -g localtunnel

# Run
lt --port 8000
```

### 2. **serveo**
```bash
ssh -R 80:localhost:8000 serveo.net
```

### 3. **Cloudflare Tunnel**
```bash
# Install cloudflared
# Run
cloudflared tunnel --url http://localhost:8000
```

---

## 📊 Monitoring

### ngrok Web Interface
Akses dashboard ngrok di:
```
http://localhost:4040
```

Dashboard menampilkan:
- Request history
- Response times
- Traffic statistics
- Replay requests

### Laravel Logs
```bash
# Tail logs
tail -f storage/logs/laravel.log

# Cek error
grep ERROR storage/logs/laravel.log
```

---

## 🎯 Untuk Production

Jika ingin deploy permanent (bukan sementara):

### Opsi 1: Shared Hosting
- Upload ke cPanel/DirectAdmin
- Set document root ke `/public`
- Import database

### Opsi 2: VPS (Recommended)
- DigitalOcean, Vultr, Linode
- Setup Nginx + PHP-FPM
- SSL dengan Let's Encrypt
- Domain sendiri

### Opsi 3: Cloud Platform
- Laravel Forge
- Ploi.io
- Cloudways

---

## 📝 Catatan Penting

1. **Tunnel Temporary**
   - ngrok tunnel ini bersifat sementara
   - Akan mati jika komputer dimatikan
   - URL akan berubah jika ngrok di-restart

2. **Performance**
   - Latency lebih tinggi dibanding hosting langsung
   - Cocok untuk demo/testing, bukan production

3. **Bandwidth**
   - ngrok free tier ada limit bandwidth
   - Untuk traffic tinggi, gunakan paid plan

4. **Uptime**
   - Tergantung komputer lokal tetap nyala
   - Tidak ada SLA/guarantee uptime

---

## ✅ Checklist Sebelum Share URL

- [ ] Password admin sudah diganti
- [ ] Data dummy sudah dihapus (jika ada)
- [ ] Database sudah di-backup
- [ ] URL sudah ditest dari device lain
- [ ] Login berfungsi normal
- [ ] Dashboard tampil dengan benar
- [ ] Export Excel berfungsi
- [ ] Upload foto berfungsi

---

## 🆘 Support

Jika ada masalah:
1. Cek log Laravel: `storage/logs/laravel.log`
2. Cek ngrok dashboard: `http://localhost:4040`
3. Restart services jika perlu
4. Clear cache Laravel

---

**Aplikasi Anda sekarang ONLINE dan bisa diakses dari mana saja! 🚀**

**URL:** https://2f91865a7cf1.ngrok-free.app/admin
