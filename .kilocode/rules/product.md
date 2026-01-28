# Product Rules & Guidelines

## 1. Overview Project
Aplikasi ini adalah sistem manajemen logistik dan keagenan terpadu yang dirancang untuk mengelola operasi pengiriman (Shipment), manajemen agen (Agent), dan administrasi rekening/keuangan secara efisien. Proyek ini mengutamakan kecepatan, keamanan data, dan antarmuka pengguna (UI/UX) yang modern dan responsif.

## 2. Tech Stack Standard
Proyek ini dibangun di atas fondasi teknologi berikut yang **wajib** dipatuhi:

- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Frontend Stack**: 
  - Livewire 4 (Reactive Components)
  - Flux UI (Component Library utama - **Wajib digunakan**)
  - Tailwind CSS v4 (Styling)
- **Database**: MySQL
- **Testing**: Pest PHP (v4)
- **Tools Tambahan**:
  - `maatwebsite/excel` (Export Data)
  - `barryvdh/laravel-dompdf` (PDF Generation)
  - `dedoc/scramble` (API Docs)

## 3. Design & UX Guidelines
Setiap implementasi antarmuka harus mengikuti prinsip berikut untuk mencapai "Wow Factor":

*   **Aesthetics (Estetika)**: Desain harus terlihat premium, bersih, dan profesional. Hindari tampilan yang kaku atau generik.
*   **Flux UI First**: Selalu prioritaskan penggunaan komponen Flux UI resmi (`<flux:button>`, `<flux:table>`, `<flux:input>`) sebelum membuat komponen kustom. Konsistensi desain sangat penting.
*   **Dark Mode Support**: Seluruh halaman **wajib** mendukung mode gelap (Dark Mode) dan terang (Light Mode) dengan transisi yang halus.
*   **Responsiveness**: Mobile-first design. Pastikan tampilan optimal di desktop, tablet, dan mobile.
*   **Interactivity**: 
    - Gunakan `wire:loading` untuk indikator visual saat proses berjalan.
    - Berikan feedback instan (Toast notification) untuk setiap aksi sukses/gagal.

## 4. Business Rules & Features

### A. Shipment Management (Pengiriman)
*   **Tracking**: Integrasi API (mis: KlikResi) untuk pelacakan status resi real-time.
*   **Data Accuracy**: Data berat, dimensi, dan status pengiriman harus valid.
*   **History**: Log setiap perubahan status pengiriman harus tercatat.
*   **Export**: Fitur export data (PDF/Excel) wajib ada untuk kebutuhan laporan/manifest.

### B. Agent Management
*   **Dashboard**: Menyediakan widget statistik (Total Paket, Komisi, dll) yang informatif.
*   **Security**: Validasi ketat pada registrasi dan login. Kode Agen bersifat **Case Sensitive**.
*   **Support**: Modul Help Desk/Ticketing untuk komunikasi antara Agen dan Admin.

### C. Financial (Rekening)
*   **Precision**: Perhitungan finansial harus presisi (gunakan tipe data yang tepat, hindari floating point error untuk mata uang).
*   **Privacy**: Data rekening sensitif harus diproteksi sesuai standar keamanan.

## 5. Coding Standards & Conventions

### Bahasa (Language)
*   **Codebase (Variabel, Fungsi, Class, Komentar)**: Bahasa Inggris (English).
*   **User Interface (Label, Pesan Error, Notifikasi)**: Bahasa Indonesia. Gunakan bahasa yang baku, sopan, dan mudah dimengerti.

### Livewire Guidelines
*   Gunakan `Form Objects` atau `Form Requests` untuk validasi input yang kompleks.
*   Hindari logika bisnis yang berat di dalam Component `mount` atau `render`. Pindahkan ke `Service Class` atau `Action Class`.
*   Gunakan `wire:navigate` untuk perpindahan halaman yang SPA-like (cepat dan halus).

### Database & Eloquent
*   **Eager Loading**: Wajib menggunakan `with()` saat mengambil data relasi untuk mencegah masalah N+1 Query.
*   **Migrations**: Jangan mengubah file migrasi yang sudah dijalankan di production. Buat migrasi baru untuk perubahan skema.

### Testing
*   Setiap fitur kritikal (Login, Create Shipment, Financial Transaction) harus memiliki **Feature Test** menggunakan Pest.
*   Gunakan **Browser Test** untuk memastikan alur UI berfungsi dengan baik.

## 6. Maintenance
*   Jalankan `pint` (Laravel Pint) sebelum commit untuk memastikan format kode standar.
*   Dokumentasikan endpoint API baru atau perubahan logika bisnis yang signifikan di file terkait atau changelog.
