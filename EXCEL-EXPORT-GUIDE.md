# 📊 Panduan Export Excel - KPI Live Streaming Tracker

## ✅ Format Export

Aplikasi ini menghasilkan file Excel dalam format **`.xlsx`** (Microsoft Excel 2007+) yang kompatibel dengan:
- Microsoft Excel 2007 atau lebih baru
- Google Sheets
- LibreOffice Calc
- WPS Office
- Dan aplikasi spreadsheet lainnya

## 📥 Cara Export Data

### 1. Export Ranking Host

**Lokasi:** Menu **Laporan → Ranking Host**

**Langkah:**
1. Pilih bulan dan tahun yang diinginkan
2. Klik tombol **"Export Excel"** (hijau dengan icon download) di pojok kanan atas
3. File akan otomatis terdownload dengan nama: `ranking-host-YYYY-MM.xlsx`

**Isi File:**
- Rank (1, 2, 3, dst)
- Nama Host
- Role (Host/Operator)
- Score (0-100)
- Status KPI (OK/WARNING/DROP)
- Total GMV (format currency)
- GMV per Jam (format currency)
- Total Jam Live
- Conversion Rate (format percentage)
- AOV - Average Order Value (format currency)
- Like per Menit

**Fitur Excel:**
- ✅ Header dengan background abu-abu dan bold
- ✅ Auto-sizing columns (lebar kolom otomatis)
- ✅ Format angka yang sesuai (currency, percentage, decimal)
- ✅ Data terurut berdasarkan ranking

### 2. Export Live Session

**Lokasi:** Menu **Master Data → Live Session**

**Langkah:**
1. (Opsional) Filter data berdasarkan host atau tanggal
2. Pilih baris yang ingin di-export (centang checkbox)
3. Klik dropdown **"Bulk actions"** di bagian bawah table
4. Pilih **"Export"**
5. File akan otomatis terdownload dengan nama: `live-sessions-YYYY-MM-DD.xlsx`

**Isi File:**
- Tanggal
- Host
- Jam Live
- GMV
- Orders
- Viewers
- Likes
- Errors

**Fitur Excel:**
- ✅ Format XLSX standar
- ✅ Summary total GMV dan Orders di bagian bawah table
- ✅ Data sesuai dengan filter yang dipilih

## 🔧 Spesifikasi Teknis

### Format File
- **Extension:** `.xlsx`
- **MIME Type:** `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`
- **Format:** Office Open XML Workbook
- **Compression:** ZIP-based (file XLSX adalah file ZIP yang berisi XML)

### Library yang Digunakan
- **maatwebsite/excel** v3.1+ (Laravel Excel)
- **PhpSpreadsheet** (underlying library)

### Implementasi

**HostRankingExport** (`app/Exports/HostRankingExport.php`):
```php
class HostRankingExport implements 
    FromCollection,      // Data source
    WithHeadings,        // Header row
    WithMapping,         // Data transformation
    WithStyles,          // Cell styling
    ShouldAutoSize,      // Auto column width
    WithColumnFormatting // Number formats
```

**Column Formats:**
- Score: `0.00` (2 decimal places)
- GMV/AOV: `#,##0` (thousand separator)
- Conversion Rate: `0.00%` (percentage)
- Hours: `0.00` (2 decimal places)

## ✅ Verifikasi File

Untuk memastikan file yang didownload adalah XLSX yang valid:

### 1. Cek Extension
File harus berakhiran `.xlsx`

### 2. Cek di File Manager
- **Windows:** Klik kanan → Properties → Type harus "Microsoft Excel Worksheet (.xlsx)"
- **Linux:** `file nama-file.xlsx` → harus menunjukkan "Microsoft Excel 2007+"
- **Mac:** Get Info → Kind harus "Microsoft Excel document"

### 3. Buka di Excel/Spreadsheet
File harus bisa dibuka tanpa error di:
- Microsoft Excel
- Google Sheets (upload ke Google Drive)
- LibreOffice Calc

### 4. Cek Header File (Advanced)
File XLSX adalah ZIP file. Header file harus dimulai dengan `PK` (0x50 0x4B):
```bash
# Linux/Mac
hexdump -C file.xlsx | head -1
# Harus menunjukkan: 00000000  50 4b 03 04 ...

# Atau
file file.xlsx
# Output: Microsoft Excel 2007+
```

## 🐛 Troubleshooting

### File tidak terdownload
**Solusi:**
1. Cek browser download settings
2. Pastikan popup blocker tidak aktif
3. Coba browser lain (Chrome, Firefox, Edge)
4. Cek console browser (F12) untuk error

### File corrupt atau tidak bisa dibuka
**Solusi:**
1. Download ulang file
2. Pastikan download selesai sempurna (cek ukuran file)
3. Cek apakah ada antivirus yang memblokir
4. Coba buka dengan aplikasi spreadsheet lain

### File berformat .xls (bukan .xlsx)
**Solusi:**
Aplikasi ini **HANYA** menghasilkan `.xlsx`. Jika file Anda `.xls`:
1. Cek nama file yang didownload
2. Rename manual ke `.xlsx` jika perlu
3. Atau re-download file

### Data tidak sesuai
**Solusi:**
1. Pastikan filter bulan/tahun sudah benar
2. Refresh halaman dan export ulang
3. Cek apakah data di database sudah benar

## 📝 Catatan Penting

1. **Format Angka:**
   - GMV dan AOV menggunakan format currency tanpa simbol Rp
   - Conversion Rate dalam format percentage (contoh: 3.50%)
   - Score dan jam dalam format decimal (contoh: 85.50)

2. **Encoding:**
   - File menggunakan UTF-8 encoding
   - Mendukung karakter Indonesia dengan baik

3. **Ukuran File:**
   - File ranking biasanya < 50 KB
   - File live session tergantung jumlah data (biasanya < 500 KB)

4. **Kompatibilitas:**
   - Compatible dengan Excel 2007, 2010, 2013, 2016, 2019, 365
   - Compatible dengan Google Sheets
   - Compatible dengan LibreOffice 5.0+

## 🎯 Best Practices

1. **Export Rutin:**
   - Export ranking setiap akhir bulan untuk arsip
   - Export live session setiap minggu untuk backup

2. **Penamaan File:**
   - File otomatis diberi nama dengan tanggal
   - Simpan dengan struktur folder yang rapi

3. **Backup:**
   - Simpan file export sebagai backup data
   - Upload ke cloud storage (Google Drive, Dropbox, dll)

4. **Analisis Lebih Lanjut:**
   - Gunakan Excel untuk pivot table
   - Buat chart tambahan sesuai kebutuhan
   - Gabungkan data dari beberapa bulan

---

**Dibuat dengan ❤️ menggunakan Laravel Excel & PhpSpreadsheet**
