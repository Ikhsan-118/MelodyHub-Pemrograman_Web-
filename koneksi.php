<?php
// koneksi.php
// konfigurasi koneksi database MySQL (ubah sesuai environment)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';     // ganti password jika ada
$db_name = 'melodyhub'; // nama database yang akan dibuat via melodyhub.sql

// membuat koneksi
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// cek koneksi
if ($conn->connect_error) {
    // jangan menampilkan kredensial pada produksi; ini untuk dev lokal
    die("Koneksi gagal: " . $conn->connect_error);
}

// set charset
$conn->set_charset("utf8mb4");

/**
 * helper sederhana untuk mendapatkan koneksi global
 * include 'koneksi.php' lalu gunakan $conn langsung.
 */
