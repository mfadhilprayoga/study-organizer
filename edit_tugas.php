<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$tugas = mysqli_fetch_assoc($hasil);

if (!$tugas) {
    die("Tugas tidak ditemukan.");
}
?>
<?php include 'includes/header.php'; ?>

<a href="tugas.php">← Kembali ke Daftar Tugas</a>
<h1>Edit Tugas</h1>

<form action="update_tugas.php" method="POST">
    <input type="hidden" name="id" value="<?= $tugas['id'] ?>">

    <label>Nama Tugas</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($tugas['title']) ?>" required><br><br>

    <label>Deadline</label><br>
    <input type="date" name="deadline" value="<?= $tugas['deadline'] ?>" required><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="belum" <?= $tugas['status'] == 'belum' ? 'selected' : '' ?>>Belum</option>
        <option value="progres" <?= $tugas['status'] == 'progres' ? 'selected' : '' ?>>Progres</option>
        <option value="selesai" <?= $tugas['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
    </select><br><br>

    <button type="submit">Update</button>
</form>

<?php include 'includes/footer.php'; ?>