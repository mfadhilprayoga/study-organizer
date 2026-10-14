<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';
require 'includes/date_helpers.php';

$id = $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT id, title, content, created_at, updated_at, UNIX_TIMESTAMP(created_at) AS created_at_epoch, UNIX_TIMESTAMP(updated_at) AS updated_at_epoch FROM notes WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$catatan = mysqli_fetch_assoc($hasil);

if (!$catatan) {
    die("Catatan tidak ditemukan.");
}

$createdAt = dateTimeDariTimestamp((int) $catatan['created_at_epoch']);
$updatedAt = $catatan['updated_at_epoch'] !== null
    ? dateTimeDariTimestamp((int) $catatan['updated_at_epoch'])
    : null;
?>
<?php include 'includes/header.php'; ?>

<section class="note-detail-page">
    <header class="note-detail-header">
        <div class="note-detail-heading">
            <h1><?= htmlspecialchars($catatan['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="note-detail-meta">
                <p>Dibuat pada · <time datetime="<?= htmlspecialchars($createdAt->format(DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(formatTanggalIndonesia($createdAt->format('Y-m-d')) . ', ' . $createdAt->format('H:i'), ENT_QUOTES, 'UTF-8') ?></time></p>
                <?php if ($updatedAt !== null): ?>
                    <p>Terakhir diperbarui · <time datetime="<?= htmlspecialchars($updatedAt->format(DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(formatTanggalIndonesia($updatedAt->format('Y-m-d')) . ', ' . $updatedAt->format('H:i'), ENT_QUOTES, 'UTF-8') ?></time></p>
                <?php else: ?>
                    <p>Belum pernah diperbarui</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="note-detail-actions">
            <a class="detail-edit-button" href="edit.php?id=<?= htmlspecialchars((string) $catatan['id'], ENT_QUOTES, 'UTF-8') ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14.5 6.5 3 3M4 20l4.1-.9L19 8.2a2.1 2.1 0 0 0-3-3L5.1 16.1 4 20Z"/></svg>
                Edit
            </a>
            <a class="detail-delete-button note-delete-trigger" href="hapus.php?id=<?= htmlspecialchars((string) $catatan['id'], ENT_QUOTES, 'UTF-8') ?>" aria-haspopup="dialog" aria-controls="note-delete-modal" data-note-title="<?= htmlspecialchars($catatan['title'], ENT_QUOTES, 'UTF-8') ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/></svg>
                Hapus
            </a>
        </div>
    </header>
    <article class="note-detail-content"><?= nl2br(htmlspecialchars($catatan['content'], ENT_QUOTES, 'UTF-8')) ?></article>
    <a class="note-detail-back" href="index.php">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m11 5-7 7 7 7M4 12h16"/></svg>
        Kembali ke Catatan
    </a>
</section>

<?php include 'includes/note_delete_modal.php'; ?>
<?php include 'includes/footer.php'; ?>
