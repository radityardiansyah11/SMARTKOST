<?php
include 'config.php'; // koneksi ke database

if (isset($_POST['booking_id']) && isset($_POST['status_pembayaran'])) {
    $booking_id = $_POST['booking_id'];
    $status_pembayaran = $_POST['status_pembayaran'];

    // Query untuk update status_pembayaran berdasarkan id booking
    $query = "UPDATE bookings SET status_pembayaran = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status_pembayaran, $booking_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }
}
?>
