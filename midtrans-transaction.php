<?php
require_once 'vendor/autoload.php'; // Pastikan ini diubah sesuai dengan struktur direktori Anda

use Midtrans\Config;
use Midtrans\Transaction;

Config::$serverKey = 'SB-Mid-server-yoGklR-b6tK7fHjvGtqS0MYx'; // Ganti dengan Server Key Anda
Config::$isProduction = false; // Set true jika di production
Config::$isSanitized = true;
Config::$is3ds = true;

function getMidtransTransactions($order_id) {
    try {
        $transaction = Transaction::status($order_id); // Dapatkan status transaksi berdasarkan order ID
        return $transaction;
    } catch (Exception $e) {
        return null;
    }
}
