<?php
include 'config.php';
session_start();

// Fetch kost details from the database based on a specific ID or parameter
$kost_id = $_GET['id']; // Assuming you pass the ID in the URL as a query string

// Fetch kost details
$sql = "SELECT * FROM kost WHERE id = '$kost_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of the kost
    $row = $result->fetch_assoc();
    $harga_setelah_diskon = $row['harga'] - $row['diskon'];

    $pemilik_sql = "SELECT u.pkname, u.image_profile
                    FROM logsys_pk u 
                    JOIN kost k ON k.pkname = u.pkname
                    WHERE k.id = '$kost_id'";
    $pemilik_result = $conn->query($pemilik_sql);
    $pemilik_data = $pemilik_result->fetch_assoc();

    // Fetch spesifikasi kamar
    $spesifikasi_sql = "SELECT spesifikasi FROM spesifikasi_kamar WHERE kost_id = '$kost_id'";
    $spesifikasi_result = $conn->query($spesifikasi_sql);

    // Fetch fasilitas kamar
    $fasilitas_sql = "SELECT fasilitas FROM fasilitas_kamar WHERE kost_id = '$kost_id'";
    $fasilitas_result = $conn->query($fasilitas_sql);

    // Fetch fasilitas kamar mandi
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
    <title>detail-SMARTKOST</title>
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
    </style>
</head>

<body class="bg-white mb-5">
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

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

            <!-- Description Section -->
            <div class="row mt-5">
                <div class="col-md-8">
                    <h2><?php echo $row['nama_kost']; ?></h2>
                    <div class="icon-text">
                        <i class="fa fa-map-marker-alt text-primary me-2"></i>
                        <span><?php echo $row['alamat']; ?></span>
                    </div>
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
                                <input type="date" class="form-control" value="2024-08-22">
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
                                <input type="date" id="startDate" class="form-control mb-2">
                                <label for="endDate">Tanggal Selesai:</label>
                                <input type="date" id="endDate" class="form-control mb-2">
                            </div>
                            <!-- Tombol Ajukan Sewa -->
                            <a href="pembayaran.php">
                                <button class="btn btn-primary w-100">Ajukan Sewa</button>
                            </a>
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

                <!-- Back to Top -->
                <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
            </div>

            <!-- JavaScript Libraries -->
            <script>
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

                    // Hitung total berdasarkan periode yang dipilih
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

                    // Tampilkan total price jika ada rent period
                    document.getElementById('totalPrice').style.display = (rentPeriod ? 'block' : 'none');
                }

                // Panggil fungsi ini saat halaman selesai dimuat
                window.onload = calculateTotal;
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