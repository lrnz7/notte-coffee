# NOTTE ERP — Arsitektur Sistem & Spesifikasi Teknis

> **Penunjukan Sistem:** Mesin ERP & POS Omnichannel NOTTE Coffee  
> **Lingkungan Target:** Laravel 12 / PHP 8.3 / Laragon / MySQL / Tailwind CSS v4 / Leaflet.js / IndexedDB  
> **Penulis:** Arsitek Senior Full-Stack Laravel  
> **Status:** Cetak Biru Arsitektur Tahap Produksi

---

## 1. Gambaran Umum Sistem & Teknologi

**NOTTE ERP** adalah platform gabungan antara *Enterprise Resource Planning* (ERP), *Point of Sale* (POS), dan E-Commerce pelanggan yang dibuat khusus untuk operasional kedai kopi *specialty* dan *F&B*. Sistem ini menjembatani kasir toko *real-time*, pemesanan pelanggan *online* yang asinkron, manajemen stok *Bill of Materials* (BOM) dengan hitungan Harga Rata-rata Bergerak (*Moving Average Costing*), penyeimbang beban Sistem Layar Dapur (KDS), toleransi sistem saat internet mati (*offline-first*), dan laporan laba-rugi otomatis ke dalam satu aplikasi Laravel utuh.

### Tabel Matriks Teknologi & Versi

| Lapisan / Komponen | Teknologi | Spesifikasi / Versi | Tujuan Penggunaan |
| :--- | :--- | :--- | :--- |
| **Framework Backend** | Laravel Framework | `12.x` (PHP `^8.2`, diuji di PHP `8.3`) | Inti aplikasi, perutean, autentikasi ganda, ORM, dan integritas transaksi |
| **Mesin Database** | MySQL / MariaDB (Laragon) | `8.0+` / `10.4+` | Penyimpanan data berelasi standar ACID, penguncian baris pesimistik (*pessimistic row locking*) |
| **Pipa Aset & Build** | Vite + `@tailwindcss/vite` | Vite `^7.0`, Tailwind CSS `^4.0` | Penataan gaya CSS generasi baru, kompilator JIT, dan pengemasan aset |
| **Pemetaan & GPS Frontend** | Leaflet.js | `1.9+` | Perhitungan radius pengiriman, koordinat lokasi yang dititik oleh pelanggan |
| **Penyimpanan Klien & Mesin Offline** | HTML5 IndexedDB (`NottePosDB`) | API Bawaan Browser (Skema v1) | *Cache* lokal untuk katalog menu & sinkronisasi antrean pesanan offline |
| **Manajemen Proses** | Concurrently | `^9.0.1` | Orkestrasi pengembangan multi-proses (HTTP, Queue, Pail, Vite) |
| **Penggerak Sesi & Cache** | Database / File Sessions | Laravel Session Manager | Sesi terisolasi penjaga-ganda (*dual-guard*) dengan perlindungan CSRF |

---

## 2. Arsitektur Database & Representasi ERD

Database dirancang dengan batasan relasi yang ketat, batas transaksi, dan indeks yang dioptimalkan untuk mendukung operasional kasir lalu lintas tinggi dan pemotongan stok gudang secara *real-time*.

### Tabel Inti & Tanggung Jawab Domain

1. **`users`**: Gudang pengguna multi-peran dengan autentikasi ganda (`admin`, `cashier`, `customer`). Menyimpan alamat pengiriman, koordinat pelanggan (`latitude`, `longitude`), dan status diskon.
2. **`materials`**: Katalog stok & inventaris. Melacak bahan mentah (contoh: biji Arabika, susu segar, sirup, cup), level stok saat ini, batas peringatan stok minimum, dan harga modal rata-rata (`unit_price`).
3. **`menus`**: Item katalog produk beserta harga jual, kategorisasi, bendera status aktif, dan gambar produk.
4. **`recipes` (Bill of Materials / BOM)**: Pemetaan *pivot* relasional yang menghubungkan setiap `menu` ke satu atau lebih `materials` (`ingredient_id`) dengan takaran pasti yang dibutuhkan untuk satu porsi (`quantity`).
5. **`orders`**: Buku besar riwayat transaksi dari semua saluran penjualan (`pos`, `online`, `offline_pos`, `merchant`). Menyimpan potret finansial pada saat transaksi terjadi (`total_amount`, `total_cogs`, `total_hpp`, `discount_amount`, `gross_profit`).
6. **`order_items`**: Rincian item spesifik yang terkait dengan sebuah pesanan, mengunci harga jual historis, modal unit (COGS), catatan kustomisasi (level es/gula), dan subtotal.
7. **`cash_flows`**: Buku kas umum untuk pengeluaran operasional (OPEX), pengeluaran non-restok bahan, log pendapatan, dan penyesuaian selisih uang laci kasir (kurang/lebih).
8. **`cashier_closings`**: Catatan rekonsiliasi kas akhir *shift* yang menangkap jumlah uang modal, ekspektasi penjualan sistem (Tunai vs QRIS), hitungan uang fisik, dan selisih akhirnya.
9. **`sessions` & `cache`**: Tabel sistem bawaan framework untuk penyimpanan sesi.

### Diagram Relasi Entitas (Mermaid)

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "membuat / mengelola"
    USERS ||--o{ CASHIER_CLOSINGS : "melakukan rekonsiliasi shift"
    MENUS ||--|{ RECIPES : "terdiri dari (BOM)"
    MATERIALS ||--|{ RECIPES : "digunakan dalam"
    ORDERS ||--|{ ORDER_ITEMS : "berisi"
    MENUS ||--o{ ORDER_ITEMS : "direferensikan oleh"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, cashier, customer"
        string phone
        text address
        string latitude
        string longitude
        boolean has_claimed_welcome_discount
        timestamps created_at_updated_at
    }

    MATERIALS {
        bigint id PK
        string name
        string unit "gram, ml, pcs"
        decimal unit_price "15,2 (Harga Rata-rata)"
        decimal stock_quantity "15,2"
        decimal min_stock_alert "12,2"
        timestamps created_at_updated_at
    }

    MENUS {
        bigint id PK
        string name
        string category
        text description
        decimal selling_price "12,2"
        string image
        boolean is_active
        timestamps created_at_updated_at
    }

    RECIPES {
        bigint id PK
        bigint menu_id FK
        bigint ingredient_id FK "Referensi ke materials.id"
        decimal quantity "10,2 (Takaran per Porsi)"
        timestamps created_at_updated_at
    }

    ORDERS {
        bigint id PK
        bigint user_id FK "Bisa kosong untuk Tamu/Walk-in"
        string invoice_number UK
        string customer_name
        string customer_phone
        text shipping_address
        enum order_type "dine_in, takeaway, delivery, pickup"
        string payment_method "cash, qris, transfer, debit"
        string payment_proof "Disimpan di folder privat"
        enum status "pending, pending_payment, waiting_verification, processing, completed, cancelled"
        string order_source "pos, online, offline_pos, merchant"
        decimal total_amount "12,2"
        decimal total_cogs "12,2"
        decimal total_hpp "12,2"
        decimal discount_amount "12,2"
        decimal gross_profit "12,2"
        timestamps created_at_updated_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint menu_id FK
        int quantity
        decimal price_at_purchase "12,2"
        decimal cogs_at_purchase "12,2"
        string note
        timestamps created_at_updated_at
    }

    CASH_FLOWS {
        bigint id PK
        enum type "inflow (masuk), outflow (keluar)"
        string category "Penjualan, Selisih Kas, OPEX, dll."
        decimal amount "12,2"
        text description
        date date
        timestamps created_at_updated_at
    }

    CASHIER_CLOSINGS {
        bigint id PK
        bigint user_id FK "Referensi kasir"
        datetime closing_time
        decimal opening_cash "12,2"
        decimal system_cash_sales "12,2"
        decimal system_qris_sales "12,2"
        decimal physical_cash_count "12,2"
        decimal cash_difference "12,2 (Fisik - Ekspektasi)"
        text notes
        timestamps created_at_updated_at
    }