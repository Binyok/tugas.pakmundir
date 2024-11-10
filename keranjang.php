<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: lore.php'); // Arahkan ke halaman login
    exit();
}


include 'koneksi1.php'; // Pastikan file ini terhubung ke database

// Ambil item keranjang dari database
$query = "SELECT * FROM cart";
$result = $koneksi->query($query);
$cartItems = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembelian</title>
    <link rel="stylesheet" href="keranjang.css?v=1.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        /* Styles for modal, buttons, etc. remain the same */
    </style>
</head>
<body>
    <header>
        <button class="back-button" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> 
        </button>
        <h1>Keranjang Belanja</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div id="cart">
            <h2>Items in Your Cart</h2>
            <div class="cart-items">
                <?php if ($cartItems): ?>
                    <form id="checkout-form">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" data-id="<?php echo $item['id']; ?>">
                                <input type="checkbox" class="item-checkbox" value="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>" onchange="updateTotalPrice()">
                                <img src="<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="cart-image">
                                <div class="item-details">
                                    <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                    <p>Harga: Rp. <?php echo number_format($item['price']); ?></p>
                                </div>
                                <button type="button" class="remove-button" onclick="showRemoveItemModal(<?php echo $item['id']; ?>)">Hapus</button>
                            </div>
                        <?php endforeach; ?>
                    </form>
                <?php else: ?>
                    <p>Keranjang Anda kosong.</p>
                <?php endif; ?>
            </div>

            <div class="cart-summary">
                <p><strong>Total: Rp. <span id="total-price">0</span></strong></p>
                <button class="checkout-button" onclick="showCheckoutConfirmation()">Checkout</button>
            </div>

            <!-- Modal Konfirmasi Checkout -->
            <div id="confirm-checkout-modal" style="display: none;">
                <div id="confirm-checkout-box">
                    <h3>Konfirmasi Checkout</h3>
                    <p>Apakah Anda yakin ingin melanjutkan dengan checkout?</p>
                    <button id="confirm-checkout-button" class="confirm-button">Ya</button>
                    <button class="cancel-button" onclick="closeConfirmCheckoutModal()">Tidak</button>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Item -->
            <div id="remove-item-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h3>Konfirmasi Penghapusan</h3>
                    <p>Apakah Anda yakin ingin menghapus item ini dari keranjang?</p>
                    <div class="modal-actions">
                        <button id="confirm-remove" class="btn btn-danger">Ya, Hapus</button>
                        <button id="cancel-remove" class="btn btn-secondary">Batal</button>
                    </div>
                </div>
            </div>

            <!-- Elemen notifikasi -->
            <div id="notification" class="notification" style="display: none;"></div>
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
        let itemToRemoveId = null;
        let selectedItemsForCheckout = [];

        // Fungsi untuk menampilkan modal penghapusan item
        function showRemoveItemModal(id) {
            itemToRemoveId = id;
            document.getElementById('remove-item-modal').style.display = 'block';
        }

        // Fungsi untuk menutup modal penghapusan item
        document.getElementById('cancel-remove').addEventListener('click', function() {
            document.getElementById('remove-item-modal').style.display = 'none';
        });

        // Konfirmasi penghapusan item
        document.getElementById('confirm-remove').addEventListener('click', function() {
    fetch(`remove_cart.php?id=${itemToRemoveId}`, {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message);
            removeItemFromUI(itemToRemoveId);
            updateTotalPrice(); // Memperbarui total harga
        } else {
            showNotification(data.message, true);
        }
        document.getElementById('remove-item-modal').style.display = 'none';
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menghapus item.', true);
        document.getElementById('remove-item-modal').style.display = 'none';
    });
});


        // Menampilkan notifikasi
        function showNotification(message, isError = false) {
            const notificationElement = document.getElementById('notification');
            notificationElement.innerText = message;
            notificationElement.style.display = 'block';
            notificationElement.style.backgroundColor = isError ? '#f44336' : '#4CAF50';
            setTimeout(() => {
                notificationElement.style.display = 'none';
            }, 3000);
        }

        // Menghapus item dari DOM
        function removeItemFromUI(id) {
            const itemElement = document.querySelector(`.cart-item[data-id="${id}"]`);
            if (itemElement) {
                itemElement.remove();
            }
            // Jika keranjang kosong
            const cartItems = document.querySelectorAll('.cart-item');
            if (cartItems.length === 0) {
                const emptyMessage = document.createElement('p');
                emptyMessage.textContent = 'Keranjang Anda kosong.';
                document.getElementById('cart').appendChild(emptyMessage);
            }
        }

        // Memperbarui total harga
        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll(".cart-items .item-checkbox:checked").forEach(checkbox => {
                const price = parseFloat(checkbox.getAttribute("data-price"));
                total += price;
            });
            document.getElementById('total-price').innerText = total.toFixed(2);
        }

        // Menampilkan modal konfirmasi checkout
        function showCheckoutConfirmation() {
            const selectedItems = [];
            document.querySelectorAll(".item-checkbox:checked").forEach(checkbox => {
                selectedItems.push(checkbox.value);
            });

            if (selectedItems.length > 0) {
                selectedItemsForCheckout = selectedItems;
                document.getElementById('confirm-checkout-modal').style.display = 'flex';
            } else {
                showNotification('Silakan pilih produk untuk checkout.');
            }
        }

        // Menutup modal konfirmasi checkout
        function closeConfirmCheckoutModal() {
            document.getElementById('confirm-checkout-modal').style.display = 'none';
        }

        // Proses checkout
        document.getElementById('confirm-checkout-button').addEventListener('click', function() {
            fetch('checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    item_ids: selectedItemsForCheckout.join(',')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `kwitansi.php?purchase_id=${data.purchase_id}`;
                } else {
                    showNotification(`Checkout gagal: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat melakukan checkout.');
            });
        });
        
    </script>
</body>
</html>
