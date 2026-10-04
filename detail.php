<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$id = $_GET['id'];

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

<article class="note-detail">
    <header class="note-detail-header">
        <div>
            <p class="note-detail-eyebrow">Detail Catatan</p>
            <h1><?= htmlspecialchars($catatan['title']) ?></h1>
            <p class="note-detail-date">Dibuat <?= date('d M Y', strtotime($catatan['created_at'])) ?></p>
        </div>
    </header>
    <div class="note-detail-content"><?= nl2br(htmlspecialchars($catatan['content'])) ?></div>
    <footer class="note-detail-actions">
        <a class="detail-back-button" href="index.php">← Kembali ke Beranda</a>
        <div class="detail-edit-delete">
            <a class="detail-delete-button" href="hapus.php?id=<?= $catatan['id'] ?>">Hapus</a>
            <a class="detail-edit-button" href="edit.php?id=<?= $catatan['id'] ?>">Edit</a>
        </div>
    </footer>
</article>

<?php include 'includes/footer.php'; ?>