<?php
session_start();
include 'config.php';

// Pastikan pengguna telah login
if (!isset($_SESSION['pkname'])) {
    header("Location: login-pk.php"); // Redirect ke halaman login jika belum login
    exit();
}

$pkname = $_SESSION['pkname']; // Ambil username dari sesi

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM bookings WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Setelah penghapusan, reset urutan ID
        $reset_id_query = "
            SET @count = 0;
            UPDATE bookings SET id = @count := @count + 1;
            ALTER TABLE bookings AUTO_INCREMENT = 1;
        ";
        mysqli_multi_query($conn, $reset_id_query);

        // Set session status to 'deleted' after successful deletion
        $_SESSION['status'] = "deleted";
    } else {
        // Set session status to 'error' if deletion fails
        $_SESSION['status'] = "error";
    }

    // Redirect setelah penghapusan dan reset
    header('Location: pk-dashboard-booking.php');
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

function limit_characters($string, $char_limit)
{
    if (strlen($string) > $char_limit) {
        return substr($string, 0, $char_limit) . '...';
    }
    return $string;
}

// Tangkap input pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; // Bersihkan input dari spasi

// Query untuk mengambil data booking yang sesuai dengan kost yang dimiliki pemilik kost
$query_bookings = "
   SELECT b.id, b.nama_penyewa, b.telp_penyewa, b.email_penyewa, k.nama_kost, 
       b.metode_pembayaran, b.total_harga, b.mulai_sewa, b.selesai_sewa, 
       k.pkname AS pemilik_kost, k.alamat AS alamat_kost, 
       b.order_id, b.status_pembayaran, b.tanggal_booking
FROM bookings b
JOIN kost k ON b.nama_kost = k.nama_kost
WHERE k.pkname = '$pkname'";

// Menambahkan kondisi pencarian jika ada input pencarian
if ($search) {
    $search = $conn->real_escape_string($search); // Mencegah SQL injection
    $query_bookings .= " AND (b.nama_penyewa LIKE '%$search%' OR b.nama_kost LIKE '%$search%' OR b.email_penyewa LIKE '%$search%' OR b.telp_penyewa LIKE '%$search%')";
}

$result_bookings = mysqli_query($conn, $query_bookings);

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

    <style>
        .card {
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            border: none;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 31px;
            height: 31px;
            border-radius: 3px;
            padding: 0;
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

        /* CSS */
        .clean-modal {
            border-radius: 10px;
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.15);
            font-family: 'Arial', sans-serif;
        }

        .modal-header {
            background-color: #00B98E;
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-body {
            padding: 20px 24px;
            background-color: #ffffff;
        }

        .modal-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 0.25rem;
            display: block;
        }

        .modal-info {
            font-size: 1rem;
            color: #333;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .section-header {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid #e9ecef;
        }

        .modal-footer {
            padding: 16px 24px;
            background-color: #00B98E;
            border-top: none;
            display: flex;
            justify-content: space-between;
        }

        .modal-footer .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .form-select {
            border: none;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        /* Responsiveness untuk 4 Kolom */
        @media (min-width: 992px) {
            .modal-body .row .col-md-3 {
                width: 25%;
            }
        }

        @media (max-width: 992px) {
            .modal-body .row .col-md-3 {
                width: 50%;
            }
        }

        @media (max-width: 576px) {
            .modal-body .row .col-md-3 {
                width: 100%;
            }
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
                style=" width: 220px; height: 100vh; position: fixed; background-color: #00765a;">
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
                        <a href="pk-dashboard-kost.php" class="nav-link text-light">
                            <i class="bi bi-house-door me-2"></i>
                            Kost
                        </a>
                    </li>
                    <li>
                        <a href="pk-dashboard-booking.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
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
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="col-lg-6 d-flex align-items-center">
                            <h4 class="mb-3 me-3">Booking</h4>
                            <!-- Form Pencarian -->
                            <form class="d-flex mb-3" action="" method="GET">
                                <input class="form-control me-2" type="search" name="search" placeholder="Cari Booking"
                                    aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-outline-success" type="submit"><i
                                        class="bi bi-search"></i></button>
                            </form>
                        </div>
                        <table class="table table-hover">
                            <thead class="table" style="background-color: #009270;">
                                <tr class="text-light">
                                    <th scope="col">ID</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">No. Telp</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Kost</th>
                                    <th scope="col">Metode</th>
                                    <th scope="col">Bayar</th>
                                    <th scope="col">Tgl Mulai</th>
                                    <th scope="col">Tgl Selesai</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_bookings)) { ?>
                                    <tr class="wow fadeIn" data-wow-delay="0.1s" >
                                        <td class="align-middle"><?php echo $row['id']; ?></td>
                                        <td class="align-middle"><?php echo limit_characters($row['nama_penyewa'], 7); ?>
                                        </td>
                                        <td class="align-middle"><?php echo limit_characters($row['telp_penyewa'], 8); ?>
                                        </td>
                                        <td class="align-middle"><?php echo limit_characters($row['email_penyewa'], 12); ?>
                                        </td>
                                        <td class="align-middle"><?php echo limit_characters($row['nama_kost'], 10); ?></td>
                                        <td class="align-middle"><?php echo ($row['metode_pembayaran']); ?></td>
                                        <td class="align-middle">Rp.
                                            <?php echo number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="align-middle"><?php echo ($row['mulai_sewa']); ?></td>
                                        <td class="align-middle"><?php echo ($row['selesai_sewa']); ?>
                                        <td class="align-middle"><?php echo $row['status_pembayaran']; ?></td>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-view mt-2"
                                                onclick='openBookingModal(<?php echo json_encode($row); ?>)'>
                                                <img src="img2/view.png" class="w-75">
                                            </button>
                                            <a href="?delete=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-danger mt-2 btn-trash"
                                                onclick="return confirm('Apa kamu yakin akan menghapus?');">
                                                <img src="img2/sampah.png" class="w-75">
                                        </td>
                                    </tr>
                                <?php } ?>
                                <!-- More rows as needed -->
                            </tbody>
                        </table>

                        <div class="modal fade" id="viewBookingModal" tabindex="-1"
                            aria-labelledby="viewBookingModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content clean-modal">
                                    <div class="modal-header">
                                        <h5 class="text-white" id="viewBookingModalLabel">Detail Booking</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- User booking detail -->
                                        <div class="section-header">User Booking Detail</div>
                                        <div class="row gy-3">
                                            <!-- User details here -->
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">ID Booking</label>
                                                <div class="modal-info" id="modalBookingId"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Nama Penyewa</label>
                                                <div class="modal-info" id="modalNamaPenyewa"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Email Penyewa</label>
                                                <div class="modal-info" id="modalEmailPenyewa"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Telp Penyewa</label>
                                                <div class="modal-info" id="modalTelpPenyewa"></div>
                                            </div>
                                        </div>

                                        <!-- Kost detail booking -->
                                        <div class="section-header">Kost Detail Booking</div>
                                        <div class="row gy-3">
                                            <!-- Kost details here -->
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Nama Kost</label>
                                                <div class="modal-info" id="modalNamaKost"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Pemilik Kost</label>
                                                <div class="modal-info" id="modalPemilikKost"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Alamat Kost</label>
                                                <div class="modal-info" id="modalAlamatKost"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Tanggal Mulai Sewa</label>
                                                <div class="modal-info" id="modalMulaiSewa"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Tanggal Selesai Sewa</label>
                                                <div class="modal-info" id="modalSelesaiSewa"></div>
                                            </div>
                                        </div>

                                        <!-- Price detail booking -->
                                        <div class="section-header">Price Detail Booking</div>
                                        <div class="row gy-3">
                                            <!-- Price details here -->
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Order ID</label>
                                                <div class="modal-info" id="modalOrderId"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Total Harga</label>
                                                <div class="modal-info" id="modalTotalHarga"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Metode Pembayaran</label>
                                                <div class="modal-info" id="modalMetodePembayaran"></div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Status Pembayaran</label>
                                                <select id="modalStatusPembayaran" class="form-select">
                                                    <option value="Pending">Pending</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Canceled">Canceled</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="modal-label">Tanggal Booking</label>
                                                <div class="modal-info" id="modalTanggalBooking"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-secondary"
                                            onclick="saveStatusPembayaran()">Confirm</button>
                                    </div>
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
        function openBookingModal(booking) {
            // Isi elemen modal dengan data booking yang diklik
            document.getElementById("modalBookingId").textContent = booking.id;
            document.getElementById("modalPemilikKost").textContent = booking.pemilik_kost;
            document.getElementById("modalNamaKost").textContent = booking.nama_kost;
            document.getElementById("modalAlamatKost").textContent = booking.alamat_kost;
            document.getElementById("modalTotalHarga").textContent = "Rp. " + new Intl.NumberFormat().format(booking.total_harga);
            document.getElementById("modalMulaiSewa").textContent = booking.mulai_sewa;
            document.getElementById("modalSelesaiSewa").textContent = booking.selesai_sewa;
            document.getElementById("modalNamaPenyewa").textContent = booking.nama_penyewa;
            document.getElementById("modalEmailPenyewa").textContent = booking.email_penyewa;
            document.getElementById("modalTelpPenyewa").textContent = booking.telp_penyewa;
            document.getElementById("modalMetodePembayaran").textContent = booking.metode_pembayaran;
            document.getElementById("modalStatusPembayaran").value = booking.status_pembayaran;
            document.getElementById("modalTanggalBooking").textContent = booking.tanggal_booking;
            document.getElementById("modalOrderId").textContent = booking.order_id;

            // Tampilkan modal
            var viewBookingModal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
            viewBookingModal.show();
        }

        function saveStatusPembayaran() {
            const bookingId = document.getElementById('modalBookingId').innerText; // Ambil ID Booking
            const statusPembayaran = document.getElementById('modalStatusPembayaran').value; // Status yang dipilih

            // Kirim data ke server menggunakan fetch API
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `booking_id=${bookingId}&status_pembayaran=${statusPembayaran}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Status pembayaran berhasil diperbarui.');
                        // Reload halaman untuk melihat perubahan
                        location.reload();
                    } else {
                        alert('Gagal memperbarui status: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan.');
                });
        }

        // Misalnya, kita ingin menampilkan status pembayaran di halaman utama
        window.onload = function () {
            const bookingId = "12345";  // ID booking yang digunakan di contoh
            updateStatusDisplay(bookingId); // Update status pembayaran saat halaman dimuat
        }

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