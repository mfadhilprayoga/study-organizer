<?php
require 'includes/db_koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

<h1>Daftar Akun Baru</h1>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form action="register.php" method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Daftar</button>
</form>

<p>Sudah punya akun? <a href="login.php">Login di sini</a></p>

<?php include 'includes/footer.php'; ?>