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

    <style>
        .main-image {
            width: 100%;
            /* Pastikan gambar memenuhi lebar kolom */
            height: auto;
            /* Tinggi menyesuaikan secara proporsional */
            object-fit: cover;
            /* Pastikan gambar tidak terdistorsi */
        }

        .thumbnail-image {
            height: 200px;
            object-fit: cover;
        }

        .price-discount {
            font-weight: bold;
        }

        .final-price {
            font-size: 24px;
            font-weight: bold;
        }

        .facility-list {
            list-style-type: none;
            padding: 0;
        }

        .facility-list li {
            display: inline;
            margin-right: 15px;
        }

        .facility-list li i {
            color: #28a745;
        }

        .contact-owner-btn {
            background-color: #28a745;
            color: white;
            width: 100%;
            padding: 15px;
            border: none;
            font-size: 18px;
        }

        .icon-text {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .icon-text i {
            font-size: 24px;
            margin-right: 10px;
        }

        .section-heading {
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .card-shadow {
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
            border: none;
        }

        .hidden {
            display: none;
        }

        .main-image {
            width: 100%;
            height: 700px;
            object-fit: cover;
        }

        .thumbnail-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar Start -->
        <div class="container-fluid nav-bar bg-transparent">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4">
                <a href="#" class="navbar-brand d-flex align-items-center text-center">
                    <div class="p-2">
                        <img class="img-fluid" src="img2/logo smartkost.png" alt="Icon"
                            style="width: 210px; height: 70px;">
                    </div>
                </a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">
                        <a href="index.html" class="nav-item nav-link">Beranda</a>

                        <a href="kost.html" class="nav-item nav-link">Kost</a>
                        <a href="kontak.html" class="nav-item nav-link">Kontak</a>
                        <a href="detail.html" class="nav-item nav-link active">Detail</a>
                    </div>
                    <a href="login.html" class="btn btn-primary px-3 d-none d-lg-flex">Login</a>
                </div>
            </nav>
        </div>
        <!-- Navbar End -->

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
                                    <?php while ($spesifikasi = $spesifikasi_result->fetch_assoc()) : ?>
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
                                    <?php while ($fasilitas = $fasilitas_result->fetch_assoc()) : ?>
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
                                    <?php while ($fasilitas_mandi = $fasilitas_mandi_result->fetch_assoc()) : ?>
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
                            <div class="icon-text text-danger">
                                <i class="fas fa-bolt"></i>
                                <span><strong>Diskon <?php echo $row['diskon']; ?></strong></span>
                            </div>
                        </div>
                        <span class="text-decoration-line-through text-muted"><?php echo $row['harga']; ?></span>
                    </div>
                    <div class="d-flex">
                        <p class="final-price mt-2 text-dark"><?php echo $row['harga']; ?></p>
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

                <hr class="m-5">

                <div class="container-xxl py-5">
                    <div class="container">
                        <div class="row g-0 gx-5 align-items-end">
                            <div class="col-lg-12">
                                <div class="text-center mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                                    <h1 class="mb-3">Kost Lainnya</h1>
                                </div>
                            </div>
                            <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                            </div>
                        </div>
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane fade show p-0 active">
                                <div class="row g-4">
                                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                        <div class="property-item rounded overflow-hidden">
                                            <div class="position-relative overflow-hidden">
                                                <a href=""><img class="img-fluid" src="img2/gbr-kost1.jpg" alt=""></a>
                                                <div
                                                    class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                    Kost</div>
                                            </div>
                                            <div class="p-4 pb-0">
                                                <h5 class="text-primary mb-3">Rp. 500.000</h5>
                                                <a class="d-block h5 mb-2" href="">Kost Comboran</a>
                                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>Jl. Tanimbar
                                                </p>
                                            </div>
                                            <div class="d-flex border-top">
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-ruler-combined text-primary me-2"></i>3x3</small>
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-bed text-primary me-2"></i>1 Bed</small>
                                                <small class="flex-fill text-center py-2"><i
                                                        class="fa fa-bath text-primary me-2"></i>2 Bath</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                                        <div class="property-item rounded overflow-hidden">
                                            <div class="position-relative overflow-hidden">
                                                <a href=""><img class="img-fluid" src="img2/gbr-kost2.jpg" alt=""></a>
                                                <div
                                                    class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                    Kost</div>
                                            </div>
                                            <div class="p-4 pb-0">
                                                <h5 class="text-primary mb-3">Rp. 500.000</h5>
                                                <a class="d-block h5 mb-2" href="">Kost Comboran</a>
                                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>Jl. Tanimbar
                                                </p>
                                            </div>
                                            <div class="d-flex border-top">
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-ruler-combined text-primary me-2"></i>3x3</small>
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-bed text-primary me-2"></i>1 Bed</small>
                                                <small class="flex-fill text-center py-2"><i
                                                        class="fa fa-bath text-primary me-2"></i>2 Bath</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                                        <div class="property-item rounded overflow-hidden">
                                            <div class="position-relative overflow-hidden">
                                                <a href=""><img class="img-fluid" src="img2/gbr-kost2.jpg" alt=""></a>
                                                <div
                                                    class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                    Kost</div>
                                            </div>
                                            <div class="p-4 pb-0">
                                                <h5 class="text-primary mb-3">Rp. 500.000</h5>
                                                <a class="d-block h5 mb-2" href="">Kost Comboran</a>
                                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>Jl. Tanimbar
                                                </p>
                                            </div>
                                            <div class="d-flex border-top">
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-ruler-combined text-primary me-2"></i>3x3</small>
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-bed text-primary me-2"></i>1 Bed</small>
                                                <small class="flex-fill text-center py-2"><i
                                                        class="fa fa-bath text-primary me-2"></i>2 Bath</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
                                        <a class="btn btn-primary py-3 px-5" href="kost.html">Lihat Lainnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Start -->
            <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
                <div class="container py-5">
                    <div class="row g-5">
                        <div class="col-lg-3 col-md-6">
                            <h5 class="text-white mb-4">Get In Touch</h5>
                            <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>comboran</p>
                            <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                            <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@smartkost.com</p>
                            <div class="d-flex pt-2">
                                <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-light btn-social" href=""><i
                                        class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-light btn-social" href=""><i
                                        class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h5 class="text-white mb-4">link</h5>
                            <a class="btn btn-link text-white-50" href="index.php">Home</a>
                            <a class="btn btn-link text-white-50" href="kost.html">Kost</a>
                            <a class="btn btn-link text-white-50" href="kost.html">Kontak</a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h5 class="text-white mb-4">Newsletter</h5>
                            <p></p>
                            <div class="position-relative mx-auto" style="max-width: 400px;">
                                <input class="form-control bg-transparent w-100 py-3 ps-4 pe-5" type="text"
                                    placeholder="Your email">
                                <button type="button"
                                    class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="copyright">
                        <div class="row">
                            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                &copy; <a class="border-bottom" href="#">SMARTKOST</a>, All Right Reserved.

                                <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                                Designed By <a class="border-bottom" href="https://htmlcodex.com">rdtyardnsyh</a>
                            </div>
                            <div class="col-md-6 text-center text-md-end">
                                <div class="footer-menu">
                                    <a href="">Home</a>
                                    <a href="">Cookies</a>
                                    <a href="">Help</a>
                                    <a href="">FQAs</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->


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