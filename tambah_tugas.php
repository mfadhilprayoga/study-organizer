<?php
require 'includes/auth_check.php';
?>
<?php include 'includes/header.php'; ?>

<a href="tugas.php">← Kembali ke Daftar Tugas</a>
<h1>Tambah Tugas Baru</h1>

<form action="simpan_tugas.php" method="POST">
    <?= csrf_field() ?>
    <label>Nama Tugas</label><br>
    <input type="text" name="title" required><br><br>

    <label>Tanggal Dibuat</label><br>
    <input type="date" name="task_date" required><br><br>

    <label>Deadline</label><br>
    <input type="date" name="deadline" required><br><br>

    <button type="submit">Simpan</button>
</form>

<?php include 'includes/footer.php'; ?>