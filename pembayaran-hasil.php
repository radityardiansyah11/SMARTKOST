<?php
// Mulai session
session_start();

// Ambil status dari URL
$status = isset($_GET['status']) ? $_GET['status'] : 'failed'; // Default status adalah "failed"

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link href="img2/mini logo smartkost.png" rel="icon">

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .container {
            text-align: center;
            background-color: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            transform: scale(0);
            opacity: 0;
            transition: transform 0.5s ease, opacity 0.5s ease;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 32%;
        }

        .container.active {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .icon {
            margin-bottom: 20px;
            display: inline-block;
            position: relative;
            animation: bounce 0.5s ease forwards;
        }

        .success .icon svg {
            fill: #00B98E;
        }

        .failed .icon svg {
            fill: #f44336;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            color: #666;
        }

        @keyframes bounce {
            0% {
                transform: scale(0);
            }

            70% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse-background {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120px;
            height: 120px;
            background-color: rgba(76, 175, 80, 0.1);
            border-radius: 50%;
            animation: pulse 2s infinite;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        .failed .pulse-background {
            background-color: rgba(244, 67, 54, 0.1);
        }

        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.4);
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .btn {
            transition: .5s;
        }

        .btn.btn-primary,
        .btn.btn-secondary {
            color: #FFFFFF;
        }
    </style>
</head>

<body>
    <?php if ($status == 'success') : ?>
        <!-- Container Pembayaran Berhasil -->
        <div id="success" class="container success active">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
                    <path d="M12 0c6.627 0 12 5.373 12 12s-5.373 12-12 12-12-5.373-12-12 5.373-12 12-12zm-1.25 17l8.25-8.25-1.75-1.75-6.5 6.5-3-3-1.75 1.75 4.75 4.75z"/>
                </svg>
            </div>
            <h1 class="text-primary">Pembayaran Berhasil!</h1>
            <p>Terima kasih telah melakukan pembayaran. <br> Transaksi Anda telah berhasil.</p>
            <a href="user-kost.php" class="btn btn-primary">Kembali</a>
        </div>
    <?php else : ?>
        <!-- Container Pembayaran Gagal -->
        <div id="failed" class="container failed active">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24">
                    <path d="M12 0c6.627 0 12 5.373 12 12s-5.373 12-12 12-12-5.373-12-12 5.373-12 12-12zm-2.121 12l-4.95 4.95 1.415 1.414 4.95-4.95 4.95 4.95 1.415-1.414-4.95-4.95 4.95-4.95-1.415-1.414-4.95 4.95-4.95-4.95-1.415 1.414 4.95 4.95z"/>
                </svg>
            </div>
            <h1>Pembayaran Gagal!</h1>
            <p>Maaf, terjadi masalah dalam proses pembayaran. Silakan coba lagi nanti.</p>
            <a href="user-kost.php" class="btn btn-primary">Kembali</a>
        </div>
    <?php endif; ?>
</body>

</html>
