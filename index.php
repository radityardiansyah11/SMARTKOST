<?php
include 'config.php';
session_start();

// Fungsi untuk membatasi jumlah karakter
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

        .modal-header {
            border-bottom: none;
        }

        .modal-body {
            text-align: center;
        }

        .option-button {
            display: flex;
            align-items: center;
            justify-content: start;
            padding: 10px;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            background-color: white;
            margin-bottom: 20px;
            box-shadow: rgba(0, 0, 0, 0.05) 0px 2px 4px;
            transition: all 0.2s ease-in-out;
        }

        .option-button img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .option-button:hover {
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 8px;
        }

        .close-btn {
            font-size: 1.5rem;
            font-weight: bold;
            border: none;
            background: none;
            position: absolute;
            top: 15px;
            right: 15px;
            cursor: pointer;
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
            height: 170px;
            object-fit: cover;
            object-position: center;
        }

        .konten {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            overflow: hidden;
            max-width: 1200px;
            width: 100%;
            height: 200px;
            padding: 0px;
            margin: 0 auto;
        }

        .text-content {
            padding: 40px;
            flex: 1;
        }

        .image-content img {
            width: 570px;
            height: 200px;
            border-radius: 0 10px 10px 0;
            object-fit: cover;
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
                        <a href="index.php" class="nav-item nav-link active">Beranda</a>

                        <a href="kost.php" class="nav-item nav-link">Kost</a>
                        <a href="kontak.php" class="nav-item nav-link">Kontak</a>
                    </div>
                    <button type="button" class="btn btn-primary px-3 d-none d-lg-flex" data-bs-toggle="modal"
                        data-bs-target="#loginModal">Login</button>
                </div>
            </nav>
        </div>
        <!-- Navbar End -->

        <!-- Modal Start -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="img2/logo_smartkost-removebg-preview.png" class="modal-title w-25">
                        <button type="button" class="close-btn" data-bs-dismiss="modal"
                            aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-4">Saya ingin masuk sebagai</p>

                        <a href="login.php" class="text-decoration-none">
                            <div class="option-button">
                                <img src="img2/login-tenant.svg" alt="Pencari Kos">
                                <strong>Pencari Kost</strong>
                            </div>
                        </a>

                        <a href="login-pk.php" class="text-decoration-none">
                            <div class="option-button">
                                <img src="img2/login-tenant.svg" alt="Pemilik Kos">
                                <strong>Pemilik Kost</strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->


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
                                <a href="register-pk.php"
                                    class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Daftarkan
                                    Kost</a>
                            </div>
                        </div>
                        <div class="owl-carousel-item position-relative">
                            <img class="img-fluid w-100 custom-height" src="img2/kost2.jpg" alt="">
                            <div class="overlay-text position-absolute top-50 start-50 translate-middle text-center">
                                <h1 class="display-5 text-white animated fadeIn mb-1 mt-5">Temukan <span
                                        class="text-light">Kost Impian</span> Hanya di Sini</h1>
                                <p class="text-white animated fadeIn mb-4 pb-2">Daftarkan kost anda di sini.</p>
                                <a href="register-pk.php"
                                    class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Daftarkan
                                    Kost</a>
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
                <form method="GET" action="kost.php">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control border-0 py-3" placeholder="Cari Kost" name="search"
                                value="">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control border-0 py-3" placeholder="Lokasi" name="Alamat"
                                value="">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select border-0 py-3">
                                <option selected>Jenis Kost</option>
                                <option value="1">Laki-laki</option>
                                <option value="2">Perempuan</option>
                                <option value="3">Campur</option>
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
                        <h1 class="mb-4">Selamat Datang di SMARTKOST</h1>
                        <p class="mb-4">Website SMARTKOST adlah website yang menyediakan berbagai kost untuk pengunjung
                            yang
                            ingin mencari kost dengan mudah hanya dengan website ini<br>
                            Keunggulan website ini :</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Pelayanan 24 jam</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Harga kost termurah</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Keamanan terjamin</p>
                        <a class="btn btn-primary py-3 px-5 mt-3" href="about.html">Selengkapnya</a>
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

        <!-- daftar -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="konten wow slideInLeft" data-wow-delay="0.1s">
                    <div class="text-content">
                        <h3>
                            Daftarkan Kos Anda di SMARTKOST
                        </h3>
                        <p>
                            Berbagai fitur dan layanan untuk meningkatkan bisnis kos Anda
                        </p>
                        <a class="btn btn-primary" href="login-pk.php">
                            Daftarkan Kost Anda
                        </a>
                    </div>
                    <div class="image-content">
                        <img alt="Two people looking at a phone together" height="400"
                            src="https://storage.googleapis.com/a1aa/image/ich0XrByvrpFCdjapmJOkhkol2qyiaaRbrozbo5EvIHyewzJA.jpg"
                            width="600" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Property List Start -->
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
                            $result = $conn->query("SELECT * FROM kost ORDER BY RAND() LIMIT 9");
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="property-item rounded overflow-hidden">
                                        <div class="position-relative overflow-hidden">
                                            <a href="detail.php?id=<?php echo $row['id']; ?>">
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
                                            <a class="d-block h5 mb-2"
                                                href=""><?php echo limit_characters($row['nama_kost'], 17); ?></a>
                                            <h5 class="text-primary mb-1">Rp.
                                                <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                            </h5>
                                            <p><i
                                                    class="fa fa-map-marker-alt text-primary me-2"></i><?php echo limit_characters($row['alamat'], 48); ?>
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
                                <a class="btn btn-primary py-3 px-5" href="kost.php">Lihat Lainnya</a>
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
                <div class="row g-4">

                    <?php
                    // Fetch Kost listings from the database
                    $result = $conn->query("SELECT * FROM kost ORDER BY RAND() LIMIT 4");
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="property-item rounded overflow-hidden">
                                <div class="position-relative overflow-hidden">
                                    <a href="detail.php?id=<?php echo $row['id']; ?>">
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
                                    <a class="d-block h5 mb-2"
                                        href=""><?php echo limit_characters($row['nama_kost'], 17); ?></a>
                                    <h5 class="text-primary mb-1">Rp.
                                        <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                    </h5>
                                    <p><i
                                            class="fa fa-map-marker-alt text-primary me-2"></i><?php echo limit_characters($row['alamat'], 48); ?>
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
                                            <span>info@smartkost.com</span>
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
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="name"
                                                    placeholder="Your Name">
                                                <label for="name">Nama Anda</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="email"
                                                    placeholder="Your Email">
                                                <label for="email">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" placeholder="Leave a message here"
                                                    id="message" style="height: 250px"></textarea>
                                                <label for="message">Pesan</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <a href="login.php">
                                                <button class="btn btn-outline-light w-100 py-3" type="button">Kirim
                                                    Pesan</button>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kontak Start -->

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
                                    <h6 class="fw-bold mb-1">Pak Bobot</h6>
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
                                    <h6 class="fw-bold mb-1">Pak Bibit</h6>
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