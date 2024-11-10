<?php
include 'koneksi1.php'; // Ensure this connects to your database

// Fetch sales data
$querySales = "SELECT id, buyer_name, product_id, sale_date FROM sales";
$resultSales = $koneksi->query($querySales);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Records</title>
    <link rel="stylesheet" href="toko.css">
</head>
<body>
    <header>
        <h1>Sales Records</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="owner.php">Manage Products</a></li>
                <li><a href="add_product.php">Add Product</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Buyer Name</th>
                    <th>Product ID</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sale = $resultSales->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['id']); ?></td>
                    <td><?php echo htmlspecialchars($sale['buyer_name']); ?></td>
                    <td><?php echo htmlspecialchars($sale['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>Made by Your Name</p>
    </footer>
</body>
</html>
