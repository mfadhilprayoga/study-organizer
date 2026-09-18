<?php
require 'db_koneksi.php';

$id = $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($catatan['title']) ?></title>
</head>
<body>
    <a href="index.php">← Kembali ke Beranda</a>

    <h1><?= htmlspecialchars($catatan['title']) ?></h1>
    <p><em><?= $catatan['created_at'] ?></em></p>
    <p><?= nl2br(htmlspecialchars($catatan['content'])) ?></p>

    <a href="edit.php?id=<?= $catatan['id'] ?>">Edit</a>
    <a href="hapus.php?id=<?= $catatan['id'] ?>">Hapus</a>
</body>
</html>
