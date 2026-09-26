<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $stmt = mysqli_prepare($koneksi, "DELETE FROM tasks WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    header("Location: tugas.php");
    exit;
}

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

<div class="confirm-box">
    <h1>Hapus Tugas?</h1>
    <p>Apakah kamu yakin ingin menghapus tugas "<?= htmlspecialchars($tugas['title']) ?>"?</p>

    <form action="hapus_tugas.php?id=<?= $tugas['id'] ?>" method="POST">
        <?= csrf_field() ?>
        <a href="tugas.php">Batal</a>
        <button type="submit">Hapus</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>