<?php
session_start();
include 'koneksi1.php';  // Pastikan koneksi database sudah benar
// checkout.php

// Setelah checkout berhasil
if (isset($_POST['checkout'])) {
    // Clear keranjang setelah checkout
    unset($_SESSION['cart']);

    // Redirect kembali ke index.php dengan clear_cart=true
    header('Location: keranjang.php?clear_cart=true');
    exit();
}


// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User tidak terautentikasi.']);
    exit();
}

// Cek apakah request menggunakan method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek apakah ada data item yang dipilih
    if (isset($_POST['item_ids']) && !empty($_POST['item_ids'])) {
        $item_ids = explode(',', $_POST['item_ids']);

        // Ambil username dari session
        $username = $_SESSION['username'];
        $total_price = 0;

        // Ambil detail produk berdasarkan item_id
        $product_details = [];
        foreach ($item_ids as $item_id) {
            $query = "SELECT * FROM cart WHERE id = ?";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Jika produk ditemukan
            if ($product = $result->fetch_assoc()) {
                $product_details[] = $product;
                $total_price += $product['price'];
            } else {
                // Jika ada item yang tidak ditemukan di cart
                echo json_encode(['success' => false, 'message' => 'Item dengan ID ' . $item_id . ' tidak ditemukan di cart.']);
                exit();
            }
        }

        // Simpan transaksi pembelian ke tabel 'purchases'
        $query = "INSERT INTO purchases (username, total_price, purchase_date) VALUES (?, ?, NOW())";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('id', $username, $total_price);
        $stmt->execute();

        if ($stmt->error) {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan transaksi: ' . $stmt->error]);
            exit();
        }

        // Ambil ID transaksi yang baru saja disimpan
        $purchase_id = $stmt->insert_id;

        // Simpan item-item yang dibeli ke tabel 'purchase_items'
        foreach ($product_details as $product) {
            $query = "INSERT INTO purchase_items (purchase_id, product_id, price) VALUES (?, ?, ?)";
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param('iid', $purchase_id, $product['id'], $product['price']);
            $stmt->execute();

            if ($stmt->error) {
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan item: ' . $stmt->error]);
                exit();
            }
        }

        // Kirim response JSON dengan ID transaksi
        echo json_encode([
            'success' => true,
            'purchase_id' => $purchase_id,
            'total_price' => $total_price,
            'message' => 'Checkout berhasil!'
        ]);
        exit();
    } else if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
       

        // Ambil username dari session
        $username = $_SESSION['username'];
        $total_price = 0;

        $query = "SELECT * FROM nikeairmax WHERE ID = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('i', $_POST['product_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        // var_dump($result->fetch_assoc());
        // exit();

        // Jika produk ditemukan
        if ($product = $result->fetch_assoc() == 0) {

            echo json_encode(['success' => false, 'message' => 'Item dengan ID ' . $item_id . ' tidak ditemukan.']);
            exit();
        }


        // Simpan transaksi pembelian ke tabel 'purchases'
        $query = "INSERT INTO purchases (username, total_price, purchase_date) VALUES (?, ?, NOW())";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('id', $username, $_POST['price']);
        $stmt->execute();

        if ($stmt->error) {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan transaksi: ' . $stmt->error]);
            exit();
        }

        // Ambil ID transaksi yang baru saja disimpan
        $purchase_id = $stmt->insert_id;

        $query = "INSERT INTO purchase_items (purchase_id, product_id, price) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('iid', $purchase_id, $_POST['product_id'], $_POST['price']);
        $stmt->execute();

       

        if ($stmt->error) {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan item: ' . $stmt->error]);
            exit();
        }


        // Kirim response JSON dengan ID transaksi
        echo json_encode([
            'success' => true,
            'purchase_id' => $purchase_id,
            'total_price' => $_POST['price'],
            'message' => 'Checkout berhasil!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada item yang dipilih untuk checkout.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Request tidak valid.']);
}
