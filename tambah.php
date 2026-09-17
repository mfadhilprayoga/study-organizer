<!DOCTYPE html>
<html>
<head>
    <title>Tambah Catatan</title>
</head>
<body>
    <a href="index.php">← Kembali ke Beranda</a>
    <h1>Tambah Catatan Baru</h1>

    <form action="simpan.php" method="POST">
        <label>Judul</label><br>
        <input type="text" name="title" required><br><br>

        <label>Isi Catatan</label><br>
        <textarea name="content" rows="5" required></textarea><br><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
