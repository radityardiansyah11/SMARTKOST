<?php
session_start();
include 'config.php';

// Pastikan pengguna telah login
if (!isset($_SESSION['username'])) {
    header("Location: login-pk.php"); // Redirect ke halaman login jika belum login
    exit();
}

$username = $_SESSION['username']; // Ambil username dari sesi
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($username); ?> Dashboard - SMARTKOST</title>
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
        .card {
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
    </style>
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border" style="color: #00B98E; width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <div class="d-flex">
            <!-- Sidebar Start -->
            <div class="d-flex flex-column flex-shrink-0 p-3"
                style=" width: 220px; height: 100vh; position: fixed;  background-color: #00765a;">
                <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <h3 class=" mt-2 text-light">Dashboard</h3>
                </a>
                <hr class="text-light">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="pk-dashboard.php" class="nav-link text-light">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="pk-dashboard-kost.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
                            <i class="bi bi-house-door me-2"></i>
                            Kost
                        </a>
                    </li>
                    <li>
                        <a href="pk-dashboard-booking.php" class="nav-link text-light">
                            <i class="bi bi-people me-2"></i>
                            Booking
                        </a>
                    </li>
                    <li>
                        <a href="pk-profile.php" class="nav-link text-light">
                            <i class="bi bi-person me-2"></i>
                            Profile
                        </a>
                    </li>
                </ul>
                <hr class="text-light">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo isset($_SESSION['image_profile']) ? htmlspecialchars($_SESSION['image_profile']) : 'https://via.placeholder.com/50'; ?>" alt="Admin" width="32" height="32"
                            class="rounded-circle me-2">
                        <strong>Hi,
                            <?php echo htmlspecialchars($username); ?>
                        </strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-light text-small shadow">
                        <li><a class="dropdown-item" href="logout.php" onclick="confirmLogout()">log out</a></li>
                    </ul>
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Content Start -->
            <div class="content p-4" style="margin-left: 220px; padding: 20px;">
                <div class="row">
                    <div class="col-md-12">
                        <img class="img-fluid w-25 mb-2" src="img2/logo smartkost.png" alt="SMARTKOST Logo">
                    </div>

                    <!-- Stats Overview -->
                    <div class="col-md-4">
                        <div class="card bg-primary" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Kost</h5>
                                <h3 class="card-text text-light">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card" style="height: 150px; background-color: #009774;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Booking</h5>
                                <h3 class="card-text text-light">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Pendapatan</h5>
                                <h3 class="card-text text-light">Rp. 0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listings -->
                <div class="container-xxl py-5">
                    <div class="container">
                        <div class="row mt-2 g-0 gx-5">
                            <div class="col-lg-6 d-flex align-items-center">
                                <h4 class="mb-3">List Kost Anda</h4>
                            </div>
                            <div class="col-lg-6 d-flex mb-3 justify-content-end">
                                <a href="pk-tambah kost.html" class="btn btn-primary px-3 d-none d-lg-flex">Tambah
                                    Kost</a>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane fade show p-0 active">
                                <div class="row g-4">

                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane fade show p-0 active">
                                            <div class="row g-4">
                                                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                                    <div class="property-item rounded overflow-hidden">
                                                        <div class="position-relative overflow-hidden">
                                                            <a href=""><img class="img-fluid" src="img2/gbr-kost1.jpg"
                                                                    alt=""></a>
                                                            <div
                                                                class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                                Kost</div>
                                                        </div>
                                                        <div class="p-4 pb-0">
                                                            <h5 class="text-primary mb-3">Rp. 500.000</h5>
                                                            <a class="d-block h5 mb-2" href="">Kost Comboran</a>
                                                            <p><i class="fa fa-map-marker-alt text-primary me-2"></i>Jl.
                                                                Tanimbar</p>
                                                        </div>
                                                        <div class="d-flex border-top">
                                                            <small class="flex-fill text-center border-end py-2"><i
                                                                    class="fa fa-ruler-combined text-primary me-2"></i>3x3</small>
                                                            <small class="flex-fill text-center border-end py-2"><i
                                                                    class="fa fa-bed text-primary me-2"></i>1
                                                                Bed</small>
                                                            <small class="flex-fill text-center py-2"><i
                                                                    class="fa fa-bath text-primary me-2"></i>2
                                                                Bath</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Content End -->
        </div>


        <!-- Footer Start -->
        <footer class="text-light py-4 mt-5" style="background-color: #000;">
            <div class="container text-center">
                <p class="mb-0">&copy; 2024 SMARTKOST. All rights reserved.</p>
            </div>
        </footer>
        <!-- Footer End -->
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