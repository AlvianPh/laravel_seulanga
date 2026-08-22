# Quick Reference — UI/UX Rules & Guidelines

### 1. Accessibility (CRITICAL)
- **Contrast**: Minimal rasio 4.5:1 untuk teks normal dan 3:1 untuk teks besar/badge.
- **Focus Rings**: Tampilkan ring fokus yang jelas (2–4px offset) saat navigasi keyboard.
- **Icon Buttons**: Berikan `title` atau `aria-label` untuk tombol icon-only.
- **Color Independence**: Jangan mengandalkan warna saja untuk menyampaikan status (tambahkan icon atau teks label).

### 2. Layout & Spacing
- **Container**: Card container dengan sudut membulat modern (`rounded-xl` atau `rounded-2xl`).
- **Inner Padding**: Gunakan padding yang lega (`p-6` atau `p-8` di desktop, `p-4` di mobile).
- **Rhythm**: Gunakan skala margin/gap kelipatan 4/8 (`gap-4`, `gap-6`, `space-y-6`).
- **Table Density**: Berikan vertical padding yang nyaman pada baris tabel (`py-3.5` atau `py-4`) dan font size yang proporsional (`text-sm`).

### 3. Typography & Hierarchy
- **Title / H1**: 24px–30px (`text-2xl` / `text-3xl`), font-bold, warna `#0f172a` (light) / `#f8fafc` (dark).
- **Subheadings / H2-H3**: 16px–20px (`text-lg` / `text-xl`), font-semibold, warna `#334155` / `#e2e8f0`.
- **Body**: 14px–15px (`text-sm` / `text-base`), line-height 1.5–1.6, warna `#475569` / `#94a3b8`.
- **Captions / Meta**: 12px (`text-xs`), font-medium, warna `#64748b` / `#64748b`.

### 4. Color Tokens (SaaS & Property Management)
- **Primary**: Brand Indigo / Royal Blue (`#4f46e5` - `#4338ca`) dengan hover state tegas.
- **Success / Positive**: Emerald / Mint Green (`#10b981` / `#059669`).
- **Warning / Pending**: Amber / Warm Gold (`#f59e0b` / `#d97706`).
- **Danger / Critical**: Rose / Crimson (`#ef4444` / `#dc2626`).
- **Neutral Backgrounds**:
  - Light: Canvas `#f8fafc` (Slate 50), Card `#ffffff`, Border `#e2e8f0` (Slate 200).
  - Dark: Canvas `#0f172a` (Slate 900), Card `#1e293b` (Slate 800), Border `#334155` (Slate 700).

### 5. Buttons & Interactive Controls
- **Primary**: Solid background dengan subtle shadow (`shadow-sm shadow-indigo-200 dark:shadow-none`), white text.
- **Secondary**: Subtle border, background hover lembut (`hover:bg-slate-100 dark:hover:bg-slate-700`).
- **Action Links in Table**: Gunakan badge atau ghost button (`px-2.5 py-1 rounded-md text-xs font-medium`) daripada teks polos garis bawah yang tampak kuno.
- **Micro-Transitions**: `transition duration-150 ease-in-out`.
