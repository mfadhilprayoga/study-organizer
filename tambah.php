<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';
?>
<?php include 'includes/header.php'; ?>

<a href="index.php">← Kembali ke Beranda</a>
<h1>Tambah Catatan Baru</h1>

<form action="simpan.php" method="POST">
    <?= csrf_field() ?>
    <label>Judul</label><br>
    <input type="text" name="title" required><br><br>

    <label>Isi Catatan</label><br>
    <textarea name="content" rows="5" required></textarea><br><br>

    <button type="submit">Simpan</button>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(event) {
    const title = document.querySelector('input[name="title"]').value.trim();
    const content = document.querySelector('textarea[name="content"]').value.trim();

    if (title === '') {
        alert('Judul tidak boleh kosong!');
        event.preventDefault();
        return;
    }

    if (content.length < 10) {
        alert('Isi catatan minimal 10 karakter!');
        event.preventDefault();
        return;
    }
});
</script>

<?php include 'includes/footer.php'; ?>