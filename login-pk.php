<?php
include 'config.php'; // Pastikan file ini mengandung koneksi database yang benar
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Query untuk mendapatkan user berdasarkan email
        $sql = "SELECT id, username, password FROM logsys_pk WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password benar, login berhasil
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $user['username']; // Simpan username dalam sesi
                
                // Arahkan ke dashboard
                header("Location: pk-dashboard.php");
                exit();
            } else {
                echo "Password salah!";
            }
        } else {
            echo "Pengguna tidak ditemukan!";
        }

        $stmt->close();
    } else {
        echo "Email dan password diperlukan!";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>sign in-SMARTKOST</title>
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
        .gradient-custom-2 {
            /* fallback for old browsers */
            background: #fccb90;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
        }

        .vh-100 {
            height: 100vh !important;
        }

        .container-custom {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .left-side {
            background-image: url(img2/kost2.jpg);
            border-radius: 2rem 0 0 2rem;
            padding: 40px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
        }

        .right-side {
            padding: 40px;
            background-color: #fff;
            border-radius: 0 2rem 2rem 0;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
        }

        .left-side img {
            max-width: 100%;
            height: auto;
        }

        .left-side h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .left-side p {
            font-size: 1.2rem;
            margin-bottom: 40px;
        }

        .left-side .btn {
            background-color: #000;
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
        }

        .right-side h5 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #000;
        }

        .right-side input {
            margin-bottom: 20px;
            padding: 10px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 25px;
            font-size: 15px;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            border: none;
            margin-left: 50px;
        }

        .right-side button {
            width: 50%;
            padding: 10px;
            border: none;
            border-radius: 25px;
            background-color: #00B98E;
            color: #fff;
            font-size: 15px;
        }

        .right-side button:hover {
            background-color: #008a6a;
        }

        .forgot-password {
            display: block;
            margin-top: 10px;
            text-align: right;
        }

        .register {
            font-size: 13px;
            color: #393f81;
            text-align: center;
            margin-top: 20px;
        }

        .register a {
            color: #393f81;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <!-- Section: Design Block -->
    <section class="vh-100 bg-light">
        <div class="container container-custom">
            <div class="row w-100">
                <!-- Left Side -->
                <div class="col-md-6 left-side text-start align-items-center"></div>

                <!-- Right Side -->
                <div class="col-md-6 right-side">
                    <div class="d-flex justify-content-center align-items-center mb-3 pb-1">
                        <img src="img2/logo smartkost.png" style="width: 240px; height: 80px;">
                    </div>
                    <h5 class="text-center fw-normal mb-1 pb-3 text-muted">Login Pemilik Kost</h5>

                    <form method="POST" action="login-pk.php">
                        <input type="email" name="email" placeholder="Email" class="form-control form-control-lg">
                        <input type="password" name="password" placeholder="Password"
                            class="form-control form-control-lg">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-dark btn-lg btn-block mt-4">Masuk</button>
                        </div>
                        <p class="register" style="color: #8d8d8d;">Tidak punya akun? <a
                                href="register-pk.php"><strong>Daftar di sini</strong></a></p>
                    </form>

                </div>
            </div>
        </div>
    </section>
    <!-- Section: Design Block -->

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
</body>