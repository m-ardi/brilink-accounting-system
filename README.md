# 🏦 Sistem Informasi Akuntansi Agen BRILink

Aplikasi web untuk mencatat dan memantau transaksi harian Agen BRILink secara akurat — mencakup Transfer, Tarik Tunai, dan PPOB — lengkap dengan kalkulasi laba bersih otomatis, laporan keuangan, dan ekspor PDF/Excel.

Dibangun dengan **Laravel 13**, **Tailwind CSS**, dan **Alpine.js**.

---

## ✨ Fitur Utama

- 📝 **Input Transaksi** — Catat Transfer, Tarik Tunai, dan PPOB dengan fee otomatis berdasarkan nominal
- 💰 **Kalkulasi Laba Otomatis** — `laba_bersih = biaya_admin - setoran_bri`
- 📊 **Saldo Modal Real-time** — `saldo_sesudah = saldo_sebelum - nominal + laba_bersih`
- 📈 **Dashboard** — Ringkasan harian + grafik transaksi 7 hari terakhir
- 📄 **Laporan PDF & Excel** — Ekspor laporan per periode dengan filter
- 🔧 **Pengaturan Agen** — Isi identitas agen dan saldo modal awal
- 🔒 **Autentikasi** — Login aman berbasis role (owner)
- 📋 **Log Aktivitas** — Rekam jejak semua aksi pengguna

---

## ⚙️ Persyaratan Sistem

Pastikan komputer Anda sudah memiliki:

| Kebutuhan | Versi | Link Download |
|-----------|-------|---------------|
| XAMPP | Terbaru | [apachefriends.org/download.html](https://www.apachefriends.org/download.html) |
| Composer | 2.x | [getcomposer.org/download](https://getcomposer.org/download/) |
| Node.js + NPM | 18+ | [nodejs.org/en/download](https://nodejs.org/en/download/) |

> **Catatan:** PHP sudah otomatis terinstall bersama XAMPP — tidak perlu install PHP secara terpisah.

---

## 🚀 Instalasi

### Cara 1 — Clone via Git

```bash
# 1. Clone repositori
git clone https://github.com/mvelyn-pi/brilink-accounting-system.git

# 2. Masuk ke folder proyek
cd brilink-accounting-system
```

### Cara 2 — Download ZIP

1. Klik tombol **Code → Download ZIP** di halaman GitHub
2. Ekstrak file ZIP ke folder yang Anda inginkan
3. Buka terminal/command prompt, lalu masuk ke folder hasil ekstrak:
   ```bash
   cd path/ke/folder/hasil-ekstrak
   ```

---

### Langkah Instalasi (Sama untuk Kedua Cara)

#### 1. Install dependensi PHP

```bash
composer install
```

#### 2. Buat file konfigurasi `.env`

```bash
cp .env.example .env
```

#### 3. Generate application key

```bash
php artisan key:generate
```

#### 4. Buat database MySQL di phpMyAdmin

1. Pastikan XAMPP sudah berjalan (Apache + MySQL **Start**)
2. Buka browser → **http://localhost/phpmyadmin**
3. Klik **New** di panel kiri
4. Isi nama database: `brilink_rambo` → klik **Create**

#### 5. Sesuaikan konfigurasi `.env`

Buka file `.env`, pastikan bagian database seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=brilink_rambo
DB_USERNAME=root
DB_PASSWORD=
```

> Jika MySQL Anda memakai password, isi `DB_PASSWORD` sesuai password tersebut.

#### 6. Jalankan migrasi & seeder

```bash
php artisan migrate --seed
```

Perintah ini akan membuat semua tabel dan mengisi data awal:
- 1 akun owner default
- 3 kategori layanan (Transfer, Tarik Tunai, PPOB)
- Fee rules bertingkat per kategori
- Saldo modal awal Rp 0

#### 7. Compile aset CSS & JavaScript (sekali saja)

```bash
npm install
```

#### 8. Build aset frontend

```bash
npm run build
```

#### 9. Jalankan server lokal

```bash
php artisan serve
```

Buka browser dan akses: **http://127.0.0.1:8000**

---

## 🔑 Akun Login Default

Setelah seeder berjalan, gunakan akun berikut untuk login pertama kali:

| Field | Nilai |
|-------|-------|
| **Email** | `owner@brilink-rambo.id` |
| **Password** | `password123` |

> **Penting:** Ganti password segera setelah login pertama melalui halaman Profil.

---

## 🏁 Konfigurasi Awal Setelah Login

Lakukan langkah-langkah ini setelah berhasil masuk:

1. **Isi Identitas Agen** — Buka menu **Pengaturan**, ubah nama agen, alamat, dan kode agen sesuai data asli.
2. **Isi Saldo Modal Awal** — Masih di halaman **Pengaturan**, masukkan jumlah uang modal yang ada di laci/brankas agen saat ini. Ini adalah titik awal perhitungan saldo.
3. **Mulai Input Transaksi** — Buka menu **Transaksi → Tambah Transaksi**.

---

## 💻 Mode Development (Untuk Developer)

Jika ingin menjalankan mode development dengan hot-reload:

```bash
# Terminal 1 — Laravel backend
php artisan serve

# Terminal 2 — Vite frontend (hot reload)
npm run dev
```

Atau gunakan satu perintah (memerlukan `concurrently`):

```bash
composer run dev
```

---

## 🧪 Menjalankan Test

```bash
php artisan test
```

Atau dengan output lebih detail:

```bash
./vendor/bin/phpunit --testdox
```

---

## 📁 Struktur Tabel Database

| Tabel | Keterangan |
|-------|-----------|
| `users` | Akun pengguna (role: owner) |
| `categories` | Jenis layanan: Transfer, Tarik Tunai, PPOB |
| `fee_rules` | Aturan fee bertingkat per kategori |
| `modal` | Saldo modal agen (singleton) |
| `transactions` | Rekap setiap transaksi + snapshot saldo |
| `agent_settings` | Identitas agen (nama, alamat, kode) |
| `activity_logs` | Log aktivitas sistem |

---

## 🔄 Reset & Seed Ulang Database

Jika ingin mengulang dari awal (semua data terhapus):

```bash
php artisan migrate:fresh --seed
```

---

## 📦 Dependensi Utama

| Package | Kegunaan |
|---------|---------|
| `laravel/framework ^13` | Framework utama |
| `laravel/breeze` | Autentikasi |
| `barryvdh/laravel-dompdf` | Ekspor laporan PDF |
| `maatwebsite/excel` | Ekspor laporan Excel |
| `tailwindcss` | Styling CSS |
| `alpinejs` | Interaktivitas frontend |

---

## ❓ Troubleshooting

**Q: Muncul error `SQLSTATE[HY000]: unable to open database file`**
> Pastikan file `database/database.sqlite` sudah dibuat. Jalankan `touch database/database.sqlite`.

**Q: Halaman muncul tapi CSS/JS tidak termuat**
> Jalankan `npm run build` terlebih dahulu agar aset frontend ter-compile.

**Q: Setelah clone ZIP, muncul error `key not set`**
> Pastikan sudah menjalankan `php artisan key:generate` setelah membuat file `.env`.

**Q: Login gagal meski pakai akun default**
> Pastikan seeder sudah berjalan: `php artisan migrate --seed`. Jika sudah ada tabel sebelumnya, gunakan `php artisan migrate:fresh --seed`.

---