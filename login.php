<?php
session_start();

// Jika user sudah login, langsung ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Data login sederhana
    if ($user === "Ikhsan" && $pass === "118") {
        $_SESSION['username'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - MelodyHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-title">MelodyHub</h1>
            <p class="login-subtitle">Masuk ke akunmu</p>

            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logout'): ?>
                <p class="success">Anda berhasil logout.</p>
            <?php endif; ?>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'belum_login'): ?>
                <p class="error">Anda harus login terlebih dahulu!</p>
            <?php endif; ?>

            <form method="POST" action="login.php" class="login-form">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="login-btn">Login</button>
            </form>

            <p class="login-footer">Belum punya akun? <a href="#">Daftar</a></p>
        </div>
    </div>
</body>
</html>
