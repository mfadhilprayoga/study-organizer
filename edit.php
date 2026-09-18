<?php
require 'db_koneksi.php';

$id = $_GET['id'];
$hasil = mysqli_query($koneksi, "SELECT * FROM notes WHERE id = $id");
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Catatan</title>
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
