<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: lore.php'); // Arahkan ke halaman login
    exit();
}
?>


<?php
include 'koneksi1.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Fetch product details from the database
    $query = "SELECT * FROM nikeairmax WHERE ID = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $namasepatu = $result->fetch_assoc(); // Get the product data

        if (!$namasepatu) {
            echo "Produk tidak ditemukan.";
            exit;
        }
    } else {
        echo "Query gagal: " . mysqli_error($koneksi);
        exit;
    }
} else {
    echo "ID produk tidak valid.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Sepatu</title>
    <link rel="stylesheet" href="toko.css?v=1.12">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <header>
        <h1>Detail Sepatu</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#contact-container">Contact</a></li>
                <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <center>
            <div class="product-card">
                <h2><?php echo htmlspecialchars($namasepatu['Nama']); ?></h2>
                <img src="<?php echo htmlspecialchars($namasepatu['images']); ?>" alt="<?php echo htmlspecialchars($namasepatu['Nama']); ?>" style="max-width: 400px; border-radius: 5px;">
                <p><?php echo htmlspecialchars($namasepatu['description']); ?></p>

            <form action="#">
                <input type="hidden" id="priceProduct" value="<?php echo $namasepatu['Harga']; ?>">
            </form>

                <p>Harga: <span id="product-price">Rp. <?php echo number_format($namasepatu['Harga'], 2); ?></span></p><br><br><br>
                <div class="button-container">
            <button id="purchase-button" class="button-buy" type="button" onclick="buyNow()">Beli Sekarang</button>
                <button id="add-to-cart-button" class="button-cart" type="button" onclick="addToCart()">Tambah ke Keranjang</button>
            </div>
            </div>
            
        </center>

        <center>
           
            <!-- Notification container -->
<div id="notification">
    Barang berhasil ditambahkan ke keranjang!
</div>

        </center>
        <br><br><br><br>
        <center>
            <div class="contact-container">
                <h3>Kontak Kami</h3>
                <p>Jika Anda memiliki pertanyaan, silakan hubungi kami:</p>
                <div class="contact-icons">
                    <a href="https://wa.me/qr/NA2VZII3LNRXC1" target="_blank" class="contact-icon">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    <a href="https://www.instagram.com/biii_binyokk/profilecard/?igsh=eTVycW5kaXA4M2Fn" target="_blank" class="contact-icon">
                        <i class="fab fa-instagram"></i>
                        <span>Instagram</span>
                    </a>
                </div>
            </div>
        </center>

        <!-- Modal for Receipt -->
        <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); justify-content:center; align-items:center;">
            <div id="receipt" style="background: #fff; padding: 20px; border-radius: 10px; width: 300px; text-align: center;">
                <h2>Kuitansi Pembelian</h2>
                <p><strong>Produk:</strong> <span id="product-name"><?php echo htmlspecialchars($namasepatu['Nama']); ?></span></p>
                <p><strong>Harga:</strong> <span id="product-price"><?php echo number_format($namasepatu['Harga'], 2); ?></span></p>
                <p><strong>Tanggal:</strong> <span id="receipt-date"></span></p>
                <button onclick="printReceipt()">Print Kuitansi</button>
                <button onclick="downloadReceipt()"><i class="fas fa-file-download"></i> Download Kuitansi</button>
                <br>
                <button onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </main>

    <footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3>Tentang Kami</h3>
            <p>Kami adalah toko sepatu online yang menyediakan berbagai macam sepatu berkualitas untuk segala usia. Kami berkomitmen untuk memberikan pengalaman belanja yang terbaik bagi pelanggan kami.</p>
        </div>
       
        <div class="footer-section">
            <h3>Ikuti Kami</h3>
            <ul class="footer-links">
                <li><a href="https://www.facebook.com">Facebook</a></li>
                <li><a href="https://www.instagram.com">Instagram</a></li>
                <li><a href="https://www.twitter.com">Twitter</a></li>
                <li><a href="https://www.youtube.com">YouTube</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Hubungi Kami</h3>
            <p>Email: support@toko-sepatu.com</p>
            <p>Telepon: +62 123 456 789</p>
            <p>Alamat: Jl. Contoh No. 123, Jakarta, Indonesia</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2023 Toko Sepatu. All rights reserved.</p>
    </div>
</footer>

    <script>
        const { jsPDF } = window.jspdf;

        function buyNow() {
    // Ambil data produk
    const productId = "<?php echo $namasepatu['ID']; ?>"; // ID produk
    const productName = "<?php echo htmlspecialchars($namasepatu['Nama']); ?>";
    const productImage = "<?php echo htmlspecialchars($namasepatu['images']); ?>"; // Gambar produk
    const productPrice = "<?php echo $namasepatu['Harga']; ?>";

    // Kirim permintaan ke server untuk menambahkan item ke keranjang dan langsung checkout
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "checkout.php", true);  // Ganti dengan URL untuk proses checkout
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Jika berhasil, proses respons JSON
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Redirect ke halaman kwitansi dengan membawa purchase_id
                    window.location.href = "kwitansi.php?purchase_id=" + response.purchase_id;
                } else {
                    console.log(response);
                    showNotification("Checkout gagal, silakan coba lagi.", true);
                }
            } else {
                showNotification("Terjadi kesalahan, silakan coba lagi.", true);
            }
        }
    };
    
    xhr.send(`product_id=${productId}&product_name=${encodeURIComponent(productName)}&price=${encodeURIComponent(productPrice)}&product_image=${encodeURIComponent(productImage)}`);
}


// Fungsi untuk menampilkan kwitansi
function showReceipt(productName, productPrice, purchaseId) {
    const date = new Date().toLocaleDateString();
    document.getElementById('receipt-date').innerText = date;
    document.getElementById('product-name').innerText = productName;
    document.getElementById('product-price').innerText = "Rp. " + productPrice;
    document.getElementById('receipt').style.display = 'block';
    document.getElementById('modal').style.display = 'block';
    document.getElementById('purchase-id').innerText = `ID Pembelian: ${purchaseId}`;
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, isError = false) {
    const notification = document.getElementById("notification");
    notification.innerHTML = message;

    // Set background color untuk error
    if (isError) {
        notification.style.backgroundColor = "#f44336"; // Merah untuk error
    } else {
        notification.style.backgroundColor = "#4CAF50"; // Hijau untuk sukses
    }

    // Tampilkan notifikasi
    notification.style.display = "block";
    setTimeout(() => {
        notification.style.display = "none";
    }, 3000);  // Sembunyikan setelah 3 detik
}

        function addToCart() {
    const productId = "<?php echo $namasepatu['NamaSepatuID']; ?>"; // Assuming you have this ID
    const productName = document.getElementById('product-name').innerText;
    const productImage = document.querySelector('img').src;

    // Ambil harga dengan cara yang lebih tepat
    const priceString = document.getElementById('priceProduct').value;

    const price = parseFloat(priceString); // Ubah string menjadi angka

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_keranjang.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Display success message
                showNotification("Barang berhasil ditambahkan ke keranjang!");

                // Optionally, you can hide the notification after 3 seconds
                setTimeout(() => {
                    document.getElementById("notification").style.display = "none";
                }, 3000);
            } else {
                // If there's an error, display a different message
                showNotification("Terjadi kesalahan saat menambahkan barang ke keranjang.", true);

                // Optionally, hide after 3 seconds
                setTimeout(() => {
                    document.getElementById("notification").style.display = "none";
                }, 3000);
            }
        }
    };
    xhr.send(`product_id=${productId}&product_name=${encodeURIComponent(productName)}&product_image=${encodeURIComponent(productImage)}&price=${price}`);
}

function showNotification(message, isError = false) {
    const notification = document.getElementById("notification");
    notification.innerHTML = message;

    // Set the background color to red for error messages
    if (isError) {
        notification.style.backgroundColor = "#f44336"; // Red for error
    } else {
        notification.style.backgroundColor = "#4CAF50"; // Green for success
    }

    // Show the notification
    notification.style.display = "block";
}

    </script>
</body>
</html>
