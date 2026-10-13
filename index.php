<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';
require 'includes/date_helpers.php';

$per_halaman = 9;
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
$searchTerm = trim($_GET['cari'] ?? '');
$hasSearch = $searchTerm !== '';
$offset = ($halaman - 1) * $per_halaman;

if ($hasSearch) {
    $keyword = '%' . $searchTerm . '%';
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE user_id = ? AND title LIKE ? ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "isii", $_SESSION['user_id'], $keyword, $per_halaman, $offset);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);

    $stmtTotal = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM notes WHERE user_id = ? AND title LIKE ?");
    mysqli_stmt_bind_param($stmtTotal, "is", $_SESSION['user_id'], $keyword);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
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

<div class="notes-page">
    <section class="notes-page-header">
        <div class="notes-title">
            <div>
                <h1>Catatan Saya</h1>
                <p>Simpan dan kelola catatanmu dengan mudah.</p>
            </div>
        </div>
        <div class="notes-toolbar">
            <form class="search-form" action="index.php" method="GET" role="search">
                <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.2 4.2"/></svg>
                <input type="text" name="cari" aria-label="Cari catatan" placeholder="Cari catatan..." value="<?= htmlspecialchars($searchTerm) ?>">
                <?php if ($hasSearch): ?>
                    <a class="reset-search" href="index.php">Semua Catatan</a>
                <?php endif; ?>
            </form>
            <a class="btn-tambah add-note-button" href="tambah.php"><span aria-hidden="true">+</span> Tambah Catatan</a>
        </div>
    </section>

    <?php if (isset($_GET['status'])): ?>
        <div id="flash-message" class="flash">
            <?php if ($_GET['status'] == 'tambah'): ?>
                Catatan berhasil ditambahkan!
            <?php elseif ($_GET['status'] == 'update'): ?>
                Catatan berhasil diperbarui!
            <?php elseif ($_GET['status'] == 'hapus'): ?>
                Catatan berhasil dihapus!
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
                <a class="note-main" title="<?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>" href="detail.php?id=<?= htmlspecialchars((string) $row['id'], ENT_QUOTES, 'UTF-8') ?>">
                    <strong><?= htmlspecialchars($row['title']) ?></strong>
                    <span><?= htmlspecialchars($row['content']) ?></span>
                </a>
                <div class="note-card-footer">
                <time datetime="<?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at'])), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(formatTanggalIndonesia($row['created_at']), ENT_QUOTES, 'UTF-8') ?></time>
                    <span class="note-actions">
                        <a href="edit.php?id=<?= htmlspecialchars((string) $row['id'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Edit <?= htmlspecialchars($row['title']) ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14.5 6.5 3 3M4 20l4.1-.9L19 8.2a2.1 2.1 0 0 0-3-3L5.1 16.1 4 20Z"/></svg>
                        </a>
                        <a class="delete-action" href="hapus.php?id=<?= htmlspecialchars((string) $row['id'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Hapus <?= htmlspecialchars($row['title']) ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/></svg>
                        </a>
                    </span>
                </div>
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