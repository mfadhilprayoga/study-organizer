<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$title = $_POST['title'];
$content = $_POST['content'];

$stmt = mysqli_prepare($koneksi, "INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iss", $_SESSION['user_id'], $title, $content);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=tambah");
exit;