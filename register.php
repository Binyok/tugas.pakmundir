<?php
// Start session
session_start();

// Connect to the database
include 'koneksi1.php'; // Make sure the database connection is correct

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve data from the form
    $name = $_POST['name'];
    $akses = $_POST['akses']; // 'akses' is either "owner" or "admin"
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password and confirmation match
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "<h2>Password and confirm password do not match!</h2>";
        header('Location: register.php'); // Redirect back to register page
        exit();
    }

    // Hash password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Set id_petugas (Assuming the first petugas is 1)
    $idPetugas = 1;  // Adjust based on valid petugas ID or fetch dynamically

    // Prepare statement to prevent SQL Injection
    $stmt = $koneksi->prepare("INSERT INTO users (username, password, akses, id_petugas) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $hashedPassword, $akses, $idPetugas); // 'sssi' means string, string, string, integer

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['message'] = "<h2>Registration successful! Please login.</h2>";
        header('Location: lore.php'); // Redirect to login page after registration
    } else {
        $_SESSION['message'] = "<h2>An error occurred, please try again!</h2>";
        header('Location: register.php'); // Redirect back to register page
    }

    // Close statement and database connection
    $stmt->close();
    $koneksi->close();
}
?>
