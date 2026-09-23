<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';

$title = trim($_POST['title']);
$content = trim($_POST['content']);

if ($title === '' || strlen($title) > 100) {
    die("Judul tidak boleh kosong dan maksimal 100 karakter.");
}

if (strlen($content) < 10) {
    die("Isi catatan minimal 10 karakter.");
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");

$stmt = mysqli_prepare($koneksi, "INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iss", $_SESSION['user_id'], $title, $content);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=tambah");
exit;