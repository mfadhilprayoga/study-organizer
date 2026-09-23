<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
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
    <title>Edit Catatan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="index.php">← Kembali ke Beranda</a>
    <h1>Edit Catatan</h1>

    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= $catatan['id'] ?>">
        <label>Judul</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($catatan['title']) ?>" required><br><br>
        <label>Isi Catatan</label><br>
        <textarea name="content" rows="5" required><?= htmlspecialchars($catatan['content']) ?></textarea><br><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>