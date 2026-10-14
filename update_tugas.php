<?php
require 'includes/auth_check.php';
csrf_verify();
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$title = trim($_POST['title']);
$deadline = $_POST['deadline'];
$status = $_POST['status'];

if ($id === false || !in_array($status, ['belum', 'progres', 'selesai'], true)) {
    $_SESSION['form_error'] = 'ID atau status tugas tidak valid.';
    header("Location: tugas.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT task_date FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$tugasLama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$tugasLama) {
    die("Tugas tidak ditemukan.");
}

$errorTitle = validasiNamaTugas($title);
$errorDeadline = validasiDeadlineTugas($tugasLama['task_date'], $deadline);

if ($errorTitle || $errorDeadline) {
    $_SESSION['form_error'] = $errorTitle ?? $errorDeadline;
    header("Location: edit_tugas.php?id=$id");
    exit;
}

$stmt = mysqli_prepare($koneksi, "UPDATE tasks SET title = ?, deadline = ?, status = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "sssii", $title, $deadline, $status, $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

header("Location: tugas.php");
exit;