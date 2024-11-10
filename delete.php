<?php
include 'koneksi1.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $koneksi->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header('Location: owner.php'); // Redirect ke halaman owner
}
?>
