<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO login_system (username, email, password, created_at) VALUES ('$username', '$email', '$password', NOW())";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['status'] = "added";
    } else {
        $_SESSION['status'] = "error";
    }

    header('Location: admin-dashboard-user.php');
    exit();
}
