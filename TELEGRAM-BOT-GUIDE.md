# Telegram Bot Integration Guide

## 📱 Setup Telegram Bot untuk Input Data KPI

Panduan ini akan membantu Anda mengintegrasikan Telegram Bot dengan aplikasi KPI Tracker untuk input data live session melalui Telegram.

---

## 🚀 Langkah 1: Buat Telegram Bot

1. **Buka Telegram** dan cari `@BotFather`
2. **Kirim perintah** `/newbot`
3. **Ikuti instruksi**:
   - Masukkan nama bot (contoh: `KPI Tracker Bot`)
   - Masukkan username bot (harus diakhiri dengan `bot`, contoh: `kpi_tracker_bot`)
4. **Simpan Bot Token** yang diberikan oleh BotFather (format: `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz`)

---

## ⚙️ Langkah 2: Konfigurasi Environment

Tambahkan konfigurasi berikut ke file `.env`:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_WEBHOOK_TOKEN=your_random_secret_token_here
```

**Keterangan:**
- `TELEGRAM_BOT_TOKEN`: Token yang didapat dari BotFather
- `TELEGRAM_WEBHOOK_TOKEN`: Token rahasia untuk validasi webhook (buat string random, contoh: `my-secret-webhook-token-12345`)

**Contoh:**
```env
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_WEBHOOK_TOKEN=kpi-tracker-webhook-secret-2026
```

---

## 🔗 Langkah 3: Setup Webhook

Setelah aplikasi di-deploy, set webhook URL ke Telegram:

### Menggunakan cURL:

```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://your-domain.com/api/telegram/webhook",
    "secret_token": "your_random_secret_token_here"
  }'
```

### Menggunakan Browser:

Buka URL berikut di browser (ganti `<YOUR_BOT_TOKEN>` dan `<YOUR_DOMAIN>`):

```
https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://<YOUR_DOMAIN>/api/telegram/webhook&secret_token=<YOUR_WEBHOOK_TOKEN>
```

**Contoh:**
```
https://api.telegram.org/bot1234567890:ABCdefGHIjklMNOpqrsTUVwxyz/setWebhook?url=https://kpi-tracker.example.com/api/telegram/webhook&secret_token=kpi-tracker-webhook-secret-2026
```

### Verifikasi Webhook:

Cek status webhook dengan:
```
https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getWebhookInfo
```

---

## 📝 Langkah 4: Cara Menggunakan Bot

### Format Input Data:

```
NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES
```

### Contoh Input:

```
Andi|2026-01-12|3.5|15000000|120|5000|1200
```

**Penjelasan:**
- `Andi` = Nama host
- `2026-01-12` = Tanggal live (format: YYYY-MM-DD)
- `3.5` = Durasi live dalam jam
- `15000000` = GMV (Gross Merchandise Value)
- `120` = Jumlah orders
- `5000` = Jumlah viewers
- `1200` = Jumlah likes

### Perintah Bot:

| Perintah | Deskripsi |
|----------|-----------|
| `/start` | Memulai bot dan melihat panduan singkat |
| `/help` | Melihat panduan lengkap format input |

---

## 🧪 Testing

### 1. Test Webhook Endpoint Lokal:

Jika ingin test di local development, gunakan **ngrok** atau **localtunnel**:

```bash
# Menggunakan ngrok
ngrok http 8000

# Atau menggunakan localtunnel
npx localtunnel --port 8000
```

Kemudian set webhook ke URL yang diberikan (contoh: `https://abc123.ngrok.io/api/telegram/webhook`)

### 2. Test Manual dengan cURL:

```bash
curl -X POST http://localhost:8000/api/telegram/webhook \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: your_webhook_token" \
  -d '{
    "message": {
      "chat": {"id": 123456789},
      "text": "Andi|2026-01-12|3.5|15000000|120|5000|1200"
    }
  }'
```

### 3. Cek Log:

Monitor log aplikasi untuk melihat webhook yang masuk:

```bash
tail -f storage/logs/laravel.log
```

---

## 🔒 Keamanan

1. **Webhook Token**: Selalu gunakan webhook token untuk validasi request
2. **HTTPS**: Telegram hanya menerima webhook dengan HTTPS (tidak bisa HTTP)
3. **Rate Limiting**: Pertimbangkan untuk menambahkan rate limiting pada endpoint
4. **IP Whitelist**: Opsional - whitelist IP Telegram servers

### IP Telegram Servers:
```
149.154.160.0/20
91.108.4.0/22
```

---

## 📊 Response Bot

Setelah data berhasil disimpan, bot akan mengirim response seperti:

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

## 🐛 Troubleshooting

### Bot tidak merespon:
1. Cek webhook status: `https://api.telegram.org/bot<TOKEN>/getWebhookInfo`
2. Pastikan URL webhook benar dan accessible
3. Cek log aplikasi untuk error

### Error "Unauthorized":
- Pastikan `TELEGRAM_WEBHOOK_TOKEN` di `.env` sama dengan yang di-set di webhook

### Data tidak tersimpan:
- Cek format input sudah benar (gunakan `/help`)
- Cek log aplikasi untuk error detail
- Pastikan database connection OK

### Webhook tidak terkirim:
- Pastikan aplikasi menggunakan HTTPS
- Pastikan port 443 atau 8443 terbuka
- Cek firewall settings

---

## 🔄 Menghapus Webhook

Jika ingin menghapus webhook:

```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/deleteWebhook"
```

---

## 📚 Resources

- [Telegram Bot API Documentation](https://core.telegram.org/bots/api)
- [Telegram Webhooks Guide](https://core.telegram.org/bots/webhooks)
- [Laravel API Routes](https://laravel.com/docs/routing#api-routes)

---

## 💡 Tips

1. **Gunakan Bot Commands**: Set commands di BotFather untuk UX yang lebih baik
2. **Notifikasi**: Tambahkan notifikasi untuk admin ketika ada data baru
3. **Validasi**: Tambahkan validasi lebih ketat untuk data input
4. **Bulk Input**: Pertimbangkan untuk support multiple entries sekaligus
5. **Export Data**: Tambahkan command untuk export data via bot

---

## 🎯 Next Steps

Setelah setup selesai:

1. ✅ Test bot dengan command `/start`
2. ✅ Test input data dengan format yang benar
3. ✅ Verifikasi data tersimpan di database
4. ✅ Monitor log untuk memastikan tidak ada error
5. ✅ Setup monitoring dan alerting

---

**Dibuat untuk KPI Tracker - Telegram Bot Integration**
