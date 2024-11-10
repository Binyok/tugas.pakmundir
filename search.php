<?php
include 'koneksi1.php'; // Ensure this path is correct
session_start();

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM nikeairmax WHERE Nama LIKE '%?%'"; // Adjust the query to search shoes
$result = $koneksi->query($query);
?>

// The rest of your HTML structure remains the same
