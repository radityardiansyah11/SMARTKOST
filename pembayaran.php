<?php
// Mulai session dan pastikan user sudah login
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data dari POST
$nama_kost = $_POST['nama_kost'];
$alamat_kost = $_POST['alamat_kost'];
$harga_kost = $_POST['harga_kost'];
$diskon_kost = $_POST['diskon_kost'];
$total_harga = $_POST['total_harga'];
$waktu_kost = $_POST['waktu_kost'];
$mulai_sewa = $_POST['mulai_sewa'];
$selesai_sewa = $_POST['selesai_sewa'];

// Fungsi untuk mengubah format tanggal
function formatTanggal($tanggal)
{
    // Ubah format dari yyyy-mm-dd ke dd-mm-yyyy
    return date("d-m-Y", strtotime($tanggal));
}

// Mengubah format tanggal
$mulai_sewa = formatTanggal($mulai_sewa);
$selesai_sewa = formatTanggal($selesai_sewa);

// Ubah format waktu_kost menjadi format yang diinginkan
switch ($waktu_kost) {
    case 'month':
        $waktu_kost = '1 Bulan';
        break;
    case '3months':
        $waktu_kost = '3 Bulan';
        break;
    case '6months':
        $waktu_kost = '6 Bulan';
        break;
    case 'year':
        $waktu_kost = 'Per Tahun';
        break;
    default:
        $waktu_kost = 'Tidak Diketahui';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SMARTKOST - Pembayaran</title>
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
    <link href="css/pembayaran.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            border: none;
        }

        .form-control {
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            border: none;
        }

        .form-select {
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            border: none;
        }

        .date-box {
            background-color: #ffffff;
            border-radius: 0.375rem;
            padding: 0.75rem 1.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 49%;
        }

        .date-box strong {
            color: #656565;
        }

        .table-bordered {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .modal-lg {
            max-width: 70%;
            /* Custom width, you can set percentage or fixed value like 900px */
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

        <!-- Navbar Start 
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
                        <a href="pembayaran.html" class="nav-item nav-link active">Pembayaran</a>
                    </div>
                    <div class="d-flex">
                        <div class="me-3 text-end">
                            <h6 class="mt-2">Halo, <br> <?php echo htmlspecialchars($username); ?></h6>
                        </div>
                        <img src="img2/Bulat.png" alt="profile" style="width: 50px; height: 50px;">
                    </div>
                </div>
            </nav>
        </div>
        Navbar End -->

        <!-- Pembayaran Start -->
        <div class="container mt-5">
            <div class="row">
                <!-- Rincian Pembayaran di Kiri -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header text-white bg-primary">
                            <h4 class="mb-2 mt-2 text-light">Rincian Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <h5 class="mb-2">Pembayaran: <?php echo htmlspecialchars($nama_kost); ?></h5>
                            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($alamat_kost); ?></p>

                            <div class="d-flex justify-content-between mb-3">
                                <div class="date-box">
                                    <strong>Mulai:</strong> <?php echo htmlspecialchars($mulai_sewa); ?>
                                </div>
                                <div class="date-box">
                                    <strong>Selesai:</strong> <?php echo htmlspecialchars($selesai_sewa); ?>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td>Sewa Kost</td>
                                        <td>Rp <?php echo number_format($harga_kost, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Diskon</td>
                                        <td>Rp <?php echo number_format($diskon_kost, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Waktu Kost</td>
                                        <td><?php echo htmlspecialchars($waktu_kost); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detail Pembayaran di Kanan -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-white bg-primary">
                            <h4 class="mb-2 mt-2 text-light">Detail Pembayaran</h4>
                        </div>
                        <div class="card-body ">
                            <form>
                                <div class="mb-3">
                                    <label for="namaPenyewa" class="form-label">Nama Penyewa</label>
                                    <input type="text" class="form-control" id="nama" placeholder="Nama Lengkap">
                                </div>
                                <div class="mb-3">
                                    <label for="emailPenyewa" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailPenyewa" placeholder="email anda">
                                </div>

                                <label for="metodePembayaran" class="form-label">Metode Pembayaran</label>
                                <select class="form-select mb-3" id="metodePembayaran">
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="ewallet">E-Wallet (OVO, GoPay, DANA)</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>


                                <button type="submit" class="btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#paymentModal">Bayar Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Template -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg mt-5"> <!-- Added modal-lg -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Detail Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- Content will be injected here by JavaScript -->
                    </div>

                </div>
            </div>
        </div>
        <!-- Pembayaran End -->

        <!-- Footer Start  
        <footer class="mt-5">
            <div
                class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-dark">
                <div class="text-white mb-3 mb-md-0">
                    SMARTKOST © 2024. All rights reserved.
                </div>
            </div>
        </footer> -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentForm = document.querySelector('form');
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            const modalBody = document.getElementById('modalBody');

            paymentForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                // Ambil nilai dari input
                const namaPenyewa = document.getElementById('nama').value;
                const emailPenyewa = document.getElementById('emailPenyewa').value;
                const method = document.getElementById('metodePembayaran').value;


                // Validasi nama dan email
                if (namaPenyewa === '' || emailPenyewa === '') {
                    alert('Silakan isi nama dan email terlebih dahulu sebelum melanjutkan pembayaran.');
                    return; // Hentikan eksekusi jika validasi gagal
                }

                let modalContent = '';

                if (method === 'transfer') {
                    modalContent = `
    <h5>Detail Transfer Bank</h5>
    <form id="transferForm">
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="senderName" class="form-label">Nama Pengirim</label>
                    <input type="text" class="form-control" id="senderName" placeholder="${namaPenyewa}">
                </div>
                <div class="mb-3">
                    <label for="senderEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="senderName" placeholder="${emailPenyewa}">
                </div>
                <div class="mb-3">
                    <label for="transferDate" class="form-label">Lama Sewa</label>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="date-box">
                            <strong>Mulai:</strong> ${<?php echo json_encode($mulai_sewa); ?>}
                        </div>
                        <div class="date-box">
                            <strong>Selesai:</strong> ${<?php echo json_encode($selesai_sewa); ?>}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="bankName" class="form-label">Bank</label>
                    <select class="form-select" id="bankName">
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="accountNumber" class="form-label">Nomor Rekening</label>
                    <input type="text" class="form-control" id="accountNumber" placeholder="Masukkan Nomor Rekening">
                </div>
                <div class="mb-3">
                    <label for="transferAmount" class="form-label">Jumlah Transfer</label>
                    <input type="text" class="form-control" id="transferAmount" value="Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>"
                        style="background-color: white;">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary w-50">Konfirmasi Transfer</button>
            </div>

    </form>

    `;

                } else if (method === 'ewallet') {
                    modalContent = `
    <h5>Detail E-Wallet</h5>
    <form id="ewalletForm">
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ewalletSenderName" class="form-label">Nama Pengirim</label>
                    <input type="text" class="form-control" id="ewalletSenderName" placeholder="${namaPenyewa}">
                </div>
                <div class="mb-3">
                    <label for="senderEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="senderName" placeholder="${emailPenyewa}">
                </div>
                <div class="mb-3">
                    <label for="ewalletPaymentDate" class="form-label">Lama Sewa</label>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="date-box">
                            <strong>Mulai:</strong> ${<?php echo json_encode($mulai_sewa); ?>}
                        </div>
                        <div class="date-box">
                            <strong>Selesai:</strong> ${<?php echo json_encode($selesai_sewa); ?>}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="Platform" class="form-label">Platform</label>
                    <select class="form-select" id="Platform">
                        <option value="ovo" data-icon="fas fa-wallet">OVO</option>
                        <option value="gopay" data-icon="fab fa-google-wallet">GoPay</option>
                        <option value="dana" data-icon="fas fa-mobile-alt">Dana</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="accountNumber" class="form-label">Nomor</label>
                    <input type="text" class="form-control" id="accountNumber" placeholder="Masukkan Nomor Rekening">
                </div>
                <div class="mb-3">
                    <label for="transferAmount" class="form-label">Jumlah Transfer</label>
                    <input type="text" class="form-control" id="transferAmount" value="Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>"
                        style="background-color: white;">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary w-50">Konfirmasi Transfer</button>
            </div>
    </form>
    `;
                } else if (method === 'cod') {
                    modalContent = `
    <h5>Detail COD</h5>
    <form id="codForm">
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ewalletSenderName" class="form-label">Nama Pengirim</label>
                    <input type="text" class="form-control" id="ewalletSenderName" placeholder="${namaPenyewa}">
                </div>
                <div class="mb-3">
                    <label for="senderEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="senderName" placeholder="${emailPenyewa}">
                </div>
                <div class="mb-3">
                    <label for="ewalletPaymentDate" class="form-label">Lama Sewa</label>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="date-box">
                            <strong>Mulai:</strong> ${<?php echo json_encode($mulai_sewa); ?>}
                        </div>
                        <div class="date-box">
                            <strong>Selesai:</strong> ${<?php echo json_encode($selesai_sewa); ?>}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="Platform" class="form-label">Platform</label>
                    <select class="form-select" id="Platform">
                        <option value="ovo" data-icon="fas fa-wallet">COD</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="transferAmount" class="form-label">Jumlah yang harus dibayarkan</label>
                    <input type="text" class="form-control" id="transferAmount" value="Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>"
                        style="background-color: white;">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary w-50">Konfirmasi Transfer</button>
            </div>
    </form>
    `;
                }

                modalBody.innerHTML = modalContent;
                paymentModal.show();
            });
        });

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