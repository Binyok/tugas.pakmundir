<?php
$host = 'localhost'; // atau alamat host database Anda
$user = 'root'; // username database
$pass = ''; // password database
$dbname = 'toko'; // nama database

$koneksi = new mysqli($host, $user, $pass, $dbname);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
