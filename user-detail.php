<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$kost_id = $_GET['id'];

$sql = "SELECT * FROM kost WHERE id = '$kost_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $harga_setelah_diskon = $row['harga'] - $row['diskon'];

    // Query untuk mendapatkan data pemilik kost berdasarkan pkname atau user_id
    $pemilik_sql = "SELECT u.pkname, u.image_profile
                    FROM logsys_pk u 
                    JOIN kost k ON k.pkname = u.pkname
                    WHERE k.id = '$kost_id'";
    $pemilik_result = $conn->query($pemilik_sql);
    $pemilik_data = $pemilik_result->fetch_assoc();

    $spesifikasi_sql = "SELECT spesifikasi FROM spesifikasi_kamar WHERE kost_id = '$kost_id'";
    $spesifikasi_result = $conn->query($spesifikasi_sql);

    $fasilitas_sql = "SELECT fasilitas FROM fasilitas_kamar WHERE kost_id = '$kost_id'";
    $fasilitas_result = $conn->query($fasilitas_sql);

    $fasilitas_mandi_sql = "SELECT fasilitas FROM fasilitas_kamar_mandi WHERE kost_id = '$kost_id'";
    $fasilitas_mandi_result = $conn->query($fasilitas_mandi_sql);

    $fasilitas_umum_sql = "SELECT fasilitas FROM fasilitas_umum WHERE kost_id = '$kost_id'";
    $fasilitas_umum_result = $conn->query($fasilitas_umum_sql);

    $peraturan_kost_sql = "SELECT peraturan FROM peraturan_kost WHERE kost_id = '$kost_id'";
    $peraturan_kost_result = $conn->query($peraturan_kost_sql);

    $ulasan_result = $conn->query("SELECT * FROM ulasan WHERE kost_id = $kost_id ORDER BY created_at DESC");

    $ulasan_query = "SELECT * FROM ulasan WHERE kost_id = ?";
    $stmt = $conn->prepare($ulasan_query);
    $stmt->bind_param("i", $kost_id);
    $stmt->execute();
    $ulasan_result = $stmt->get_result();

    // Hitung jumlah ulasan
    $total_ulasan = $ulasan_result->num_rows;

} else {
    echo "No kost found";
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

    <link href="css/detail-kost.css" rel="stylesheet">
    <style>
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

        .card-shadow {
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        }

        .hr-desk {
            border: none;
            border-top: 1.5px dashed #000;
        }

        .hr-desk2 {
            border: none;
            border-top: 1.5px solid #000;
        }

        .sticky-container {
            position: relative;
        }

        .sticky {
            position: sticky;
            top: 20px;
            z-index: 1000;
        }

        .card {
            margin-bottom: 20px;
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
            height: 250px;
            object-fit: cover;
            object-position: center;
        }

        .date {
            display: none;
        }
    </style>
</head>

<body class="bg-white">
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
                        <a href="user-home.php" class="nav-item nav-link">Beranda</a>
                        <a href="user-kost.php" class="nav-item nav-link">Kost</a>
                        <a href="user-kontak.php" class="nav-item nav-link">Kontak</a>
                        <a href="#" class="nav-item nav-link active">Detail</a>
                    </div>
                    <div class="d-flex">
                        <div class="me-3 text-end">
                            <h6 class="mt-2">Halo, <br> <?php echo htmlspecialchars($username); ?></h6>
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

        <!-- gambar -->
        <div class="container mt-5">
            <div class="row">
                <!-- Left Section: Image Gallery -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <img src="<?php echo $row['gambar_1']; ?>" class="img-fluid main-image rounded"
                                alt="Main Image">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <img src="<?php echo $row['gambar_2']; ?>" class="img-fluid thumbnail-image rounded"
                                alt="Thumbnail 1">
                        </div>
                        <div class="col-4">
                            <img src="<?php echo $row['gambar_3']; ?>" class="img-fluid thumbnail-image rounded"
                                alt="Thumbnail 2">
                        </div>
                        <div class="col-4">
                            <img src="<?php echo $row['gambar_4']; ?>" class="img-fluid thumbnail-image rounded"
                                alt="Thumbnail 3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi 1 -->
            <div class="row mt-5">
                <div class="col-md-8">
                    <h2><?php echo $row['nama_kost']; ?></h2>
                    <div class="icon-text">
                        <i class="fa fa-map-marker-alt text-primary me-2"></i>
                        <span><?php echo $row['alamat']; ?></span>
                    </div>

                    <hr class="hr-desk">

                    <h5 class="text-muted mt-3"><strong> Deskripsi Kost </strong></h5>
                    <p><?php echo $row['deskripsi']; ?></p>

                    <hr class="hr-desk">

                    <!-- Deskripsi dan Fasilitas Lengkap -->
                    <div class="row ">
                        <div class="col-md-12 d-flex">
                            <!-- Spesifikasi Tipe Kamar -->
                            <div class="me-5">
                                <h4 class="section-heading">Spesifikasi Tipe Kamar</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($spesifikasi = $spesifikasi_result->fetch_assoc()): ?>
                                            <li><?php echo $spesifikasi['spesifikasi']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Fasilitas Kamar -->
                            <div class="me-5">
                                <h4 class="section-heading">Fasilitas Kamar</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($fasilitas = $fasilitas_result->fetch_assoc()): ?>
                                            <li><?php echo $fasilitas['fasilitas']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Fasilitas Kamar Mandi -->
                            <div class="desk">
                                <h4 class="section-heading">Fasilitas Kamar Mandi</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($fasilitas_mandi = $fasilitas_mandi_result->fetch_assoc()): ?>
                                            <li><?php echo $fasilitas_mandi['fasilitas']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="hr-desk">

                    <div class="row ">
                        <div class="col-md-12 d-flex">
                            <!-- Fasilitas Umum -->
                            <div class="me-5">
                                <h4 class="section-heading">Fasilitas Umum</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($fasilitas_umum = $fasilitas_umum_result->fetch_assoc()): ?>
                                            <li><?php echo $fasilitas_umum['fasilitas']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>

                            <!-- Peraturan Kost -->
                            <div class="me-5">
                                <h4 class="section-heading">Peraturan Kost</h4>
                                <div class="icon-text">
                                    <ul>
                                        <?php while ($peraturan_kost = $peraturan_kost_result->fetch_assoc()): ?>
                                            <li><?php echo $peraturan_kost['peraturan']; ?></li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="hr-desk2">

                    <!-- Profil Pemilik Kost -->
                    <div class="d-flex">
                        <div>
                            <?php
                            // Cek apakah gambar profil ada, jika tidak pakai gambar default
                            $image_path = isset($pemilik_data['image_profile']) && !empty($pemilik_data['image_profile'])
                                ? $pemilik_data['image_profile']
                                : 'img2/bulat.png';
                            ?>
                            <img src="<?php echo $image_path; ?>" class="img-fluid rounded-circle" alt="Owner Image">
                        </div>
                        <div class="mt-1 ms-3">
                            <h5><strong><?php echo !empty($pemilik_data['pkname']) ? $pemilik_data['pkname'] : 'admin smarkost'; ?></strong>
                            </h5>
                            <p class="mb-0">Pemilik Kost</p>
                        </div>
                    </div>

                    <hr class="hr-desk2">

                    <!-- Ulasan -->
                    <div class="mt-4">
                        <h4>Ulasan (<?php echo $total_ulasan; ?>)</h4>
                        <div class="user-reviews">
                            <?php
                            // Ambil dua ulasan pertama dari database
                            $ulasan_query = "SELECT * FROM ulasan WHERE kost_id = '$kost_id' LIMIT 2";
                            $ulasan_result = $conn->query($ulasan_query);

                            // Menampilkan ulasan untuk kost ini
                            while ($ulasan = $ulasan_result->fetch_assoc()): ?>
                                <!-- Card untuk setiap ulasan -->
                                <div class="card mt-3">
                                    <div class="card-body card-shadow">
                                        <!-- Profil pengguna di sebelah username dan tanggal ulasan -->
                                        <div class="d-flex align-items-center">
                                            <img src="img2/Bulat.png" class="rounded-circle me-3" alt="User Profile"
                                                style="width: 50px; height: 50px;">
                                            <div>
                                                <h6 class="card-title mb-0">
                                                    <strong><?php echo htmlspecialchars($ulasan['username']); ?></strong>
                                                </h6>
                                                <p class="card-subtitle text-muted mt-1 small">
                                                    <?php echo htmlspecialchars($ulasan['created_at']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <p class="card-text mt-2"><?php echo htmlspecialchars($ulasan['review']); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- Tautan untuk melihat semua ulasan -->
                        <?php if ($total_ulasan > 2): ?>
                            <div>
                                <button id="show-more-reviews" class="btn btn-link">Lihat Semua Ulasan</button>
                            </div>
                        <?php endif; ?>

                        <!-- Div untuk menampilkan semua ulasan (disembunyikan pada awal) -->
                        <div id="all-reviews" style="display:none;">
                            <?php
                            // Ambil semua ulasan yang tersisa dari database (ulasan setelah dua pertama)
                            $all_ulasan_query = "SELECT * FROM ulasan WHERE kost_id = '$kost_id' LIMIT 2, $total_ulasan"; // Ambil ulasan setelah dua yang pertama
                            $all_ulasan_result = $conn->query($all_ulasan_query);

                            // Menampilkan semua ulasan yang tersisa
                            while ($ulasan = $all_ulasan_result->fetch_assoc()): ?>
                                <div class="card mt-3">
                                    <div class="card-body card-shadow">
                                        <div class="d-flex align-items-center">
                                            <img src="img2/Bulat.png" class="rounded-circle me-3" alt="User Profile"
                                                style="width: 50px; height: 50px;">
                                            <div>
                                                <h6 class="card-title mb-0">
                                                    <strong><?php echo htmlspecialchars($ulasan['username']); ?></strong>
                                                </h6>
                                                <p class="card-subtitle text-muted mt-1 small">
                                                    <?php echo htmlspecialchars($ulasan['created_at']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <p class="card-text mt-2"><?php echo htmlspecialchars($ulasan['review']); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>

                            <!-- Tombol untuk menutup semua ulasan -->
                            <div>
                                <button id="close-reviews" class="btn btn-link">Tutup</button>
                            </div>
                        </div>

                        <!-- Form untuk menambahkan ulasan -->
                        <div class="review-form mt-1">
                            <h7 class="text-muted mb-2"><strong>Tambahkan Ulasan</strong></h7>
                            <form action="submit_review.php" method="POST">
                                <div class="form-group mb-3 ">
                                    <textarea name="review" class="form-control" rows="2"
                                        placeholder="Tulis ulasan Anda di sini..." required></textarea>
                                </div>
                                <input type="hidden" name="kost_id" value="<?php echo $kost_id; ?>">
                                <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="sticky-container sticky">
                        <!-- Card Bayar -->
                        <div class="card p-4 card-shadow mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="icon-text text-danger mt-3">
                                        <i class="fas fa-bolt"></i>
                                        <span><strong>Diskon Rp.
                                                <?php echo number_format($row['diskon'], 0, ',', '.'); ?></strong></span>
                                    </div>
                                </div>
                                <span class="text-decoration-line-through text-muted">Rp.
                                    <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex">
                                <p class="final-price mt-2 text-dark" id="finalPrice">Rp.
                                    <?php echo number_format($harga_setelah_diskon, 0, ',', '.'); ?>
                                </p>
                                <span class="mt-3 ml-2">/bulan</span>
                            </div>
                            
                            <!-- Tanggal dan Periode Sewa -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="paymentDate" placeholder="Tanggal Mulai"
                                    onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">
                                <select class="form-select" id="rentPeriod" onchange="calculateTotal()">
                                    <option value="" selected>Waktu kost</option>
                                    <option value="month">1 Bulan</option>
                                    <option value="3months">3 Bulan</option>
                                    <option value="6months">6 Bulan</option>
                                    <option value="year">Per Tahun</option>
                                </select>
                            </div>

                            <!-- Total Harga -->
                            <div id="totalPrice" class="mb-3" style="display: none;">
                                <h5>Total Harga: <span id="totalAmount">Rp0</span></h5>
                            </div>
                            <!-- Rent Time Range (Awalnya Tersembunyi) -->
                            <div id="rentRange" class="hidden">
                                <label for="startDate">Tanggal Mulai:</label>
                                <input type="date" id="startDate" class="form-control mb-2" disabled>
                                <label for="endDate">Tanggal Selesai:</label>
                                <input type="date" id="endDate" class="form-control mb-2" disabled>
                            </div>
                            <!-- Tombol Ajukan Sewa -->
                            <form action="pembayaran.php" method="POST">
                                <!-- Input tersembunyi untuk mengirim nama kost, alamat, harga, dan lainnya -->
                                 <input type="hidden" name="pemilik_kost" value="<?php echo $row['pkname']; ?>">
                                <input type="hidden" name="nama_kost" value="<?php echo $row['nama_kost']; ?>">
                                <input type="hidden" name="alamat_kost" value="<?php echo $row['alamat']; ?>">
                                <input type="hidden" name="harga_kost" id="hargaKost"
                                    value="<?php echo $harga_setelah_diskon; ?>">
                                <input type="hidden" name="diskon_kost" value="<?php echo $row['diskon']; ?>">
                                <input type="hidden" name="total_harga" id="totalHarga">
                                <input type="hidden" name="waktu_kost" id="waktuKost">
                                <!-- Input untuk tanggal mulai dan selesai -->
                                <input type="hidden" name="mulai_sewa" id="startDateHidden">
                                <input type="hidden" name="selesai_sewa" id="endDateHidden">

                                <!-- Tombol Ajukan Sewa -->
                                <button type="submit" class="btn btn-primary w-100" onclick="submitForm()">Ajukan
                                    Sewa</button>
                            </form>
                        </div>

                        <!-- Card Lokasi -->
                        <div class="card p-4 card-shadow">
                            <h5>Lokasi</h5>
                            <div class="icon-text">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                <span><?php echo $row['alamat']; ?></span>
                            </div>
                            <div>
                                <iframe
                                    src="https://maps.google.com/maps?q=<?php echo urlencode($row['alamat']); ?>&output=embed"
                                    width="100%" height="200" style="border:0;" allowfullscreen=""
                                    loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt-5">

                <!-- Kost List Start -->
                <div class="container-xxl py-5">
                    <div class="container">
                        <div class="row g-0 gx-5 align-items-end">
                            <div class="mt-1">
                                <div class="text-center mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                                    <h2 class="mb-3">Rekomendasi Lainnya</h2>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane fade show p-0 active">
                                <div class="row g-4">
                                    <?php
                                    // Fetch Kost listings from the database
                                    $result = $conn->query("SELECT * FROM kost ORDER BY RAND() LIMIT 3");
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
                                                    <p><i
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
                <!-- kost List End -->
            </div>
        </div>

        <!-- Footer start -->
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

        <!-- JavaScript Libraries -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const stickyContainer = document.querySelector('.sticky-container');
                const kostListStart = document.querySelector('.container-xxl.py-5');

                // Get the offset position of the kost list start
                const kostListStartOffset = kostListStart.getBoundingClientRect().top + window.scrollY;

                window.addEventListener('scroll', function () {
                    const scrollPosition = window.scrollY;

                    // Check if we have scrolled to the start of the kost list section
                    if (scrollPosition >= kostListStartOffset) {
                        // Remove sticky class to allow cards to scroll away
                        stickyContainer.classList.remove('sticky');
                    } else {
                        // Add sticky class to keep cards in place if we have not reached the kost list
                        stickyContainer.classList.add('sticky');
                    }
                });
            });

            document.getElementById('show-more-reviews').addEventListener('click', function () {
                document.getElementById('all-reviews').style.display = 'block';
                this.style.display = 'none'; // Sembunyikan tombol setelah diklik
            });

            document.getElementById('close-reviews').addEventListener('click', function () {
                document.getElementById('all-reviews').style.display = 'none'; // Sembunyikan semua ulasan
                document.getElementById('show-more-reviews').style.display = 'inline'; // Tampilkan kembali tombol "Lihat Semua Ulasan"
            });

            function calculateTotal() {
                var hargaSetelahDiskon = <?php echo $harga_setelah_diskon; ?>; // Harga setelah diskon dari PHP
                var rentPeriod = document.getElementById('rentPeriod').value; // Nilai waktu sewa (bulan/tahun)
                var totalAmount = 0;

                // Kalkulasi total harga berdasarkan periode sewa
                switch (rentPeriod) {
                    case 'month':
                        totalAmount = hargaSetelahDiskon;
                        break;
                    case '3months':
                        totalAmount = hargaSetelahDiskon * 3;
                        break;
                    case '6months':
                        totalAmount = hargaSetelahDiskon * 6;
                        break;
                    case 'year':
                        totalAmount = hargaSetelahDiskon * 12;
                        break;
                }

                // Tampilkan total harga
                document.getElementById('totalAmount').innerText = 'Rp. ' + totalAmount.toLocaleString();
                document.getElementById('totalPrice').style.display = (rentPeriod ? 'block' : 'none');

                // Set value untuk input tersembunyi
                document.getElementById('totalHarga').value = totalAmount;
                document.getElementById('waktuKost').value = rentPeriod;

                // Set tanggal mulai dan selesai
                var startDate = document.getElementById('paymentDate').value;
                document.getElementById('startDateHidden').value = startDate;

                if (startDate) {
                    var endDate = calculateEndDate(startDate, rentPeriod);
                    document.getElementById('endDateHidden').value = endDate;
                    document.getElementById('endDate').value = endDate; // Update UI
                }
            }

            // Fungsi untuk menghitung tanggal selesai sewa berdasarkan periode sewa
            function calculateEndDate(startDate, rentPeriod) {
                var start = new Date(startDate);
                switch (rentPeriod) {
                    case 'month':
                        start.setMonth(start.getMonth() + 1);
                        break;
                    case '3months':
                        start.setMonth(start.getMonth() + 3);
                        break;
                    case '6months':
                        start.setMonth(start.getMonth() + 6);
                        break;
                    case 'year':
                        start.setFullYear(start.getFullYear() + 1);
                        break;
                }
                return start.toISOString().split('T')[0];
            }

            function submitForm() {
                calculateTotal(); // Pastikan total dihitung dan input tersembunyi diisi
            }

            // Atur tanggal default ke hari ini ketika halaman dimuat
            document.addEventListener("DOMContentLoaded", function () {
                var today = new Date().toISOString().split('T')[0];
                document.getElementById("paymentDate").value = today; // Set tanggal mulai
                calculateTotal(); // Kalkulasi total saat halaman dimuat
            });

            function confirmLogout() {
                if (confirm("Anda yakin ingin logout?")) {
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