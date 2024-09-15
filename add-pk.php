<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nomor_hp = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO logsys_pk (username, email, nomor_hp, password) VALUES ('$username', '$email', '$nomor_hp', '$password')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['status'] = "added";
    } else {
        $_SESSION['status'] = "error";
    }

    header('Location: admin-dahsboard-pk.php');
    exit();
}
?>
