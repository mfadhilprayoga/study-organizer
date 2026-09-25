<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

$title = trim($_POST['title']);
$content = trim($_POST['content']);

$errorTitle = validasiJudulCatatan($title);
$errorContent = validasiIsiCatatan($content);

if ($errorTitle || $errorContent) {
    $_SESSION['form_error'] = $errorTitle ?? $errorContent;
    header("Location: tambah.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iss", $_SESSION['user_id'], $title, $content);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=tambah");
exit;