<?php
require 'includes/auth_check.php';
?>
<?php include 'includes/header.php'; ?>

<div class="detail-box">
    <h1>Profil Saya</h1>

    <p><strong>Nama:</strong> <?= htmlspecialchars($_SESSION['nama']) ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>

    <form action="logout.php" method="POST">
        <?= csrf_field() ?>
        <button type="submit">Logout</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>