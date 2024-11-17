<?php
session_start();
require 'vendor/autoload.php'; // Load SDK Midtrans

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-yoGklR-b6tK7fHjvGtqS0MYx';
\Midtrans\Config::$isProduction = false; // set ke true untuk produksi
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$pemilik_kost = $_POST['pemilik_kost'];
$nama_kost = $_POST['nama_kost'];
$alamat_kost = $_POST['alamat_kost'];
$harga_kost = $_POST['harga_kost'];
$diskon_kost = $_POST['diskon_kost'];
$total_harga = $_POST['total_harga'];
$waktu_kost = $_POST['waktu_kost'];
$mulai_sewa = $_POST['mulai_sewa'];
$selesai_sewa = $_POST['selesai_sewa'];

// Fungsi format tanggal
function formatTanggal($tanggal)
{
    return date("d-m-Y", strtotime($tanggal));
}

$mulai_sewa = formatTanggal($mulai_sewa);
$selesai_sewa = formatTanggal($selesai_sewa);

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
    <link href="img2/mini logo smartkost.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
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
        }

        .no-margin {
            margin-bottom: 5px;
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
                            <h5 class="mb-2" for="namaKost">Pembayaran: <?php echo htmlspecialchars($nama_kost); ?></h5>
                            <p class="no-margin"><strong>Pemilik Kost:</strong>
                                <?php echo htmlspecialchars($pemilik_kost); ?></p>
                            <p class="no-margin"><strong>Alamat:</strong> <?php echo htmlspecialchars($alamat_kost); ?>
                            </p>

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
                <!-- Form Pembayaran -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-white bg-primary">
                            <h4 class="mb-2 mt-2 text-light">Detail Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="paymentForm">
                                <!-- Input Fields -->
                                <div class="mb-3">
                                    <label for="namaPenyewa" class="form-label">Nama Penyewa</label>
                                    <input type="text" name="nama_penyewa" class="form-control" id="namaPenyewa"
                                        value="<?php echo $_SESSION['username']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="emailPenyewa" class="form-label">Email</label>
                                    <input type="email" name="email_penyewa" class="form-control" id="emailPenyewa"
                                        value="<?php echo $_SESSION['email']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telpPenyewa" class="form-label">No. Telepon</label>
                                    <input type="text" name="telp_penyewa" class="form-control" id="telpPenyewa"
                                        required>
                                </div>

                                <!-- Metode Pembayaran -->
                                <label for="metodePembayaran" class="form-label">Metode Pembayaran</label>
                                <select class="form-select mb-3" name="metode_pembayaran" id="metodePembayaran"
                                    required>
                                    <option value="ewallet">E-Wallet</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>

                                <!-- Tombol Pembayaran -->
                                <button type="submit" id="payButton" class="btn btn-primary w-100">Bayar
                                    Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

    <!-- JS Midtrans & JavaScript -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-X0lPbsGJWAgG3bw3"></script>
    <script type="text/javascript">
        document.getElementById('paymentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let paymentMethod = document.getElementById('metodePembayaran').value;

            if (paymentMethod === 'ewallet') {
                // Proses pembayaran E-Wallet
                fetch('process-payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pemilik_kost: "<?php echo $pemilik_kost; ?>",
                        nama_kost: "<?php echo $nama_kost; ?>",
                        alamat_kost: "<?php echo $alamat_kost; ?>",
                        total_harga: "<?php echo $total_harga; ?>",
                        mulai_sewa: "<?php echo $mulai_sewa; ?>",
                        selesai_sewa: "<?php echo $selesai_sewa; ?>",
                        nama_penyewa: document.getElementById('namaPenyewa').value,
                        email_penyewa: document.getElementById('emailPenyewa').value,
                        telp_penyewa: document.getElementById('telpPenyewa').value,
                        metode_pembayaran: paymentMethod,
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.token) {
                            snap.pay(data.token, {
                                onSuccess: function (result) {
                                    window.location.href = 'pembayaran-hasil.php?status=success&order_id=' + result.order_id;
                                },
                                onPending: function (result) {
                                    window.location.href = 'pembayaran-hasil.php?status=pending&order_id=' + result.order_id;
                                },
                                onError: function (result) {
                                    window.location.href = 'pembayaran-hasil.php?status=failed&order_id=' + result.order_id;
                                },
                                onClose: function () {
                                    alert('Pembayaran dibatalkan.');
                                }
                            });
                        }
                    });
            } else if (paymentMethod === 'cod') {
                // Proses pembayaran COD
                fetch('process-payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        pemilik_kost: "<?php echo $pemilik_kost; ?>",
                        nama_kost: "<?php echo $nama_kost; ?>",
                        alamat_kost: "<?php echo $alamat_kost; ?>",
                        total_harga: "<?php echo $total_harga; ?>",
                        mulai_sewa: "<?php echo $mulai_sewa; ?>",
                        selesai_sewa: "<?php echo $selesai_sewa; ?>",
                        nama_penyewa: document.getElementById('namaPenyewa').value,
                        email_penyewa: document.getElementById('emailPenyewa').value,
                        telp_penyewa: document.getElementById('telpPenyewa').value,
                        metode_pembayaran: paymentMethod,
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            window.location.href = 'pembayaran-hasil.php?status=success';
                        } else {
                            alert(data.message || 'Gagal memproses COD');
                            window.location.href = 'pembayaran-hasil.php?status=failed';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
            }
        });

    </script>
</body>
</html>