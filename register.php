<?php
session_start();
require 'includes/csrf.php';
require 'includes/db_koneksi.php';
require 'includes/validasi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errorNama = validasiNamaLengkap($nama);
    $errorEmail = validasiEmail($email);

    if ($errorNama || $errorEmail) {
        $_SESSION['form_error'] = $errorNama ?? $errorEmail;
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama, username, email, password) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $email, $hashed);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php?status=daftar_sukses");
            exit;
        } else {
            $_SESSION['form_error'] = "Username atau email sudah dipakai, coba yang lain.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<h1>Daftar Akun Baru</h1>

<?php if (isset($_SESSION['form_error'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_SESSION['form_error']) ?></p>
    <?php unset($_SESSION['form_error']); ?>
<?php endif; ?>

<form action="register.php" method="POST">
    <?= csrf_field() ?>
    <label>Nama Lengkap</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Daftar</button>
</form>

<p>Sudah punya akun? <a href="login.php">Login di sini</a></p>

<?php include 'includes/footer.php'; ?>