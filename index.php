<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$per_halaman = 6;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$offset = ($halaman - 1) * $per_halaman;

if (isset($_GET['cari']) && $_GET['cari'] !== '') {
    $keyword = '%' . $_GET['cari'] . '%';
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE user_id = ? AND title LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "isii", $_SESSION['user_id'], $keyword, $per_halaman, $offset);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);

    $stmtTotal = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM notes WHERE user_id = ? AND title LIKE ?");
    mysqli_stmt_bind_param($stmtTotal, "is", $_SESSION['user_id'], $keyword);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "iii", $_SESSION['user_id'], $per_halaman, $offset);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);

    $stmtTotal = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM notes WHERE user_id = ?");
    mysqli_stmt_bind_param($stmtTotal, "i", $_SESSION['user_id']);
}

mysqli_stmt_execute($stmtTotal);
$hasilTotal = mysqli_stmt_get_result($stmtTotal);
$totalCatatan = mysqli_fetch_assoc($hasilTotal)['total'];
$totalHalaman = ceil($totalCatatan / $per_halaman);
?>
<?php include 'includes/header.php'; ?>

<h1>Daftar Catatan</h1>

<?php if (isset($_GET['status'])): ?>
    <div id="flash-message" class="flash">
        <?php if ($_GET['status'] == 'tambah'): ?>
            ✅ Catatan berhasil ditambahkan!
        <?php elseif ($_GET['status'] == 'update'): ?>
            ✅ Catatan berhasil diperbarui!
        <?php elseif ($_GET['status'] == 'hapus'): ?>
            ✅ Catatan berhasil dihapus!
        <?php endif; ?>f
    </div>
<?php endif; ?>
<form action="index.php" method="GET" style="margin-bottom: 15px;">
    <input type="text" name="cari" placeholder="Cari judul catatan..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
    <button type="submit">Cari</button>
</form>
<div class="card-container">
    <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
        <div class="card">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><?= htmlspecialchars(substr($row['content'], 0, 60)) ?>...</p>
            <a href="detail.php?id=<?= $row['id'] ?>">Baca Selengkapnya</a>
        </div>
    <?php endwhile; ?>
</div>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
        <a href="index.php?halaman=<?= $i ?><?= isset($_GET['cari']) ? '&cari=' . urlencode($_GET['cari']) : '' ?>"
           class="<?= $i == $halaman ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>

<script>
    setTimeout(function() {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 3000);
</script>

<?php include 'includes/footer.php'; ?>