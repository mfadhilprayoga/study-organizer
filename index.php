<?php
require 'db_koneksi.php';

$hasil = mysqli_query($koneksi, "SELECT * FROM notes ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Catatan Kuliah</title>
</head>
<body>
    <h1>Daftar Catatan</h1>
<?php if (isset($_GET['status']) && $_GET['status'] == 'tambah'): ?>
    <p style="color: green;">✅ Catatan berhasil ditambahkan!</p>
<?php endif; ?>
    <a href="tambah.php">+ Tambah Catatan</a>

    <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;">
        <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
            <div style="border: 1px solid #ccc; padding: 15px; width: 250px;">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><?= htmlspecialchars(substr($row['content'], 0, 60)) ?>...</p>
                <a href="detail.php?id=<?= $row['id'] ?>">Baca Selengkapnya</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
