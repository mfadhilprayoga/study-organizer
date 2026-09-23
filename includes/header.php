<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Study Organizer</title>
     <link rel="stylesheet" href="assets/style.css">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <nav>
    <a href="index.php">Beranda</a>
    <a href="tambah.php">+ Tambah Catatan</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <span>Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>