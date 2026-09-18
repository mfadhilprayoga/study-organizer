<?php
require 'db_koneksi.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    mysqli_query($koneksi, "DELETE FROM notes WHERE id = $id");
    header("Location: index.php?status=hapus");
    exit;
}

$hasil = mysqli_query($koneksi, "SELECT * FROM notes WHERE id = $id");
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hapus Catatan</title>
</head>
<body>
    <h1>Hapus Catatan?</h1>
    <p>Apakah kamu yakin ingin menghapus catatan "<?= htmlspecialchars($catatan['title']) ?>"?</p>

    <form action="hapus.php?id=<?= $catatan['id'] ?>" method="POST">
        <a href="index.php">Batal</a>
        <button type="submit">Hapus</button>
    </form>
</body>
</html>