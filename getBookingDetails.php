<?php
include 'config.php';  // Pastikan file konfigurasi database sudah benar

// Cek apakah parameter ID ada
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Query untuk mengambil data booking berdasarkan ID
    $sql = "SELECT id, pemilik_kost, nama_kost, alamat_kost, total_harga, mulai_sewa, selesai_sewa, nama_penyewa, email_penyewa, telp_penyewa, metode_pembayaran, status_pembayaran, tanggal_booking, order_id 
            FROM bookings 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch data as an associative array
        $data = $result->fetch_assoc();
        
        // Return data in JSON format
        echo json_encode($data);
    } else {
        // If no record found, return an error message
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["error" => "ID tidak disediakan"]);
}

$conn->close();
?>
