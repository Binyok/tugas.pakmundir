<?php
include 'koneksi1.php'; // Ensure this connects to your database

// Fetch all products for display
$queryProducts = "SELECT * FROM nikeairmax";
$resultProducts = $koneksi->query($queryProducts); // Corrected variable name

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Owner</title>
    <link rel="stylesheet" href="toko.css">
</head>
<body>
    <header>
        <h1>Dashboard Owner</h1>
        <nav>
            <ul>
                <li><a href="owner.php">Home</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="sales.php">View Sales</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Manage Products</h2>

        <h3>Existing Products</h3>
        <div id="product-list">
            <?php while ($nikeairmax = $resultProducts->fetch_assoc()): ?> <!-- Corrected loop -->
            <div class="product-item"> <!-- Changed the class to a valid one -->
                <img src="<?php echo htmlspecialchars($nikeairmax['image']); ?>" alt="<?php echo htmlspecialchars($nikeairmax['name']); ?>">
                <h4><?php echo htmlspecialchars($nikeairmax['name']); ?></h4>
                <p><?php echo htmlspecialchars($nikeairmax['description']); ?></p>
                <p class="price">Harga: $<?php echo number_format($nikeairmax['price'], 2); ?></p>
                <a href="edit.php?id=<?php echo $nikeairmax['id']; ?>"><button>Edit</button></a>
                <a href="delete.php?id=<?php echo $nikeairmax['id']; ?>"><button>Delete</button></a>
            </div>
            <?php endwhile; ?>
        </div> <!-- Close product list div -->
    </main>

    <footer>
        <p>Made by Your Name</p>
    </footer>
</body>
</html>
