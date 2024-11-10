<?php
session_start();
include 'koneksi1.php';



// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Ambil data produk dari POST
$product_id = $_POST['product_id'];
$product_price = $_POST['product_price'];
$product_image = $_POST['product_image'];

// Ambil username dari session
$username = $_SESSION['username']; // Ini adalah username pengguna yang login

// Cek apakah produk sudah ada di tabel `nikeairmax`
$query = "SELECT * FROM nikeairmax WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika produk tidak ditemukan di `nikeairmax`, tampilkan pesan error
if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found in nikeairmax']);
    exit;
}

// Ambil data produk dari hasil query
$product = $result->fetch_assoc();
$product_name = $product['Nama'];
$product_image = $product['images'];
$product_price = $product['Harga'];

// Step 1: Tambahkan produk ke cart (di session) jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Inisialisasi cart jika belum ada
}

// Cek apakah produk sudah ada di cart (session)
$product_exists_in_cart = false;
foreach ($_SESSION['cart'] as $item) {
    if ($item['product_id'] == $product_id) {
        $product_exists_in_cart = true;
        break;
    }
}

// Jika produk belum ada di cart, tambahkan
if (!$product_exists_in_cart) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'product_image' => $product_image,
        'price' => $product_price
    ];

    // Masukkan ke dalam tabel `cart` jika produk belum ada
    $query = "SELECT * FROM cart WHERE product_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    if ($cart_result->num_rows == 0) {
        // Produk belum ada di tabel cart, maka kita insert
        $query = "INSERT INTO cart (product_id, product_image, price) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('isi', $product_id, $product_image, $product_price);
        $stmt->execute();

        if ($stmt->error) {
            echo json_encode(['success' => false, 'message' => 'Error inserting into cart: ' . $stmt->error]);
            exit;
        }
    }
}

// Step 2: Buat catatan pembelian di tabel `purchases`
$purchase_date = date('Y-m-d H:i:s');
$query = "INSERT INTO purchases (username, total_price, purchase_date) VALUES (?, ?, NOW())";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('sd', $username, $product_price); // 'd' untuk harga sebagai float
$stmt->execute();

if ($stmt->error) {
    echo json_encode(['success' => false, 'message' => 'Error creating purchase record: ' . $stmt->error]);
    exit();
}

$purchase_id = $stmt->insert_id; // Ambil ID pembelian yang baru dibuat

// Step 3: Masukkan item yang dibeli ke tabel `purchase_items`
foreach ($_SESSION['cart'] as $item) {
    // Pastikan produk ada di cart sebelum memasukkannya ke purchase_items
    $query = "INSERT INTO purchase_items (purchase_id, product_id, price) VALUES (?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('iii', $purchase_id, $item['product_id'], $item['price']);
    $stmt->execute();
}

// Jika pembelian berhasil
if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'purchase_id' => $purchase_id]);

    // Setelah pembelian selesai, bersihkan cart
    unset($_SESSION['cart']);  // Menghapus cart setelah pembelian
} else {
    echo json_encode(['success' => false, 'message' => 'Purchase failed']);
}
?>
