<?php
require 'includes/auth_check.php';
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

$userId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($userId === false) {
    http_response_code(403);
    exit('Akses profil tidak valid.');
}

$stmt = mysqli_prepare($koneksi, 'SELECT nama, username, email FROM users WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$profile = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$profile) {
    http_response_code(404);
    exit('Profil pengguna tidak ditemukan.');
}

$_SESSION['nama'] = $profile['nama'];
$_SESSION['username'] = $profile['username'];
$_SESSION['email'] = $profile['email'];

$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$profileError = null;
$isEditing = false;
$formValues = ['nama' => $profile['nama'], 'email' => $profile['email']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    if (($_POST['action'] ?? '') !== 'update_profile') {
        http_response_code(400);
        exit('Permintaan profil tidak valid.');
    }
    $isEditing = true;

    $namaInput = $_POST['nama'] ?? null;
    $emailInput = $_POST['email'] ?? null;
    if (!is_string($namaInput) || !is_string($emailInput)) {
        $profileError = 'Informasi profil tidak valid.';
    } else {
        $formValues = ['nama' => trim($namaInput), 'email' => trim($emailInput)];
        $profileError = validasiNamaLengkap($formValues['nama']) ?? validasiEmail($formValues['email']);
        if ($profileError === null && strlen($formValues['email']) > 100) {
            $profileError = 'Email maksimal 100 karakter.';
        }
    }

    if ($profileError === null) {
        try {
            $check = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE email = ? AND id <> ?');
            mysqli_stmt_bind_param($check, 'si', $formValues['email'], $userId);
            mysqli_stmt_execute($check);

            if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
                $profileError = 'Email tersebut sudah digunakan oleh akun lain.';
            } else {
                $update = mysqli_prepare($koneksi, 'UPDATE users SET nama = ?, email = ? WHERE id = ?');
                mysqli_stmt_bind_param($update, 'ssi', $formValues['nama'], $formValues['email'], $userId);
                mysqli_stmt_execute($update);

                $_SESSION['nama'] = $formValues['nama'];
                $_SESSION['email'] = $formValues['email'];
                $_SESSION['profile_notice'] = 'Informasi profil berhasil diperbarui.';
                header('Location: profil.php');
                exit;
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Profile update failed: ' . $exception->getMessage());
            $profileError = 'Profil tidak dapat diperbarui saat ini. Silakan coba lagi.';
        }
    }
}

$profileNotice = $_SESSION['profile_notice'] ?? null;
unset($_SESSION['profile_notice']);
if ($profileError !== null) {
    $profile['nama'] = $formValues['nama'];
    $profile['email'] = $formValues['email'];
}
?>
<?php include 'includes/header.php'; ?>

<section class="profile-page">
    <header class="profile-page-header">
        <h1>Profil</h1>
        <p>Kelola informasi akunmu.</p>
    </header>

    <?php if ($profileNotice): ?>
        <p class="profile-message profile-message-success" role="status"><?= $escape($profileNotice) ?></p>
    <?php endif; ?>
    <?php if ($profileError !== null): ?>
        <p class="profile-message profile-message-error" role="alert"><?= $escape($profileError) ?></p>
    <?php endif; ?>

    <div class="profile-card-grid">
        <section class="profile-card profile-information-card" aria-labelledby="profile-information-title">
            <div class="profile-card-heading">
                <h2 id="profile-information-title">Informasi Profil</h2>
                <p>Pastikan informasi akunmu selalu terbaru.</p>
            </div>

            <dl class="profile-details" id="profile-details" <?= $isEditing ? 'hidden' : '' ?>>
                <div class="profile-field">
                    <dt>Nama Lengkap</dt>
                    <dd><?= $escape($profile['nama']) ?></dd>
                </div>
                <div class="profile-field">
                    <dt>Username</dt>
                    <dd class="profile-readonly-value"><?= $escape($profile['username']) ?></dd>
                </div>
                <div class="profile-field">
                    <dt>Email</dt>
                    <dd><?= $escape($profile['email']) ?></dd>
                </div>
            </dl>

            <form class="profile-edit-form" id="edit-profile-form" action="profil.php" method="POST" <?= $isEditing ? '' : 'hidden' ?>>
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_profile">
                <label class="profile-field">
                    <span>Nama Lengkap</span>
                    <input type="text" name="nama" maxlength="100" required value="<?= $escape($formValues['nama']) ?>">
                </label>
                <label class="profile-field">
                    <span>Username</span>
                    <input type="text" value="<?= $escape($profile['username']) ?>" readonly aria-readonly="true">
                </label>
                <label class="profile-field">
                    <span>Email</span>
                    <input type="email" name="email" maxlength="100" required value="<?= $escape($formValues['email']) ?>">
                </label>
                <div class="profile-edit-actions">
                    <a class="profile-cancel-button" href="profil.php">Batal</a>
                    <button type="submit">Simpan Perubahan</button>
                </div>
            </form>

            <?php if (!$isEditing): ?>
                <div class="profile-card-actions">
                    <button id="edit-profile-trigger" type="button">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 6 4 4M4 20l4.2-.9L19 8.3a2.1 2.1 0 0 0-3-3L5.2 16.1 4 20Z"/></svg>
                        Edit Profil
                    </button>
                </div>
            <?php endif; ?>
        </section>

        <aside class="profile-card profile-security-card" aria-labelledby="profile-security-title">
            <div class="profile-security-heading">
                <span class="profile-security-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 3 19 6v5c0 4.4-2.8 8-7 10-4.2-2-7-5.6-7-10V6l7-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <div class="profile-card-heading">
                    <h2 id="profile-security-title">Keamanan Akun</h2>
                    <p>Perlindungan akunmu tetap menjadi prioritas.</p>
                </div>
            </div>
            <p class="profile-security-message">Gunakan password yang kuat dan jangan membagikan informasi login kepada siapa pun.</p>
        </aside>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
