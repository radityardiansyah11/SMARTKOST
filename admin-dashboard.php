<?php
include 'config.php';
session_start();

$sql = "SELECT id, username, email, password, created_at, profile_image FROM login_system";
$result = mysqli_query($conn, $sql);

// Cek apakah query berhasil
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Handle delete request for user
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $sql = "DELETE FROM login_system WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Setelah penghapusan, reset urutan ID
        $reset_id_query = "
            SET @count = 0;
            UPDATE login_system SET id = @count := @count + 1;
            ALTER TABLE login_system AUTO_INCREMENT = 1;
        ";
        mysqli_multi_query($conn, $reset_id_query);

        // Redirect setelah penghapusan dan reset
        header('Location: admin-dashboard.php');
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

$sql = "SELECT id, pkname, email, password, nomor_hp, created_at, image_profile FROM logsys_pk";
$result = mysqli_query($conn, $sql);

// Cek apakah query berhasil
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Handle delete request for pemilik kost
if (isset($_GET['delete_pk'])) {
    $id = intval($_GET['delete_pk']);
    $sql = "DELETE FROM logsys_pk WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Setelah penghapusan, reset urutan ID
        $reset_id_query = "
            SET @count = 0;
            UPDATE logsys_pk SET id = @count := @count + 1;
            ALTER TABLE logsys_pk AUTO_INCREMENT = 1;
        ";
        mysqli_multi_query($conn, $reset_id_query);

        // Redirect setelah penghapusan dan reset
        header('Location: admin-dashboard.php');
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}


// Mendapatkan semua pengguna dari tabel login_system
$sql = "SELECT id, email FROM login_system";
$result_users = mysqli_query($conn, $sql);

// Get only 10 user sessions
$sql = "SELECT * FROM login_system ORDER BY id ASC LIMIT 10";
$result_user_sessions = mysqli_query($conn, $sql);

// Query to count the total number of users
$count_user_sql = "SELECT COUNT(*) AS total_users FROM login_system";
$count_user_result = mysqli_query($conn, $count_user_sql);
$user_data = mysqli_fetch_assoc($count_user_result);
$total_users = $user_data['total_users'];

// Get all pemilik kost
$sql_pk = "SELECT * FROM logsys_pk ORDER BY id ASC";
$result_pk = mysqli_query($conn, $sql_pk);

// Get only 10 pemilik kost sessions
$sql_pk = "SELECT * FROM logsys_pk ORDER BY id ASC LIMIT 10";
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - SMARTKOST</title>
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
            margin: 10px 0;
            margin: 0 5px;
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
                style="width: 220px; height: 100vh; position: fixed; background-color: #00765a;">
                <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                    <h3 class="mt-2 text-light">Dashboard</h3>
                </a>
                <hr class="text-light">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="admin-dashboard.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
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
                        <a href="admin-dashboard-kost.php" class="nav-link text-light">
                            <i class="bi bi-house-door me-2"></i>
                            Kost
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

                    <!-- Stats Overview Carousel -->
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
                                                <h5 class="card-title">Promosi</h5>
                                                <h3 class="card-text">Rp. 0</h3>
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
                                                <h5 class="card-title">Kost</h5>
                                                <h3 class="card-text"><?php echo $total_kost; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">Email</h5>
                                                <h3 class="card-text"><?php echo $total_email; ?></h3>
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

                <!-- User Listings -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h2 class="h4 mb-3  wow fadeInZoom  wow fadeInDown" data-wow-delay="0.3s" data-wow-delay="0.1s">
                            User</h2>
                        <table class="table table-hover">
                            <thead class="table text-light" style="background-color: #009270;">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Profil</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Password</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_user_sessions)): ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $row['id']; ?></td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                            <img src="<?php echo !empty($row['profile_image']) ? $row['profile_image'] : 'img2/Bulat.png'; ?>"
                                                    class="rounded-circle"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                        </td>
                                        <td class="align-middle"><strong><?php echo $row['username']; ?></strong></td>
                                        <td class="align-middle"><?php echo $row['email']; ?></td>
                                        <td class="align-middle"><?php echo substr($row['password'], 0, 20) . '...'; ?></td>
                                        <td class="align-middle"><?php echo $row['created_at']; ?></td>
                                        <td class="">
                                            <a href="edit-user.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-primary mt-2" style="width:57px;">Edit</a>
                                            <a href="?delete_user=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-danger mt-2 btn-trash"
                                                onclick="return confirm('Apa kamu yakin akan menghapus user ini?');">
                                                <img src="img2/sampah.png" class="w-75">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="admin-dashboard-user.php" class="text-end" style="color: grey;">View More</a>
                </div>

                <!-- Pemilik Kost Listings -->
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h2 class="h4 mb-3  wow fadeInDown" data-wow-delay="0.3s">Pemilik Kost</h2>
                        <table class="table table-hover">
                            <thead class="table text-light" style="background-color: #009270;">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Profil</th>
                                    <th scope="col">nama</th>
                                    <th scope="col">email</th>
                                    <th scope="col">no telp</th>
                                    <th scope="col">password</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <th class="align-middle" scope="row"><?php echo $row['id']; ?></th>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <!-- Tampilkan gambar profil sesuai dengan database -->
                                                <img src="<?php echo !empty($row['image_profile']) ? $row['image_profile'] : 'img2/Bulat.png'; ?>"
                                                    class="rounded-circle"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                        </td>
                                        <td class="align-middle"><strong><?php echo $row['pkname']; ?></td>
                                        <td class="align-middle"><?php echo $row['email']; ?></td>
                                        <td class="align-middle"><?php echo $row['nomor_hp']; ?></td>
                                        <td class="align-middle"><?php echo substr($row['password'], 0, 10) . '...'; ?></td>

                                        <td class="align-middle"><?php echo $row['created_at']; ?></td>
                                        <td class="mt-3">
                                            <a href="edit-pk.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-primary mt-2" style="width:57px ;">Edit</a>
                                            <a href="?delete=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-danger mt-2 btn-trash"
                                                onclick="return confirm('Apa kamu yakin akan menghapus?');"><img
                                                    src="img2/sampah.png" class="w-75"> </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>
                    </div>
                    <a href="admin-dahsboard-pk.php" class="text-end" style="color: grey;">View More</a>
                </div>
            </div>
            
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