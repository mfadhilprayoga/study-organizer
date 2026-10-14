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
    <a class="note-editor-back" href="index.php">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m11 5-7 7 7 7M4 12h16"/></svg>
        Kembali ke Catatan
    </a>
    <header class="note-editor-heading">
        <h1>Edit Catatan</h1>
        <p>Ubah isi catatanmu di sini.</p>
    </header>

    <form class="note-editor-form" action="update.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $catatan['id'] ?>">

        <div class="note-editor-field">
            <label for="note-title">Judul Catatan</label>
            <input id="note-title" type="text" name="title" value="<?= htmlspecialchars($catatan['title'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan judul catatan..." required>
        </div>

        <div class="note-editor-field">
            <label for="note-content">Isi Catatan</label>
            <textarea id="note-content" name="content" rows="10" placeholder="Tulis isi catatan di sini..." required><?= htmlspecialchars($catatan['content'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="note-editor-actions">
            <a class="cancel-button" href="index.php">Batal</a>
            <button type="submit">Simpan Perubahan</button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>