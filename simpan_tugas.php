<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$title = $_POST['title'];
$task_date = $_POST['task_date'];
$deadline = $_POST['deadline'];

$stmt = mysqli_prepare($koneksi, "INSERT INTO tasks (user_id, title, task_date, deadline) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isss", $_SESSION['user_id'], $title, $task_date, $deadline);
mysqli_stmt_execute($stmt);

header("Location: tugas.php");
exit;