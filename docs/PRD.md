# SEULANGA — BUSINESS FLOW & ENGINEERING BASELINE v1.0

Gunakan dokumen ini sebagai baseline sebelum melanjutkan implementasi fitur Seulanga.

Jangan melakukan refactor besar atau membuat fitur baru hanya berdasarkan asumsi. Pertahankan fitur F1 yang sudah berjalan dan lakukan perubahan hanya jika diperlukan untuk mendukung business flow berikut.

---

## 1. BUSINESS POSITIONING

Seulanga adalah sistem operasional dan management kost pribadi yang menghubungkan tiga aktor:

* Owner = pengambil keputusan bisnis
* Admin = pengelola operasional harian
* Tenant = penghuni/pelanggan

Seulanga bukan sekadar CRUD management kost.

Sistem harus mendukung lifecycle penghuni:

Calon Penghuni
→ Registrasi
→ Melengkapi Profil
→ Mengajukan Kamar
→ Review Admin
→ Approval
→ Contract
→ Pembayaran Awal
→ Tenant Aktif
→ Tagihan Bulanan
→ Pembayaran
→ Maintenance / Permission / Request
→ Renewal atau Move Out
→ Kamar kembali tersedia

---

# 2. CORE DESIGN PRINCIPLE

Gunakan prinsip:

Owner asks:
"Apa kondisi bisnis kost saya?"

Admin asks:
"Apa yang harus saya kerjakan?"

Tenant asks:
"Apa yang harus saya lakukan?"

Karena itu dashboard ketiga role tidak boleh sekadar menjadi dashboard yang sama dengan data berbeda.

---

# 3. ROLE RESPONSIBILITY

## OWNER

Fokus pada:

* business overview
* financial performance
* occupancy
* receivables
* maintenance cost
* BEP
* cash flow
* risk
* reports
* settings
* user management

Owner memiliki akses penuh, tetapi tetap mengikuti policy dan authorization system.

## ADMIN

Fokus pada operasional:

* rooms
* tenants
* tenant applications
* contracts
* invoices
* payment verification
* expenses
* maintenance
* tenant permissions
* tenant requests
* operational reports
* communication

## TENANT

Tenant hanya dapat mengakses data miliknya sendiri:

* profile
* room
* contract
* invoices
* payments
* documents
* maintenance requests
* permission requests
* notifications

Tenant tidak boleh mengakses data tenant lain.

---

# 4. SIMPLIFIED TENANT NAVIGATION

Jangan membuat terlalu banyak menu.

Tenant navigation:

1. Beranda
2. Kamar Saya
3. Tagihan
4. Pengajuan
5. Dokumen
6. Profil

Maintenance dan permission harus berada di dalam "Pengajuan".

Contoh:

Pengajuan
├── Laporkan Kerusakan
├── Izin Tamu Menginap
├── Izin Pulang Larut
└── Izin Perangkat Tambahan

---

# 5. TENANT ONBOARDING

Tenant tidak langsung menjadi tenant aktif ketika melakukan registrasi.

Flow:

Register
→ Email verification
→ Login
→ Lengkapi profile
→ Lihat kamar tersedia
→ Ajukan kamar
→ Admin review
→ Approved / Rejected

Jika approved:

Application approved
→ Contract dibuat
→ Dokumen tersedia
→ Tenant melakukan pembayaran awal
→ Tenant menjadi ACTIVE
→ Room menjadi OCCUPIED

Jika rejected:

Application rejected
→ Tenant dapat melihat alasan
→ Tenant dapat mengajukan kembali jika aturan bisnis mengizinkan

---

# 6. TENANT APPLICATION

Tambahkan domain Tenant Application.

Minimal data:

* id
* user_id
* room_id
* status
* application_date
* reviewed_by
* reviewed_at
* rejection_reason
* notes
* timestamps

Status:

* pending
* approved
* rejected
* cancelled

Tenant Application adalah pengajuan calon penghuni, bukan Contract.

Jangan membuat Contract sebelum application disetujui.

---

# 7. ROOM STATUS

Pertahankan room status:

* available
* occupied
* maintenance

Jangan menambahkan "reserved" hanya untuk menangani tenant application.

Reservation/application merupakan proses bisnis tersendiri.

Room menjadi occupied ketika contract aktif.

---

# 8. CONTRACT

Contract adalah sumber histori hubungan sewa.

Jangan memperpanjang contract lama dengan sekadar mengubah end_date jika renewal merupakan periode baru.

Contoh:

Contract #1:
01 Jan 2026 → 31 Dec 2026
status ended

Contract #2:
01 Jan 2027 → 31 Dec 2027
status active

Sistem harus mencegah overlapping active contract pada tenant/room yang sama.

Minimal status:

* draft
* active
* ended
* terminated

Renewal membuat contract baru jika renewal merupakan periode sewa baru.

---

# 9. PAYMENT & INVOICE RULES

Invoice status:

* pending
* partially_paid
* paid
* overdue
* cancelled

Payment status:

* pending
* verified
* rejected

Payment yang masih pending atau rejected tidak dihitung sebagai pembayaran lunas.

Invoice tidak boleh otomatis menjadi paid hanya karena tenant upload bukti transfer.

Flow:

Tenant upload proof
→ Payment pending
→ Admin/Owner verify
→ Payment verified
→ Recalculate invoice paid amount
→ Update invoice status

Jika:

paid_amount < total_amount
→ partially_paid

Jika:

paid_amount >= total_amount
→ paid

Jika:

due_date passed AND outstanding amount > 0
→ overdue

Sistem harus mendukung lebih dari satu payment untuk satu invoice.

---

# 10. DEPOSIT

Contract harus menyimpan lifecycle deposit.

Minimal:

* deposit_amount
* deposit_returned_amount
* deposit_returned_at
* deposit_notes

Contoh:

Deposit = Rp1.000.000
Damage deduction = Rp300.000
Returned = Rp700.000

Informasi deposit harus dapat dilihat Owner/Admin dan histori harus tetap tersedia setelah tenant keluar.

---

# 11. MAINTENANCE

Tenant dapat membuat maintenance request.

Data minimal:

* tenant
* room
* title
* description
* photo
* priority
* status
* cost
* resolution notes
* resolved by
* resolved at

Status:

pending
→ in_progress
→ resolved

pending
→ rejected

Tenant hanya dapat melihat request miliknya.

Admin menangani request.

Owner dapat melihat keseluruhan dan analytics.

---

# 12. MAINTENANCE & EXPENSE

Jika maintenance menghasilkan biaya, expense harus dapat dikaitkan dengan maintenance request.

Tambahkan relationship:

expenses.maintenance_request_id

Tujuan:

* mencegah perbedaan data maintenance cost dan expense
* memungkinkan analisis biaya maintenance per kamar
* memungkinkan Owner mengetahui kamar yang paling mahal dipelihara

Contoh:

Maintenance #32
→ Repair AC
→ Cost Rp350.000

Expense #87
→ Category Maintenance
→ Amount Rp350.000
→ maintenance_request_id = 32

---

# 13. TENANT PERMISSIONS

Permission tetap menggunakan satu domain request.

Types:

* guest_stay
* electronic_device
* late_return
* other

Status:

* pending
* approved
* rejected

Flow:

Tenant submit
→ Admin review
→ Approved / Rejected
→ Tenant receives notification

Permission harus memiliki histori.

---

# 14. COMMUNICATION

Communication merupakan cross-cutting service.

Channel:

* in-app
* email
* WhatsApp

Jangan membuat communication menjadi domain bisnis terpisah yang terlalu kompleks.

Event utama:

* payment submitted
* payment verified
* payment rejected
* invoice reminder
* invoice overdue
* contract nearing end
* maintenance updated
* permission approved/rejected
* application approved/rejected

Communication log harus dapat ditelusuri.

Recipient jangan hanya bergantung pada tenant_id karena Owner/Admin juga dapat menjadi recipient.

Gunakan konsep recipient/notifiable yang generic jika diperlukan.

---

# 15. DASHBOARD DESIGN

## OWNER DASHBOARD

Tujuan:
Menjawab kondisi bisnis.

Tampilkan:

* occupancy
* total income
* total expense
* net profit
* outstanding receivables
* overdue invoices
* maintenance cost
* cash flow
* BEP
* contract expiry risk

Health/risk score tidak boleh menjadi angka tanpa penjelasan.

Jika menggunakan score, tampilkan faktor pembentuknya.

Contoh:

Risk:
Medium

Factors:

* 3 overdue invoices
* 2 maintenance requests urgent
* occupancy 76%
* 2 contracts ending within 30 days

---

## ADMIN DASHBOARD

Tujuan:
Menjawab pekerjaan yang harus dilakukan.

Tampilkan:

* payment verification pending
* overdue invoices
* pending tenant applications
* pending maintenance
* pending permission requests
* contracts nearing expiration
* recent tenant activity

Gunakan konsep "urgent actions".

---

## TENANT DASHBOARD

Tujuan:
Menjawab tindakan yang harus dilakukan tenant.

Tampilkan:

* current room
* current contract
* current invoice
* due date
* payment status
* active requests
* important documents
* notifications

CTA utama harus jelas.

Contoh:

Tagihan bulan ini
Rp1.250.000

Jatuh tempo:
10 September 2026

[ BAYAR SEKARANG ]

---

# 16. RBAC

Gunakan Laravel middleware + policies.

Jangan hanya mengandalkan hidden menu.

Authorization harus dilakukan pada backend.

Tenant harus selalu dibatasi berdasarkan ownership:

tenant.user_id == authenticated_user.id

Tenant tidak boleh mengakses:

* tenant lain
* invoice tenant lain
* payment tenant lain
* maintenance tenant lain
* permission tenant lain
* contract tenant lain

Owner tidak perlu impersonate tenant.

Owner mengakses data tenant melalui management interface.

---

# 17. STATE MACHINE

Application:

pending
→ approved
→ rejected / cancelled

Contract:

draft
→ active
→ ended / terminated

Invoice:

pending
→ partially_paid
→ paid

pending
→ overdue

partially_paid
→ paid

partially_paid
→ overdue

overdue
→ paid

Payment:

pending
→ verified / rejected

Maintenance:

pending
→ in_progress
→ resolved

pending
→ rejected

Permission:

pending
→ approved / rejected

Jangan menambahkan status baru tanpa alasan bisnis yang jelas.

---

# 18. UX PRINCIPLE

Gunakan progressive disclosure.

Tenant tidak perlu memahami struktur database.

Contoh:

Database:
maintenance_requests
tenant_permissions
payment_proofs
contracts

UX:

Pengajuan
Tagihan
Dokumen

UI harus menggunakan bahasa bisnis yang mudah dipahami pengguna.

Prioritaskan:

* mobile-first
* clear CTA
* status yang mudah dipahami
* empty state
* loading state
* error state
* success feedback
* confirmation untuk destructive action
* responsive layout
* consistent design system

---

# 19. BUSINESS RULES

Implementasikan business rules di service/domain layer jika logic mulai kompleks.

Controller jangan menjadi tempat utama business logic.

Gunakan:

* Form Request untuk validation
* Policy untuk authorization
* Service untuk business operation
* Resource untuk API/structured output jika diperlukan
* Repository hanya jika kompleksitas/query benar-benar membutuhkannya

Jangan membuat abstraction berlebihan.

Reuse existing code terlebih dahulu.

---

# 20. ANALYTICS

Analytics harus berasal dari data operasional nyata.

Minimal:

### Financial

* income
* expense
* net profit
* outstanding receivables
* overdue amount
* cash flow

### Occupancy

* occupied rooms
* available rooms
* maintenance rooms
* occupancy rate
* contract expiry

### Maintenance

* request count
* average resolution time
* maintenance cost
* maintenance cost per room
* recurring problems

### Tenant

* active tenants
* retention
* contract renewal
* move-out
* overdue frequency

### BEP

Gunakan definisi yang konsisten untuk:

* fixed cost
* variable cost
* average rent
* occupancy

Jangan menampilkan BEP sebelum accounting rules tersebut jelas.

---

# 21. DEVELOPMENT PRIORITY

Jangan langsung mengimplementasikan semua fitur.

Priority:

## Phase 1 — Domain Foundation

* tenant application
* user → tenant relationship
* room selection/application
* contract lifecycle
* invoice/payment correction
* deposit lifecycle
* maintenance → expense relationship
* RBAC/policies

## Phase 2 — Tenant Portal

* tenant dashboard
* profile
* room
* contract
* bills
* payment proof
* documents
* requests
* notifications

## Phase 3 — Communication

* in-app notification
* email
* WhatsApp integration
* automated reminders

## Phase 4 — Management Intelligence

* financial analytics
* occupancy
* receivables aging
* maintenance analytics
* BEP
* cash flow projection
* risk indicators

---

# 22. IMPLEMENTATION RULE

Sebelum coding setiap feature:

1. Jelaskan business purpose.
2. Identifikasi actor.
3. Identifikasi state.
4. Identifikasi database impact.
5. Identifikasi authorization.
6. Identifikasi UX flow.
7. Buat acceptance criteria.
8. Implementasikan.
9. Buat/update tests.
10. Jalankan regression test.

Jangan mengubah schema hanya karena "lebih rapi" tanpa kebutuhan bisnis.

Jangan menghapus existing functionality yang sudah berjalan tanpa alasan.

Prioritaskan backward compatibility terhadap F1.

---

# 23. DEFINITION OF DONE

Feature dianggap selesai jika:

* business flow sudah jelas
* UI responsive
* authorization sudah diterapkan
* validation tersedia
* error handling tersedia
* state transition benar
* existing feature tidak rusak
* automated test tersedia untuk business-critical logic
* regression test lulus
* tidak ada obvious N+1 query
* tidak ada unauthorized data exposure
* empty/loading/error/success state tersedia
* UX dapat digunakan oleh user non-teknis

---

# 24. IMPORTANT

Sebelum mengimplementasikan fitur baru, jangan langsung coding.

Terlebih dahulu:

1. inspect existing code
2. inspect existing migrations/models/controllers/policies
3. reuse existing implementation
4. identifikasi conflict dengan F1
5. jelaskan perubahan schema
6. implementasikan perubahan sekecil mungkin
7. test

Jika requirement ambigu, jangan membuat asumsi besar pada database.

Gunakan business flow sebagai source of truth.