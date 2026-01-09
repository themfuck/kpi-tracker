# 🆓 Panduan Hosting Gratis - KPI Live Streaming Tracker

## 🎯 Rekomendasi Platform Hosting Gratis

Untuk project Laravel + Filament + SQLite ini, berikut opsi hosting gratis terbaik:

---

## 🥇 **1. Railway.app (RECOMMENDED)**

### ✅ Kelebihan:
- ✅ **Support Laravel** out of the box
- ✅ **SQLite support** (persistent storage)
- ✅ **Free tier:** $5 credit/bulan (cukup untuk project kecil)
- ✅ **Auto deploy** dari GitHub
- ✅ **Custom domain** gratis
- ✅ **HTTPS** otomatis
- ✅ **Environment variables** mudah
- ✅ **Build time** cepat

### 📋 Cara Deploy:

#### **Step 1: Persiapan Project**

1. **Buat `.railwayignore`:**
```bash
cat > .railwayignore << 'EOF'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
EOF
```

2. **Buat `Procfile`:**
```bash
cat > Procfile << 'EOF'
web: php artisan serve --host=0.0.0.0 --port=$PORT
EOF
```

3. **Update `composer.json` (tambahkan post-install script):**
```json
"scripts": {
    "post-install-cmd": [
        "@php artisan storage:link --force",
        "@php artisan config:cache",
        "@php artisan route:cache",
        "@php artisan view:cache"
    ]
}
```

#### **Step 2: Push ke GitHub**

```bash
# Init git (jika belum)
git init
git add .
git commit -m "Initial commit for Railway deployment"

# Create repo di GitHub, lalu:
git remote add origin https://github.com/USERNAME/kpi-tracker.git
git branch -M main
git push -u origin main
```

#### **Step 3: Deploy di Railway**

1. Buka https://railway.app
2. Sign up dengan GitHub
3. Click **"New Project"**
4. Pilih **"Deploy from GitHub repo"**
5. Pilih repository `kpi-tracker`
6. Railway akan auto-detect Laravel
7. Tunggu build selesai (~3-5 menit)

#### **Step 4: Setup Environment Variables**

Di Railway dashboard → Variables, tambahkan:

```env
APP_NAME="KPI Tracker"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.railway.app

DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

FILAMENT_FILESYSTEM_DISK=public
```

#### **Step 5: Generate APP_KEY**

```bash
# Di local
php artisan key:generate --show

# Copy output dan paste ke Railway Variables
```

#### **Step 6: Run Migrations**

Di Railway → Settings → Deploy:
```bash
php artisan migrate --force
php artisan db:seed --force
```

#### **Step 7: Setup Storage**

Railway akan otomatis create persistent volume untuk SQLite.

### 💰 **Biaya:**
- **Free tier:** $5 credit/bulan
- **Estimasi usage:** ~$3-4/bulan untuk project ini
- **Cukup untuk 1 bulan gratis!**

### 🔗 **URL:**
- Auto-generated: `https://kpi-tracker-production.up.railway.app`
- Custom domain: Bisa tambahkan domain sendiri (gratis)

---

## 🥈 **2. Render.com**

### ✅ Kelebihan:
- ✅ **Free tier permanent** (dengan batasan)
- ✅ **Support Laravel**
- ✅ **SQLite support**
- ✅ **Auto deploy** dari GitHub
- ✅ **HTTPS** gratis
- ✅ **Custom domain** gratis

### ⚠️ Kekurangan:
- ⚠️ **Spin down** setelah 15 menit idle (cold start ~30 detik)
- ⚠️ **750 jam/bulan** gratis (cukup untuk 24/7)

### 📋 Cara Deploy:

#### **Step 1: Buat `render.yaml`**

```yaml
services:
  - type: web
    name: kpi-tracker
    env: php
    buildCommand: |
      composer install --no-dev --optimize-autoloader
      php artisan storage:link
      php artisan config:cache
      php artisan route:cache
      php artisan view:cache
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: DB_CONNECTION
        value: sqlite
      - key: DB_DATABASE
        value: /opt/render/project/src/database/database.sqlite
    disk:
      name: database
      mountPath: /opt/render/project/src/database
      sizeGB: 1
```

#### **Step 2: Deploy**

1. Push ke GitHub
2. Buka https://render.com
3. Sign up dengan GitHub
4. **New → Web Service**
5. Connect repository
6. Render akan detect `render.yaml`
7. Click **Create Web Service**

### 💰 **Biaya:**
- **100% GRATIS** (dengan batasan spin down)

---

## 🥉 **3. Fly.io**

### ✅ Kelebihan:
- ✅ **Free tier:** 3 shared-cpu VMs
- ✅ **Support Laravel**
- ✅ **SQLite dengan persistent volume**
- ✅ **Global CDN**
- ✅ **Auto scaling**

### 📋 Cara Deploy:

#### **Step 1: Install flyctl**

```bash
curl -L https://fly.io/install.sh | sh
```

#### **Step 2: Login**

```bash
flyctl auth login
```

#### **Step 3: Launch App**

```bash
cd /home/ramdan/Documents/kpi-tracker
flyctl launch

# Jawab pertanyaan:
# - App name: kpi-tracker
# - Region: Singapore (sin)
# - PostgreSQL: No
# - Redis: No
```

#### **Step 4: Buat `fly.toml`**

```toml
app = "kpi-tracker"
primary_region = "sin"

[build]
  [build.args]
    PHP_VERSION = "8.2"

[env]
  APP_ENV = "production"
  DB_CONNECTION = "sqlite"
  DB_DATABASE = "/data/database.sqlite"

[http_service]
  internal_port = 8080
  force_https = true
  auto_stop_machines = true
  auto_start_machines = true
  min_machines_running = 0

[[mounts]]
  source = "kpi_data"
  destination = "/data"
```

#### **Step 5: Deploy**

```bash
flyctl deploy
```

### 💰 **Biaya:**
- **Free tier:** 3 VMs gratis
- **Estimasi:** $0/bulan (dalam free tier)

---

## 🏆 **Perbandingan Platform**

| Feature | Railway | Render | Fly.io |
|---------|---------|--------|--------|
| **Free Tier** | $5 credit/bulan | Unlimited (spin down) | 3 VMs gratis |
| **SQLite Support** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Auto Deploy** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Custom Domain** | ✅ Free | ✅ Free | ✅ Free |
| **HTTPS** | ✅ Auto | ✅ Auto | ✅ Auto |
| **Cold Start** | ❌ No | ⚠️ Yes (15 min) | ⚠️ Yes (optional) |
| **Persistent Storage** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Build Time** | ~3 min | ~5 min | ~4 min |
| **Ease of Use** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ |

---

## 🎯 **Rekomendasi Berdasarkan Kebutuhan**

### **Untuk Demo/Testing (1-2 bulan):**
👉 **Railway.app**
- Paling mudah setup
- Performance terbaik
- $5 credit cukup untuk 1 bulan

### **Untuk Jangka Panjang (Gratis Selamanya):**
👉 **Render.com**
- 100% gratis
- Spin down tidak masalah untuk internal tool
- Cold start ~30 detik acceptable

### **Untuk Production (Perlu Uptime 24/7):**
👉 **Fly.io** atau **Railway** (berbayar)
- No cold start
- Better performance
- SLA guarantee

---

## 📦 **Persiapan Project untuk Deployment**

### **1. Optimize Autoloader**

```bash
composer install --optimize-autoloader --no-dev
```

### **2. Cache Configuration**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **3. Set Production Environment**

Pastikan `.env` production:
```env
APP_ENV=production
APP_DEBUG=false
```

### **4. Setup Storage Link**

```bash
php artisan storage:link --force
```

### **5. Migrate Database**

```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## 🔒 **Security Checklist**

Sebelum deploy:

- [ ] **Ganti APP_KEY** (generate baru)
- [ ] **Set APP_DEBUG=false**
- [ ] **Ganti password admin default**
- [ ] **Hapus data dummy** (jika ada)
- [ ] **Setup .gitignore** (jangan commit .env)
- [ ] **Enable HTTPS** (auto di semua platform)
- [ ] **Set CORS** jika perlu
- [ ] **Backup database** sebelum deploy

---

## 🚀 **Quick Start: Railway (Tercepat)**

### **5 Menit Deploy:**

```bash
# 1. Install Railway CLI
npm install -g @railway/cli

# 2. Login
railway login

# 3. Init project
railway init

# 4. Link to GitHub (optional)
railway link

# 5. Deploy!
railway up

# 6. Set environment variables
railway variables set APP_KEY=$(php artisan key:generate --show)
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false

# 7. Run migrations
railway run php artisan migrate --force
railway run php artisan db:seed --force

# 8. Done! Get URL
railway domain
```

---

## 📊 **Monitoring & Maintenance**

### **Railway:**
- Dashboard: https://railway.app/dashboard
- Logs: Real-time di dashboard
- Metrics: CPU, Memory, Network

### **Render:**
- Dashboard: https://dashboard.render.com
- Logs: Real-time di dashboard
- Auto-deploy on git push

### **Fly.io:**
```bash
# Logs
flyctl logs

# Status
flyctl status

# SSH access
flyctl ssh console
```

---

## 💡 **Tips & Tricks**

### **1. Reduce Build Time**

Tambahkan di `.gitignore`:
```
/node_modules
/vendor
/public/hot
/public/storage
```

### **2. Optimize Images**

Compress foto host sebelum upload:
```bash
# Install imagemagick
# Compress images
mogrify -resize 500x500 -quality 85 storage/app/public/host-photos/*.jpg
```

### **3. Database Backup**

Setup auto-backup di Railway:
```bash
# Add to cron (Railway)
0 0 * * * cp /app/database/database.sqlite /app/storage/backups/db-$(date +\%Y\%m\%d).sqlite
```

### **4. Custom Domain**

Semua platform support custom domain gratis:
- Railway: Settings → Domains
- Render: Settings → Custom Domains
- Fly.io: `flyctl certs add yourdomain.com`

---

## 🆘 **Troubleshooting**

### **Error: "No such file or directory (database.sqlite)"**

**Solusi:**
```bash
# Pastikan path database benar
# Railway: /app/database/database.sqlite
# Render: /opt/render/project/src/database/database.sqlite
# Fly.io: /data/database.sqlite
```

### **Error: "Permission denied (storage)"**

**Solusi:**
```bash
# Set permissions
chmod -R 775 storage bootstrap/cache
```

### **Error: "Mix manifest not found"**

**Solusi:**
```bash
# Tidak perlu Vite/Mix untuk Filament
# Hapus dari composer.json jika ada
```

---

## 📚 **Resources**

- **Railway Docs:** https://docs.railway.app
- **Render Docs:** https://render.com/docs
- **Fly.io Docs:** https://fly.io/docs
- **Laravel Deployment:** https://laravel.com/docs/deployment

---

## ✅ **Kesimpulan**

**Pilihan Terbaik untuk Project Ini:**

### **🥇 Railway.app**
```
✅ Paling mudah
✅ Performance terbaik
✅ $5 credit = 1 bulan gratis
✅ Perfect untuk demo/testing
```

**Deploy sekarang:**
```bash
npm install -g @railway/cli
railway login
railway init
railway up
```

**URL akan tersedia dalam 5 menit! 🚀**

---

**Butuh bantuan deploy? Saya siap membantu! 😊**
