<?php
include 'koneksi1.php'; // Pastikan jalur ini benar
session_start();

// Dapatkan query pencarian jika ada
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
// $searchQueryEscaped = $koneksi->real_escape_string($searchQuery); // Mencegah SQL injection

// Sesuaikan query untuk mencari semua sepatu berdasarkan namanya
$query = "SELECT * FROM namasepatu";
$result = $koneksi->query($query);

$querySepatu = "SELECT * FROM nikeairmax  WHERE Nama LIKE '%$searchQuery%'";

$resultSepatu = $koneksi->query($querySepatu);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REKAYASA PERANGKAT LUNAK</title>
    <link rel="stylesheet" href="satu.css?v=1.17">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
            <h1 class="title">A Shoes</h1>
        </div>
        <div class="search">
            <form action="" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari Sepatu..." class="search-input" id="search-input" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
                <button type="submit" style="display: none;">Search</button> <!-- Tambahkan tombol submit yang tidak terlihat -->
            </form>
        </div>
        <div class="header-right">
            <nav>
                <ul class="nav-links">
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="#"><i class="fas fa-info-circle"></i> About Us</a></li>
                    <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> cart</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="sidebar">
            

            <form action="lore.php" method="post" class="login-form" style="margin-top: 10px;">
                <button type="submit">Login/Regis</button>
            </form>

            <?php if (isset($_SESSION['username'])): ?>
                <h2>Semua Sepatu</h2>
                <div id="brand-list">
                    <ul>

                        <?php while ($namasepatu = $result->fetch_assoc()): ?>
                            <li>
                                <a href="#" class="shoe-link" data-id="<?php echo $namasepatu['ID']; ?>">
                                    <h3><?php echo $namasepatu['Nama']; ?></h3>
                                </a>
                            </li>
                        <?php endwhile; ?>

                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div id="content" class="namasepatu-info">
            <?php if ($resultSepatu->num_rows > 0): ?>
                <?php while ($sepatu = $resultSepatu->fetch_assoc()): ?>
                    <div class="namasepatu-card">
                        <h3><?php echo htmlspecialchars($sepatu['Nama']); ?></h3><br>
                        <img src="<?php echo htmlspecialchars($sepatu['image']); ?>" alt="<?php echo htmlspecialchars($sepatu['Nama']); ?>"><br><br>
                        <p><?php echo htmlspecialchars($sepatu['description']); ?></p><br>
                        <p><strong>Harga: Rp. </strong><?php echo number_format($sepatu['Harga'], ); ?></p><br>
                        <a href="beli.php?id=<?php echo $sepatu['ID']; ?>" class="btn-lihat">Lihat</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <li>Tidak ada hasil untuk "<?php echo htmlspecialchars($searchQuery); ?>"</li>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.querySelector('#search-input').addEventListener('input', function() {
            const searchValue = this.value;
            const searchQuery = encodeURIComponent(searchValue); // Encode untuk keamanan

            fetch(`search.php?search=${searchQuery}`) // Ubah sesuai dengan file yang memproses pencarian
                .then(response => response.json())
                .then(data => {
                    const content = document.getElementById('content');
                    content.innerHTML = ''; // Clear previous content

                    if (data.length > 0) {
                        data.forEach(namasepatu => {
                            content.innerHTML += `
                        <div class="namasepatu-card">
                            <h3>${namasepatu.Nama}</h3><br>
                            <img src="${namasepatu.image}" alt="${namasepatu.Nama}"><br><br>
                            <p>${namasepatu.description}</p><br>
                            <p><strong>Harga: Rp. </strong>${namasepatu.Harga}</p><br>
                            <a href="beli.php?id=${namasepatu.NamaSepatuID}" class="btn-lihat">Lihat</a>
                        </div>
                    `;
                        });
                    } else {
                        content.innerHTML = '<p>Tidak ada hasil untuk pencarian ini.</p>';
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');

        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        document.querySelectorAll('.shoe-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const namasepatuId = this.getAttribute('data-id');
                fetch(`get_namasepatu.php?id=${namasepatuId}`)
                    .then(response => response.json())
                    .then(data => {
                        const content = document.getElementById('content');
                        content.innerHTML = ''; // Clear previous content
                        data.forEach(namasepatu => {
                            content.innerHTML += `
                        <div class="namasepatu-card">
                            <h3>${namasepatu.Nama}</h3><br>
                            <img src="${namasepatu.image}" alt="${namasepatu.Nama}"><br><br>
                            <p>${namasepatu.description}</p><br>
                            <p><strong>Harga: Rp. </strong>${namasepatu.Harga}</p><br>
                            <a href="beli.php?id=${namasepatu.ID}" class="btn-lihat">Lihat</a>
                        </div>
                    `;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>

</html>