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
    <title><?php echo htmlspecialchars($username); ?>  Dashboard - SMARTKOST</title>
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
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            border: none;
        }

        .btn-trash {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 31px;
            height: 31px;
            border-radius: 3px;
            padding: 0;
        }

        .dropdown-menu {
            border-radius: 10px;
            padding: 10px 0;
            transition: 0.3s ease;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: 0.2s ease-in-out;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu .dropdown-divider {
            margin: 5px 0;
        }

        .btn-light {
            background-color: #ffffff;
            border-radius: 50%;
            padding: 5px 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .btn-light:focus {
            outline: none;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border" style="color: #0D6EFD; width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <div class="d-flex">
            <!-- Sidebar Start -->
            <div class="d-flex flex-column flex-shrink-0 p-3"
                style=" width: 220px; height: 100vh; position: fixed; background-color: #00765a;">
                <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <h3 class=" mt-2 text-light">Dashboard</h3>
                </a>
                <hr class="text-light">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="pk-dashboard.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="pk-dashboard-kost.php" class="nav-link text-light">
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
                        <a href="pk-profile.html" class="nav-link text-light">
                            <i class="bi bi-person me-2"></i>
                            Profile
                        </a>
                    </li>
                </ul>
                <hr class="text-light">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/50" alt="Admin" width="32" height="32"
                            class="rounded-circle me-2">
                        <strong>Hi,
                            <?php echo htmlspecialchars($username); ?>
                        </strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-light text-small shadow">
                        <li><a class="dropdown-item" href="logout.php"  onclick="confirmLogout()">log out</a></li>
                    </ul>
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Content Start -->
            <div class="content p-4" style="margin-left: 220px; padding: 20px;">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                        <img class="img-fluid w-25 mb-2" src="img2/logo smartkost.png" alt="SMARTKOST Logo">
                        <!-- <a href="">
                            <button class="btn btn-sm btn-danger">Bayar Promosi Kost</button>
                        </a> -->
                    </div>


                    <!-- Stats Overview -->
                    <div class="col-md-4  wow fadeInUp" data-wow-delay="0.1s">
                        <div class="card bg-primary" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Kost</h5>
                                <h3 class="card-text text-light">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4  wow fadeInUp" data-wow-delay="0.2s">
                        <div class="card" style="height: 150px; background-color: #009774;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Booking</h5>
                                <h3 class="card-text text-light">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4  wow fadeInUp" data-wow-delay="0.3s">
                        <div class="card bg-primary" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Pendapatan</h5>
                                <h3 class="card-text text-light">Rp. 0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Listings -->
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h2 class="h4 mb-3">Booking</h2>
                        <table class="table table-hover">
                            <thead class="table" style="background-color: #009270;">
                                <tr class="text-light">
                                    <th scope="col">ID</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Kost</th>
                                    <th scope="col">Bayar</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row" class="align-middle">1</th>
                                    <td class="align-middle">User 1</td>
                                    <td class="align-middle">Kost Malang</td>
                                    <td class="align-middle">Rp. 500.000</td>
                                    <td class="align-middle">2024-08-09</td>
                                    <td class="">
                                        <a href="edit-user.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-primary" style="width:57px;">Edit</a>
                                        <a href="?delete=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-danger btn-trash"
                                            onclick="return confirm('Apa kamu yakin akan menghapus?');"><img
                                                src="img2/sampah.png" class="w-75"></a>
                                    </td>
                                </tr>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>
                    </div>
                    <a href="pk-dashboard-booking.html" class="text-end" style="color: grey;">View More</a>
                </div>

                <!-- List Kost -->
                <div class="container-xxl py-5">
                    <div class="container">
                        <div class="row mt-2 g-0 gx-5">
                            <div class="col-lg-6">
                                <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                                    <h4 class="mb-2">List Kost Anda</h4>
                                </div>
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
                                                    class="bg-white rounded-top text-warning position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                    Premium
                                                </div>

                                                <!-- Dropdown Edit & Delete -->
                                                <div class="dropdown position-absolute top-0 end-0 mt-2 me-2">
                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                                        id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right shadow border-0"
                                                        aria-labelledby="dropdownMenuButton">
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                                <i class="fas fa-edit me-2 text-primary"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="#">
                                                                <i class="fas fa-trash-alt me-2 text-danger"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="p-4 pb-0">
                                                <h5 class="text-primary mb-3">Rp. 500.000</h5>
                                                <a class="d-block h5 mb-2" href="">Kost Comboran</a>
                                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>Jl. Tanimbar
                                                </p>
                                            </div>
                                            <div class="d-flex border-top">
                                                <small class="flex-fill text-center border-end py-2">
                                                    <i class="fa fa-ruler-combined text-primary me-2"></i>3x3
                                                </small>
                                                <small class="flex-fill text-center border-end py-2">
                                                    <i class="fa fa-bed text-primary me-2"></i>1 Bed
                                                </small>
                                                <small class="flex-fill text-center py-2">
                                                    <i class="fa fa-bath text-primary me-2"></i>2 Bath
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="pk-dashboard-kost.html" class="text-end" style="color: grey;">View More</a>
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
    <script>
        function confirmLogout() {
            if (confirm("Anda yakin ingin logout?")) {
                // Jika konfirmasi diterima, arahkan ke logout.php
                window.location.href = "logout.php";
            }
        }
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