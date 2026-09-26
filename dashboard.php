<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$userId = $_SESSION['user_id'];

function countForUser(mysqli $koneksi, string $query, int $userId): int
{
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    return (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
}

$totalCatatan = countForUser($koneksi, "SELECT COUNT(*) AS total FROM notes WHERE user_id = ?", $userId);
$totalTugas = countForUser($koneksi, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ?", $userId);
$selesai = countForUser($koneksi, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND status = 'selesai'", $userId);
$belumSelesai = $totalTugas - $selesai;

$stmt = mysqli_prepare($koneksi, "SELECT id, title, content, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$catatanTerbaru = mysqli_stmt_get_result($stmt);

$stmt = mysqli_prepare($koneksi, "SELECT id, title, deadline, status FROM tasks WHERE user_id = ? ORDER BY deadline ASC LIMIT 3");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$tugasMendatang = mysqli_stmt_get_result($stmt);
?>
<?php include 'includes/header.php'; ?>

<section class="welcome">
    <p class="eyebrow">Dashboard</p>
    <h1>Halo, <?= htmlspecialchars($_SESSION['username']) ?>! <span aria-hidden="true">👋</span></h1>
    <p>Semangat terus belajarnya, masa depanmu masih panjang! <span aria-hidden="true">✨</span></p>
</section>

<section class="stats-grid" aria-label="Ringkasan aktivitas">
    <div class="stat-card stat-blue"><span class="stat-icon">▣</span><span>Total Catatan</span><strong><?= $totalCatatan ?></strong></div>
    <div class="stat-card stat-mint"><span class="stat-icon">☑</span><span>Total Tugas</span><strong><?= $totalTugas ?></strong></div>
    <div class="stat-card stat-green"><span class="stat-icon">☑</span><span>Selesai</span><strong><?= $selesai ?></strong></div>
    <div class="stat-card stat-orange"><span class="stat-icon">◷</span><span>Belum Selesai</span><strong><?= $belumSelesai ?></strong></div>
</section>

<section class="dashboard-columns">
    <div class="panel">
        <div class="panel-heading"><h2>Catatan Terbaru</h2><a href="index.php">Lihat Semua <span>→</span></a></div>
        <div class="item-list">
        <?php if (mysqli_num_rows($catatanTerbaru) === 0): ?>
            <p class="empty-state">Belum ada catatan.</p>
        <?php else: while ($note = mysqli_fetch_assoc($catatanTerbaru)): ?>
            <a class="list-item" href="detail.php?id=<?= $note['id'] ?>">
                <span class="item-icon blue-icon">▣</span>
                <span class="item-copy"><strong><?= htmlspecialchars($note['title']) ?></strong><small><?= htmlspecialchars(substr($note['content'], 0, 46)) ?><?= strlen($note['content']) > 46 ? '...' : '' ?></small><time><?= date('d M Y', strtotime($note['created_at'])) ?></time></span><span class="item-arrow">›</span>
            </a>
        <?php endwhile; endif; ?>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading"><h2>Tugas Mendatang</h2><a href="tugas.php">Lihat Semua <span>→</span></a></div>
        <div class="item-list">
        <?php if (mysqli_num_rows($tugasMendatang) === 0): ?>
            <p class="empty-state">Belum ada tugas.</p>
        <?php else: while ($task = mysqli_fetch_assoc($tugasMendatang)): ?>
            <a class="list-item" href="edit_tugas.php?id=<?= $task['id'] ?>">
                <span class="task-check <?= $task['status'] === 'selesai' ? 'checked' : '' ?>">✓</span>
                <span class="item-copy"><strong><?= htmlspecialchars($task['title']) ?></strong><small>▣ <?= date('d M Y', strtotime($task['deadline'])) ?></small></span><span class="status-pill status-<?= htmlspecialchars($task['status']) ?>"><?= ucfirst($task['status']) ?></span>
            </a>
        <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
