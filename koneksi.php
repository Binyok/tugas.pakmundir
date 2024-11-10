<?php
// koneksi.php

// Mengatur koneksi database
$host = "localhost";
$user = "root";
$password = "";
$database = "pendaftaran_bimbel";

// Membuat koneksi menggunakan mysqli
$koneksi = new mysqli($host, $user, $password, $database);

// Memeriksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Koneksi berhasil
// echo "Koneksi berhasil"; // Anda bisa menghilangkan atau menambahkan baris ini sesuai kebutuhan
?>
