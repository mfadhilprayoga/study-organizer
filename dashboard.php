<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';
require 'includes/date_helpers.php';

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

$stmt = mysqli_prepare($koneksi, "SELECT id, title, content, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$catatanTerbaru = mysqli_stmt_get_result($stmt);

$stmt = mysqli_prepare($koneksi, "SELECT id, title, deadline, status FROM tasks WHERE user_id = ? ORDER BY deadline ASC LIMIT 5");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$tugasMendatang = mysqli_stmt_get_result($stmt);
$statusLabels = ['belum' => 'Belum', 'progres' => 'Proses', 'selesai' => 'Selesai'];
?>
<?php include 'includes/header.php'; ?>

<section class="welcome">
    <p class="eyebrow">DASHBOARD</p>
    <h1>Halo, <?= htmlspecialchars($sessionDisplayName, ENT_QUOTES, 'UTF-8') ?>! <span aria-hidden="true">👋</span></h1>
    <p>Kelola catatan dan tugasmu dengan lebih teratur.</p>
</section>

<section class="stats-grid" aria-label="Ringkasan aktivitas">
    <div class="stat-card stat-blue"><span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3.75h8l4 4V20a1.25 1.25 0 0 1-1.25 1.25h-10.5A1.25 1.25 0 0 1 5 20V5a1.25 1.25 0 0 1 1-1.25Z"/><path d="M14 4v4h4M8.5 12h7M8.5 15.5h7"/></svg></span><span>Total Catatan</span><strong><?= htmlspecialchars((string) $totalCatatan, ENT_QUOTES, 'UTF-8') ?></strong></div>
    <div class="stat-card stat-yellow"><span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m4.5 7 1.5 1.5L8.5 6M11 7h8.5M4.5 13l1.5 1.5 2.5-2.5M11 13h8.5M4.5 19l1.5 1.5 2.5-2.5M11 19h8.5"/></svg></span><span>Total Tugas</span><strong><?= htmlspecialchars((string) $totalTugas, ENT_QUOTES, 'UTF-8') ?></strong></div>
    <div class="stat-card stat-pink"><span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3.25 2"/></svg></span><span>Belum Selesai</span><strong><?= htmlspecialchars((string) $belumSelesai, ENT_QUOTES, 'UTF-8') ?></strong></div>
    <div class="stat-card stat-green"><span class="stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="m8.5 12.25 2.25 2.25 4.75-5"/></svg></span><span>Selesai</span><strong><?= htmlspecialchars((string) $selesai, ENT_QUOTES, 'UTF-8') ?></strong></div>
</section>

<section class="dashboard-columns">
    <div class="panel">
        <div class="panel-heading">
            <h2><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3.5h8l4 4V20a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1Z"/><path d="M14.5 4v4h4M8.5 12h7M8.5 15.5h5"/></svg>Catatan Terbaru</h2>
            <a href="index.php">Lihat Semua <span aria-hidden="true">→</span></a>
        </div>
        <div class="item-list">
        <?php if (mysqli_num_rows($catatanTerbaru) === 0): ?>
            <p class="empty-state">Belum ada catatan.</p>
        <?php else: while ($note = mysqli_fetch_assoc($catatanTerbaru)): ?>
            <a class="list-item note-list-item" href="detail.php?id=<?= htmlspecialchars((string) $note['id'], ENT_QUOTES, 'UTF-8') ?>">
                <span class="note-dot" aria-hidden="true"></span>
                <span class="item-copy note-copy">
                    <span class="note-title-row"><strong><?= htmlspecialchars($note['title'], ENT_QUOTES, 'UTF-8') ?></strong><time class="dashboard-item-date"><?= htmlspecialchars(formatTanggalIndonesia($note['created_at']), ENT_QUOTES, 'UTF-8') ?></time></span>
                    <small><?= htmlspecialchars($note['content'], ENT_QUOTES, 'UTF-8') ?></small>
                </span>
            </a>
        <?php endwhile; endif; ?>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h2><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 7 1.5 1.5L8 6M11 7h9M4 13l1.5 1.5L8 12M11 13h9M4 19l1.5 1.5L8 18M11 19h9"/></svg>Daftar Tugas</h2>
            <a href="tugas.php">Lihat Semua <span aria-hidden="true">→</span></a>
        </div>
        <div class="item-list">
        <?php if (mysqli_num_rows($tugasMendatang) === 0): ?>
            <p class="empty-state">Belum ada tugas.</p>
        <?php else: while ($task = mysqli_fetch_assoc($tugasMendatang)): ?>
            <a class="list-item task-list-item" href="tugas.php?edit=<?= htmlspecialchars((string) $task['id'], ENT_QUOTES, 'UTF-8') ?>">
                <span class="task-check" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="m8.5 12.25 2.25 2.25 4.75-5"/></svg></span>
                <span class="item-copy"><strong><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars(formatTanggalIndonesia($task['deadline']), ENT_QUOTES, 'UTF-8') ?></small></span>
                <span class="status-pill status-<?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($statusLabels[$task['status']], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
