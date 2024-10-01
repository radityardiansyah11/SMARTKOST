<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$jenis_kost = isset($_GET['jenis_kost']) ? $_GET['jenis_kost'] : '';
$jenis_kategori = isset($_GET['jenis_kategori']) ? $_GET['jenis_kategori'] : '';

$query = "SELECT * FROM kost WHERE 1=1";

if ($search) {
    $search = $conn->real_escape_string($search);
    $query .= " AND nama_kost LIKE '%$search%'";
}

if ($jenis_kost) {
    $jenis_kost = $conn->real_escape_string($jenis_kost);
    $query .= " AND jenis_kost = '$jenis_kost'";
}

if ($jenis_kategori && $jenis_kategori != 'Semua') {
    $jenis_kategori = $conn->real_escape_string($jenis_kategori);
    $query .= " AND kategori = '$jenis_kategori'";
}

$result = $conn->query($query);

// Cek apakah hasil query kosong
if ($result->num_rows === 0) {
    $kost_tidak_ada = true; // Menandakan bahwa tidak ada kost ditemukan
} else {
    $kost_tidak_ada = false; // Menandakan bahwa ada kost ditemukan
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>user kost-SMARTKOST</title>
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
        .custom-height {
            height: 500px;
            /* Tinggi yang diinginkan */
            object-fit: cover;
            /* Memastikan gambar memenuhi area tanpa distorsi */
            width: 100%;
            /* Memastikan gambar tetap full-width */
        }

        @media (max-width: 768px) {
            .custom-height {
                height: 300px;
                /* Tinggi lebih pendek untuk layar kecil */
            }
        }

        .position-relative {
            position: relative;
        }

        .position-absolute {
            position: absolute;
        }

        .overlay-text {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            z-index: 2;
        }

        .profile-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .jenis-kost-label {
            width: auto;
            padding: 5px;
            white-space: nowrap;
            border-radius: 5px;
        }

        .property-item {
            width: 100%;
            max-height: 400px;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .property-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            object-position: center;
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
                        <a href="user-home.php" class="nav-item nav-link">Beranda</a>

                        <a href="user-kost.php" class="nav-item nav-link active">Kost</a>
                        <a href="user-kontak.php" class="nav-item nav-link">Kontak</a>
                    </div>
                    <div class="d-flex">
                        <div class="me-3 text-end">
                            <h6 class="mt-2">Halo, <br> <?php echo htmlspecialchars($username); ?></h6>
                        </div>
                        <a href="user-profile.php">
                            <img src="<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'img2/Bulat.png'; ?>"
                                alt="profile" class="profile-image mt-1">
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Navbar End -->


        <!-- Header Start -->
        <div class="container-fluid header bg-white p-0">
            <div class="row g-0">
                <div class="col-md-12 position-relative">
                    <div class="owl-carousel header-carousel">
                        <div class="owl-carousel-item position-relative">
                            <img class="img-fluid w-100 custom-height" src="img2/kost1.jpg" alt="">
                            <div class="overlay-text position-absolute top-50 start-50 translate-middle text-center">
                                <h1 class="display-5 animated fadeIn mb-4 text-light">Cari Kost Impian Anda</h1>
                                <nav aria-label="breadcrumb animated fadeIn">
                                    <ol class="breadcrumb text-uppercase">
                                        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                                        <li class="breadcrumb-item text-light active" aria-current="page">Kost</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->


        <!-- Search Start -->
        <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px;">
            <div class="container">
                <form method="GET" action="user-kost.php">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control border-0 py-3" placeholder="Cari Kost" name="search"
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select border-0 py-3" name="jenis_kost">
                                <option value="">Jenis Kost</option>
                                <option value="Laki-laki" <?php if ($jenis_kost == 'Laki-laki')
                                    echo 'selected'; ?>>
                                    Laki-laki</option>
                                <option value="Perempuan" <?php if ($jenis_kost == 'Perempuan')
                                    echo 'selected'; ?>>
                                    Perempuan</option>
                                <option value="Campur" <?php if ($jenis_kost == 'Campur')
                                    echo 'selected'; ?>>Campur
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select border-0 py-3">
                                <option selected>Lokasi</option>
                                <option value="1">Location 1</option>
                                <option value="2">Location 2</option>
                                <option value="3">Location 3</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark border-0 w-100 py-3">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Search End -->

        <!-- Property List Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-0 gx-5 align-items-end">
                    <div class="col-lg-6">
                        <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                            <h1 class="mb-3">CARI KOST ANDA</h1>
                        </div>
                    </div>

                    <!-- kategori kost -->
                    <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                        <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-primary <?php echo $jenis_kategori == '' ? 'active' : ''; ?>"
                                    href="user-kost.php?search=<?php echo urlencode($search); ?>&jenis_kost=<?php echo urlencode($jenis_kost); ?>">Semua</a>
                            </li>
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-primary <?php echo $jenis_kategori == 'Premium' ? 'active' : ''; ?>"
                                    href="user-kost.php?search=<?php echo urlencode($search); ?>&jenis_kost=<?php echo urlencode($jenis_kost); ?>&jenis_kategori=Premium">Premium</a>
                            </li>
                            <li class="nav-item me-0">
                                <a class="btn btn-outline-primary <?php echo $jenis_kategori == 'Standart' ? 'active' : ''; ?>"
                                    href="user-kost.php?search=<?php echo urlencode($search); ?>&jenis_kost=<?php echo urlencode($jenis_kost); ?>&jenis_kategori=Standart">Standart</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- kost -->
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <?php
                            if ($kost_tidak_ada) { // Cek apakah tidak ada kost
                                echo '<div class="col-12 text-center"><h4 style="color: #99A3A3;">Kost tidak ada <i class="bi bi-emoji-frown me-3"></i> </h4></div>';
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                        <div class="property-item rounded overflow-hidden">
                                            <div class="position-relative overflow-hidden">
                                                <a href="user-detail.php?id=<?php echo $row['id']; ?>">
                                                    <img class="img-fluid" src="<?php echo $row['gambar_1']; ?>" alt="">
                                                </a>
                                                <div
                                                    class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                    <?php echo $row['kategori']; ?>
                                                </div>
                                                <div class="dropdown position-absolute top-0 end-0 mt-2 me-2">
                                                    <div
                                                        class="bg-white text-primary position-absolute end-0 bottom-3 pt-1 px-3 jenis-kost-label">
                                                        <?php echo $row['jenis_kost']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-4 pb-0">
                                                <a class="d-block h5 mb-2" href=""><?php echo $row['nama_kost']; ?></a>
                                                <h5 class="text-primary mb-1">Rp.
                                                    <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                                </h5>
                                                <p><i
                                                        class="fa fa-map-marker-alt text-primary me-2"></i><?php echo $row['alamat']; ?>
                                                </p>
                                            </div>
                                            <div class="d-flex border-top">
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-ruler-combined text-primary me-2"></i><?php echo $row['ukuran_kamar']; ?></small>
                                                <small class="flex-fill text-center border-end py-2"><i
                                                        class="fa fa-bed text-primary me-2"></i><?php echo $row['banyak_kasur']; ?>
                                                    Bed</small>
                                                <small class="flex-fill text-center py-2"><i
                                                        class="fa fa-bath text-primary me-2"></i><?php echo $row['banyak_kamar_mandi']; ?>
                                                    Bath</small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Property List End -->

        <!-- Process Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center pb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <h6 class="section-title bg-white text-center text-primary px-3">Process</h6>
                    <h1 class="mb-5">3 Langkah Mudah</h1>
                </div>
                <div class="row gy-5 gx-4 justify-content-center">
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="position-relative border border-primary pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-search fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4">Cari Kost kost</h5>
                            <hr class="w-25 mx-auto bg-primary mb-1">
                            <hr class="w-50 mx-auto bg-primary mt-0">
                            <p class="mb-0">Cari kost yang anda inginkan sesuai dengan yang anda butuhkan</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="position-relative border border-primary pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-bed fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4">Pilih Kost</h5>
                            <hr class="w-25 mx-auto bg-primary mb-1">
                            <hr class="w-50 mx-auto bg-primary mt-0">
                            <p class="mb-0">Pastikan pilihna kost sesuai dengan kebtuhan</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 text-center pt-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="position-relative border border-primary pt-5 pb-4 px-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle position-absolute top-0 start-50 translate-middle shadow"
                                style="width: 100px; height: 100px;">
                                <i class="fa fa-dollar-sign fa-3x text-white"></i>
                            </div>
                            <h5 class="mt-4">Booking</h5>
                            <hr class="w-25 mx-auto bg-primary mb-1">
                            <hr class="w-50 mx-auto bg-primary mt-0">
                            <p class="mb-0">Anda dapat melakukan pembayaran pada website maupun cod</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Process Start -->

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
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
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