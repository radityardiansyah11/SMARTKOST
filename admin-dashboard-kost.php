<?php
include 'config.php';
session_start();

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM kost WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Setelah penghapusan, reset urutan ID
        $reset_id_query = "
            SET @count = 0;
            UPDATE kost SET id = @count := @count + 1;
            ALTER TABLE kost AUTO_INCREMENT = 1;
        ";
        mysqli_multi_query($conn, $reset_id_query);

        // Set session status to 'deleted' after successful deletion
        $_SESSION['status'] = "deleted";
    } else {
        // Set session status to 'error' if deletion fails
        $_SESSION['status'] = "error";
    }

    // Redirect setelah penghapusan dan reset
    header('Location: admin-dashboard-kost.php');
    exit();
}

// Get all user sessions
$sql = "SELECT * FROM logsys_pk ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

// Query to count the number of users
$count_user_sql = "SELECT COUNT(*) AS total_users FROM login_system";
$count_user_result = mysqli_query($conn, $count_user_sql);
$user_data = mysqli_fetch_assoc($count_user_result);
$total_users = $user_data['total_users'];

// Get all pemilik kost
$sql_pk = "SELECT * FROM logsys_pk ORDER BY id ASC";
$result_pk = mysqli_query($conn, $sql_pk);

// Query to count the number of pemilik kost
$count_pk_sql = "SELECT COUNT(*) AS total_pk FROM logsys_pk";
$count_pk_result = mysqli_query($conn, $count_pk_sql);
$pk_data = mysqli_fetch_assoc($count_pk_result);
$total_pk = $pk_data['total_pk'];

/* Get all Kost */
$sql_kost = "SELECT * FROM kost ORDER BY id ASC";
$result_kost = mysqli_query($conn, $sql_kost);

// Query to count the number of kost
$count_kost_sql = "SELECT COUNT(*) AS total_kost FROM kost";
$count_kost_result = mysqli_query($conn, $count_kost_sql);
$kost_data = mysqli_fetch_assoc($count_kost_result);
$total_kost = $kost_data['total_kost'];

/* Get all email */
$sql_email = "SELECT * FROM kontak ORDER BY id ASC";
$result_email = mysqli_query($conn, $sql_email);

// Query to count the number of email
$count_email_sql = "SELECT COUNT(*) AS total_email FROM kontak";
$count_email_result = mysqli_query($conn, $count_email_sql);
$email_data = mysqli_fetch_assoc($count_email_result);
$total_email = $email_data['total_email'];

// Query untuk menghitung 5% dari total harga booking
$query_pendapatan = "SELECT SUM(total_harga * 0.10) AS total_pendapatan FROM bookings";
$result_pendapatan = mysqli_query($conn, $query_pendapatan);

if (!$result_pendapatan) {
    die("Query error: " . mysqli_error($conn));
}

// Ambil hasil query
$row_pendapatan = mysqli_fetch_assoc($result_pendapatan);
$total_pendapatan = $row_pendapatan['total_pendapatan'] ?? 0; // Default ke 0 jika NULL

// Tangkap input pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql_kost = "SELECT * FROM kost";

if ($search) {
    $search = $conn->real_escape_string($search);
    $sql_kost .= " WHERE nama_kost LIKE '%$search%'";
}

$sql_kost .= " ORDER BY id ASC"; 
$result_kost = mysqli_query($conn, $sql_kost);

$count_kost_sql = "SELECT COUNT(*) AS total_kost FROM kost";
if ($search) {
    $count_kost_sql .= " WHERE nama_kost LIKE '%$search%'";
}
$count_kost_result = mysqli_query($conn, $count_kost_sql);
$kost_data = mysqli_fetch_assoc($count_kost_result);
$total_kost = $kost_data['total_kost'];

function limit_characters($string, $char_limit)
{
    if (strlen($string) > $char_limit) {
        return substr($string, 0, $char_limit) . '...';
    }
    return $string;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin-Dashboard - SMARTKOST</title>
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

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        .jenis-kost-label {
            width: auto;
            margin-right: 35px;
            padding-right: 10px;
            white-space: nowrap;
            border-radius: 5px;
        }

        .carousel-item {
            padding: 0 15px;
        }

        .carousel-item .row {
            padding: 15px 0;
        }

        .carousel-item,
        .row {
            overflow: visible;
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
            height: 150px;
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
            <div class="spinner-border" style="color: #00B98E; width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <div class="d-flex">
            <!-- Sidebar Start -->
            <div class="d-flex flex-column flex-shrink-0 p-3"
                style="width: 220px; height: 100vh; position: fixed; background-color: #00765a;">
                <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <h3 class="mt-2 text-light">Dashboard</h3>
                </a>
                <hr class="text-light">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="admin-dashboard.php" class="nav-link text-light" aria-current="page">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="admin-dahsboard-pk.php" class="nav-link text-light">
                            <i class="bi bi-person me-2"></i>
                            Pemilik Kost
                        </a>
                    </li>
                    <li>
                        <a href="admin-dashboard-user.php" class="nav-link text-light">
                            <i class="bi bi-people me-2"></i>
                            User
                        </a>
                    </li>
                    <li>
                        <a href="admin-dashboard-kost.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
                            <i class="bi bi-house-door me-2"></i>
                            Kost
                        </a>
                    </li>
                    <li>
                        <a href="admin-dashboard-booking.php" class="nav-link text-light">
                            <i class="bi bi-bookmarks me-2"></i>
                            Booking
                        </a>
                    </li>
                    <li>
                        <a href="admin-dashboard-email.php" class="nav-link text-light">
                            <i class="bi bi-envelope me-2"></i>
                            Email
                        </a>
                    </li>
                </ul>
                <hr class="text-light">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="img2/mini logo smartkost.png" alt="Admin" width="32" height="32"
                            class="rounded-circle me-2">
                        <strong>Admin</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-light text-small shadow" onclick="confirmLogout()">
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
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
                    <div id="statsCarousel" class="carousel slide wow fadeInUp" data-bs-ride="carousel"
                        data-wow-delay="0.1s">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="row" style="overflow: visible; padding: 15px 0;">
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">User</h5>
                                                <h3 class="card-text"><?php echo $total_users; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">Pemilik Kost</h5>
                                                <h3 class="card-text"><?php echo $total_pk; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">Kost</h5>
                                                <h3 class="card-text"><?php echo $total_kost; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="carousel-item">
                                <div class="row" style="overflow: visible; padding: 15px 0;">
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">Email</h5>
                                                <h3 class="card-text"><?php echo $total_email; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">Pendapatan</h5>
                                                <h3 class="card-text">Rp. <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carousel Controls -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#statsCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#statsCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                <!-- Listings -->
                <div class="container-xxl py-5">
                    <div class="container">
                        <div class="row mt-2 g-0 gx-5">
                            <div class="col-lg-6 d-flex align-items-center">
                                <h4 class="mb-3">Kost</h4>
                                <form class="d-flex mb-3 ms-3" action="" method="GET">
                                    <input class="form-control me-2" type="search" name="search" placeholder="Cari Kost"
                                        aria-label="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button class="btn btn-outline-success" type="submit"><i
                                            class="bi bi-search"></i></button>
                                </form>
                            </div>
                            <div class="col-lg-6 d-flex mb-3 justify-content-end">
                                <a href="admin-tambah-kost.php" class="btn btn-primary px-3">
                                    Tambah Kost
                                </a>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane fade show p-0 active">
                                <div class="row g-4">
                                    <?php
                                    if ($result_kost->num_rows > 0) {
                                        // Jika ada hasil pencarian atau data kost tersedia
                                        while ($row = $result_kost->fetch_assoc()) {
                                            ?>
                                            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                                <div class="property-item rounded overflow-hidden">
                                                    <div class="position-relative overflow-hidden">
                                                        <a href="admin-detail.php?id=<?php echo $row['id']; ?>">
                                                            <img class="img-fluid" src="<?php echo $row['gambar_1']; ?>" alt="">
                                                        </a>

                                                        <div
                                                            class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                            <?php echo $row['kategori']; ?>
                                                        </div>

                                                        <div class="dropdown position-absolute top-0 end-0 mt-2 me-2">
                                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                                                id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-right shadow border-0"
                                                                aria-labelledby="dropdownMenuButton">
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center"
                                                                        href="edit-kost.php?id=<?php echo $row['id']; ?>">
                                                                        <i class="fas fa-edit me-2 text-primary"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center"
                                                                        href="?delete=<?php echo $row['id']; ?>"
                                                                        onclick="return confirm('Anda yakin ingin menghapus kost ini?');">
                                                                        <i class="fas fa-trash-alt me-2 text-danger"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            <div
                                                                class="bg-white text-primary position-absolute end-0 bottom-0 pt-1 px-3 jenis-kost-label">
                                                                <?php echo $row['jenis_kost']; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="p-4 pb-0">
                                                        <a class="d-block h5 mb-2"
                                                            href=""><?php echo limit_characters($row['nama_kost'], 14); ?></a>
                                                        <h5 class="text-primary mb-2">Rp.
                                                            <?php echo number_format($row['harga'], 0, ',', '.'); ?></h5>
                                                        <p><i
                                                                class="fa fa-map-marker-alt text-primary me-2"></i><?php echo limit_characters($row['alamat'], 33); ?>
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
                                    } else {
                                        // Jika tidak ada hasil pencarian
                                        echo "<p class='text-center'>Tidak ada kost yang ditemukan.</p>";
                                    }
                                    ?>
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
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_SESSION['status'])): ?>
                var status = "<?php echo $_SESSION['status']; ?>";

                if (status === "deleted") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kost Berhasil Dihapus!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#00B98E',
                        background: '#f4f4f9',
                        width: '350px',
                        customClass: {
                            title: 'custom-title',
                            content: 'custom-content'
                        }
                    });
                } else if (status === "error") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus Pengguna!',
                        text: 'Terjadi kesalahan saat menghapus pengguna. Silakan coba lagi.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#00765a',
                        background: '#f4f4f9',
                        width: '350px',
                        customClass: {
                            title: 'custom-title',
                            content: 'custom-content'
                        }
                    });
                }

                // Clear session status after displaying the message
                <?php unset($_SESSION['status']); ?>
            <?php endif; ?>
        });

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