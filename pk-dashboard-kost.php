<?php
session_start();
include 'config.php';

// Pastikan pengguna telah login
if (!isset($_SESSION['pkname'])) {
    header("Location: login-pk.php"); // Redirect ke halaman login jika belum login
    exit();
}

$pkname = $_SESSION['pkname']; // Ambil username dari sesi

 // Fetch Kost listings from the database
 $pkname = $_SESSION['pkname']; // Ambil pkname dari sesi
 $result = $conn->query("SELECT * FROM kost WHERE pkname = '$pkname'");

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pkname = $_SESSION['pkname']; // Dapatkan pkname dari sesi

    // Cek apakah kost ini milik pkname yang sedang login
    $check_owner_sql = "SELECT * FROM kost WHERE id = $id AND pkname = '$pkname'";
    $result = mysqli_query($conn, $check_owner_sql);

    if (mysqli_num_rows($result) > 0) {
        // Jika kost milik pemilik yang login, lakukan penghapusan
        $delete_sql = "DELETE FROM kost WHERE id = $id";
        if (mysqli_query($conn, $delete_sql)) {
            $_SESSION['status'] = "deleted"; // Tampilkan status berhasil
        } else {
            $_SESSION['status'] = "error"; // Tampilkan status gagal
        }
    } else {
        // Jika tidak, tampilkan pesan error
        $_SESSION['status'] = "unauthorized";
    }

    // Redirect kembali ke dashboard setelah penghapusan
    header('Location: pk-dashboard-kost.php');
    exit();
}

// Perbaiki query untuk menghitung jumlah booking dengan JOIN pada tabel kost
$query_count_bookings = "
    SELECT COUNT(*) AS total_bookings
    FROM bookings b
    JOIN kost k ON b.nama_kost = k.nama_kost
    WHERE k.pkname = '$pkname'
";
$result_count_bookings = mysqli_query($conn, $query_count_bookings);
$row_count_bookings = mysqli_fetch_assoc($result_count_bookings);
$total_bookings = $row_count_bookings['total_bookings'];

// Query untuk menghitung jumlah kost milik pemilik kost yang login
$query_count_kost = "SELECT COUNT(*) AS total_kost FROM kost WHERE pkname = '$pkname'";
$result_count_kost = mysqli_query($conn, $query_count_kost);
$row_count_kost = mysqli_fetch_assoc($result_count_kost);
$total_kost = $row_count_kost['total_kost'];

// Query untuk menghitung jumlah kost milik pemilik kost yang login
$query_count_kost = "SELECT COUNT(*) AS total_kost FROM kost WHERE pkname = '$pkname'";
$result_count_kost = mysqli_query($conn, $query_count_kost);
$row_count_kost = mysqli_fetch_assoc($result_count_kost);
$total_kost = $row_count_kost['total_kost'];

// Tangani parameter pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query untuk menghitung jumlah kost yang dimiliki oleh pkname
$sql_kost = "SELECT * FROM kost WHERE pkname = '$pkname'";

// Jika ada parameter pencarian
if ($search) {
    $search = $conn->real_escape_string($search);  // Sanitize input
    $sql_kost .= " AND nama_kost LIKE '%$search%'";  // Mencari berdasarkan nama kost
}

$result = $conn->query($sql_kost);  // Eksekusi query

function limit_characters($string, $char_limit)
{
    if (strlen($string) > $char_limit) {
        return substr($string, 0, $char_limit) . '...';
    }
    return $string;
}

// Query untuk menghitung total pendapatan
$query_total_pendapatan = "
    SELECT SUM(b.total_harga) AS total_pendapatan
    FROM bookings b
    JOIN kost k ON b.nama_kost = k.nama_kost
    WHERE k.pkname = '$pkname'
";
$result_total_pendapatan = mysqli_query($conn, $query_total_pendapatan);
$row_total_pendapatan = mysqli_fetch_assoc($result_total_pendapatan);
$total_pendapatan = $row_total_pendapatan['total_pendapatan'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pkname); ?> Dashboard - SMARTKOST</title>
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
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
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

        .jenis-kost-label {
            width: auto;
            margin-right: 35px;
            padding-right: 10px;
            white-space: nowrap;
            border-radius: 5px;
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
                        <img src="<?php echo isset($_SESSION['image_profile']) ? htmlspecialchars($_SESSION['image_profile']) : 'https://via.placeholder.com/50'; ?>"
                            alt="Admin" width="32" height="32" class="rounded-circle me-2">
                        <strong>Hi,
                            <?php echo htmlspecialchars($pkname); ?>
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
                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="card bg-primary" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Kost</h5>
                                <h3 class="card-text text-light">
                                    <?php echo $total_kost; ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="card" style="height: 150px; background-color: #009774;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Booking</h5>
                                <h3 class="card-text text-light"><?php echo $total_bookings; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="card bg-primary" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title text-light">Pendapatan</h5>
                                <h3 class="card-text text-light">Rp. <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listings -->
                <div class="container-xxl py-5">
                    <div class="container">
                        <div class="row mt-2 g-0 gx-5 mb-4">
                            <div class="col-lg-6 d-flex align-items-center">
                                <h4 class="mb-3">List Kost Anda</h4>
                                <form class="d-flex mb-3 ms-3" action="" method="GET">
                                    <input class="form-control me-2" type="search" name="search" placeholder="Cari Kost"
                                        aria-label="Search"
                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button class="btn btn-outline-success" type="submit"><i
                                            class="bi bi-search"></i></button>
                                </form>
                            </div>
                        </div>


                        <!-- list kost -->
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane fade show p-0 active">
                                <div class="row g-4">

                                    <?php
                                   
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="property-item rounded overflow-hidden">
                                                <div class="position-relative overflow-hidden">
                                                    <a href="admin-detail.php?id=<?php echo $row['id']; ?>">
                                                        <img class="img-fluid" src="<?php echo $row['gambar_1']; ?>" alt="">
                                                    </a>

                                                    <div
                                                        class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">
                                                        </i><?php echo $row['kategori']; ?>
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
                                                                    href="pk-edit-kost.php?id=<?php echo $row['id']; ?>">
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
                                                        <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                                    </h5>
                                                    <p>
                                                        <i
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