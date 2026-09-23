# 📚 Study Organizer

Aplikasi sederhana untuk mengelola catatan dan tugas.

Awalnya dibuat sebagai project belajar "Catatan Kuliah" menggunakan PHP dan MariaDB, lalu dikembangkan secara bertahap menjadi **Study Organizer** — sekaligus menjadi media belajar Web Development → Security → DevOps → DevSecOps.

Target utama pengguna adalah mahasiswa, tetapi sistem tidak dibuat khusus untuk mahasiswa sehingga tetap dapat digunakan oleh siapa saja yang membutuhkan pengelolaan catatan dan tugas.

## Prinsip Pengembangan

> **Simple for users, structured for developers.**

Tampilan dan penggunaan aplikasi dibuat sederhana dan tidak membingungkan pengguna, tetapi struktur backend, database, keamanan, dan deployment tetap dibuat dengan baik. Fitur hanya ditambahkan jika mempunyai tujuan yang jelas — bukan sekadar karena bisa dibuat.

## Fitur

**Catatan**
- Tambah, lihat, edit, hapus catatan
- Tampilan card dengan cuplikan isi + halaman detail
- Pencarian judul catatan
- Pagination

**Tugas**
- Tambah, lihat, edit, hapus tugas
- Field: nama tugas, tanggal, deadline, status
- Filter berdasarkan status (Belum / Progres / Selesai)

**Dashboard**
- Ringkasan jumlah catatan, jumlah tugas, tugas belum selesai, dan deadline terdekat

**Akun & Keamanan**
- Register, login, logout dengan session
- Password di-hash (`password_hash`), tidak pernah disimpan sebagai teks biasa
- Setiap pengguna hanya dapat mengakses data miliknya sendiri (authorization)
- Prepared statement di seluruh query (anti SQL Injection)
- Validasi input di sisi server
- CSRF protection di semua form

## Tech Stack

- **Backend:** PHP (native, tanpa framework)
- **Database:** MariaDB
- **Frontend:** HTML, CSS, JavaScript
- **Version Control:** Git & GitHub

## Cara Menjalankan

1. Clone repository:
   ```bash
   git clone https://github.com/mfadhilprayoga/study-organizer.git
   cd study-organizer
   ```
2. Buat database dan tabel — jalankan skema SQL yang ada di `database.sql` *(lihat catatan di bawah)*.
3. Salin `config.example.php` menjadi `includes/config.php`, lalu sesuaikan kredensial database:
   ```bash
   cp config.example.php includes/config.php
   ```
4. Jalankan server bawaan PHP:
   ```bash
   php -S localhost:8000
   ```
5. Buka `http://localhost:8000` di browser.

## Roadmap Pengembangan

Project ini dikembangkan bertahap, setiap tahap punya *Definition of Done* sendiri sebelum lanjut ke tahap berikutnya. Detail lengkap ada di [ROADMAP.md](ROADMAP.md).

- [x] Tahap 1 — Perapian struktur project (header/footer, search, pagination, responsive)
- [x] Tahap 2 — Authentication & User System
- [x] Tahap 3 — Modul Tugas
- [x] Tahap 4 — Dashboard
- [x] Tahap 5 — Security Hardening (validasi server, CSRF)
- [ ] Tahap 6 — Git & GitHub Workflow
- [ ] Tahap 7 — Automated Testing & CI
- [ ] Tahap 8 — Docker
- [ ] Tahap 9 — DevSecOps (security & dependency scanning)
- [ ] Tahap 10 — Deployment (domain, HTTPS)
- [ ] Tahap 11 — Monitoring & Logging

## Visi Jangka Panjang

Setelah sistem matang, backend Study Organizer berpotensi dikembangkan menjadi API yang dapat digunakan bersama oleh web dan aplikasi mobile.
