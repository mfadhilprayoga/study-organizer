<?php
require 'includes/db_koneksi.php';

$title = $_POST['title'];
$content = $_POST['content'];

$stmt = mysqli_prepare($koneksi, "INSERT INTO notes (title, content) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $title, $content);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=tambah");
exit;