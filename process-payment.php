<?php
include 'config.php';
require 'vendor/autoload.php';

use Midtrans\Config;
use Midtrans\Snap;

session_start();

\Midtrans\Config::$serverKey = 'SB-Mid-server-yoGklR-b6tK7fHjvGtqS0MYx';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$data = json_decode(file_get_contents("php://input"), true);

// Cek metode pembayaran
if ($data['metode_pembayaran'] === 'cod') {
    $stmt = $conn->prepare("INSERT INTO bookings (
        pemilik_kost, nama_kost, alamat_kost, total_harga,
        mulai_sewa, selesai_sewa, nama_penyewa, email_penyewa, telp_penyewa,
        metode_pembayaran, status_pembayaran
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Cek apakah query berhasil disiapkan
    if (!$stmt) {
        die(json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]));
    }

    // Bind parameter untuk menghindari SQL injection
    $metode_pembayaran = 'COD';
    $status_pembayaran = 'Pending';
    $stmt->bind_param(
        "sssssssssss",
        $data['pemilik_kost'],
        $data['nama_kost'],
        $data['alamat_kost'],
        $data['total_harga'],
        $data['mulai_sewa'],
        $data['selesai_sewa'],
        $data['nama_penyewa'],
        $data['email_penyewa'],
        $data['telp_penyewa'],
        $metode_pembayaran,
        $status_pembayaran
    );

    // Eksekusi query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Pesanan COD berhasil diproses']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan COD: ' . $stmt->error]);
    }

    // Tutup statement
    $stmt->close();
} else {
    $order_id = uniqid();  // Order ID unik
    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => (int) $data['total_harga'],
        ],
        'customer_details' => [
            'first_name' => $data['nama_penyewa'],
            'email' => $data['email_penyewa'],
            'phone' => $data['telp_penyewa'],
        ],
        'enabled_payments' => [
            'gopay',
            'shopeepay',
            'bca_va',
            'bni_va',        
            'bri_va',     
            'echannel',    
            'permata_va',  
            'credit_card', 
            'alfamart',     
            'indomaret',  
            'qris',       
            'akulaku',      
            'kredivo'  
        ],
    ];

    try {
        // Simpan data sementara ke dalam tabel bookings_temp
        $stmt = $conn->prepare("INSERT INTO bookings (
            pemilik_kost, nama_kost, alamat_kost, total_harga,
            mulai_sewa, selesai_sewa, nama_penyewa, email_penyewa, telp_penyewa,
            metode_pembayaran, status_pembayaran, order_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameter
        $metode_pembayaran = 'E-wallet';
        $status_pembayaran = 'Pending';
        $stmt->bind_param(
            "ssssssssssss",
            $data['pemilik_kost'],
            $data['nama_kost'],
            $data['alamat_kost'],
            $data['total_harga'],
            $data['mulai_sewa'],
            $data['selesai_sewa'],
            $data['nama_penyewa'],
            $data['email_penyewa'],
            $data['telp_penyewa'],
            $metode_pembayaran,
            $status_pembayaran,
            $order_id
        );

        // Eksekusi query untuk menyimpan data sementara
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan sementara: ' . $stmt->error]);
            exit;
        }

        // Dapatkan Snap Token Midtrans
        $snapToken = Snap::getSnapToken($params);
        echo json_encode(['status' => 'success', 'token' => $snapToken, 'order_id' => $order_id]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()]);
    }
}
?>