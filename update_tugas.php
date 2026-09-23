<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_POST['id'];
$title = $_POST['title'];
$deadline = $_POST['deadline'];
$status = $_POST['status'];

$stmt = mysqli_prepare($koneksi, "UPDATE tasks SET title = ?, deadline = ?, status = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "sssii", $title, $deadline, $status, $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

header("Location: tugas.php");
exit;