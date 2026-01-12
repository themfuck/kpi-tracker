# 🎯 KPI Live Streaming Tracker

Aplikasi monitoring KPI live streaming bulanan menggunakan Laravel 12 dan Filament v3.

## ✨ Fitur Utama

### 📊 Dashboard KPI
- **Total GMV Bulan Ini** - Tracking GMV real-time dengan target Rp 1.300.000.000
- **Achievement Percentage** - Persentase pencapaian terhadap target
- **GMV per Jam Rata-rata** - Monitoring efisiensi live streaming
- **Chart GMV Harian** - Visualisasi trend GMV harian
- **Ranking Host Top 8** - Leaderboard host dengan score dan status KPI

### 👥 Master Data Host
- Upload foto profil host
- Role management (Host / Operator)
- Status aktif/non-aktif
- Tracking total sesi live per host

### 📹 Live Session Management
- Input data harian: tanggal, jam live, GMV, order, viewer, like, error
- Filter berdasarkan host dan tanggal
- Summary total GMV dan orders
- Export ke Excel

### 🏆 Ranking Host
- Halaman khusus ranking dengan tampilan card menarik
- Medal badge untuk Top 3 (🥇🥈🥉)
- Filter bulan dan tahun
- Score 0-100 berdasarkan weighted KPI
- Status KPI: OK (hijau), WARNING (kuning), DROP (merah)
- Export ranking ke Excel

### 📈 Sistem Scoring

**Bobot Perhitungan Score (0-100):**
- GMV per jam → 30%
- Conversion Rate → 20%
- AOV (Average Order Value) → 15%
- Like per menit → 10%
- Total GMV bulanan → 25%

**Status KPI:**
- ✅ **OK** → Score ≥ 100% target (Hijau)
- ⚠️ **WARNING** → Score ≥ 80% target (Kuning)
- 🔴 **DROP** → Score < 80% target (Merah)

### 🎯 Target KPI (Editable)
- GMV per jam: Rp 2.700.000
- Conversion Rate: 0.03 (3%)
- AOV: Rp 180.000
- Like per menit: 300

## 🚀 Instalasi

### Requirements
- PHP 8.2+
- Composer
- SQLite (sudah included)

### Setup

1. **Clone atau extract project**
```bash
cd kpi-tracker
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Setup database**
```bash
php artisan migrate:fresh --seed
```

5. **Setup storage untuk upload foto**
```bash
php artisan storage:link
```

6. **Jalankan server**
```bash
php artisan serve
```

7. **Akses aplikasi**
```
URL: http://localhost:8000/admin
Email: admin@kpi.com
Password: password
```

## 📁 Struktur Kode

```
app/
├── Models/
│   ├── Host.php              # Model host dengan relasi liveSessions
│   ├── LiveSession.php       # Model data live session harian
│   └── KpiTarget.php         # Model target KPI
├── Services/
│   └── KpiCalculatorService.php  # Service layer untuk semua perhitungan KPI
├── Filament/
│   ├── Resources/
│   │   ├── HostResource.php          # CRUD Host dengan upload foto
│   │   ├── LiveSessionResource.php   # CRUD Live Session dengan export
│   │   └── KpiTargetResource.php     # Edit target KPI
│   ├── Widgets/
│   │   ├── KpiStatsOverview.php      # Widget stats GMV & achievement
│   │   ├── GmvChart.php              # Widget chart GMV harian
│   │   └── HostRankingTable.php      # Widget table ranking top 8
│   └── Pages/
│       └── HostRanking.php           # Halaman khusus ranking host
└── Exports/
    └── HostRankingExport.php     # Export Excel ranking
```

## 🎨 Fitur UI

- ✅ Upload foto profil host dengan image editor dan circle cropper
- ✅ Badge warna untuk role, status, dan KPI
- ✅ Avatar circular di ranking table
- ✅ Medal emoji untuk Top 3 ranking
- ✅ Progress bar score di halaman ranking
- ✅ Filter bulan/tahun di halaman ranking
- ✅ Dark mode support
- ✅ Responsive design

## 📊 Export Excel

### Live Session Export
- Dari halaman Live Session → Select rows → Export
- Format: XLSX
- Kolom: Tanggal, Host, Jam Live, GMV, Orders, Viewers, Likes, Errors

### Ranking Host Export
- Dari halaman Ranking Host → Tombol "Export Excel"
- Format: XLSX
- Kolom: Rank, Nama Host, Role, Score, Status KPI, Total GMV, GMV/Jam, Total Jam, CR, AOV, Like/Menit

## 🔧 Konfigurasi

### Edit Target KPI
1. Login sebagai admin
2. Menu: **Pengaturan → Target KPI**
3. Edit nilai target sesuai kebutuhan
4. Save

### Upload Foto Host
1. Menu: **Master Data → Host**
2. Create/Edit host
3. Upload foto di field "Foto Profil"
4. Gunakan image editor untuk crop circular

## 📝 Input Data Live Session

1. Menu: **Master Data → Live Session**
2. Klik "Create"
3. Isi form:
   - Host (dropdown searchable)
   - Tanggal Live
   - Jam Live (float, contoh: 3.5 untuk 3 jam 30 menit)
   - GMV (Gross Merchandise Value)
   - Jumlah Order
   - Jumlah Viewer
   - Jumlah Like
   - Jumlah Error
4. Save

## 🤖 Input Data via Telegram Bot

Aplikasi ini mendukung input data melalui Telegram Bot untuk kemudahan entry data dari mobile.

### Setup Telegram Bot

1. **Buat bot di Telegram** (via @BotFather)
2. **Tambahkan konfigurasi ke `.env`:**
   ```env
   TELEGRAM_BOT_TOKEN=your_bot_token_here
   TELEGRAM_WEBHOOK_TOKEN=your_random_secret_token
   ```

3. **Set webhook:**
   ```bash
   php artisan telegram:webhook set
   ```

### Cara Menggunakan Bot

Format input data:
```
NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES
```

Contoh:
```
Andi|2026-01-12|3.5|15000000|120|5000|1200
```

**Perintah Bot:**
- `/start` - Memulai bot dan melihat panduan
- `/help` - Melihat format input lengkap

📖 **Lihat panduan lengkap:** [TELEGRAM-BOT-GUIDE.md](TELEGRAM-BOT-GUIDE.md)


## 🏆 Melihat Ranking

### Di Dashboard
- Widget "Ranking Host (Top 8)" menampilkan ranking real-time
- Auto-refresh setiap kali data berubah

### Halaman Ranking Khusus
1. Menu: **Laporan → Ranking Host**
2. Pilih bulan dan tahun
3. Lihat ranking dengan tampilan card
4. Export ke Excel jika diperlukan

## 🎯 Cara Kerja Scoring

Sistem menghitung score untuk setiap host berdasarkan:

1. **GMV per Jam** = Total GMV / Total Jam Live
2. **Conversion Rate** = Total Orders / Total Viewers
3. **AOV** = Total GMV / Total Orders
4. **Like per Menit** = Total Likes / (Total Jam × 60)

Setiap metrik dibandingkan dengan target, lalu dikalikan dengan bobot masing-masing.

**Contoh:**
- GMV per jam actual: Rp 3.000.000
- Target: Rp 2.700.000
- Achievement: 111.11%
- Kontribusi ke score: 111.11% × 30% = 33.33 poin

Total score = jumlah semua kontribusi (max 100)

## 🚀 Deploy ke Hosting

### Shared Hosting
1. Upload semua file
2. Set document root ke `/public`
3. Import database SQLite atau migrate
4. Set permissions untuk `storage/` dan `bootstrap/cache/`
5. Update `.env` sesuai environment

### VPS/Cloud
```bash
# Clone repo
git clone <repo-url>
cd kpi-tracker

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup web server (Nginx/Apache)
```

## 📦 Database

Menggunakan **SQLite** untuk kemudahan deployment.

File database: `database/database.sqlite`

### Backup Database
```bash
cp database/database.sqlite database/backup-$(date +%Y%m%d).sqlite
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

## 🔐 User Management

Default admin:
- Email: `admin@kpi.com`
- Password: `password`

**⚠️ PENTING:** Ganti password default setelah first login!

## 🛠️ Troubleshooting

### Foto tidak muncul
```bash
php artisan storage:link
```

### Error permission
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📞 Support

Untuk pertanyaan atau issue, silakan hubungi developer.

## 📄 License

Proprietary - All rights reserved

---

**Dibuat dengan ❤️ menggunakan Laravel 12 & Filament v3**
