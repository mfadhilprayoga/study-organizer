<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$formError = $_SESSION['form_error'] ?? null;
$formErrors = $_SESSION['form_errors'] ?? [];
$formValues = $_SESSION['form_values'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_errors'], $_SESSION['form_values']);

$id = $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM notes WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}

$title = (string) ($formValues['title'] ?? $catatan['title']);
$content = (string) ($formValues['content'] ?? $catatan['content']);
$errorMessages = array_values(array_filter(
    $formErrors,
    static fn ($message) => is_string($message) && $message !== ''
));
if (!$errorMessages && is_string($formError) && $formError !== '') {
    $errorMessages[] = $formError;
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

        <?php if ($errorMessages): ?>
            <p class="alert-error" role="alert">!!! <?= htmlspecialchars(implode(' ', $errorMessages), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="note-editor-field<?= !empty($formErrors['title']) ? ' has-error' : '' ?>">
            <label for="note-title">Judul Catatan</label>
            <input id="note-title" type="text" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan judul catatan..." required<?= !empty($formErrors['title']) ? ' aria-invalid="true"' : '' ?>>
        </div>

        <div class="note-editor-field<?= !empty($formErrors['content']) ? ' has-error' : '' ?>">
            <label for="note-content">Isi Catatan</label>
            <textarea id="note-content" name="content" rows="10" placeholder="Tulis isi catatan di sini..." aria-describedby="note-content-hint" required<?= !empty($formErrors['content']) ? ' aria-invalid="true"' : '' ?>><?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?></textarea>
            <small class="note-create-hint" id="note-content-hint">Minimal 10 karakter.</small>
        </div>

        <div class="note-editor-actions">
            <a class="cancel-button" href="index.php">Batal</a>
            <button type="submit">Simpan Perubahan</button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>