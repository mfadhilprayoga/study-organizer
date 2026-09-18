<?php
require 'db_koneksi.php';

$id = $_POST['id'];
$title = $_POST['title'];
$content = $_POST['content'];

$stmt = mysqli_prepare($koneksi, "UPDATE notes SET title = ?, content = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssi", $title, $content, $id);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=update");
exit;