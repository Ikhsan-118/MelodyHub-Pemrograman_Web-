<?php
session_start();

// Jika sudah login arahkan ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
} else {
    // Jika belum login arahkan ke login
    header("Location: login.php");
    exit();
}
