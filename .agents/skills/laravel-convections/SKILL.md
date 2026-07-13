---
name: laravel-conventions
description: Konvensi dasar Laravel untuk project kost-management — dipakai setiap kali menulis atau mengubah kode PHP/Laravel
---

## Stack
Laravel versi stabil terbaru, PHP 8.3+, MySQL, Breeze, Blade + TailwindCSS + Alpine.js.
Jangan ganti stack ini tanpa persetujuan eksplisit dari user.

## Struktur
- Logic bisnis di `app/Services/`, bukan di Controller
- Enum di `app/Enums/` (contoh: StatusPembayaran, StatusKontrak)
- Form Request di `app/Http/Requests/` — semua input WAJIB divalidasi lewat sini
- Policy di `app/Policies/` — otorisasi role (Owner/Admin) wajib lewat Policy + middleware
- Route: `routes/web.php`, `routes/auth.php`, `routes/api.php`
- Semua Controller pakai Resource Controller
- Model: definisikan `$fillable` eksplisit, jangan `$guarded = []`
- Setiap Model/Service/Controller diberi PHPDoc singkat
- Nama tabel & kolom pakai istilah dari docs/PRD.md — jangan diterjemahkan ke bahasa Inggris