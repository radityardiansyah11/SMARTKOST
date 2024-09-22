<?php
include 'config.php';
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika tidak, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$username = $_SESSION['username'];
$message_status = "";  // Variabel untuk menyimpan status pesan

// Periksa apakah formulir telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $pesan = htmlspecialchars($_POST['pesan']);

    // Validasi sederhana
    if (!empty($nama) && !empty($email) && !empty($pesan)) {
        // Persiapkan pernyataan SQL untuk menyimpan data ke database
        $sql = "INSERT INTO kontak (nama, email, pesan) VALUES (?, ?, ?)";

        // Koneksi ke database
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $nama, $email, $pesan);

            // Eksekusi query
            if ($stmt->execute()) {
                $message_status = "success";  // Pesan berhasil dikirim
            } else {
                $message_status = "error";  // Pesan gagal dikirim
            }
            $stmt->close();
        }
    } else {
        $message_status = "incomplete";  // Kolom tidak lengkap
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>home-SMARTKOST</title>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        .btn-custom-logout {
            font-size: 12px;
            padding: 2px 8px;
            line-height: 1.5;
            border-radius: 4px;
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
                        <a href="user-home.php" class="nav-item nav-link active">Beranda</a>
                        <a href="user-kost.php" class="nav-item nav-link">Kost</a>
                        <a href="user-kontak.php" class="nav-item nav-link">Kontak</a>
                    </div>
                    <div class="d-flex">
                        <div class="me-3 text-end">
                            <h6 class="mt-2">Halo, <?php echo htmlspecialchars($username); ?></h6>
                            <form action="logout.php" method="POST">
                                <button type="submit" class="btn btn-outline-danger btn-custom-logout"
                                    onclick="confirmLogout()">Log out</button>
                            </form>
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
                                <h1 class="display-5 text-white animated fadeIn mb-1 mt-5">Temukan <span
                                        class="text-light">Kost Impian</span> Hanya di Sini</h1>
                                <p class="text-white animated fadeIn mb-4 pb-2">Daftarkan kost anda di sini.</p>
                                <a href="" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Daftarkan Kost</a>
                            </div>
                        </div>
                        <div class="owl-carousel-item position-relative">
                            <img class="img-fluid w-100 custom-height" src="img2/kost2.jpg" alt="">
                            <div class="overlay-text position-absolute top-50 start-50 translate-middle text-center">
                                <h1 class="display-5 text-white animated fadeIn mb-1 mt-5">Temukan <span
                                        class="text-light">Kost Impian</span> Hanya di Sini</h1>
                                <p class="text-white animated fadeIn mb-4 pb-2">Daftarkan kost anda di sini.</p>
                                <a href="" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Daftarkan Kost</a>
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
                <div class="row g-2">
                    <div class="col-md-10">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control border-0 py-3" placeholder="Cari Kost">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select border-0 py-3">
                                    <option selected>Tipe Kost</option>
                                    <option value="1">Standart</option>
                                    <option value="2">Premium</option>
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
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-dark border-0 w-100 py-3">Cari</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Search End -->

        <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <div class="about-img position-relative overflow-hidden p-5 pe-0">
                            <img class="img-fluid w-100" src="img/about.jpg">
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="mb-4">Welcome to SMARTKOST</h1>
                        <p class="mb-4">Website SMARTKOST adlah website yang menyediakan berbagai kost untuk pengunjung
                            yang
                            ingin mencari kost dengan mudah hanya dengan website ini<br>
                            Keunggulan website ini :</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Pelayanan 24 jam</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Harga kost termurah</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Keamanan terjamin</p>
                        <a class="btn btn-primary py-3 px-5 mt-3" href="">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

        <!-- Category Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Pelayanan Kami</h1>
                    <p>Kami menyediakan pelayanan terbaik hanya untuk pelanggan SMARTKOST</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                        <a class="cat-item d-block bg-light text-center rounded p-3" href="">
                            <div class="rounded p-4">
                                <div class="icon mb-3">
                                    <img class="img-fluid" src="img/icon-apartment.png" alt="Icon">
                                </div>
                                <h6>Layanan 24 jam</h6>
                                <span>kami memberikan pelayanan terbaik</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                        <a class="cat-item d-block bg-light text-center rounded p-3" href="">
                            <div class="rounded p-4">
                                <div class="icon mb-3">
                                    <img class="img-fluid" src="img/icon-villa.png" alt="Icon">
                                </div>
                                <h6>Booking Kost</h6>
                                <span>Anda dapat booking kost yang anda inginkan</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                        <a class="cat-item d-block bg-light text-center rounded p-3" href="">
                            <div class="rounded p-4">
                                <div class="icon mb-3">
                                    <img class="img-fluid" src="img/icon-house.png" alt="Icon">
                                </div>
                                <h6>Pembayaran</h6>
                                <span>Kami menjamin keamana transaksi</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                        <a class="cat-item d-block bg-light text-center rounded p-3" href="">
                            <div class="rounded p-4">
                                <div class="icon mb-3">
                                    <img class="img-fluid" src="img/icon-housing.png" alt="Icon">
                                </div>
                                <h6>Kost</h6>
                                <span>Terdapat berbagai kost yang tersedia</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Category End -->

        <!-- Kost List Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-0 gx-5 align-items-end">
                    <div class="col-lg-6">
                        <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                            <h1 class="mb-3">Kost Terlaris</h1>
                            <p>Berikut ini kami menampilkan beberapa kost yang sedang laris saat ini</p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                    </div>
                </div>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">

                            <?php
                            // Fetch Kost listings from the database
                            $result = $conn->query("SELECT * FROM kost LIMIT 9");
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
                                            <p>
                                                <i
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
                            ?>

                            <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
                                <a class="btn btn-primary py-3 px-5" href="user-kost.php">Lihat Lainnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Property List End -->

        <!-- Destination Start -->
        <div class="container-xxl mb-3 py-5 destination">
            <div class="container">
                <div class="text-start wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="mb-1">Sedang Diskon</h1>
                    <p class="mb-4">Beberapa kost yang sedang diskon saat ini</p>
                </div>
                <div class="row g-3">
                    <div class="col-lg-7 col-md-6">
                        <div class="row g-3">
                            <div class="col-lg-12 col-md-12 wow zoomIn" data-wow-delay="0.1s">
                                <a class="position-relative d-block overflow-hidden" href=""
                                    style="border-radius: 10px;">
                                    <img class="img-fluid" src="img2/kost1.jpg" alt="">
                                    <div
                                        class="bg-white text-danger fw-bold position-absolute top-0 start-0 m-3 py-1 px-2">
                                        30% OFF</div>
                                    <div
                                        class="bg-white text-primary fw-bold position-absolute bottom-0 end-0 m-3 py-1 px-2">
                                        Malang</div>
                                </a>
                            </div>
                            <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.3s">
                                <a class="position-relative d-block overflow-hidden" href=""
                                    style="border-radius: 10px;">
                                    <img class="img-fluid" src="img2/gbr-kost2.jpg" alt="">
                                    <div
                                        class="bg-white text-danger fw-bold position-absolute top-0 start-0 m-3 py-1 px-2">
                                        25% OFF</div>
                                    <div
                                        class="bg-white text-primary fw-bold position-absolute bottom-0 end-0 m-3 py-1 px-2">
                                        Malang</div>
                                </a>
                            </div>
                            <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.5s">
                                <a class="position-relative d-block overflow-hidden" href=""
                                    style="border-radius: 10px;">
                                    <img class="img-fluid" src="img2/gbr-kost3.jpg" alt="">
                                    <div
                                        class="bg-white text-danger fw-bold position-absolute top-0 start-0 m-3 py-1 px-2">
                                        35% OFF</div>
                                    <div
                                        class="bg-white text-primary fw-bold position-absolute bottom-0 end-0 m-3 py-1 px-2">
                                        Malang</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6 wow zoomIn" data-wow-delay="0.7s" style="min-height: 350px;">
                        <a class="position-relative d-block h-100 overflow-hidden" href="" style="border-radius: 10px;">
                            <img class="img-fluid position-absolute w-100 h-100" src="img2/kost1.jpg" alt=""
                                style="object-fit: cover;">
                            <div class="bg-white text-danger fw-bold position-absolute top-0 start-0 m-3 py-1 px-2">20%
                                OFF</div>
                            <div class="bg-white text-primary fw-bold position-absolute bottom-0 end-0 m-3 py-1 px-2">
                                Malang</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Destination Start -->


        <!--Kontak Start -->
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-1">Kontak Kami</h1>
        </div>
        <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container-xxl py-5">
                <div class="container">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="row gy-4">
                                <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex align-items-center bg-white rounded p-3"
                                            style="border: 1px dashed rgba(0, 185, 142, .3)">
                                            <div class="icon me-3" style="width: 45px; height: 45px;">
                                                <i class="fa fa-map-marker-alt text-primary"></i>
                                            </div>
                                            <span>Malang</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.3s">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex align-items-center bg-white rounded p-3"
                                            style="border: 1px dashed rgba(0, 185, 142, .3)">
                                            <div class="icon me-3" style="width: 45px; height: 45px;">
                                                <i class="fa fa-envelope-open text-primary"></i>
                                            </div>
                                            <span>info@example.com</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 wow fadeIn" data-wow-delay="0.5s">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex align-items-center bg-white rounded p-3"
                                            style="border: 1px dashed rgba(0, 185, 142, .3)">
                                            <div class="icon me-3" style="width: 45px; height: 45px;">
                                                <i class="fa fa-phone-alt text-primary"></i>
                                            </div>
                                            <span>+012 345 6789</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <iframe class="position-relative rounded w-100 h-100"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d505737.8453050333!2d112.0523413734375!3d-7.990993800000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd62900c86bf52f%3A0xffb1b6c89ccbdafd!2sComboran!5e0!3m2!1sid!2sid!4v1724291586014!5m2!1sid!2sid"
                                frameborder="0" style="min-height: 400px; border:0;" allowfullscreen=""
                                aria-hidden="false" tabindex="0"></iframe>
                        </div>
                        <div class="col-md-6">
                            <div class="wow fadeInUp" data-wow-delay="0.5s">
                                <p class="mb-4 text-light">Kontak kami jika ada masalah/kendala dalam website kami </p>
                                <form method="POST" action="user-home.php">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="nama" placeholder="Nama"
                                                    name="nama">
                                                <label for="nama">Nama</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="email" placeholder="Email"
                                                    name="email">
                                                <label for="email">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" placeholder="Leave a message here"
                                                    id="pesan" name="pesan" style="height: 250px;"></textarea>
                                                <label for="pesan">Pesan</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-outline-light w-100 py-3" type="submit">Kirim
                                                Pesan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kontak end -->

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
                            <h5 class="mt-4">Cari Kost</h5>
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

        <!-- Testimonial Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Review</h1>
                </div>
                <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                    <div class="testimonial-item bg-light rounded p-3">
                        <div class="bg-white border rounded p-4">
                            <p>Website ini sangat membantu dalam pencarian kost</p>
                            <div class="d-flex align-items-center">
                                <img class="img-fluid flex-shrink-0 rounded" src="img/testimonial-1.jpg"
                                    style="width: 45px; height: 45px;">
                                <div class="ps-3">
                                    <h6 class="fw-bold mb-1">Uni Bakwan</h6>
                                    <small>client</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light rounded p-3">
                        <div class="bg-white border rounded p-4">
                            <p>Saya sangat berterimakasih dengan website ini karna memudahka saya dalam mencari kost</p>
                            <div class="d-flex align-items-center">
                                <img class="img-fluid flex-shrink-0 rounded" src="img/testimonial-2.jpg"
                                    style="width: 45px; height: 45px;">
                                <div class="ps-3">
                                    <h6 class="fw-bold mb-1">Pak Mahmud</h6>
                                    <small>Client</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-light rounded p-3">
                        <div class="bg-white border rounded p-4">
                            <p>Website nya joss.. sangat rekomendasi untuk mencari kost</p>
                            <div class="d-flex align-items-center">
                                <img class="img-fluid flex-shrink-0 rounded" src="img/testimonial-3.jpg"
                                    style="width: 45px; height: 45px;">
                                <div class="ps-3">
                                    <h6 class="fw-bold mb-1">Pak Ali</h6>
                                    <small>client</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonial End -->


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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var status = "<?php echo $message_status; ?>";

            if (status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesan Berhasil Dikirim!',
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
                    title: 'Pesan Gagal Dikirim!',
                    text: 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#00765a', // Warna tombol sesuai tema website
                    width: '350px', // Ukuran kotak lebih kecil
                    customClass: {
                        title: 'custom-title',
                        content: 'custom-content'
                    }
                });
            } else if (status === "incomplete") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulir Tidak Lengkap!',
                    text: 'Harap isi semua kolom.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f39c12', // Warna tombol peringatan
                    width: '350px', // Ukuran kotak lebih kecil
                    customClass: {
                        title: 'custom-title',
                        content: 'custom-content'
                    }
                });
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