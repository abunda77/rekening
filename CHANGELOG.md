# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan di file ini.

## [Unreleased]

### Added
- Kolom **VIP** (Yes/No) pada manajemen Rekening dengan badge, pengurutan, dan field `flux:switch` di formulir
- Pilihan bank pada formulir pendaftaran Rekening/Nasabah
- Integrasi `livewireblaze` (Livewire Blaze)
- Fitur Catatan (Notes) untuk informasi tambahan
- Fitur Backup Database dengan tombol unduh di dashboard admin
- Tombol lonceng notifikasi (Notification Bell) pada dashboard Agent
- Konfigurasi Laravel Octane (`octane.config`)
- Panel pengiriman (Shipment) di portal Agent
- Otorisasi berbasis Spatie Roles & Permissions untuk model `User`
- Pengecekan resi pengiriman dan tabel shipment pada widget dashboard
- Widget dashboard tambahan untuk informasi ringkas
- Toggle mode gelap/terang (dark/light mode) di dashboard admin

### Changed
- Penambahan kolom `is_vip` (boolean) pada tabel `accounts` melalui migrasi baru
- Pembaruan dokumen panduan `agents.md` & `claude.md`
- Penyempurnaan template AI Guidelines dan Skill Laravel Rekening
- Perubahan label menu dari "Agent" menjadi "User"
- Perubahan istilah teks dari "Customer" ke "Nasabah" untuk penyesuaian pasar Indonesia
- Refaktor halaman login untuk admin
- Pembaruan halaman login untuk agen
- Penambahan tanggal kedaluwarsa (Expired At) pada akun rekening
- Pembaruan icon menu di sidebar
- Pembaruan modul `laravel-boost` (laravel boost)
- Penggunaan UUID sebagai `user_id` di sesi aplikasi

### Fixed
- Perbaikan bug saat menghapus user (delete user)
- Perbaikan masalah keamanan pada dependensi Composer
- Perbaikan tombol unduh backup database di dashboard admin
- Perbaikan layout formulir login agar lebih responsif dan ramah pengguna
- Penambahan badge angka keluhan/notification pada menu compliant di sidebar
- Perbaikan rute login agen pada halaman welcome
