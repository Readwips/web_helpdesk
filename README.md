# IT Helpdesk

Aplikasi web Laravel untuk mengelola layanan dukungan IT, workflow tiket, inventaris perangkat, penugasan aset, dokumentasi perbaikan, knowledge base, dashboard operasional, serta laporan.

Antarmuka menggunakan design system orisinal yang terdokumentasi pada `DESIGN.md`: palet netral, aksen sage, tipografi tegas, border tipis, komponen Blade reusable, serta layout responsif desktop dan mobile.

## Fitur

- Autentikasi Laravel Breeze Blade: login, logout, lupa/reset kata sandi, profil, email, dan perubahan kata sandi.
- Tiga role (`admin`, `technician`, `user`), middleware role, policy per data, akun aktif/nonaktif, halaman 403 dan 404.
- CRUD pengguna, teknisi, departemen, kategori tiket, dan kategori aset.
- Workflow tiket: baru → ditugaskan → diproses → menunggu konfirmasi → selesai, dengan pembukaan kembali atau pembatalan.
- Nomor tiket atomik `TKT-YYYYMM-0001`, diagnosis, solusi, komentar, catatan internal, lampiran privat, pencarian, filter, pagination, durasi, dan timeline.
- Inventaris dengan kode `AST-KODE-0001`, serial unik, pencarian/filter, kondisi, status, lokasi, garansi, dan soft delete.
- Penugasan serta pengembalian aset dalam transaksi database; satu penugasan aktif per aset.
- Riwayat perbaikan: diagnosis, tindakan, komponen, biaya, hasil, status aset, dan jadwal perawatan.
- Knowledge base: draft/published/archived, pencarian, kategori, artikel terkait, terbaru, populer, dan view counter.
- Dashboard terisolasi per role dengan data database dan tujuh visualisasi Chart.js untuk admin.
- Laporan tiket, inventaris, perbaikan, dan teknisi; tampilan web/cetak serta export Excel/PDF yang mengikuti filter aktif.
- Seeder demo realistis dan Feature Test untuk authorization serta aturan bisnis utama.

## Hak Akses

| Role | Akses utama |
|---|---|
| Admin | Seluruh tiket/aset, pengguna, master data, penugasan, laporan, Excel, dan PDF |
| Teknisi | Tiket yang ditugaskan, diagnosis/solusi, seluruh detail aset, perbaikan, dan artikel sendiri |
| Pengguna | Tiket sendiri, konfirmasi/buka kembali, aset sendiri, artikel published, dan profil |

## Teknologi

- PHP 8.2+, Laravel 12, Laravel Breeze Blade, Eloquent ORM
- MySQL 8 untuk runtime lokal
- Blade, Tailwind CSS, Alpine.js, JavaScript, Chart.js
- Maatwebsite Laravel Excel dan Barryvdh Laravel DOMPDF
- PHPUnit Feature Test dan Laravel Pint

## Persyaratan Lokal

- PHP 8.2 atau lebih baru dengan ekstensi `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, dan `zip`.
- Composer 2, MySQL 8, Node.js 20.19+ (atau 22.12+), dan npm.
- Windows PowerShell, Command Prompt, atau terminal lain yang mendukung perintah PHP/Composer/npm.

## Instalasi

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
```

Buat database MySQL kosong:

```sql
CREATE DATABASE it_helpdesk_asset_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Isi kredensial lokal pada `.env` (jangan menyimpan kredensial produksi di source code):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_helpdesk_asset_management
DB_USERNAME=root
DB_PASSWORD=kata_sandi_mysql_lokal
```

Kemudian siapkan data dan frontend:

```powershell
php artisan migrate:fresh --seed --no-interaction
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk pengembangan frontend:

```powershell
npm run dev
```

## Akun Demo

Khusus development dan demonstrasi lokal:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| Teknisi | `teknisi@example.com` | `password` |
| Pengguna | `user@example.com` | `password` |

Seeder juga membuat dua teknisi tambahan, tujuh pengguna tambahan, 5 departemen, 30 tiket, 25 aset, 15 riwayat penugasan, 12 perbaikan, dan 10 artikel.

## Workflow Tiket

1. Pengguna membuat tiket; sistem memberi nomor unik dan status `baru`.
2. Admin memilih teknisi dan prioritas; status menjadi `ditugaskan`.
3. Teknisi memulai pekerjaan, mengisi diagnosis/catatan, lalu solusi.
4. Tiket hanya dapat dikirim ke `menunggu_konfirmasi` jika solusi sudah ada.
5. Pemilik tiket mengonfirmasi `selesai` atau membuka kembali ke `diproses`.
6. Semua aksi penting masuk ke timeline riwayat.

## Struktur Modul

- `app/Http/Controllers` — controller per tanggung jawab: dashboard, tiket, aset, perbaikan, knowledge base, dan laporan.
- `app/Http/Requests` — validasi form utama.
- `app/Policies` dan `app/Http/Middleware` — authorization data, role, dan akun aktif.
- `app/Services` — nomor unik, workflow tiket, serta transaksi penugasan/pengembalian.
- `app/Models` — model Eloquent dan relasi.
- `database/migrations`, `database/factories`, `database/seeders` — skema dan data demo.
- `resources/views` — antarmuka Blade berbahasa Indonesia.
- `tests/Feature` — test autentikasi, IDOR, workflow, inventaris, knowledge base, dan laporan.

## Menjalankan Test dan Formatter

Test memakai SQLite in-memory agar terisolasi dari database lokal:

```powershell
php artisan test
vendor\bin\pint --test
```

Validasi instalasi runtime tetap harus dijalankan pada MySQL lokal:

```powershell
php artisan optimize:clear
php artisan migrate:fresh --seed --no-interaction
php artisan migrate:status
php artisan route:list
npm run build
```

## Catatan Keamanan

- Semua operasi data dilindungi middleware/policy dan validasi server-side; akses tiket/aset/lampiran diuji untuk mencegah IDOR.
- Catatan internal dan lampirannya tidak tersedia bagi pengguna biasa.
- Upload hanya JPG/JPEG/PNG/PDF, maksimal 5 MB per file, disimpan di disk privat, dan diunduh melalui controller terotorisasi dengan `nosniff`.
- Output Blade di-escape, model membatasi mass assignment, login memiliki rate limiting, dan session diregenerasi setelah login.
- Penugasan aset dan perubahan workflow multi-tabel menggunakan transaksi database.
- `.env` diabaikan oleh Git. Jangan memakai data atau kredensial produksi.
