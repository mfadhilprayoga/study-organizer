<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

// Hitung jumlah catatan
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM notes WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$totalCatatan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Hitung jumlah tugas
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM tasks WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$totalTugas = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Hitung tugas belum selesai (belum + progres)
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM tasks WHERE user_id = ? AND status != 'selesai'");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$belumSelesai = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

// Cari deadline terdekat (yang belum selesai)
$stmt = mysqli_prepare($koneksi, "SELECT title, deadline FROM tasks WHERE user_id = ? AND status != 'selesai' ORDER BY deadline ASC LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$deadlineTerdekat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<?php include 'includes/header.php'; ?>

<h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>

<div class= "card-container">
    <div class="card">
        <h3>Catatan</h3>
        <p style="font-size: 28px; font-weight: bold;" ><?= $totalCatatan ?></p>
</div>
<div class="card">
    <h3>Deadline Terdekat</h3>
        <?php if ($deadlineTerdekat): ?>
            <p><?= htmlspecialchars($deadlineTerdekat['title']) ?></p>
            <p style="font-weight: bold;"><?= $deadlineTerdekat['deadline'] ?></p>
        <?php else: ?>
            <p>Tidak ada tugas mendatang</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>