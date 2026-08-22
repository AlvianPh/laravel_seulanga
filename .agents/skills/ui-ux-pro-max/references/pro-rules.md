# Pro UI/UX Rules & Visual Polish Checklist

Gunakan checklist ini saat mempercantik atau mereview visual antarmuka:

### 1. Icons & Visual Balance
- **Gunakan SVG Icon yang Seragam**: Jangan mencampur emoji teks dengan SVG icon. Gunakan set konsisten (Heroicons outline/solid 20x20 atau 24x24).
- **Icon Alignment**: Selalu sejajarkan icon vertikal dengan teks (`flex items-center gap-2`).
- **Icon Sizing**: Icon tombol `w-4 h-4` atau `w-5 h-5`. Icon metric card `w-6 h-6` atau `w-8 h-8`.

### 2. Table & Data Lists
- **Header Standar**: Background subtle (`bg-slate-50/80 dark:bg-slate-800/80`), uppercase halus (`text-xs font-semibold text-slate-500 uppercase tracking-wider`).
- **Divider Halus**: Border tipis (`border-b border-slate-150 dark:border-slate-700/60`).
- **Row Hover**: Hover state lembut (`hover:bg-slate-50/60 dark:hover:bg-slate-800/50 transition-colors`).
- **Numeric Alignment**: Kolom harga atau angka rata kanan atau bold dengan format Rp yang konsisten.

### 3. Forms & Inputs
- **Focus Rings**: `focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none`.
- **Border**: Border netral halus (`border-slate-300 dark:border-slate-600`).
- **Input Padding**: `px-3.5 py-2.5 rounded-lg text-sm`.
- **Helper & Errors**: Helper text abu-abu `text-xs text-slate-500`, pesan error merah `text-xs text-rose-500 mt-1.5`.

### 4. Cards & Containers
- **Card Radius**: `rounded-xl` atau `rounded-2xl`.
- **Card Shadow**: `shadow-sm border border-slate-200/80 dark:border-slate-700/60 bg-white dark:bg-slate-800`.
- **Metric / KPI Cards**: Sertakan container icon dengan background pastel (`bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600`), angka besar `text-2xl font-bold`, dan label jelas.
