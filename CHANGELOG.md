# Changelog

All notable changes to this project will be documented in this file.

## [0.0.1] - 2026-09-22

### Added
- Setup awal project e-commerce dan ERP **NOTTE Coffee** (Laravel 12 + Tailwind CSS).
- Struktur database relasi *many-to-many* antara Menu dan Bahan Baku (`menu_materials`) untuk kalkulasi HPP otomatis (BOM).
- Fitur manajemen bahan baku (Materials) CRUD.
- Fitur manajemen katalog menu & resep HPP (Create, Edit, Delete).
- Fitur Kasir POS Offline (dengan pemotongan stok bahan baku otomatis).
- Fitur e-commerce publik (Landing page, Katalog menu, Modifikasi pesanan Ice & Sugar level, dan sistem Checkout).
- Fitur manajemen pesanan online dan update status pesanan admin.


## [0.0.2] - 2026-09-22

### Added
- Fitur upload foto produk menu di modul Admin (support format JPG, PNG, WEBP max 2MB)[cite: 4, 5].
- Fitur preview foto produk lama di form Edit Menu[cite: 5].
- Fitur auto-delete foto produk lama dari storage saat foto di-update atau menu dihapus[cite: 4].
- Box alert penanganan error validasi input pada form Edit Menu (`edit.blade.php`)[cite: 5].
- Drawer panel **Riwayat POS Offline** di pojok kanan atas halaman kasir untuk memantau 20 transaksi *walk-in* terakhir secara *real-time*.
- Modul **Laporan Keuangan & Arus Kas** lengkap dengan visualisasi grafik interaktif (Line Chart untuk tren kas harian & Doughnut Chart untuk breakdown pengeluaran) menggunakan Chart.js.
- Fitur integrasi otomatis omset transaksi POS *offline* (`NOTTE-POS-`) ke dalam total pemasukan dan grafik arus kas.
- Fitur pencatatan transaksi kas manual (Inflow/Outflow) di panel admin.

### Fixed
- Fix bug `ViewCompilationException` (*Malformed @foreach / @forelse*) di file `edit.blade.php`[cite: 5].
- Fix masalah *silent validation redirect* / reload tanpa respon saat tombol "Simpan Perubahan" diklik[cite: 4, 5].
- Perbaikan sinkronisasi relasi pivot resep HPP/BOM agar penanganan array ID bahan baku lebih fleksibel[cite: 4].
- Perbaikan tulisan tautan navbar pada `landing.blade.php` dari "LOCATIONS" menjadi "FAQ" agar selaras dengan rute halaman.
- Fix error SQL pada transaksi POS akibat kolom database yang belum terisi otomatis (HPP, laba kotor, dan varian rasa).
- Pemisahan jalur data antara Pesanan Online e-commerce dan Kasir POS (`NOTTE-POS-`) pada modul manajemen pesanan admin agar tidak tercampur.
- Perbaikan tombol "Kembali" dinamis pada detail pesanan (`orders/show.blade.php`) agar mengarahkan kembali ke POS Kasir atau Pesanan Online secara akurat.
- Perbaikan highlight menu aktif di sidebar admin menggunakan helper `request()->routeIs()`.
- Fix error namespace, inheritance `Controller`, dan perbedaan kapitalisasi model pada `CashFlowController`.
- Fix *SQLSTATE data truncation* pada kolom `type` tabel `cash_flows` dengan mapping otomatis dari form ke format database (`income`/`expense`).