<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitles = [
    'index.php' => 'Catatan',
    'tugas.php' => 'Tugas',
    'dashboard.php' => 'Dashboard',
    'profil.php' => 'Profil',
];
$sessionDisplayName = trim((string) ($_SESSION['nama'] ?? ''));
if ($sessionDisplayName === '') {
    $sessionDisplayName = trim((string) ($_SESSION['username'] ?? 'Pengguna'));
}
$sessionDisplayName = ucwords(strtolower($sessionDisplayName));
$sessionInitial = strtoupper(substr($sessionDisplayName, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] . ' | ' : '' ?>Study Organizer</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= in_array($currentPage, ['login.php','register.php']) ? 'login-view' : ($currentPage === 'dashboard.php' ? 'dashboard-view' : '') ?>">
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="dashboard.php" aria-label="Study Organizer">
            <span class="brand-mark" aria-hidden="true">
                <svg width="30" height="30" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                    <path d="M3 7c4.5-2 9-1.2 13 1.6V27C11.8 24.2 7.4 23.5 3 25V7Z"/>
                    <path d="M29 7c-4.5-2-9-1.2-13 1.6V27c4.2-2.8 8.6-3.5 13-2V7Z"/>
                    <path d="M16 8.6V27"/>
                    <path class="brand-bookmark" d="M12 3h8v11l-4-2.7-4 2.7V3Z"/>
                </svg>
            </span>
            <span>Study <strong>Organizer</strong></span>
        </a>
        <nav class="main-nav" aria-label="Navigasi utama">
            <a class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m3.5 10 8.5-7 8.5 7v10a1 1 0 0 1-1 1h-5.2v-6.5h-4.6V21H4.5a1 1 0 0 1-1-1V10Z"/></svg></span>
                <span class="nav-label">Beranda</span>
            </a>
            <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3.5h8l4 4V20a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1Z"/><path d="M14.5 4v4h4M8.5 12h7M8.5 15.5h7"/></svg></span>
                <span class="nav-label">Catatan</span>
            </a>
            <a class="<?= $currentPage === 'tugas.php' ? 'active' : '' ?>" href="tugas.php">
                <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="m8 12 2.5 2.5L16 9"/></svg></span>
                <span class="nav-label">Tugas</span>
            </a>
            <a class="<?= $currentPage === 'profil.php' ? 'active' : '' ?>" href="profil.php">
                <?php if ($currentPage === 'dashboard.php'): ?>
                    <span class="profile-avatar" aria-hidden="true"><?= htmlspecialchars($sessionInitial) ?></span>
                    <span class="nav-profile-copy"><span class="nav-label">Profil</span><small><?= htmlspecialchars($sessionDisplayName) ?></small></span>
                <?php else: ?>
                    <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M4.5 20c.5-3.4 3.3-5.5 7.5-5.5s7 2.1 7.5 5.5"/></svg></span>
                    <span class="nav-profile-copy"><span class="nav-label">Profil</span><small><?= htmlspecialchars($sessionDisplayName) ?></small></span>
                <?php endif; ?>
            </a>
        </nav>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <span class="mobile-brand">Study <strong>Organizer</strong></span>
            <?php if ($currentPage === 'dashboard.php'): ?>
                <h1 class="dashboard-topbar-title">Dashboard</h1>
            <?php endif; ?>
            <div class="topbar-actions">
                <button class="theme-button" type="button" aria-label="Ubah tema"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg></button>
                <span class="avatar" aria-hidden="true"><?= htmlspecialchars($sessionInitial) ?></span>
                <span class="user-name"><?= htmlspecialchars($sessionDisplayName) ?></span>
                <?php if ($currentPage !== 'dashboard.php'): ?>
                    <span class="chevron">⌄</span>
                <?php endif; ?>
            </div>
        </header>
        <div class="page-content <?= $currentPage === 'dashboard.php' ? 'dashboard-content' : '' ?>">