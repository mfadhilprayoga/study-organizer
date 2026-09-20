<?php
require 'includes/db_koneksi.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM notes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: index.php?status=hapus");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}
?>
<? include 'includes/header.php';?>
<!DOCTYPE html>
<html>
<head>
    <title>Hapus Catatan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="confirm-box">
    <   h1>Hapus Catatan?</h1>
        <p>Apakah kamu yakin ingin menghapus catatan "<?= htmlspecialchars($catatan['title']) ?>"?</p>
        <form action="hapus.php?id=<?= $catatan['id'] ?>" method="POST">
        <a href="index.php">Batal</a>
        <button type="submit">Hapus</button>
    </form>
    </div>
</body>
</html>