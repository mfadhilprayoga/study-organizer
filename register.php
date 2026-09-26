<?php
session_start();
require 'includes/csrf.php';
require 'includes/db_koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php?status=daftar_sukses");
        exit;
    } else {
        $error = "Username sudah dipakai, coba yang lain.";
    }
}
?>
<?php include 'includes/header.php'; ?>

<section class="login-panel">
    <div class="login-art">
        <div class="study-illustration" aria-hidden="true">
            <span class="art-orbit"></span>
            <svg viewBox="0 0 260 180" role="presentation">
                <path class="art-book-cover" d="M48 76h164a7 7 0 0 1 7 7v69H41V83a7 7 0 0 1 7-7Z"/>
                <path class="art-book-page" d="M50 82h66v54H50zM144 82h66v54h-66z"/>
                <path class="art-book-spine" d="M116 78c10-8 23-8 28 0v58c-6-8-18-8-28 0V78Z"/>
                <path class="art-laptop" d="M32 151h196l12 13H20l12-13Z"/>
                <path class="art-laptop-line" d="M104 157h52"/>
                <path class="art-plant-pot" d="M224 119h25l-3 31h-19l-3-31Z"/>
                <path class="art-plant" d="M236 118V88m0 18c-12-1-15-8-14-16 9 1 14 6 14 16Zm0-8c1-10 7-15 16-15 0 9-5 15-16 15Z"/>
                <path class="art-spark" d="m207 61 3 7 7 3-7 3-3 7-3-7-7-3 7-3 3-7Zm-169 50 2 5 5 2-5 2-2 5-2-5-5-2 5-2 2-5Z"/>
            </svg>
        </div>
        <h2>Study Organizer</h2>
        <p>Kelola catatan dan tugasmu<br> dengan lebih mudah.</p>
    </div>
    <div class="login-content">
        <h1>Daftar Akun</h1>
        <p class="login-subtitle">Buat akun baru untuk mulai belajar</p>

        <?php if (isset($error)): ?>
            <p class="login-message error-message"><?= $error ?></p>
        <?php endif; ?>

        <form class="login-form" action="register.php" method="POST">
            <?= csrf_field() ?>

            <label for="fullname">Nama Lengkap</label>
            <input id="fullname" type="text" name="fullname" placeholder="Masukkan nama lengkap">

            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Pilih username" required>

            <label for="password">Password</label>
            <div class="password-field">
                <input id="password" type="password" name="password" placeholder="Minimal 6 karakter" required>
                <span class="password-icon" aria-hidden="true">◉</span>
            </div>

            <button type="submit">Daftar</button>
        </form>

        <p class="register-prompt">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>