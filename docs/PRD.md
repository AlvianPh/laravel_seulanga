# PRD — Aplikasi Management Kost Pribadi

## 1. Ringkasan

Aplikasi web untuk pemilik kost pribadi mengelola kamar, penghuni, kontrak
sewa, pembayaran, tagihan, pengeluaran operasional, laporan keuangan, dan
notifikasi jatuh tempo, dengan dashboard sebagai pusat pemantauan.

## 2. Target Pengguna

| Role  | Deskripsi | Hak Akses |
|-------|-----------|-----------|
| Owner | Pemilik kost | Akses penuh ke semua modul dan laporan keuangan |
| Admin | Pengelola harian | Input data kamar/penghuni/pembayaran, tanpa akses laporan keuangan sensitif (batas akses final ditentukan saat Tahap Authorization) |

## 3. Modul & Kebutuhan

### 3.1 Dashboard
Menampilkan: total kamar, kamar kosong, kamar terisi, penghuni aktif,
pendapatan bulan ini, pengeluaran bulan ini, laba bulan ini, tagihan jatuh
tempo, pembayaran hari ini. Chart: pendapatan per bulan, pengeluaran per
bulan, occupancy rate, cash flow.

### 3.2 Kamar
Field: nomor kamar, lantai, tipe, luas, harga bulanan, deposit, status,
fasilitas, foto. Fitur: CRUD, upload foto, filter, search, pagination.

### 3.3 Penghuni
Field: nama, NIK, nomor HP, email, jenis kelamin, tanggal lahir, alamat,
foto KTP, foto penghuni, kontak darurat. Fitur: CRUD, upload dokumen,
riwayat sewa.

### 3.4 Kontrak Sewa
Field: penghuni, kamar, tanggal masuk, tanggal keluar, harga sewa, deposit,
status kontrak. Fitur: perpanjang kontrak, selesai kontrak, riwayat kontrak.

### 3.5 Pembayaran
Field: bulan, tahun, nominal, metode pembayaran, status (Paid/Pending/
Overdue), bukti transfer, catatan. Fitur: pembayaran bulanan, upload bukti,
cetak kuitansi, riwayat pembayaran.

### 3.6 Tagihan
Dibuat otomatis tiap awal bulan via Laravel Scheduler. Komponen: sewa,
listrik, air, internet, denda, biaya lainnya.

### 3.7 Pengeluaran
Kategori: listrik, air, internet, perbaikan, kebersihan, gaji, lainnya.
CRUD lengkap.

### 3.8 Laporan
Jenis: pendapatan, pengeluaran, cash flow, occupancy, piutang, laba rugi.
Filter: harian, mingguan, bulanan, tahunan, custom date. Export: PDF, Excel,
CSV.

### 3.9 Notifikasi
Dikirim saat: tagihan jatuh tempo, kontrak hampir habis, pembayaran
terlambat. Menggunakan Laravel Notification.

## 4. Non-Functional Requirements

- Responsive (mobile & desktop), dukung dark mode & light mode
- Keamanan: CSRF protection, Policy-based authorization, middleware,
  validasi input di semua form, mass assignment protection, rate limiting
- REST API tersedia untuk: login, kamar, penghuni, pembayaran, tagihan,
  pengeluaran (pakai Laravel API Resource)

## 5. Di Luar Scope (Saat Ini)

- Integrasi payment gateway online (belum disebutkan di requirement awal)
- Multi-kost/multi-cabang (belum disebutkan di requirement awal)

## 6. Referensi

Detail teknis (ERD, schema, use case) ada di docs/TechDesign.md.
Breakdown tahap pengerjaan ada di docs/TASKS.md.