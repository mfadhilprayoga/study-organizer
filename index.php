<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$per_halaman = 6;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$searchTerm = trim($_GET['cari'] ?? '');
$hasSearch = $searchTerm !== '';
$offset = ($halaman - 1) * $per_halaman;

if ($hasSearch) {
    $keyword = '%' . $searchTerm . '%';
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

<section class="notes-page-header">
    <div class="notes-title">
        <span class="notes-title-icon">▣</span>
        <div>
            <h1>Catatan Saya</h1>
            <p>Kelola semua catatanmu di sini.</p>
        </div>
    </div>
    <div class="notes-toolbar">
        <form class="search-form" action="index.php" method="GET">
            <span class="search-icon">⌕</span>
            <input type="text" name="cari" placeholder="Cari catatan..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit">Cari</button>
            <?php if ($hasSearch): ?>
                <a class="reset-search" href="index.php">Semua Catatan</a>
            <?php endif; ?>
        </form>
        <a class="btn-tambah add-note-button" href="tambah.php">＋ Tambah Catatan</a>
    </div>
</section>

<?php if (isset($_GET['status'])): ?>
    <div id="flash-message" class="flash">
        <?php if ($_GET['status'] == 'tambah'): ?>
            ✅ Catatan berhasil ditambahkan!
        <?php elseif ($_GET['status'] == 'update'): ?>
            ✅ Catatan berhasil diperbarui!
        <?php elseif ($_GET['status'] == 'hapus'): ?>
            ✅ Catatan berhasil dihapus!
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if ($hasSearch): ?>
    <p class="search-summary">Menampilkan hasil pencarian untuk <strong>"<?= htmlspecialchars($searchTerm) ?>"</strong></p>
<?php endif; ?>
<div class="notes-list">
    <?php if (mysqli_num_rows($hasil) === 0): ?>
        <div class="empty-notes">
            <strong>Catatan tidak ditemukan</strong>
            <span>Coba gunakan kata kunci lain atau kembali ke semua catatan.</span>
            <?php if ($hasSearch): ?><a href="index.php">Lihat Semua Catatan</a><?php endif; ?>
        </div>
    <?php else: while ($row = mysqli_fetch_assoc($hasil)): ?>
        <article class="note-row">
            <a class="note-main" href="detail.php?id=<?= $row['id'] ?>">
                <span class="note-icon">▣</span>
                <span class="note-copy">
                    <strong><?= htmlspecialchars($row['title']) ?></strong>
                    <span><?= htmlspecialchars(substr($row['content'], 0, 70)) ?><?= strlen($row['content']) > 70 ? '...' : '' ?></span>
                    <time>▣ <?= date('d M Y', strtotime($row['created_at'])) ?></time>
                </span>
            </a>
            <span class="note-actions">
                <a href="edit.php?id=<?= $row['id'] ?>" aria-label="Edit <?= htmlspecialchars($row['title']) ?>">✎</a>
                <a class="delete-action" href="hapus.php?id=<?= $row['id'] ?>" aria-label="Hapus <?= htmlspecialchars($row['title']) ?>">🗑</a>
            </span>
        </article>
    <?php endwhile; endif; ?>
</div>

<?php if ($totalHalaman > 1): ?><div class="pagination">
    <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
        <a href="index.php?halaman=<?= $i ?><?= $hasSearch ? '&cari=' . urlencode($searchTerm) : '' ?>"
           class="<?= $i == $halaman ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div><?php endif; ?>

<script>
    setTimeout(function() {
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 3000);
</script>

<?php include 'includes/footer.php'; ?>