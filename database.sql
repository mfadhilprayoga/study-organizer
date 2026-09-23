-- Study Organizer - Database Schema
-- Jalankan file ini di MariaDB/MySQL untuk membuat database dan tabel yang dibutuhkan.
--
-- Cara pakai:
--   mysql -u root -p < database.sql
-- atau masuk ke MariaDB lalu:
--   SOURCE database.sql;

CREATE DATABASE IF NOT EXISTS catatan_kuliah;
USE catatan_kuliah;

-- Tabel pengguna
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel catatan
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel tugas
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    task_date DATE NOT NULL,
    deadline DATE NOT NULL,
    status ENUM('belum', 'progres', 'selesai') DEFAULT 'belum',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Buat user database khusus untuk aplikasi (least privilege - jangan pakai root)
-- Ganti 'password_kamu' dengan password pilihanmu sendiri
-- CREATE USER 'webapp'@'localhost' IDENTIFIED BY 'password_kamu';
-- GRANT ALL PRIVILEGES ON catatan_kuliah.* TO 'webapp'@'localhost';
-- FLUSH PRIVILEGES;
