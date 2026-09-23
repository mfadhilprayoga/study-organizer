<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

if ($filter !== 'semua') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tasks WHERE user_id = ? AND status = ? ORDER BY deadline ASC");
    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $filter);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tasks WHERE user_id = ? ORDER BY deadline ASC");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
}
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
?>
<?php include 'includes/header.php'; ?>

<h1>Daftar Tugas</h1>

<a href="tambah_tugas.php" class="btn-tambah">+ Tambah Tugas</a>

<div style="margin: 15px 0;">
    <a href="tugas.php?filter=semua">Semua</a> |
    <a href="tugas.php?filter=belum">Belum</a> |
    <a href="tugas.php?filter=progres">Progres</a> |
    <a href="tugas.php?filter=selesai">Selesai</a>
</div>

<table border="1" cellpadding="8" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th>Nama Tugas</th>
        <th>Deadline</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= $row['deadline'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td>
                <a href="edit_tugas.php?id=<?= $row['id'] ?>">Edit</a>
                <a href="hapus_tugas.php?id=<?= $row['id'] ?>">Hapus</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'includes/footer.php'; ?>