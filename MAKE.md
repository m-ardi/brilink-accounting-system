# PROMPT UNTUK ANTIGRAVITY — Sistem Informasi Akuntansi Agen BRILink


---

## ROLE

Kamu adalah seorang **Senior Full-Stack Developer** yang ahli dalam Laravel dan sistem akuntansi berbasis web. Kamu akan membantu saya membangun aplikasi skripsi berjudul **"Sistem Informasi Akuntansi Pelaporan Transaksi Real-Time pada Agen BRILink Menggunakan Framework Laravel"**. Bekerja secara metodis, tanya balik ke saya kalau ada keputusan penting yang ambigu (jangan menebak logika bisnis keuangan), dan jelaskan setiap keputusan arsitektur yang kamu ambil.

## KONTEKS PROYEK

Objek penelitian: **Agen BRILink Rambo** (Dr. Ratulangi Kilometer 5). Saat ini pencatatan transaksi masih manual di buku besar, menyebabkan risiko human error, sulit rekap, dan tidak ada audit trail. Aplikasi ini akan menggantikan proses tersebut dengan sistem berbasis web yang mencatat transaksi secara *real-time*, otomatis menghitung laba, dan menghasilkan laporan siap unduh.

Metode pengembangan: **Waterfall** (Analisis → Desain → Implementasi → Testing), tapi eksekusi coding-nya boleh kamu lakukan secara **iteratif per modul** (auth dulu → transaksi → laporan → dashboard) supaya gampang di-review setiap tahap.

## TECH STACK (WAJIB)

- **Backend**: Laravel 12 (PHP 8.3+)
- **Database**: MySQL
- **Frontend**: Blade Templating + Tailwind CSS 
- **Auth**: Laravel Breeze atau Laravel-native Auth (tanpa API/SPA kompleks — ini aplikasi internal agen, bukan multi-tenant publik)
- **Export laporan**: `barryvdh/laravel-dompdf` untuk PDF, `maatwebsite/excel` untuk Excel
- **Realtime UI**: cukup pakai AJAX/Livewire untuk update saldo & laba tanpa reload — **[KONFIRMASI: mau pakai Livewire atau cukup fetch/AJAX biasa? Livewire lebih rapi tapi nambah kompleksitas]**

## SCOPE (BATASAN MASALAH — JANGAN KELUAR DARI INI)

- Hanya mencatat 3 jenis layanan: **Transfer, Tarik Tunai, Pembayaran Tagihan (PPOB)**
- Fokus ke pengelolaan keuangan internal agen — **bukan** integrasi ke sistem core banking BRI (tidak ada koneksi API perbankan sungguhan)
- Single agent per instalasi dulu (bukan multi-cabang), kecuali nanti diminta scale up

## FITUR UTAMA (FUNCTIONAL REQUIREMENTS)

### 1. Autentikasi
- Login untuk role **Agen** (pemilik/pengelola). Jika login gagal, tampilkan pesan error dan kembalikan ke halaman login.
- **[KONFIRMASI]**: apakah perlu multi-user per agen (misal pemilik + karyawan dengan role berbeda)? Di BAB II disebut ada kebutuhan audit trail "jika agen memiliki karyawan" — kalau iya, tambahkan role `owner` dan `staff`.

### 2. Dashboard
- Menampilkan ringkasan **saldo modal** dan **total laba hari ini** secara real-time setelah transaksi diinput (tanpa reload halaman).
- Tampilkan juga grafik/ringkasan singkat transaksi mingguan (nice-to-have, bukan wajib).

### 3. Manajemen Transaksi
- Form "Tambah Transaksi": pilih kategori (Transfer / Tarik Tunai / PPOB), input nominal transaksi.
- Sistem menjalankan kalkulasi otomatis laba dan mengurangi saldo modal agen secara otomatis di database.
- **Rumus laba — [WAJIB DIKONFIRMASI SEBELUM CODING]**: dokumen sumber menulis "Laba = Biaya Admin − Setoran Bank", tapi ini janggal secara logika bisnis sharing fee BRILink. Default yang lebih masuk akal (verifikasi ke pemilik agen saat wawancara BAB III):
  ```
  Laba Bersih = Fee dari Bank (sharing fee per kategori transaksi) − Biaya Operasional Agen
  Saldo Modal Baru = Saldo Modal Lama − Nominal Transaksi (untuk transaksi tarik tunai/transfer keluar) + Setoran Nasabah (untuk transaksi setor/transfer masuk)
  ```
  Minta Antigravity membuat variabel `fee_percentage` atau tabel `fee_rules` per kategori transaksi supaya rumus mudah diubah tanpa ubah kode (bukan hardcoded), karena rumus final baru didapat dari wawancara.

### 4. Filter & Pencarian Riwayat Transaksi
- Halaman riwayat transaksi bisa difilter berdasarkan: **rentang tanggal, kategori (Transfer/Tarik Tunai/PPOB), dan nominal (min-max)**.
- Tambahkan search box untuk pencarian cepat (misal cari berdasarkan ID transaksi atau catatan/keterangan).
- Gunakan query builder Eloquent dengan filter dinamis (`when()` clause) supaya kombinasi filter fleksibel tanpa bikin banyak query terpisah.
- Ini langsung menjawab pain point yang disebut di latar belakang skripsi: sulitnya mencari riwayat transaksi lama di pencatatan manual.

### 5. Laporan
- Menu "Laporan" untuk generate rekapitulasi transaksi & laba berdasarkan rentang periode (harian/mingguan/bulanan/custom) — bisa pakai filter yang sama dengan poin di atas.
- Export ke **PDF** dan **Excel**.
- **[KONFIRMASI]**: format laporan mengikuti standar apa? (misal: perlu contoh format laporan manual yang biasa dipakai agen sebagai referensi layout)

### 6. Audit Trail / Log Sistem
- Setiap perubahan data (create/update/delete transaksi) harus tercatat: siapa user-nya, kapan, dan perubahan apa (gunakan misalnya package `spatie/laravel-activitylog` atau bikin tabel `activity_logs` custom).
- Ini fitur keamanan yang ditekankan  — jangan sampai terlewat saat implementasi.

### 7. Dark Mode
- Toggle dark/light mode di seluruh halaman (dashboard, form transaksi, laporan).
- Implementasi pakai Tailwind `dark:` variant (kalau pilih Tailwind).
- Simpan preferensi user di `localStorage` (browser) supaya tetap konsisten tiap buka aplikasi lagi — **bukan** disimpan di database, karena ini murni preferensi tampilan per device.
- Prioritas rendah dibanding fitur transaksi/laporan — kerjakan paling akhir setelah semua fitur inti selesai dan stabil.

## DESAIN DATABASE (DRAFT AWAL — MINTA ANTIGRAVITY REVIEW & LENGKAPI)

```
users
  - id, name, email, password, role (owner/staff), created_at, updated_at

categories
  - id, name (Transfer/Tarik Tunai/PPOB), fee_rule (nominal/persentase), created_at

transactions
  - id, user_id (FK), category_id (FK), nominal, fee_bank, biaya_operasional,
    laba_bersih, saldo_modal_sebelum, saldo_modal_sesudah, created_at, updated_at

activity_logs
  - id, user_id (FK), action, description, subject_type, subject_id, created_at
```

Minta Antigravity untuk menyempurnakan skema ini (index, foreign key constraint, migration Laravel) sebelum mulai coding.

## NON-FUNCTIONAL REQUIREMENTS

- Keamanan: manfaatkan Eloquent ORM (proteksi SQL Injection bawaan Laravel), validasi input di Form Request, CSRF protection default Laravel.
- UI/UX: *user-centric*, navigasi cepat untuk input transaksi (agen sering input berulang kali per hari — minimalkan klik).
- Performa: query dashboard harus efisien (gunakan index pada kolom yang sering di-filter, misal `created_at`, `category_id`).

## CARA KERJA YANG SAYA MAU DARI KAMU (ANTIGRAVITY)

1. Ikuti best practice Laravel: gunakan Form Request untuk validasi, Service/Repository class untuk logika bisnis kalkulasi laba (jangan taruh logic di Controller), dan Blade Component untuk elemen UI yang berulang.
2. Tulis komentar/README singkat di setiap fitur besar, karena kode ini juga akan dijadikan lampiran/dokumentasi di BAB IV skripsi saya.

---