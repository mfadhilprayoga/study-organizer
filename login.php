<?php
session_start();
require 'includes/csrf.php';
require 'includes/db_koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $identitas = trim($_POST['identitas']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $identitas, $identitas);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($hasil);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username/email atau password salah.";
    }
}
?>
<?php include 'includes/header.php'; ?>

<h1>Login</h1>

<?php if (isset($_GET['status']) && $_GET['status'] == 'daftar_sukses'): ?>
    <p style="color: green;">Registrasi berhasil, silakan login.</p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="login.php" method="POST">
    <?= csrf_field() ?>
    <label>Username atau Email</label><br>
    <input type="text" name="identitas" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>

<?php include 'includes/footer.php'; ?>