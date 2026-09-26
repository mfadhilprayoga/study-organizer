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

<section class="note-editor-page">
    <header class="note-editor-heading">
        <span class="note-editor-icon" aria-hidden="true">▣</span>
        <div>
            <h1>Edit Catatan</h1>
            <p>Perbarui catatanmu di bawah ini.</p>
        </div>
    </header>

    <form class="note-editor-form" action="update.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $catatan['id'] ?>">

        <label for="note-title">Judul Catatan</label>
        <input id="note-title" type="text" name="title" value="<?= htmlspecialchars($catatan['title']) ?>" placeholder="Masukkan judul catatan..." required>

        <label for="note-content">Isi Catatan</label>
        <textarea id="note-content" name="content" rows="6" placeholder="Tulis isi catatan di sini..." required><?= htmlspecialchars($catatan['content']) ?></textarea>

        <div class="note-editor-actions">
            <a class="cancel-button" href="index.php">Batal</a>
            <button type="submit"><span aria-hidden="true">▣</span> Simpan Perubahan</button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>