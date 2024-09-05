<?php
include 'config.php';
session_start();

// Ambil data dari database
$sql = "SELECT id, nama, email, pesan, tanggal FROM kontak ORDER BY tanggal DESC";
$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row; // Simpan semua pesan dalam array $messages
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM kontak WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Setelah penghapusan, reset urutan ID jika diperlukan
        $reset_id_query = "
            SET @count = 0;
            UPDATE kontak SET id = @count := @count + 1;
            ALTER TABLE kontak AUTO_INCREMENT = 1;
        ";
        mysqli_multi_query($conn, $reset_id_query);

        // Redirect setelah penghapusan dan reset
        header('Location: admin-dashboard-email.php');
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Amin-Dashboard - SMARTKOST</title>
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
        * {
            box-sizing: border-box;
        }

        .card {
            display: flex;
            flex-direction: column;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            border: none;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card {
            flex: 1 1 calc(50% - 1rem);
            /* Set 50% width minus gap */
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
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

        <class="d-flex">
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
                        <a href="admin-dashboard-kost.php" class="nav-link text-light">
                            <i class="bi bi-house-door me-2"></i>
                            Kost
                        </a>
                    </li>
                    <li>
                        <a href="admin-dashboard-email.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
                            <i class="bi bi-envelope me-2"></i>
                            Email
                        </a>
                    </li>
                </ul>
                <hr class="text-light">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/50" alt="Admin" width="32" height="32"
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
                    <div class="col-md-4  wow fadeInUp" data-wow-delay="0.1s">
                        <div class="card" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title">User</h5>
                                <h3 class="card-text">1</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4  wow fadeInUp" data-wow-delay="0.2s">
                        <div class="card" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title">Pemilik Kost</h5>
                                <h3 class="card-text">1</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4  wow fadeInUp" data-wow-delay="0.3s">
                        <div class="card" style="height: 150px;">
                            <div class="card-body">
                                <h5 class="card-title">Promosi</h5>
                                <h3 class="card-text">Rp. 0</h3>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mt-5">
                    <h3 class="wow fadeInUp mb-4 text-center" data-wow-delay="0.1s">Pesan dari User</h3>
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="col-md-6 wow fadeInUp mb-4" data-wow-delay="0.1s">
                                <div class="card border-0 shadow-lg h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar rounded-circle me-3"
                                                style="background-color: #009270; width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; color: white; font-size: 20px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($message['nama']); ?></h5>
                                        </div>
                                        <hr>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                                        <p class="mb-2"><strong>Pesan:</strong>
                                            <?php echo htmlspecialchars($message['pesan']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><i
                                                    class="fa fa-clock me-1"></i><?php echo date("d F Y, H:i", strtotime($message['tanggal'])); ?></small>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill"
                                                    style="margin-right: 0.5rem;"><i class="fa fa-reply me-2"></i>Balas</button>
                                                <a href="?delete=<?php echo $message['id']; ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-pill"
                                                    onclick="return confirm('Anda yakin ingin menghapus pesan ini?')"><i
                                                        class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center">Tidak ada pesan dari user.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content End -->

    </div>


    <!-- Footer Start -->
    <footer class="text-light py-4" style="background-color: #000;">
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