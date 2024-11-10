<?php
// Memulai sesi
session_start();

// Menghubungkan file koneksi.php yang berada dalam folder yang sama
include "koneksi1.php"; 

// Mengambil data dari form
$username = $_POST['username']; // Perbaiki typo dari 'ussername' ke 'username'
$password = $_POST['password'];

// Menyiapkan dan menjalankan query untuk mencegah SQL Injection
$stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Mengecek apakah username ditemukan
if ($data = $result->fetch_assoc()) {
    // Mengecek password menggunakan password_verify() karena password di-hash
    if (password_verify($password, $data['password'])) {
        // Menyimpan session setelah login berhasil
        $_SESSION['message'] = "<h2>Login berhasil</h2>";
        $_SESSION['akses'] = $data['akses'];
        $_SESSION['username'] = $data['username']; 
        
        // Arahkan pengguna sesuai level akses
        if ($_SESSION['akses'] == "owner") {
            header('Location: owner.php'); // Sesuaikan dengan path sebenarnya
        } elseif ($_SESSION['akses'] == "admin") {
            header('Location: index.php'); // Sesuaikan dengan path sebenarnya
        }
        
        exit();
    } else {
        $_SESSION['message'] = "<h2>Username atau password salah</h2>";
        header('Location: lore.php'); // Redirect ke halaman login dengan pesan kesalahan
        exit();
    }
} else {
    $_SESSION['message'] = "<h2>Username atau password salah</h2>";
    header('Location: lore.php'); // Redirect ke halaman login dengan pesan kesalahan
    exit();
}
?>
