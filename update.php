<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';

$id = $_POST['id'];
$title = trim($_POST['title']);
$content = trim($_POST['content']);

if ($title === '' || strlen($title) > 100) {
    die("Judul tidak boleh kosong dan maksimal 100 karakter.");
}

if (strlen($content) < 10) {
    die("Isi catatan minimal 10 karakter.");
}

$stmt = mysqli_prepare($koneksi, "UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ssii", $title, $content, $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=update");
exit;