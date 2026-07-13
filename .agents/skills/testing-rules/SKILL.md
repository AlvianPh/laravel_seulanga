---
name: testing-rules
description: Aturan testing — dipakai saat membuat atau menjalankan test untuk modul CRUD
---

- Setiap modul CRUD minimal 1 Feature Test: create, update, delete, dan authorization gagal untuk role yang salah
- Lokasi: `tests/Feature/` dan `tests/Unit/`
- Command: `php artisan test`
- Jalankan test setelah tahap selesai, sebelum lanjut ke tahap berikutnya