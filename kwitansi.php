<?php
session_start();
include 'koneksi1.php'; // Ensure the database connection is correct

// Check if purchase_id is available in the URL
if (isset($_GET['purchase_id'])) {
    $purchase_id = $_GET['purchase_id'];



    // Query to fetch purchase details by purchase_id
    $query = "
    SELECT p.id AS purchase_id, p.username, p.total_price, p.purchase_date, 
           pi.product_id, pi.price AS item_price, 
           cart.product_name, cart.product_image 
    FROM purchases p
    JOIN purchase_items pi ON p.id = pi.purchase_id
    JOIN cart ON pi.product_id = cart.id
    WHERE p.id = ?";  // Ensure the query fetches details for a single purchase by purchase_id

    // Prepare and execute the query
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param('i', $purchase_id);  // Bind the purchase_id as an integer parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any data was returned
    if ($result->num_rows > 0) {
        $purchase_details = [];
        while ($row = $result->fetch_assoc()) {
            $purchase_details[] = $row;
        }
    } else {

        $query = "
        SELECT p.id AS purchase_id, p.username, p.total_price, p.purchase_date, 
           pi.product_id, pi.price AS item_price, 
           nikeairmax.Nama, nikeairmax.images , nikeairmax.Harga
    FROM purchases p
    JOIN purchase_items pi ON p.id = pi.purchase_id
    JOIN nikeairmax ON pi.product_id = nikeairmax.ID
    WHERE p.id = ?";  // Ensure the query fetches details for a single purchase by purchase_id

        // Prepare and execute the query
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('i', $purchase_id);  // Bind the purchase_id as an integer parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any data was returned
        if ($result->num_rows > 0) {
            $purchase_details = [];
            while ($row = $result->fetch_assoc()) {
                $purchase_details[] = $row;
            }
        } else {
            echo "Transaction not found.";
            exit();
        }

        
    }
} else {
    echo "Purchase ID not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembelian</title>
    <link rel="stylesheet" href="">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: 80%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .receipt h1 {
            text-align: center;
            color: #4CAF50;
            font-size: 2.5em;
        }

        .receipt .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .receipt .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .receipt .header h3 {
            font-size: 1.5em;
            margin: 0;
        }

        .receipt .header p {
            margin: 0;
            font-size: 0.9em;
        }

        .receipt .details {
            margin-bottom: 30px;
        }

        .receipt .details ul {
            list-style-type: none;
            padding: 0;
        }

        .receipt .details li {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }

        .receipt .details .item-name {
            font-weight: bold;
        }

        .receipt .details .item-price {
            color: #28a745;
        }

        .receipt .total {
            font-size: 1.5em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .receipt .date {
            text-align: center;
            margin-top: 20px;
            font-size: 1em;
            color: #777;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        .buttons button {
            color: white;
            background-color: #4CAF50;
            padding: 10px 20px;
            margin: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .print-hide {
            display: none;
        }

        /* CSS untuk menghilangkan tombol saat print */
        @media print {

            .buttons,
            .print-hide {
                display: none;
            }

            .receipt {
                width: 100%;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>

    <div class="receipt" id="receipt">
        <div class="header">
            <img src="images/logo.png" alt="Logo Toko">
            <h3>Toko Sepatu A Shoesh</h3>
            <p>Jl. Contoh No.123, Jakarta, Indonesia</p>
        </div>

        <h1>Kwitansi Pembelian</h1>

        <div class="details">
            <ul>
                <?php
                $total_price = 0;
                foreach ($purchase_details as $item) {
                    if(isset($item['Nama'])) {
                        $total_price += $item['Harga'];  // Use Harga to calculate total price
                        echo "<li><span class='item-name'>" . htmlspecialchars($item['Nama']) . "</span> - Rp. " . number_format($item['Harga'], 2) . "</li>";
                    } else {
                        $total_price += $item['item_price'];  // Use item_price to calculate total price
                        echo "<li><span class='item-name'>" . htmlspecialchars($item['product_name']) . "</span> - Rp. " . number_format($item['item_price'], 2) . "</li>";
                    }
                }
                ?>
            </ul>

            <div class="total">
                Total: Rp. <?php echo number_format($total_price, 2); ?>
            </div>

            <div class="date">
                <strong>Tanggal Pembelian:</strong> <?php echo date('d-m-Y', strtotime($purchase_details[0]['purchase_date'])); ?>
            </div>
        </div>

        <div class="buttons">
            <button onclick="window.print()">Cetak Kwitansi</button>
            <button onclick="downloadReceipt()">Download Kwitansi PDF</button>
            <a href="index.php?clear_cart=true"><button>Kembali</button></a>

        </div>
    </div>

    <!-- Load html2canvas -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>



    <!-- Load jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        const {
            jsPDF
        } = window.jspdf;

        // Define the downloadReceipt function after external scripts are loaded
        function downloadReceipt() {
            const content = document.getElementById('receipt');

            if (!content) {
                alert('Elemen receipt tidak ditemukan!');
                return;
            }

            if (typeof html2canvas === 'undefined') {
                alert('html2canvas tidak dimuat dengan benar!');
                return;
            }

            // Hide the buttons before generating the PDF
            document.querySelector('.buttons').style.display = 'none';

            // Use html2canvas and jsPDF to generate the PDF
            html2canvas(content).then(function(canvas) {
                const doc = new jsPDF();
                const imgData = canvas.toDataURL('image/png');
                doc.addImage(imgData, 'PNG', 10, 10);
                doc.save('kwitansi.pdf');

                // Show the buttons again after generating the PDF
                document.querySelector('.buttons').style.display = 'block';
            }).catch(function(err) {
                console.error('Gagal membuat canvas', err);
                document.querySelector('.buttons').style.display = 'block'; // Ensure buttons are shown even if there's an error
            });
        }
    </script>

</body>

</html>