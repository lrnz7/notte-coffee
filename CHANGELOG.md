# 📜 Catatan Perubahan (Changelog) NOTTE ERP

Semua riwayat pengembangan, perbaikan *bug*, dan penambahan fitur aplikasi NOTTE Coffee dicatat secara rapi di sini.

---

## 🚀 [0.0.1] - 22 September 2026
*Peluncuran Pondasi Awal Sistem E-Commerce & Kasir*

### ➕ Added (Fitur Baru)
- **Setup Project Awal:** Inisialisasi awal repositori Laravel 12 + Tailwind CSS untuk aplikasi e-commerce dan panel admin ERP NOTTE Coffee.
- **Relasi BOM (Bill of Materials):** Membuat tabel pivot `menu_materials` (relasi *many-to-many* antara tabel `menus` dan `materials`) untuk kalkulasi Harga Pokok Penjualan (HPP) otomatis berdasarkan racikan resep.
- **Modul Kelola Bahan Baku (`MaterialController.php`):** Fitur CRUD (Create, Read, Update, Delete) untuk manajemen stok dan harga beli bahan baku dapur.
- **Modul Kelola Menu & Resep (`MenuController.php`):** Form pembuatan menu baru lengkap dengan penetapan resep bahan baku dan harga jual.
- **Kasir POS Offline Sederhana (`PosController.php`):** Fitur kasir toko *walk-in* yang langsung memotong jumlah stok bahan baku di tabel `materials` secara otomatis tiap ada transaksi.
- **E-Commerce Publik Pelanggan (`CustomerController.php`):** Halaman catalog (`store.blade.php` & `menu.blade.php`), fitur kustomisasi pesanan (*Ice & Sugar level*), dan form *checkout* pelanggan.
- **Manajemen Pesanan Admin (`OrderController.php`):** Panel untuk memantau pesanan masuk dan mengubah status pesanan (`pending`, `processing`, `completed`).

---

## 🛠️ [0.0.2] - 22 September 2026
*Laporan Keuangan, Upload Foto, dan Pemisahan Kasir vs Web*

### ➕ Added (Fitur Baru)
- **Upload Foto Produk (`MenuController.php`):** Penanganan upload berkas gambar produk (JPG, PNG, WEBP max 2MB) dengan simpan otomatis ke `storage/app/public/menus`.
- **Pratinjau Foto & Auto-Delete (`edit.blade.php`):** Pratinjau foto lama saat edit menu, serta penghapusan fisik berkas foto dari storage via `Storage::delete()` saat gambar diubah atau menu dihapus.
- **Drawer Riwayat POS (`pos/index.blade.php`):** Panel *slide-out* di pojok kasir untuk memantau 20 transaksi *walk-in* terakhir (`NOTTE-POS-`) secara *real-time*.
- **Laporan Arus Kas Visual (`CashFlowController.php`):** Pencatatan transaksi manual (*Inflow/Outflow*) dan grafik interaktif Chart.js (*Line Chart* tren harian & *Doughnut Chart* kategori biaya).

### 🔧 Fixed (Perbaikan Bug)
- **Pemisahan Data POS vs Web (`OrderController.php`):** Pemisahan query antara transaksi web pelanggan dan kasir toko (`NOTTE-POS-`) agar laporan penjualan tidak bercampur.
- **Fix Error Blade Compilation (`edit.blade.php`):** Memperbaiki error `ViewCompilationException` (*Malformed @foreach / @forelse*) di form edit menu.
- **Fix Validation Silent Redirect:** Penanganan validasi request di `MenuController.php` agar form tidak reload sendiri tanpa pesan error.
- **Fix SQL Truncation & Enum Mapping (`CashFlowController.php`):** Memperbaiki error `SQLSTATE[22001]` pada kolom `type` tabel `cash_flows` dengan pemetaan otomatis input form ke format database (`income`/`expense`).

---

## 💳 [0.0.3] - 22 September 2026
*Sistem Bayar QRIS, Upload Bukti Transfer, dan Tracking Pesanan*

### ➕ Added (Fitur Baru)
- **Verifikasi QRIS & Status Baru (`OrderController.php`):** Menambahkan status transaksi baru `pending_payment` dan `waiting_verification` untuk alur pembayaran QRIS online.
- **Dropzone Upload Bukti Bayar (`track.blade.php`):** Fitur *upload screenshot* transfer (JPG/PNG max 5MB) di halaman invoice pembeli.
- **Auto-Create Directory Logic:** Pembuatan folder otomatis `public/uploads/payment_proofs` di backend via `File::makeDirectory()` jika folder belum ada.
- **Sticky Live Tracker Bar (`menu.blade.php`):** Bar melayang di bagian bawah katalog web pembeli yang mendeteksi pesanan aktif berdasarkan `session('active_order_id')`.
- **Real-Time Polling Tracker (`track.blade.php`):** Fitur AJAX *polling* otomatis tiap 5 detik untuk cek perubahan status pesanan di database tanpa *refresh* halaman.
- **Panel Verifikasi Admin (`orders/show.blade.php`):** Pratinjau gambar bukti bayar di detail pesanan admin dengan tombol aksi setujui (*potong stok*) atau tolak (*batal*).

### 🔄 Changed (Penyesuaian)
- **Konfigurasi Timezone (`config/app.php`):** Mengubah setelan zona waktu aplikasi dari `UTC` menjadi `Asia/Jakarta` (WIB) agar pencatatan waktu transaksi presisi.

---

## 🗺️ [1.0.0] - 23 September 2026
*Deteksi Radius Pengiriman (GPS) & Jalur Transaksi Ojek Online*

### ➕ Added (Fitur Baru)
- **Modul Radius Leaflet.js (`menu.blade.php`):**
  - Mengunci koordinat toko NOTTE Jatimurni (`Lat: -6.31971, Lng: 106.92484`) sebagai acuan titik $0\text{ KM}$.
  - Fitur sensor GPS browser (`navigator.geolocation`) dan *Drag & Drop Pin* pada peta interaktif Leaflet.
  - Validasi Radius: Jarak $\le 3\text{ KM}$ dapat Ongkir Rp0; Jarak $> 3\text{ KM}$ mengunci tombol *checkout* dan mengalihkan ke ShopeeFood.
  - Modal Pop-Up PC (Hybrid ShopeeFood) khusus pengakses dari browser PC/Desktop.
- **Kanal Transaksi POS (`PosController.php`):** Dropdown pilihan sumber transaksi pada kasir (*Offline POS*, *ShopeeFood*, *GoFood*, *GrabFood Merchant*) yang disimpan ke kolom `order_source` di tabel `orders`.
- **Filter Dashboard ERP (`AdminController.php`):** Tab navigasi dinamis (Alpine.js) pada tabel transaksi untuk menyaring data berdasarkan `order_source`.
- **Migrasi Database (`orders` & `users`):** Menambahkan kolom `order_source` dan `delivery_address` pada tabel `orders`, serta koordinat lokasi pada tabel `users`.

---

## ⚡ [1.0.1] - 23 September 2026
*Sistem Anti-Tumbang, Layar Dapur (KDS), dan Getar HP Pembeli*

### ➕ Added (Fitur Baru)
- **Auto-Throttle Antrean Dapur (`OrderController.php`):** Sistem otomatis menolak pesanan baru jika jumlah transaksi berstatus `processing` di dapur sudah mencapai batas **5 pesanan**.
- **Live KDS / Kitchen Display System (`pos/index.blade.php`):** Panel antrean dapur interaktif di layar POS yang menarik data 5 pesanan aktif tiap 5 detik via AJAX *polling*.
- **Mobile Card View (`admin/orders/index.blade.php`):** Transformasi tampilan tabel manajemen pesanan dan bahan baku dari tabel HTML kaku menjadi susunan kartu bertumpuk di layar HP.
- **Green Handoff & Vibration API (`track.blade.php`):** Saat pesanan diubah ke `completed`, layar HP pembeli berubah hijau *fullscreen* dan bergetar otomatis via JavaScript `navigator.vibrate()`.

### 🔧 Fixed & Security (Perbaikan & Keamanan)
- **Pessimistic Locking (`lockForUpdate()`):** Penerapan `DB::table('materials')->lockForUpdate()` pada `CustomerController.php` dan `PosController.php` untuk mengunci baris database saat transaksi terjadi biar stok tidak tekor/minus saat *traffic* padat.
- **Keseragaman Alur POS:** Transaksi POS tidak lagi *auto-complete* secara *hardcode*, melainkan masuk ke status `processing` terlebih dahulu agar dapur punya antrean yang seragam.

---

## 🛡️ [1.1.1] - 26 September 2026
*Keamanan Akun, Hitung Laba Bersih (P&L), dan Proteksi Server*

### ➕ Added (Fitur Baru)
- **Dual-Guard Security Isolation (`config/auth.php`):** Pemisahan total autentikasi antara guard `web` (Pelanggan) dan `admin` (Staf/Admin) untuk mencegah kebocoran sesi login.
- **Private Storage Shield (`OrderController.php`):** Pemindahan lokasi simpan bukti bayar dari direktori publik ke folder privat `Storage::disk('local')` yang hanya bisa diakses via rute khusus terautentikasi `auth:admin`.
- **Analitik Laba Bersih / P&L (`AdminController.php`):** Formula kalkulasi finansial lengkap di backend:
  - *Gross Profit*: Omzet Penjualan Selesai - Total HPP Snapshot.
  - *Opex*: Total Pengeluaran Kas Operasional.
  - *Net Profit*: Laba Kotor - Opex.
- **Dynamic Kitchen Queue (`.env`):** Mengubah batas antrean dapur dari angka *hardcoded* menjadi variabel terkonfigurasi `MAX_KITCHEN_QUEUE` di file `.env`.

### 🔧 Fixed (Perbaikan Bug)
- **Fix Double-Inflow Bug (`OrderController.php`):** Penguncian penulisan arus kas otomatis di `updateStatus` khusus untuk `order_source === 'online'` agar transaksi POS tidak tercatat ganda.
- **Weighted Average Cost Engine (`MaterialController.php`):** Pembelian stok baru (*restock*) menggunakan rumus rata-rata tertimbang (*weighted average*) untuk memperbarui `unit_price`, serta mengunci `$oldUnitPrice` lama saat *spoilage* (barang rusak) agar HPP tidak hancur.
- **Eliminasi N+1 Query (`OrderController.php`):** Menerapkan *Eager Loading* `$order->load(['orderItems.menu.recipes.material'])` untuk menghemat query database.
- **Linux Deployment Case-Sensitivity Fix:** Mengubah penamaan file dari `CrmContoller.php` menjadi `CrmController.php` agar tidak error *Fatal Class Not Found* saat di-deploy di server Linux (Ubuntu/Debian).

---

## 📊 [2.0.0] - 28 September 2026
*Filter Tanggal Analitik, Closing Shift Kasir, POS Mode Offline & Clean UI*

### ➕ Added (Fitur Baru)
- **Filter Rentang Waktu (`app/Traits/FilterableByDate.php`):**
  - Trait terpusat untuk menangani filter tanggal multi-granulitas: `today` (per jam `00:00 - 23:00`), `this_week` (per hari), `this_month` (per tanggal), `this_year` (per bulan), `all_time`, dan `custom` (`start_date` s/d `end_date`).
  - Diintegrasikan ke `AdminController.php`, `CashFlowController.php`, dan `OrderController.php` dengan mempertahankan query parameter paginasi via `->appends($request->all())`.
- **Live Visual Charting Re-rendering (`Chart.js`):** Sumbu X/Y grafik tren penjualan, arus kas, dan *donut chart* (Omnichannel & Biaya) otomatis menggambar ulang data sesuai rentang tanggal yang dipilih.
- **Closing Shift Kasir (`CashierClosing.php` & `PosController@closeShift`):**
  - Tabel migrasi `cashier_closings` untuk mencatat audit trail penutupan kasir.
  - Form opname kasir di POS dengan rekonsiliasi otomatis: $\text{Fisik} - (\text{Kas Awal} + \text{Penjualan Tunai Sistem})$.
  - Auto-journaling selisih kas (*Shortage/Surplus*) secara otomatis ke tabel `cash_flows` dengan kategori `'Selisih Kas Kasir'`.
- **POS Mode Offline & Local Sync Engine (`public/js/pos-offline-sync.js`):**
  - Modul JavaScript `NottePosDB` menggunakan `IndexedDB` browser untuk menyimpan *cache* katalog menu dan antrean pesanan offline (`offline_orders`).
  - Generasi invoice sementara `OFFLINE-POS-` saat internet mati total.
  - Auto-sync via rute `POST /admin/pos/sync-offline` saat koneksi pulih: eksekusi batch terisolasi dalam `DB::beginTransaction()` dengan `lockForUpdate()`, pemotongan stok, kalkulasi HPP *real-time*, dan penulisan arus kas.
- **Strict UI Audit & Anti-AI Slop Cleanup:**
  - **Admin ERP & POS (`pos/index.blade.php`):** Pengantian badge pastel dan animasi kedap-kedip `animate-pulse` dengan *Sleek Slate/Dark Utility Style*, penggunaan sudut tegas (`rounded-md`), dan pembersihan emoji/teks *fluff*.
  - **Customer Frontend (`menu.blade.php`, `store.blade.php`, `track.blade.php`):** Pemangkasan *oversized rounded* (`rounded-2xl/3xl` ke `rounded-md/lg`), perbaikan *heavy shadow*, serta penyelarasan penuh tombol *navbar* `/menu` (`rounded-full`) agar persis sama dengan *landing page*.


## 🚀 [2.1.0] - 04 October 2026
*Auth Guard Keranjang, Peningkatan UI Live Tracker, Gross Revenue, dan Patch 419*

### ➕ Added (Fitur Baru)
- **Auth Guard Keranjang Belanja (`menu.blade.php`):** Proteksi akses *frontend* yang mencegah pengguna *guest* (belum login) menambahkan menu ke keranjang atau membuka modal varian, serta pengalihan otomatis ke halaman *login* dengan parameter khusus.
- **Post-Login Smart Redirect (`AuthController.php` & `login.blade.php`):** Fitur penelusuran niat (*intent*) pengguna yang mengarahkan kembali pelanggan ke halaman katalog menu disertai pesan sambutan sukses setelah proses autentikasi berhasil.
- **Branding Favicon Menyeluruh:** Pemasangan ikon resmi NOTTE Coffee (`logo-notte.png`) secara konsisten pada tag `<head>` di seluruh halaman *frontend* (Landing, Menu, Profil Akun, hingga Pelacakan Pesanan).
- **Indikator Omset Kotor / Gross Revenue (`DashboardController.php` & `CashFlowController.php`):** Penambahan metrik finansial rekapitulasi total penjualan kotor (`SUM(total_amount)` dari seluruh pesanan valid) sebelum dipotong HPP dan biaya operasional, yang ditampilkan secara berdampingan di Dashboard Admin dan Laporan Keuangan.
- **Web Audio API Chime (`menu.blade.php`):** Notifikasi suara tiga nada naik (*A5 → C6 → E6*) yang dipicu secara otomatis saat status pesanan berubah menjadi `completed` sebagai pengganti getar seluler yang sering diblokir oleh kebijakan sistem peramban.

### 🔧 Fixed (Perbaikan Bug)
- **Fix CSRF / 419 Page Expired (`.env`):** Penyelarasan variabel konfigurasi `APP_URL` ke domain lokal Laragon (`http://notte-coffee.test`) untuk memperbaiki kecocokan *cookie session* pada tombol aksi "Proses Dapur" di panel admin.
- **Stabilisasi Tata Letak Live Tracker (`menu.blade.php`):** Perbaikan posisi *floating widget* bagian bawah layar dengan menghapus kelas CSS yang menyebabkan pergeseran layout (*layout shift* / tumpang tindih pada gambar produk), serta mengubah animasi *bounce* menjadi border tipis elegan saat status pesanan selesai.