<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'includes/csrf.php';
require 'includes/db_koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $identitasInput = $_POST['identitas'] ?? null;
    $passwordInput = $_POST['password'] ?? null;
    $identitas = is_string($identitasInput) ? trim($identitasInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';
    $loginIdentity = $identitas;

    $stmt = mysqli_prepare($koneksi, 'SELECT id, nama, username, email, password FROM users WHERE username = ? OR email = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $identitas, $identitas);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($hasil);

    if ($identitas !== '' && $password !== '' && $user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        header('Location: index.php');
        exit;
    }
    $error = 'Username atau email atau password tidak sesuai.';
}
?>
<?php include 'includes/header.php'; ?>

<main class="auth-page" aria-labelledby="auth-title">
    <a class="auth-brand" href="login.php" aria-label="Study Organizer">
        <svg class="auth-brand-mark" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 7c4.5-2 9-1.2 13 1.6V27C11.8 24.2 7.4 23.5 3 25V7Z"/>
            <path d="M29 7c-4.5-2-9-1.2-13 1.6V27c4.2-2.8 8.6-3.5 13-2V7Z"/>
            <path d="M16 8.6V27"/>
            <path class="auth-brand-bookmark" d="M12 3h8v11l-4-2.7-4 2.7V3Z"/>
        </svg>
        <span>Study Organizer</span>
    </a>

    <section class="auth-card">
        <header class="auth-card-heading">
            <h1 id="auth-title">Selamat datang kembali!</h1>
            <p>Masuk ke akunmu untuk melanjutkan.</p>
        </header>

        <?php if (($_GET['status'] ?? '') === 'daftar_sukses'): ?>
            <p class="auth-message auth-message-success" role="status">Pendaftaran berhasil. Silakan login.</p>
        <?php elseif (($_GET['status'] ?? '') === 'akun_dihapus'): ?>
            <p class="auth-message auth-message-success" role="status">Akun dan data terkait berhasil dihapus.</p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="auth-message auth-message-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form class="auth-form" action="login.php" method="POST">
            <?= csrf_field() ?>
            <label class="auth-field">
                <span>Username atau Email</span>
                <input type="text" name="identitas" autocomplete="username" required value="<?= htmlspecialchars($loginIdentity ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan username atau email">
            </label>

            <label class="auth-field">
                <span>Password</span>
                <span class="auth-password-control">
                    <input type="password" name="password" autocomplete="current-password" required placeholder="Masukkan password">
                    <button class="auth-password-toggle" type="button" data-toggle-password aria-label="Tampilkan password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    </button>
                </span>
            </label>

            <button class="auth-submit" type="submit">Login</button>
        </form>
        <p class="auth-prompt">Belum punya akun? <a href="register.php">Daftar di sini.</a></p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>