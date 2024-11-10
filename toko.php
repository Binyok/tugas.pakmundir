<?php
include 'koneksi1.php'; // Pastikan Anda menghubungkan database

$query = "SELECT * FROM products"; // Ganti dengan nama tabel yang sesuai
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jual Beli Sepatu</title>
    <link rel="stylesheet" href="toko.css">
</head>
<body>
    <header>
        <h1>Welcome to Sepatu E-Commerce</h1>
        <nav>
            <ul>
                <li><a href="toko.php">Home</a></li>
                <li><a href="beli.php">Products</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
            <form action="#" method="get">
                <input type="text" placeholder="Search products..." name="search">
                <button type="submit">Search</button>
            </form>
        </nav>
    </header>

    <main>
        <h2>Available Shoes</h2>
        <div id="product-list">
            <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <h3><?php echo $product['name']; ?></h3>
                <div class="product-description">
                    <h4>Spesifikasi:</h4>
                    <p><?php echo $product['description']; ?></p>
                </div>
                <p class="price">Harga: Rp. <?php echo number_format($product['price'],decimals:2 ); ?></p>
                <a href="beli.php?id=<?php echo $product['id']; ?>"><button>Lihat</button></a>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

</body>
</html>
