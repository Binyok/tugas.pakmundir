<?php
include 'koneksi1.php'; // Pastikan file ini terhubung ke database

// Cek apakah ada parameter id yang diterima
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    // Menyiapkan query untuk menghapus item dari keranjang
    $query = "DELETE FROM cart WHERE id = ?";
    
    // Menyiapkan statement
    if ($stmt = $koneksi->prepare($query)) {
        // Bind parameter (untuk menghindari SQL injection)
        $stmt->bind_param("i", $itemId);

        // Eksekusi query
        if ($stmt->execute()) {
            // Kirim response JSON jika berhasil
            echo json_encode(['success' => true, 'message' => 'Item berhasil dihapus dari keranjang.']);
        } else {
            // Kirim response JSON jika gagal
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus item.']);
        }

        // Menutup statement setelah eksekusi
        $stmt->close();
    } else {
        // Jika statement gagal dipersiapkan
        echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan query.']);
    }
} else {
    // Jika tidak ada id yang diberikan
    echo json_encode(['success' => false, 'message' => 'ID item tidak ditemukan.']);
}

// Menutup koneksi setelah operasi selesai
$koneksi->close();
?>
