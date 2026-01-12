# 🤖 Telegram Bot - Quick Reference

## 📋 Format Input Data

```
NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES
```

### Contoh:
```
Andi|2026-01-12|3.5|15000000|120|5000|1200
```

---

## 🎯 Penjelasan Field

| Field | Tipe | Contoh | Keterangan |
|-------|------|--------|------------|
| NAMA_HOST | Text | `Andi` | Nama host (akan dibuat otomatis jika belum ada) |
| TANGGAL | Date | `2026-01-12` | Format: YYYY-MM-DD |
| JAM_LIVE | Float | `3.5` | Durasi live dalam jam (3.5 = 3 jam 30 menit) |
| GMV | Number | `15000000` | Gross Merchandise Value (tanpa titik/koma) |
| ORDERS | Integer | `120` | Jumlah pesanan |
| VIEWERS | Integer | `5000` | Jumlah penonton |
| LIKES | Integer | `1200` | Jumlah likes |

---

## 🎮 Perintah Bot

| Perintah | Fungsi |
|----------|--------|
| `/start` | Memulai bot dan melihat panduan singkat |
| `/help` | Melihat panduan lengkap format input |

---

## ✅ Contoh Input Valid

```
Andi|2026-01-12|3.5|15000000|120|5000|1200
Budi|2026-01-13|4|20000000|150|6000|1500
Citra|2026-01-14|2.5|10000000|80|4000|900
```

---

## ❌ Contoh Input Tidak Valid

```
❌ Andi 2026-01-12 3.5 15000000 120 5000 1200
   (Harus pakai | sebagai pemisah)

❌ Andi|12-01-2026|3.5|15000000|120|5000|1200
   (Format tanggal salah, harus YYYY-MM-DD)

❌ Andi|2026-01-12|3,5|15.000.000|120|5000|1200
   (Jangan pakai koma atau titik untuk angka)

❌ Andi|2026-01-12|3.5|15000000
   (Data tidak lengkap, harus 7 field)
```

---

## 📊 Response Bot

Setelah input berhasil, bot akan mengirim ringkasan:

```
✅ Data berhasil disimpan!

📊 Ringkasan:
Host: Andi
Tanggal: 2026-01-12
Durasi: 3.5 jam
GMV: Rp 15.000.000
Orders: 120
Viewers: 5000
Likes: 1200

📈 Metrics:
GMV/Jam: Rp 4.285.714
Conversion Rate: 2.40%
AOV: Rp 125.000
Likes/Menit: 5.7
```

---

## 🔧 Setup (Admin Only)

### 1. Buat Bot di Telegram
1. Buka Telegram, cari `@BotFather`
2. Kirim `/newbot`
3. Ikuti instruksi
4. Simpan bot token

### 2. Konfigurasi .env
```env
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_WEBHOOK_TOKEN=your-secret-token-here
```

### 3. Set Webhook
```bash
php artisan telegram:webhook set
```

### 4. Cek Status Webhook
```bash
php artisan telegram:webhook info
```

---

## 🐛 Troubleshooting

### Bot tidak merespon?
1. Cek webhook status: `php artisan telegram:webhook info`
2. Pastikan aplikasi accessible dari internet (HTTPS)
3. Cek log: `tail -f storage/logs/laravel.log`

### Error "Format salah"?
- Pastikan pakai `|` sebagai pemisah
- Pastikan ada 7 field
- Gunakan `/help` untuk melihat format

### Data tidak tersimpan?
- Cek format tanggal (YYYY-MM-DD)
- Pastikan angka tidak pakai koma/titik
- Cek log aplikasi untuk error detail

---

## 💡 Tips

1. **Copy-paste format** dari `/help` untuk menghindari kesalahan
2. **Gunakan kalkulator** untuk menghitung jam (contoh: 3 jam 30 menit = 3.5)
3. **Jangan pakai pemisah ribuan** untuk angka (15000000, bukan 15.000.000)
4. **Cek response bot** untuk memastikan data tersimpan dengan benar
5. **Simpan template** di Saved Messages untuk input cepat

---

## 📞 Support

Jika ada masalah atau pertanyaan, hubungi admin aplikasi.

---

**KPI Tracker - Telegram Bot Integration**
