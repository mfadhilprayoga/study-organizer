<?php session_start();?>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitles = [
    'index.php' => 'Catatan',
    'tugas.php' => 'Tugas',
    'dashboard.php' => 'Beranda',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] . ' | ' : '' ?>Study Organizer</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= in_array($currentPage, ['login.php','register.php']) ? 'login-view' : '' ?>">
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="dashboard.php" aria-label="Study Organizer">
            <span class="brand-mark" aria-hidden="true">◇</span>
            <span>Study <strong>Organizer</strong></span>
        </a>
        <nav class="main-nav" aria-label="Navigasi utama">
            <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><span class="nav-icon">⌂</span>Beranda</a>
            <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php"><span class="nav-icon">▣</span>Catatan</a>
            <a class="<?= $currentPage === 'tugas.php' ? 'active' : '' ?>" href="tugas.php"><span class="nav-icon">☑</span>Tugas</a>
            <a class="<?= in_array($currentPage, ['login.php', 'register.php']) ? 'active' : '' ?>" href="dashboard.php#profil"><span class="nav-icon">♙</span>Profil</a>
        </nav>
        <a class="logout-link" href="logout.php"><span class="nav-icon">⇥</span>Logout</a>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <span class="mobile-brand">Study <strong>Organizer</strong></span>
            <div class="topbar-actions">
                <button class="theme-button" type="button" aria-label="Ubah tema">☼</button>
                <span class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></span>
                <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'Pengguna') ?></span>
                <span class="chevron">⌄</span>
            </div>
        </header>
        <div class="page-content">
