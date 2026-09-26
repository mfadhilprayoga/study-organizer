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

<section class="tasks-page">
    <header class="tasks-page-header">
        <div class="tasks-title">
            <span class="tasks-title-icon" aria-hidden="true">✓</span>
            <div>
                <h1>Tugas</h1>
                <p>Kelola tugas dan deadline kamu.</p>
            </div>
        </div>
        <a href="tambah_tugas.php" class="btn-tambah add-task-button">＋ Tambah Tugas</a>
    </header>

    <nav class="task-filters" aria-label="Filter tugas">
        <a class="<?= $filter === 'semua' ? 'active' : '' ?>" href="tugas.php?filter=semua"><span class="filter-mark">○</span> Semua</a>
        <a class="<?= $filter === 'selesai' ? 'active' : '' ?>" href="tugas.php?filter=selesai"><span class="filter-mark done">○</span> Selesai</a>
        <a class="<?= $filter === 'progres' ? 'active' : '' ?>" href="tugas.php?filter=progres"><span class="filter-mark progress">○</span> Progres</a>
        <a class="<?= $filter === 'belum' ? 'active' : '' ?>" href="tugas.php?filter=belum"><span class="filter-mark pending">○</span> Belum</a>
    </nav>

    <div class="task-list">
        <?php if (mysqli_num_rows($hasil) === 0): ?>
            <div class="task-empty">
                <span class="task-empty-icon" aria-hidden="true">☑</span>
                <strong>Belum ada tugas</strong>
                <span>Tugas untuk filter ini akan muncul di sini.</span>
                <a href="tambah_tugas.php">＋ Tambah Tugas</a>
            </div>
        <?php else: while ($row = mysqli_fetch_assoc($hasil)): ?>
            <article class="task-row">
                <span class="task-row-check <?= $row['status'] === 'selesai' ? 'is-done' : '' ?>" aria-hidden="true"><?= $row['status'] === 'selesai' ? '✓' : '' ?></span>
                <div class="task-row-copy">
                    <strong><?= htmlspecialchars($row['title']) ?></strong>
                    <time datetime="<?= htmlspecialchars($row['deadline']) ?>">▣ <?= date('d M Y', strtotime($row['deadline'])) ?></time>
                </div>
                <span class="status-pill status-<?= htmlspecialchars($row['status']) ?>"><?= ucfirst($row['status']) ?></span>
                <details class="task-actions">
                    <summary aria-label="Aksi untuk <?= htmlspecialchars($row['title']) ?>">⋮</summary>
                    <div class="task-action-menu">
                        <a href="edit_tugas.php?id=<?= $row['id'] ?>">Edit</a>
                        <a href="hapus_tugas.php?id=<?= $row['id'] ?>">Hapus</a>
                    </div>
                </details>
            </article>
        <?php endwhile; endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>