<?php
include 'koneksi1.php'; // Ensure this connects to your database

// Handle adding a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $stmt = $koneksi->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $description, $price, $image);
    $stmt->execute();
    header("Location: owner.php"); // Redirect after adding
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="toko.css">
</head>
<body>
    <header>
        <h1>Add New Product</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="owner.php">Manage Products</a></li>
                <li><a href="sales.php">View Sales</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form action="add_product.php" method="post">
            <input type="text" name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Product Description" required></textarea>
            <input type="number" name="price" placeholder="Price" required step="0.01">
            <input type="text" name="image" placeholder="Image URL" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </main>

    <footer>
        <p>Made by Your Name</p>
    </footer>
</body>
</html>
