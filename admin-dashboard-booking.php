<?php
include 'config.php';
session_start();

$sql = "SELECT id, nama_penyewa, telp_penyewa, 	email_penyewa, pemilik_kost, nama_kost, metode_pembayaran, total_harga, mulai_sewa, order_id FROM bookings";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

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
    header('Location: admin-dashboard-booking.php');
    exit();
}

// Cek apakah query berhasil
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Query to count the total number of users
$count_user_sql = "SELECT COUNT(*) AS total_users FROM login_system";
$count_user_result = mysqli_query($conn, $count_user_sql);
$user_data = mysqli_fetch_assoc($count_user_result);
$total_users = $user_data['total_users'];

// Query to count the number of pemilik kost
$count_pk_sql = "SELECT COUNT(*) AS total_pk FROM logsys_pk";
$count_pk_result = mysqli_query($conn, $count_pk_sql);
$pk_data = mysqli_fetch_assoc($count_pk_result);
$total_pk = $pk_data['total_pk'];

// Query to count the number of kost
$count_kost_sql = "SELECT COUNT(*) AS total_kost FROM kost";
$count_kost_result = mysqli_query($conn, $count_kost_sql);
$kost_data = mysqli_fetch_assoc($count_kost_result);
$total_kost = $kost_data['total_kost'];

// Query to count the number of email
$count_email_sql = "SELECT COUNT(*) AS total_email FROM kontak";
$count_email_result = mysqli_query($conn, $count_email_sql);
$email_data = mysqli_fetch_assoc($count_email_result);
$total_email = $email_data['total_email'];

// Query untuk menghitung 5% dari total harga booking
$query_pendapatan = "SELECT SUM(total_harga * 0.10) AS total_pendapatan FROM bookings";
$result_pendapatan = mysqli_query($conn, $query_pendapatan);

if (!$result_pendapatan) {
    die("Query error: " . mysqli_error($conn));
}

// Ambil hasil query
$row_pendapatan = mysqli_fetch_assoc($result_pendapatan);
$total_pendapatan = $row_pendapatan['total_pendapatan'] ?? 0; // Default ke 0 jika NULL


function limit_characters($string, $char_limit)
{
    if (strlen($string) > $char_limit) {
        return substr($string, 0, $char_limit) . '...';
    }
    return $string;
}

// Ambil input pencarian dari user
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Tentukan query dasar dengan JOIN ke tabel `kost` untuk mendapatkan detail kost
$query_bookings = "
    SELECT b.id, b.nama_penyewa, b.telp_penyewa, b.email_penyewa, 
           b.nama_kost, b.metode_pembayaran, b.total_harga, 
           b.mulai_sewa, b.selesai_sewa, b.order_id, 
           b.status_pembayaran, b.tanggal_booking,
           k.pkname AS pemilik_kost, k.alamat AS alamat_kost
    FROM bookings b
    LEFT JOIN kost k ON b.nama_kost = k.nama_kost
";

// Tambahkan kondisi pencarian jika ada input pencarian
if ($search) {
    $search = $conn->real_escape_string($search); // Mencegah SQL injection
    $query_bookings .= " WHERE (b.nama_penyewa LIKE '%$search%' OR b.nama_kost LIKE '%$search%')";
}

// Tambahkan klausa ORDER BY agar hasil query diurutkan berdasarkan id secara ascending
$query_bookings .= " ORDER BY b.id ASC";

$result_bookings = mysqli_query($conn, $query_bookings);
if (!$result_bookings) {
    die("Query error: " . mysqli_error($conn));
}

include 'midtrans-transaction.php'; // Include file fungsi Midtrans

$order_ids = ['YOUR_ORDER_ID1', 'YOUR_ORDER_ID2']; // Daftar Order ID yang ingin diambil
$transactions = [];

foreach ($order_ids as $order_id) {
    $transaction = getMidtransTransactions($order_id);
    if ($transaction) {
        $transactions[] = $transaction;
    }
}
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
                        <a href="admin-dashboard.php" class="nav-link text-light">
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
                        <a href="admin-dashboard-booking.php" class="nav-link active text-light"
                            style="background-color: #00B98E;" aria-current="page">
                            <i class="bi bi-bookmarks me-2"></i>
                            Booking
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
                                                <h5 class="card-title">Kost</h5>
                                                <h3 class="card-text"><?php echo $total_kost; ?></h3>
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
                                                <h5 class="card-title">Email</h5>
                                                <h3 class="card-text"><?php echo $total_email; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card" style="height: 160px; margin: 10px 0;">
                                            <div class="card-body">
                                                <h5 class="card-title">Pendapatan</h5>
                                                <h3 class="card-text">Rp. <?php echo number_format($total_pendapatan, 0, ',', '.'); ?>
                                                </h3>
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

                <!-- Listings -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="col-lg-6 d-flex align-items-center">
                            <h4 class="mb-3 me-3">Booking</h4>
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
                                    <th scope="col">P. Kost</th>
                                    <th scope="col">Kost</th>
                                    <th scope="col">Metode</th>
                                    <th scope="col">Bayar</th>
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
                                        <td class="align-middle"><?php echo limit_characters($row['telp_penyewa'], 12); ?>
                                        </td>
                                        <td class="align-middle"><?php echo limit_characters($row['email_penyewa'], 16); ?>
                                        </td>
                                        <td class="align-middle"><?php echo limit_characters($row['pemilik_kost'], 9); ?>
                                        </td>
                                        <td class="align-middle"><?php echo limit_characters($row['nama_kost'], 13); ?></td>
                                        <td class="align-middle"><?php echo $row['metode_pembayaran']; ?></td>
                                        <td class="align-middle">Rp.
                                            <?php echo number_format($row['total_harga'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="align-middle"><?php echo $row['status_pembayaran']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-view mt-2"
                                                onclick='openBookingModal(<?php echo json_encode($row); ?>)'>
                                                <img src="img2/view.png" class="w-75">
                                            </button>
                                            <a href="?delete=<?php echo $row['id']; ?>"
                                                class="btn btn-sm btn-danger mt-2 btn-trash"
                                                onclick="return confirm('Apa kamu yakin akan menghapus?');">
                                                <img src="img2/sampah.png" class="w-75">
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
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

            // Tambahkan ini untuk menampilkan order_id
            document.getElementById("modalOrderId").textContent = booking.order_id;

            // Tampilkan modal
            var modal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
            modal.show();
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