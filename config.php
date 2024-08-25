<?php
$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika Anda tidak menggunakan password
$db   = "db_smartkost";

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (mysqli_connect_errno()){
    echo "Koneksi database gagal : " . mysqli_connect_error();
   }
?>
