<?php
session_start();
include 'koneksi1.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $buyer_name = $_POST['buyer_name'];
    $product_id = $_POST['product_id'];

    $stmt = $koneksi->prepare("INSERT INTO sales (buyer_name, product_id) VALUES (?, ?)");
    $stmt->bind_param("si", $buyer_name, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Data penjualan berhasil disimpan.";
    } else {
        echo "Gagal menyimpan data penjualan.";
    }

    $stmt->close();
    $koneksi->close();
}
?>
