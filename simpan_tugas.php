<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

$title = trim($_POST['title']);
$task_date = $_POST['task_date'];
$deadline = $_POST['deadline'];

$errorTitle = validasiNamaTugas($title);
$errorDeadline = validasiDeadlineTugas($task_date, $deadline);

if ($errorTitle || $errorDeadline) {
    $_SESSION['form_error'] = $errorTitle ?? $errorDeadline;
    header("Location: tambah_tugas.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO tasks (user_id, title, task_date, deadline) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "isss", $_SESSION['user_id'], $title, $task_date, $deadline);
mysqli_stmt_execute($stmt);

header("Location: tugas.php");
exit;