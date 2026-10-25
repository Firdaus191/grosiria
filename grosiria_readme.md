# 🛒 Grosiria - E-Grocery, Customer Loyalty & Omnichannel Retail

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v5-FBBF24?style=for-the-badge&logo=filament&logoColor=black)](https://filamentphp.com)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

Grosiria adalah aplikasi web manajemen *e-grocery* berbasis sistem *omnichannel* yang memungkinkan pelanggan berbelanja secara daring dengan opsi pengiriman (Delivery) atau pengambilan mandiri di toko (Pickup Kasir). Proyek ini dirancang secara arsitektural untuk memenuhi standar evaluasi **UTS - Pengujian Endpoint T08**.

Aplikasi ini dilengkapi dengan **G-Poin Loyalty System**, mesin rekomendasi statis (*Static Recommendation Engine*), serta Dasbor Admin interaktif (*real-time polling*) yang mematuhi standar pembagian wilayah kerja (Git Flow).

---

## 🚀 Arsitektur & Spesifikasi Teknologi

Proyek ini dibangun menggunakan *stack* teknologi modern dengan pembagian entitas bisnis yang ketat sesuai dengan [ERD Grosiria (13 Tabel) & Modul Git Flow](docs/):

*   **Core Framework:** Laravel 11.x (PHP 8.2+)
*   **Admin Back-Office:** FilamentPHP v5 (Mendukung lokalisasi Rupiah/IDR secara global)
*   **API Authentication:** Laravel Sanctum (Stateful Cookie & Token-based)
*   **Database:** MySQL (Relasi ketat dengan `CASCADE` dan `RESTRICT` via Migration)
*   **Mock Payment Gateway:** Midtrans Sandbox (Simulasi Webhook)
*   **Frontend UI:** Tailwind CSS, Alpine.js, & Swiper.js (Proses *Slicing*)

---

## 🎯 Fitur Inti Berdasarkan Swimlane Flowchart

1.  **Omnichannel Fulfillment:**
    *   **Pickup:** Pelanggan mendapatkan `pickup_code` (PIN) unik saat <em>checkout</em> yang tervalidasi langsung oleh Kasir di dasbor admin.
    *   **Delivery:** Integrasi status pesanan dari `pending_payment` hingga `on_delivery`.
2.  **Loyalty System & Voucher Marketplace:**
    *   Mekanisme transaksi poin (`earn` dan `redeem`) yang dilindungi menggunakan `DB::transaction()` untuk mencegah in-konsistensi data (Race Conditions).
3.  **Real-Time Admin Dashboard:**
    *   Widget *polling* otomatis memantau omzet, antrean pesanan, peringatan stok menipis (< 20 pcs), dan sirkulasi G-Poin tanpa membebani <em>server</em> (Optimasi Eager Loading).
4.  **Robust API Endpoint (T08 UTS Validation):**
    *   Endpoint `/api/v1/users` dilindungi oleh *Rate Limiter* (60 request/menit).
    *   Menjamin balasan struktur JSON mutlak untuk seluruh skenario pengujian: HTTP `200 OK` (Sukses), `404 Not Found` (Typo URL), `405 Method Not Allowed` (Salah HTTP Method), dan `500 Internal Server Error` (Koneksi DB Terputus).

---

## 🛠 Panduan Instalasi Lokal (Setup Environment)

Bagi anggota tim pengembang atau dosen penguji yang ingin menjalankan aplikasi Grosiria di komputer lokal (Laragon/XAMPP), ikuti langkah-langkah standar berikut:

### 1. Kloning Repositori
```bash
git clone https://github.com/Firdaus191/grosiria.git
cd grosiria
```

### 2. Instalasi Dependensi & Konfigurasi
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Konfigurasi Basis Data (`.env`)
Pastikan Laragon/MySQL berjalan, lalu buat *database* kosong bernama `db_grosiria`. Buka file `.env` dan pastikan konfigurasi berikut aktif untuk mencegah *crash* saat simulasi server mati (TC-05):
```env
DB_CONNECTION=mysql
DB_DATABASE=db_grosiria
SESSION_DRIVER=file
CACHE_STORE=file
```

### 4. Eksekusi Skema Database & Data Awal (Seeder)
Perintah ini akan membangun 13 tabel relasional dan menyuntikkan data uji (termasuk 3 tipe *user*, 14 kategori, 16 produk, dan sampel pesanan Omnichannel).
```bash
php artisan migrate:fresh --seed
```

### 5. Jalankan Server
```bash
php artisan optimize:clear
php artisan serve
```
*Akses Panel Admin di `http://127.0.0.1:8000/admin` menggunakan:*
*   **Email:** `admin@grosiria.test`
*   **Password:** `password123`

---

## 🧪 Skenario Pengujian Endpoint (T08 UTS)

Sesuai dengan lampiran `T08 - Pengujian Endpoint UTS.xlsx`, lakukan pengujian berikut menggunakan aplikasi **Postman** pada *endpoint* `GET http://127.0.0.1:8000/api/v1/users` dengan header `Accept: application/json`:

| ID Test | Kondisi Skenario | Ekspektasi Status HTTP |
| :--- | :--- | :--- |
| **TC-01** | Mengambil data saat tabel `users` terisi | `200 OK` (JSON Array) |
| **TC-02** | Mengambil data saat tabel `users` kosong | `200 OK` (JSON Empty Array) |
| **TC-03** | Typo URL (Misal: `/api/v1/userr`) | `404 Not Found` (JSON Error) |
| **TC-04** | Menggunakan method `POST` dengan body `{}` | `405 Method Not Allowed` (JSON Error) |
| **TC-05** | *Service* MySQL dimatikan secara paksa | `500 Internal Server Error` (JSON Error) |

---

## 📁 Dokumentasi Arsitektur (*Single Source of Truth*)

Seluruh artefak akademik dan panduan rekayasa perangkat lunak tim tersimpan secara rapi pada direktori `/docs/`:
1.  **[Skema Database & ERD](/docs/database/)** (`schema_grosiria.sql` & `erd_grosiria_final.jpg`)
2.  **[Swimlane Flowchart](/docs/flowchart/)** (`flowchart_grosiria.jpg`)
3.  **[Prototipe UI/UX](/docs/ui-prototype/)** (`ui_grosiria.html`)
4.  **[SOP Git Flow & Pull Request](/docs/)** (Panduan Kolaborasi Tim Edisi PDF)