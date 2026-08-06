# 🏍️ Sistem Informasi Inventory Sparepart Motor — Yamaha Mataram Sakti

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![BNSP Certified](https://img.shields.io/badge/BNSP-Database_Administrator-blue?style=for-the-badge)

Aplikasi web manajemen inventaris suku cadang (sparepart) motor yang dikembangkan untuk **Yamaha Mataram Sakti**. Sistem ini mengatasi kendala pencatatan manual, mengurangi risiko kesalahan manusia (*human error*), serta memberikan pemantauan stok secara *real-time* berbasis web.

> **Proyek Klaster Sertifikasi Profesi BNSP Database Administrator**  
> *Skema Sertifikasi: SKM-2023-70209-09 / SKM-2025-60102-19*

---

## 🚀 Fitur Utama

- 🔐 **Role-Based Access Control (RBAC)**: Pembatasan hak akses berbasis peran pengguna (Admin Gudang & Owner).
- 📦 **Pengelolaan Data Sparepart (CRUD)**: Tambah, ubah, hapus, lihat, dan pencarian data suku cadang secara terstruktur.
- 🔄 **Transaksi Barang Masuk & Keluar**: Pencatatan otomatis yang langsung memperbarui jumlah stok barang.
- ⚠️ **Sistem Peringatan "Stok Kritis"**: Notifikasi visual pendukung keputusan untuk mencegah terjadinya *stockout* atau *overstock*.
- 📊 **Dashboard Analytics & Visual Monitoring**: Pemantauan statistik transaksi dan grafik barang secara visual.
- 📥 **Integrasi Data CSV & Excel**: Mendukung impor data anggota/sparepart dan ekspor laporan transaksi.
- 🗄️ **Manajemen Basis Data & Automated SQL Backup**: Fitur pencadangan data otomatis ke format `.sql`.
- 🔍 **Query SQL Tingkat Lanjut**: Implementasi query `INNER JOIN`, `LEFT JOIN`, `RIGHT JOIN`, dan `FULL OUTER JOIN` untuk validasi data dan pembuatan laporan.

---

## 🛠️ Teknologi yang Digunakan

- **Framework Backend**: Laravel 12 (PHP 8.2+)
- **Database Management**: MySQL & phpMyAdmin
- **Frontend & Styling**: Tailwind CSS, Bootstrap, Blade Templating
- **Development Environment**: Laragon / XAMPP (Local Server)
- **Version Control**: Git & GitHub

---

## 🖼️ Tampilan Aplikasi (Screenshots)

### 1. Halaman Login & Otentikasi
<img src="screenshots/Screenshot 2026-07-10 225427.png" alt="Tampilan Login" width="100%"/>

---

### 2. Dashboard Monitoring & Grafik Analytics
<img src="screenshots/Screenshot 2026-07-10 230214.png" alt="Dashboard Admin" width="100%"/>

---

### 3. Pengelolaan Data Sparepart (CRUD)
<img src="screenshots/Screenshot 2026-07-10 230715.png" alt="Data Sparepart" width="100%"/>

---

### 4. Transaksi Barang Masuk & Keluar
<img src="screenshots/Screenshot 2026-07-11 000840.png" alt="Barang Masuk" width="100%"/>

---

### 5. Peringatan Fitur Stok Kritis & Transaksi Real-time
<img src="screenshots/Screenshot 2026-07-11 001001.png" alt="Stok Kritis" width="100%"/>

---

### 6. Pengujian Query SQL Lanjut (phpMyAdmin)
<img src="screenshots/Screenshot 2026-07-12 190634.png" alt="Query SQL INNER JOIN" width="100%"/>

<img src="screenshots/Screenshot 2026-07-12 191106.png" alt="Query SQL LEFT JOIN" width="100%"/>

---

### 7. Pengujian Hak Akses Owner & Modul Backup Database
<img src="screenshots/Screenshot 2026-07-12 195240.png" alt="Dashboard Owner" width="100%"/>

<img src="screenshots/Screenshot 2026-07-12 213555.png" alt="Backup Database SQL" width="100%"/>

---

## 💻 Panduan Instalasi Lokal

Jika ingin menjalankan proyek ini secara lokal di PC Anda:

1. **Clone repository ini**:
   ```bash
   git clone https://github.com/dzakimansiz3-dmz/inventory-yamaha-mataram-sakti.git
   cd inventory-yamaha-mataram-sakti
