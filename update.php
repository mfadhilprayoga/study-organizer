<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_POST['id'];
$title = $_POST['title'];
$content = $_POST['content'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

header("Location: index.php?status=update");
exit;