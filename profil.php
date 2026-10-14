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
$accountDeleteError = null;
$isDeleteModalOpen = false;
$isEditing = false;
$formValues = ['nama' => $profile['nama'], 'email' => $profile['email']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $csrfToken = $_POST['csrf_token'] ?? null;
    if (!is_string($csrfToken) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        if ($action === 'delete_account') {
            $isDeleteModalOpen = true;
            $accountDeleteError = 'Token keamanan tidak valid. Akun tidak dihapus. Silakan coba lagi.';
        } else {
            $isEditing = true;
            $profileError = 'Token keamanan tidak valid. Perubahan profil tidak disimpan.';
        }
    } else {
        csrf_verify();
    }

    if ($profileError === null && $accountDeleteError === null && $action === 'delete_account') {
        $isDeleteModalOpen = true;
        $passwordInput = $_POST['current_password'] ?? null;

        if (!is_string($passwordInput) || $passwordInput === '') {
            $accountDeleteError = 'Masukkan kata sandi saat ini untuk mengonfirmasi penghapusan.';
        } else {
            try {
                $passwordQuery = mysqli_prepare($koneksi, 'SELECT password FROM users WHERE id = ?');
                mysqli_stmt_bind_param($passwordQuery, 'i', $userId);
                mysqli_stmt_execute($passwordQuery);
                $userCredentials = mysqli_fetch_assoc(mysqli_stmt_get_result($passwordQuery));

                if (!$userCredentials || !password_verify($passwordInput, $userCredentials['password'])) {
                    $accountDeleteError = 'Kata sandi saat ini tidak sesuai. Akun tidak dihapus.';
                } else {
                    $delete = mysqli_prepare($koneksi, 'DELETE FROM users WHERE id = ?');
                    mysqli_stmt_bind_param($delete, 'i', $userId);
                    mysqli_stmt_execute($delete);

                    if (mysqli_stmt_affected_rows($delete) !== 1) {
                        $accountDeleteError = 'Akun tidak dapat dihapus saat ini. Silakan coba lagi.';
                    } else {
                        $_SESSION = [];
                        if (ini_get('session.use_cookies')) {
                            $cookie = session_get_cookie_params();
                            setcookie(session_name(), '', time() - 42000, $cookie['path'], $cookie['domain'], (bool) $cookie['secure'], (bool) $cookie['httponly']);
                        }
                        session_destroy();
                        header('Location: login.php?status=akun_dihapus');
                        exit;
                    }
                }
            } catch (mysqli_sql_exception $exception) {
                error_log('Account deletion failed: ' . $exception->getMessage());
                $accountDeleteError = 'Akun tidak dapat dihapus saat ini. Silakan coba lagi.';
            }
        }
    } elseif ($profileError === null && $accountDeleteError === null && $action !== 'update_profile') {
        http_response_code(400);
        exit('Permintaan profil tidak valid.');
    } elseif ($profileError === null && $accountDeleteError === null) {
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

    <section class="profile-danger-zone" aria-labelledby="profile-danger-title">
        <div>
            <h2 id="profile-danger-title">Pengaturan Akun</h2>
            <p>Penghapusan akun bersifat permanen dan akan menghapus catatan serta tugas yang terkait.</p>
        </div>
        <button class="profile-delete-trigger" id="open-account-delete" type="button">Hapus Akun</button>
    </section>
</section>

<dialog class="profile-delete-dialog" id="account-delete-dialog" aria-labelledby="account-delete-title" aria-describedby="account-delete-description" <?= $isDeleteModalOpen ? 'data-reopen="true"' : '' ?>>
    <form class="account-delete-form" action="profil.php" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete_account">
        <span class="account-delete-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m3 0-.8 13H6.8L6 7m4 4v5m4-5v5"/></svg>
        </span>
        <h2 id="account-delete-title">Hapus akun?</h2>
        <p id="account-delete-description">Tindakan ini permanen. Akun, catatan, dan tugas milikmu akan dihapus dan tidak dapat dipulihkan.</p>

        <?php if ($accountDeleteError !== null): ?>
            <p class="profile-message profile-message-error account-delete-error" role="alert"><?= $escape($accountDeleteError) ?></p>
        <?php endif; ?>

        <label class="profile-field account-delete-password">
            <span>Kata sandi saat ini</span>
            <input type="password" name="current_password" autocomplete="current-password" required>
        </label>
        <div class="account-delete-actions">
            <button class="account-delete-cancel" type="button" data-close-account-delete>Batal</button>
            <button class="account-delete-confirm" type="submit">Hapus Akun</button>
        </div>
    </form>
</dialog>

<?php include 'includes/footer.php'; ?>
