<?php
include 'koneksi1.php'; // Include your database connection

// Pastikan semua data yang dibutuhkan ada di POST
if (isset($_POST['product_id'], $_POST['product_name'], $_POST['product_image'], $_POST['price'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_image = $_POST['product_image'];
    $price = $_POST['price'];

    // Cek apakah produk sudah ada di cart
    $checkQuery = "SELECT * FROM cart WHERE id = ?";
    $stmt = $koneksi->prepare($checkQuery);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Produk sudah ada di keranjang!";
    } else {
        // Insert produk ke keranjang
        $insertQuery = "INSERT INTO cart (product_id, product_name, product_image, price) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($insertQuery);
        $stmt->bind_param('issd', $product_id, $product_name, $product_image, $price);
        if ($stmt->execute()) {
            echo "Produk berhasil ditambahkan ke keranjang!";
        } else {
            echo "Terjadi kesalahan saat menambahkan produk ke keranjang: " . $stmt->error;
        }
    }
} else {
    echo "Data tidak lengkap!";
}

?>
