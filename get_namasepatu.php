<?php
include 'koneksi1.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo json_encode(['error' => 'ID tidak valid.']);
    exit();
}

$query = "SELECT * FROM nikeairmax WHERE NamaSepatuID = $id";
$result = $koneksi->query($query);

if ($result) {
    $namasepatuList = [];
    while ($namasepatu = $result->fetch_assoc()) {
        $namasepatuList[] = $namasepatu;
    }

    if (count($namasepatuList) > 0) {
        echo json_encode($namasepatuList);
    } else {
        echo json_encode(['error' => 'Produk tidak ditemukan.']);
    }
} else {
    echo json_encode(['error' => 'Query gagal: ' . mysqli_error($koneksi)]);
}
?>
