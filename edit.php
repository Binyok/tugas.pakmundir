<?php
include 'koneksi1.php'; // Ensure this connects to your database

// Handle editing a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $stmt = $koneksi->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
    $stmt->bind_param("ssdsi", $name, $description, $price, $image, $id);
    $stmt->execute();
    header("Location: owner.php"); // Redirect after updating
    exit();
}        

// Fetch the product to edit
$product_id = $_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="toko.css">
</head>
<body>
    <header>
        <h1>Edit Product</h1>
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
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required step="0.01">
            <input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" required>
            <button type="submit" name="edit">Update Product</button>
        </form>
    </main>

    <footer>
        <p>Made by Your Name</p>
    </footer>
</body>
</html>
