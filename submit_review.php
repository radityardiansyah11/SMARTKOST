<?php
include 'config.php';
session_start();

if (isset($_POST['review']) && isset($_POST['kost_id'])) {
    $username = $_SESSION['username']; // Username dari session
    $review = $conn->real_escape_string($_POST['review']); // Mencegah SQL Injection
    $kost_id = $_POST['kost_id']; // Mendapatkan kost_id dari form

    // Query untuk memasukkan ulasan ke database
    $sql = "INSERT INTO ulasan (kost_id, username, review) VALUES ('$kost_id', '$username', '$review')";

    if ($conn->query($sql) === TRUE) {
        // Redirect kembali ke halaman detail kost
        header("Location: user-detail.php?id=" . $kost_id);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Review or Kost ID not set.";
}
?>
