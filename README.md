# Kost Management - Seulanga 🏢

Aplikasi web modern untuk manajemen kost dan properti sewa pribadi berbasis Laravel. Dirancang untuk memudahkan pemilik kost (*Owner*) dan pengelola (*Admin*) dalam mengelola operasional harian, pemantauan hunian, penagihan sewa, hingga pelaporan keuangan secara terintegrasi.

---

## 🌟 Fitur Utama

- **Dashboard & Analitik**: Ringkasan KPI keuangan, tingkat keterisian kamar (*occupancy rate*), arus kas bulanan, dan widget tindakan mendesak (*urgent actions*).
- **Manajemen Kamar & Tipe Kamar**: Monitoring status kamar (*available*, *occupied*, *maintenance*), fasilitas, dan pengelompokan harga/tipe kamar.
- **Data Penghuni**: Pencatatan identitas penghuni (NIK, kontak darurat, foto KTP) dan riwayat sewa.
- **Kontrak Sewa**: Pembuatan kontrak, penyesuaian tarif sewa, pencatatan deposit jaminan, perpanjangan sewa, dan penghentian kontrak.
- **Tagihan & Notifikasi**: Pembuatan tagihan bulanan otomatis & manual, perhitungan denda keterlambatan, dan fitur kirim pengingat tagihan via WhatsApp.
- **Pembayaran**: Pencatatan transaksi pembayaran (Tunai, Transfer Bank, QRIS), unggah bukti bayar, dan verifikasi pembayaran.
- **Pengeluaran Operasional**: Pencatatan biaya pemeliharaan, utilitas (listrik/air/internet), dan kategori pengeluaran kost.
- **Laporan Keuangan & Operasional**: Ekspor & rekapitulasi Laporan Pendapatan, Pengeluaran, Arus Kas (*Cash Flow*), Laba Rugi, Piutang/Tunggakan, dan Okupansi.
- **Manajemen Pengguna & Pengaturan**: Role & Permission (Owner vs Admin), konfigurasi profil kost (nama, alamat, logo, rekening bank, jenis biaya tambahan).

---

## 🛠️ Stack Teknologi

- **Backend**: [Laravel](https://laravel.com) (PHP 8.3+)
- **Database**: MySQL / MariaDB
- **Autentikasi**: Laravel Breeze
- **Frontend / Styling**: Blade Templates, [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Visualisasi & Charts**: [Chart.js](https://www.chartjs.org)
- **Asset Bundler**: [Vite](https://vitejs.dev)

---

## 🚀 Panduan Instalasi

### 1. Prasyarat Sistem
- PHP >= 8.2 (direkomendasikan PHP 8.3+)
- Composer
- Node.js & NPM
- MySQL / MariaDB

### 2. Langkah Instalasi

1. **Clone repository & masuk ke direktori proyek**:
   ```bash
   git clone <repository-url>
   cd kost-management
   ```

2. **Install dependensi PHP**:
   ```bash
   composer install
   ```

3. **Install dependensi Frontend**:
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**:
   Salin file konfigurasi environment dan sesuaikan pengaturan database:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur koneksi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kost_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate --seed
   ```

7. **Buat Storage Symlink** (untuk upload bukti bayar & foto KTP):
   ```bash
   php artisan storage:link
   ```

8. **Build Asset / Jalankan Server Pengembangan**:
   - Untuk development:
     ```bash
     npm run dev
     ```
     dan di terminal lain:
     ```bash
     php artisan serve
     ```
   - Untuk production build:
     ```bash
     npm run build
     ```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
