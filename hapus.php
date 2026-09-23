<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $stmt = mysqli_prepare($koneksi, "DELETE FROM notes WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    header("Location: index.php?status=hapus");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}
?>
<?php include 'includes/header.php'; ?>

<div class="confirm-box">
    <h1>Hapus Catatan?</h1>
    <p>Apakah kamu yakin ingin menghapus catatan "<?= htmlspecialchars($catatan['title']) ?>"?</p>

    <form action="hapus.php?id=<?= $catatan['id'] ?>" method="POST">
        <?= csrf_field() ?>
        <a href="index.php">Batal</a>
        <button type="submit">Hapus</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>