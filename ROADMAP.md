# Roadmap Pengembangan — Study Organizer

Setiap tahap punya *Definition of Done* (DoD) sendiri. Sebuah tahap baru dianggap selesai kalau seluruh checklist-nya terpenuhi — kalau belum, tidak lanjut ke tahap berikutnya.

Aturan kerja: tiap fitur selesai dicek dengan 3 pertanyaan — (1) Apakah fiturnya bekerja? (2) Apakah aman dan tidak merusak fitur lain? (3) Apakah Definition of Done tahap ini terpenuhi?

---

## ✅ Tahap 1 — Perapian Struktur Project

**Dibangun:** `header.php`/`footer.php`, folder `includes/` dan `assets/`, konfigurasi database terpisah, fitur search, pagination, responsive design, meta viewport.

**Definition of Done**
- [x] Header dan footer tidak lagi ditulis berulang di setiap halaman
- [x] Koneksi database berada di file terpisah
- [x] Struktur folder jelas dan mudah dipahami
- [x] CRUD Catatan tetap berfungsi normal
- [x] Search dapat mencari catatan
- [x] Pagination berjalan dengan benar
- [x] Tampilan dapat digunakan di desktop dan mobile

---

## ✅ Tahap 2 — Authentication & User System

**Dibangun:** Register, login, logout, session, password hashing, tabel `users`, kolom `user_id` di tabel `notes`.

**Definition of Done**
- [x] User dapat register dan login
- [x] Password tidak disimpan sebagai plaintext
- [x] Halaman yang butuh login tidak dapat diakses tanpa login
- [x] `notes` memiliki `user_id`, catatan baru otomatis terhubung ke user yang login
- [x] User hanya dapat melihat catatan miliknya sendiri

---

## ✅ Tahap 3 — Modul Tugas

**Dibangun:** Tabel `tasks` (nama tugas, tanggal, deadline, status), CRUD lengkap, filter status.

**Definition of Done**
- [x] User dapat menambah, mengedit, menghapus tugas miliknya
- [x] Filter Semua / Belum / Progres / Selesai berjalan
- [x] User tidak dapat melihat atau mengubah tugas milik user lain
- [x] CRUD Tugas tidak merusak modul Catatan

---

## ✅ Tahap 4 — Dashboard

**Dibangun:** Ringkasan jumlah catatan, jumlah tugas, tugas belum selesai, dan deadline terdekat.

**Definition of Done**
- [x] Dashboard hanya dapat diakses setelah login
- [x] Semua angka sesuai dengan data milik user yang login, tidak menampilkan data user lain

---

## ✅ Tahap 5 — Security Hardening

**Dibangun:** Validasi input di sisi server untuk semua form, CSRF token di semua form dan verifikasi di semua proses POST.

**Definition of Done**
- [x] Semua query menggunakan prepared statement
- [x] Input divalidasi di server, bukan hanya di JavaScript
- [x] Form penting memiliki CSRF protection — teruji dengan mengubah token secara manual dan request berhasil ditolak
- [x] User tidak dapat mengakses/mengubah data user lain lewat manipulasi ID di URL

---

## ⏳ Tahap 6 — Git & GitHub Workflow

**Rencana:** Branching per fitur (`feature/...`), konvensi commit (`feat:`, `fix:`, `security:`, `refactor:`), dokumentasi project (README + roadmap ini).

**Definition of Done**
- [ ] Repository memiliki struktur yang rapi
- [ ] Perubahan besar berikutnya dilakukan melalui branch terpisah
- [ ] `main` selalu dalam kondisi yang dapat dijalankan
- [ ] File sensitif tidak masuk repository (`.gitignore` benar)
- [ ] README menjelaskan project dan cara menjalankannya

---

## ⏳ Tahap 7 — Automated Testing & CI

**Rencana:** GitHub Actions untuk PHP syntax check, automated test, dan build otomatis setiap push/PR.

**Definition of Done**
- [ ] GitHub Actions berjalan otomatis saat ada push/PR
- [ ] Pipeline memberi status berhasil/gagal yang jelas

---

## ⏳ Tahap 8 — Docker

**Rencana:** Containerize aplikasi (PHP + web server + MariaDB) dengan Docker Compose.

**Definition of Done**
- [ ] Aplikasi dapat dijalankan sepenuhnya lewat Docker
- [ ] Data database tetap tersimpan walau container dibuat ulang

---

## ⏳ Tahap 9 — DevSecOps

**Rencana:** Security scanning (Trivy), dependency scanning, masuk ke pipeline CI/CD.

**Definition of Done**
- [ ] Security check berjalan otomatis di pipeline
- [ ] Pipeline dapat menghentikan proses jika ditemukan masalah serius

---

## ⏳ Tahap 10 — Deployment

**Rencana:** Deploy ke VPS/cloud dengan domain sendiri dan HTTPS.

**Definition of Done**
- [ ] Aplikasi dapat diakses lewat internet dengan HTTPS aktif
- [ ] Kredensial production tidak berada di repository

---

## ⏳ Tahap 11 — Monitoring & Logging

**Rencana:** Application logging, server monitoring dasar.

**Definition of Done**
- [ ] Error aplikasi dapat dilacak lewat log
- [ ] Kondisi aplikasi dapat diketahui tanpa membuka source code
