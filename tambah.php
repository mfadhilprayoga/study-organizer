<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';

$formError = $_SESSION['form_error'] ?? null;
$formErrors = $_SESSION['form_errors'] ?? [];
$formValues = $_SESSION['form_values'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_errors'], $_SESSION['form_values']);

$title = (string) ($formValues['title'] ?? '');
$content = (string) ($formValues['content'] ?? '');
$errorMessages = array_values(array_filter(
    $formErrors,
    static fn ($message) => is_string($message) && $message !== ''
));
if (!$errorMessages && is_string($formError) && $formError !== '') {
    $errorMessages[] = $formError;
}
?>
<?php include 'includes/header.php'; ?>

<section class="note-editor-page note-create-page">
    <a class="note-editor-back" href="index.php">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m11 5-7 7 7 7M4 12h16"/></svg>
        Kembali ke Catatan
    </a>
    <header class="note-editor-heading">
        <h1>Tambah Catatan</h1>
        <p>Tulis catatanmu di bawah ini.</p>
    </header>

    <form class="note-editor-form note-create-form" action="simpan.php" method="POST">
        <?= csrf_field() ?>

        <?php if ($errorMessages): ?>
            <p class="alert-error" role="alert">!!! <?= htmlspecialchars(implode(' ', $errorMessages), ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="note-editor-field<?= !empty($formErrors['title']) ? ' has-error' : '' ?>">
            <label for="note-title">Judul</label>
            <input id="note-title" type="text" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan judul catatan..." maxlength="100" aria-describedby="note-title-hint"<?= !empty($formErrors['title']) ? ' aria-invalid="true"' : '' ?>>
            <small class="note-create-hint" id="note-title-hint">Maksimal 100 karakter.</small>
        </div>

        <div class="note-editor-field<?= !empty($formErrors['content']) ? ' has-error' : '' ?>">
            <label for="note-content">Isi Catatan</label>
            <textarea id="note-content" name="content" rows="8" placeholder="Tulis catatan di sini..." aria-describedby="note-content-hint"<?= !empty($formErrors['content']) ? ' aria-invalid="true"' : '' ?>><?= htmlspecialchars($content, ENT_QUOTES, 'UTF-8') ?></textarea>
            <small class="note-create-hint" id="note-content-hint">Minimal 10 karakter.</small>
        </div>

        <div class="note-editor-actions">
            <a class="cancel-button" href="index.php">Batal</a>
            <button type="submit">Simpan</button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>