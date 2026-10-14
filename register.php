<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'includes/csrf.php';
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$registerValues = ['nama' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $namaInput = $_POST['nama'] ?? null;
    $usernameInput = $_POST['username'] ?? null;
    $emailInput = $_POST['email'] ?? null;
    $passwordInput = $_POST['password'] ?? null;
    $nama = is_string($namaInput) ? trim($namaInput) : '';
    $username = is_string($usernameInput) ? trim($usernameInput) : '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';
    $registerValues = ['nama' => $nama, 'username' => $username, 'email' => $email];

    $errorNama = validasiNamaLengkap($nama);
    $errorEmail = validasiEmail($email);
    $usernameLength = function_exists('mb_strlen') ? mb_strlen($username, 'UTF-8') : strlen($username);
    if (function_exists('mb_strlen')) {
        $passwordLength = mb_strlen($password, 'UTF-8');
    } elseif (preg_match_all('/./us', $password, $passwordCharacters) !== false) {
        $passwordLength = count($passwordCharacters[0]);
    } else {
        $passwordLength = 0;
    }
    $errorUsername = $username === '' ? 'Username tidak boleh kosong.' : ($usernameLength > 50 ? 'Username maksimal 50 karakter.' : null);
    $errorPassword = $passwordLength < 8 ? 'Password minimal 8 karakter.' : null;
    if ($errorEmail === null && strlen($email) > 100) {
        $errorEmail = 'Email maksimal 100 karakter.';
    }

    if ($errorNama || $errorUsername || $errorEmail || $errorPassword) {
        $_SESSION['form_error'] = $errorNama ?? $errorUsername ?? $errorEmail ?? $errorPassword;
    } else {
        try {
            $duplicate = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
            mysqli_stmt_bind_param($duplicate, 'ss', $username, $email);
            mysqli_stmt_execute($duplicate);

            if (mysqli_fetch_assoc(mysqli_stmt_get_result($duplicate))) {
                $_SESSION['form_error'] = 'Username atau email sudah digunakan.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($koneksi, 'INSERT INTO users (nama, username, email, password) VALUES (?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'ssss', $nama, $username, $email, $hashed);
                mysqli_stmt_execute($stmt);
                header('Location: login.php?status=daftar_sukses');
                exit;
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Account registration failed: ' . $exception->getMessage());
            $_SESSION['form_error'] = 'Username atau email sudah digunakan.';
        }
    }
}
$registerError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);
?>
<?php include 'includes/header.php'; ?>

<main class="auth-page auth-page-register" aria-labelledby="auth-title">
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
            <h1 id="auth-title">Buat Akun Baru</h1>
            <p>Mulai atur catatan dan tugasmu dengan lebih mudah.</p>
        </header>

        <?php if ($registerError !== null): ?>
            <p class="auth-message auth-message-error" role="alert"><?= $escape($registerError) ?></p>
        <?php endif; ?>

        <form class="auth-form" action="register.php" method="POST">
            <?= csrf_field() ?>
            <label class="auth-field">
                <span>Nama Lengkap</span>
                <input type="text" name="nama" autocomplete="name" maxlength="100" required value="<?= $escape($registerValues['nama']) ?>" placeholder="Masukkan nama lengkap">
            </label>

            <label class="auth-field">
                <span>Username</span>
                <input type="text" name="username" autocomplete="username" maxlength="50" required value="<?= $escape($registerValues['username']) ?>" placeholder="Buat username">
            </label>

            <label class="auth-field">
                <span>Email</span>
                <input type="email" name="email" autocomplete="email" maxlength="100" required value="<?= $escape($registerValues['email']) ?>" placeholder="Masukkan email">
            </label>

            <label class="auth-field">
                <span>Password</span>
                <span class="auth-password-control">
                    <input type="password" name="password" autocomplete="new-password" minlength="8" required aria-describedby="password-help" placeholder="Buat password">
                    <button class="auth-password-toggle" type="button" data-toggle-password aria-label="Tampilkan password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.3-6 9.5-6 9.5 6 9.5 6-3.3 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    </button>
                </span>
                <small class="auth-field-hint" id="password-help">Minimal 8 karakter.</small>
            </label>

            <button class="auth-submit" type="submit">Daftar</button>
        </form>
        <p class="auth-prompt">Sudah punya akun? <a href="login.php">Login di sini.</a></p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>