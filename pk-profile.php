<?php
include 'config.php';
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['pkname']))  {
    header("Location: login-pk.php");
    exit();
}

// Ambil data pengguna dari session
$pkname_session = $_SESSION['pkname'];
$sql = "SELECT pkname, email, nomor_hp, image_profile FROM logsys_pk WHERE pkname = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $pkname_session);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pkname = $row['pkname'];
    $email = $row['email'];
    $nomor_hp = $row['nomor_hp'];
    $image_profile = $row['image_profile'];
} else {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Proses pembaruan profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses upload gambar profil
    if (isset($_FILES['image_profile']) && $_FILES['image_profile']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image_profile']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            // Pastikan folder uploads ada
            $uploadDir = 'uploads/';
            $userFolder = $uploadDir . 'pk-' . $pkname_session . '/';


            if (!is_dir($userFolder)) {
                mkdir($userFolder, 0777, true); // Buat folder jika belum ada
            }

            // Buat nama file unik
            $uploadFile = $userFolder . time() . '_' . basename($_FILES['image_profile']['name']);

            if (move_uploaded_file($_FILES['image_profile']['tmp_name'], $uploadFile)) {
                // Simpan path file gambar ke database
                $sql = "UPDATE logsys_pk SET image_profile = ? WHERE pkname = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $uploadFile, $pkname_session);

                if ($stmt->execute()) {
                    $_SESSION['image_profile'] = $uploadFile;
                    header("Location: pk-profile.php");
                    exit();
                } else {
                    echo "Terjadi kesalahan saat menyimpan gambar ke database.";
                }
            } else {
                echo "Terjadi kesalahan saat mengupload file.";
            }
        } else {
            echo "Hanya file gambar (JPEG, PNG, GIF) yang diperbolehkan.";
        }
    }

    // Update username, email, dan nomor telepon
    if (!empty($_POST['pkname']) && !empty($_POST['email']) && !empty($_POST['nomor_hp'])) {
        $new_pkname = htmlspecialchars($_POST['pkname']);
        $new_email = htmlspecialchars($_POST['email']);
        $new_nomor_hp = htmlspecialchars($_POST['nomor_hp']);

        // Cek apakah username baru sudah ada di database (selain pengguna saat ini)
        if ($new_pkname !== $pkname_session) {
            $sql_check_pkname = "SELECT pkname FROM logsys_pk WHERE pkname = ?";
            $stmt_check = $conn->prepare($sql_check_pkname);
            $stmt_check->bind_param("s", $new_pkname);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                echo "Username sudah digunakan oleh pengguna lain. Silakan pilih username lain.";
                exit();
            }
        }

        // Simpan perubahan ke dalam database
        $sql = "UPDATE logsys_pk SET pkname = ?, email = ?, nomor_hp = ? WHERE pkname = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $new_pkname, $new_email, $new_nomor_hp, $pkname_session);

        if ($stmt->execute()) {
            // Update session dengan data terbaru
            $_SESSION['pkname'] = $new_pkname;
            $_SESSION['email'] = $new_email;
            $_SESSION['nomor_hp'] = $new_nomor_hp;

            // Redirect ke halaman profil setelah update berhasil
            header("Location: pk-profile.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat memperbarui data.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($pkname); ?> Dashboard - Profile Pemilik Kost</title>
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
        .profile-card {
            border: none;
            background-color: #ffffff;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            height: 350px;
        }

        .profile-card img {
            border-radius: 50%;
            width: 150px;
            margin-bottom: 1rem;
        }

        .profile-card h5 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-control {
            border-radius: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .btn {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
        }

        #image_profile_input {
            display: none;
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
                        <a href="pk-dashboard.php" class="nav-link text-light">
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
                        <a href="pk-profile.php" class="nav-link active text-light" style="background-color: #00B98E;"
                            aria-current="page">
                            <i class="bi bi-person me-2"></i>
                            Profile
                        </a>
                    </li>
                </ul>
                <hr class="text-light">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle"
                        id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($image_profile); ?>" alt="Admin" width="32" height="32"
                            class="rounded-circle me-2">
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

                    <!-- Profile Section -->
                    <div class="col-lg-4 mb-4 mt-3">
                        <div class="profile-card text-center">
                            <?php if (isset($image_profile) && file_exists($image_profile)): ?>
                                <img id="image_profile_preview" src="<?php echo $image_profile; ?>" alt="Profile_Image"
                                    class="profile-image" onclick="document.getElementById('image_profile_input').click();">
                            <?php else: ?>
                                <img src="img2/Bulat.png" alt="Default Profile Image" class="profile-image"
                                    onclick="document.getElementById('image_profile_input').click();">
                            <?php endif; ?>
                            <h5><?php echo htmlspecialchars($pkname); ?></h5>
                            <p class="text-muted">Pemilik Kost</p>
                        </div>
                    </div>

                    <div class="col-md-8 mt-3">
                        <h2 class="h4 mb-3">Profile Pemilik Kost</h2>
                        <form action="pk-profile.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="image_profile" id="image_profile_input" class="form-control"
                                accept="image/*">

                            <div class="mb-3">
                                <label for="ownerName" class="form-label">Nama Pemilik</label>
                                <input type="text" class="form-control" id="ownerName" name="pkname"
                                    placeholder="Enter Owner Name" value="<?php echo htmlspecialchars($pkname); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="ownerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="ownerEmail" name="email"
                                    placeholder="Enter Email" value="<?php echo htmlspecialchars($email); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="ownerPhone" class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" id="ownerPhone" name="nomor_hp"
                                    placeholder="Enter Phone Number" value="<?php echo htmlspecialchars($nomor_hp); ?>">
                            </div>

                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        </form>
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
        document.getElementById('image_profile_input').addEventListener('change', function (event) {
            const file = event.target.files[0]; // Get the selected file
            if (file) {
                const reader = new FileReader(); // Create a new FileReader to readA the file
                reader.onload = function (e) {
                    // Set the src of the preview image to the loaded file data
                    document.getElementById('image_profile_preview').src = e.target.result;
                }
                reader.readAsDataURL(file); // Read the file as a data URL
            }
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