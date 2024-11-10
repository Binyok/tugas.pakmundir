<?php
include 'koneksi1.php'; // Pastikan path ini benar

$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Mengambil ID dari query string
$query = "SELECT * FROM products WHERE id = $id"; // Ganti dengan nama tabel yang sesuai
$result = $koneksi->query($query);

if ($result) {
    $product = $result->fetch_assoc();
    if ($product) {
        echo json_encode($product); // Mengembalikan data dalam format JSON
    } else {
        echo json_encode(['error' => 'Produk tidak ditemukan.']);
    }
} else {
    echo json_encode(['error' => 'Query gagal: ' . mysqli_error($koneksi)]);
}
?>
