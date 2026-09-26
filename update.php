<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

$id = $_POST['id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);

$errorTitle = validasiJudulCatatan($title);
$errorContent = validasiIsiCatatan($content);

if ($errorTitle || $errorContent) {
    $_SESSION['form_error'] = $errorTitle ?? $errorContent;
    header("Location: edit.php?id=$id");
    exit;
}

$stmt = mysqli_prepare($koneksi, "UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ssii", $title, $content, $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=update");
exit;