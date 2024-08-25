<?php
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM login_system WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header('Location: admin-dashboard-user.php');
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>