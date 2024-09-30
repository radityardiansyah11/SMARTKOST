<?php
include 'config.php';
session_start();

// Fetch kost details from the database based on a specific ID or parameter
$kost_id = $_GET['id']; // Assuming you pass the ID in the URL as a query string

// Fetch kost details
$sql = "SELECT * FROM kost WHERE id = '$kost_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of the kost
    $row = $result->fetch_assoc();

    // Fetch spesifikasi kamar
    $spesifikasi_sql = "SELECT spesifikasi FROM spesifikasi_kamar WHERE kost_id = '$kost_id'";
    $spesifikasi_result = $conn->query($spesifikasi_sql);

    // Fetch fasilitas kamar
    $fasilitas_sql = "SELECT fasilitas FROM fasilitas_kamar WHERE kost_id = '$kost_id'";
    $fasilitas_result = $conn->query($fasilitas_sql);

    // Fetch fasilitas kamar mandi
    $fasilitas_mandi_sql = "SELECT fasilitas FROM fasilitas_kamar_mandi WHERE kost_id = '$kost_id'";
    $fasilitas_mandi_result = $conn->query($fasilitas_mandi_sql);

} else {
    echo "No kost found";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>detail-SMARTKOST</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img2/mini logo smartkost.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <link href="css/detail-kost.css" rel="stylesheet">
</head>

<body class="bg-white mb-5">
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- gambar -->
        <div class="container mt-5">
            <div class="row">
                <!-- Left Section: Image Gallery -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <img src="<?php echo $row['gambar_1']; ?>" class="img-fluid main-image rounded"
                                alt="Main Image">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <img src="<?php echo $row['gambar_2']; ?>" class="img-fluid thumbnail-image rounded"
                                alt="Thumbnail 1">
                        </div>
                        <div class="col-4">
                            <img src="<?php echo $row['gambar_3']; ?>" class="img-fluid thumbnail-image rounded"
                                alt="Thumbnail 2">
                        </div>
                        <div class="col-4">
                            <img src="<?php echo $row['gambar_4']; ?>" class="img-fluid thumbnail-image rounded"
                                alt="Thumbnail 3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="row mt-5">
                <div class="col-md-8">
                    <h2><?php echo $row['nama_kost']; ?></h2>
                    <div class="icon-text">
                        <i class="fa fa-map-marker-alt text-primary me-2"></i>
                        <span><?php echo $row['alamat']; ?></span>
                    </div>
                    <h5 class="text-muted mt-3"><strong> Deskripsi Kost </strong></h5>
                    <p><?php echo $row['deskripsi']; ?></p>
                    <hr>
                    <!-- Deskripsi dan Fasilitas Lengkap -->
                    <div class="row ">
                        <div class="col-md-12 d-flex">
                            <!-- Spesifikasi Tipe Kamar -->
                            <div class="me-5">
                                <h4 class="section-heading">Spesifikasi Tipe Kamar</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($spesifikasi = $spesifikasi_result->fetch_assoc()): ?>
                                            <li><?php echo $spesifikasi['spesifikasi']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Fasilitas Kamar -->
                            <div class="me-5">
                                <h4 class="section-heading">Fasilitas Kamar</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($fasilitas = $fasilitas_result->fetch_assoc()): ?>
                                            <li><?php echo $fasilitas['fasilitas']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Fasilitas Kamar Mandi -->
                            <div class="desk">
                                <h4 class="section-heading">Fasilitas Kamar Mandi</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($fasilitas_mandi = $fasilitas_mandi_result->fetch_assoc()): ?>
                                            <li><?php echo $fasilitas_mandi['fasilitas']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- card bayar -->
                <div class="card col-md-4 p-4 card-shadow">
                    <!-- Discount and Price -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="icon-text text-danger mt-3">
                                <i class="fas fa-bolt"></i>
                                <span><strong>Diskon Rp.
                                        <?php echo number_format($row['diskon'], 0, ',', '.'); ?></strong></span>
                            </div>
                        </div>
                        <span class="text-decoration-line-through text-muted">Rp.
                            <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex">
                        <p class="final-price mt-2 text-dark">Rp.
                            <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                        </p>
                        <span class="mt-3 ml-2">/bulan</span>
                    </div>

                    <!-- Date Picker and Rent Period -->
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" value="2024-08-22">
                        <select class="form-select" id="rentPeriod" onchange="calculateTotal()">
                            <option value="" selected>Waktu kost</option>
                            <option value="month">Per Bulan</option>
                            <option value="year">Per Tahun</option>
                        </select>
                    </div>

                    <!-- Total Price -->
                    <div id="totalPrice" class="mb-3" style="display: none;">
                        <h5>Total Harga: <span id="totalAmount">Rp0</span></h5>
                    </div>

                    <!-- Rent Time Range (Initially Hidden) -->
                    <div id="rentRange" class="hidden">
                        <label for="startDate">Tanggal Mulai:</label>
                        <input type="date" id="startDate" class="form-control mb-2">

                        <label for="endDate">Tanggal Selesai:</label>
                        <input type="date" id="endDate" class="form-control mb-2">
                    </div>

                    <!-- Apply for Rent Button -->
                    <a href="pembayaran.html">
                        <button class="btn btn-primary w-100">Ajukan Sewa</button>
                    </a>
                </div>

                 <!-- Card Lokasi -->
                 <div class="card col-md-4 p-4 card-shadow mt-4 offset-md-8 ">
                    <h5>Lokasi</h5>
                    <div class="icon-text">
                        <i class="fa fa-map-marker-alt text-primary me-2"></i>
                        <span><?php echo $row['alamat']; ?></span>
                    </div>
                    <div>
                        <iframe
                            src="https://maps.google.com/maps?q=<?php echo urlencode($row['alamat']); ?>&output=embed"
                            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>

            <!-- Back to Top -->
            <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
        </div>

        <!-- JavaScript Libraries -->
        <script>
            const rentPeriod = document.getElementById('rentPeriod');
            const rentRange = document.getElementById('rentRange');

            rentPeriod.addEventListener('change', function () {
                if (rentPeriod.value !== "") {
                    rentRange.classList.remove('hidden');
                } else {
                    rentRange.classList.add('hidden');
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                function calculateTotal() {
                    const rentPeriod = document.getElementById('rentPeriod').value;
                    const finalPriceElement = document.querySelector('.final-price');
                    const totalPriceElement = document.getElementById('totalPrice');
                    const totalAmountElement = document.getElementById('totalAmount');

                    // Harga dasar per bulan
                    const pricePerMonth = 1070000; // Rp 1.070.000 per bulan
                    let totalAmount = 0;

                    // Kalkulasi harga berdasarkan periode sewa
                    if (rentPeriod === 'month') {
                        totalAmount = pricePerMonth; // Untuk 1 bulan
                    } else if (rentPeriod === 'year') {
                        totalAmount = pricePerMonth * 12; // Untuk 1 tahun (12 bulan)
                    }

                    // Menampilkan total harga jika periode dipilih
                    if (totalAmount > 0) {
                        totalPriceElement.style.display = 'block';
                        totalAmountElement.textContent = `Rp${totalAmount.toLocaleString()}`;
                    } else {
                        totalPriceElement.style.display = 'none';
                    }
                }

                // Inisialisasi listener untuk dropdown rentPeriod
                document.getElementById('rentPeriod').addEventListener('change', calculateTotal);
            });

        </script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>
</body>

</html>