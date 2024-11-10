<?php
session_start();
include 'koneksi1.php'; // Pastikan koneksi database sudah benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User tidak terautentikasi.']);
    exit();
}

// Cek apakah purchase_id diterima
if (isset($_POST['purchase_id'])) {
    $purchase_id = $_POST['purchase_id'];

    // Query untuk mendapatkan semua product_id yang dibeli berdasarkan purchase_id
    $query = "SELECT product_id FROM purchase_items WHERE purchase_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('i', $purchase_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil semua product_id yang terkait dengan purchase_id
    $product_ids = [];
    while ($row = $result->fetch_assoc()) {
        $product_ids[] = $row['product_id'];
    }

    if (count($product_ids) > 0) {
        // Hapus produk dari cart yang ada di purchase_items
        $product_ids_placeholder = implode(',', array_fill(0, count($product_ids), '?'));
        $query = "DELETE FROM cart WHERE id IN ($product_ids_placeholder)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt->execute();

        if ($stmt->error) {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus item dari cart: ' . $stmt->error]);
            exit();
        }

        echo json_encode(['success' => true, 'message' => 'Produk berhasil dihapus dari cart.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan di cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID pembelian tidak ditemukan.']);
}
?>
