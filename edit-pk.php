<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM logsys_pk WHERE id = $id");
    $pemilik = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $pkname = mysqli_real_escape_string($conn, $_POST['pkname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nomor_hp = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Perbaiki sintaks SQL
    $sql = "UPDATE logsys_pk SET pkname='$pkname', email='$email', nomor_hp='$nomor_hp', password='$password' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header('Location: admin-dahsboard-pk.php');  // Periksa nama file
        exit();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error updating record: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Pemilik Kost - SMARTKOST</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img2/mini logo smartkost.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        .form {
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            background-color: #fff;
            border-radius: 30px;
        }

        .form-control {
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            border: none;
            border-radius: 20px;
        }

        .btn {
            border-radius: 10px;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Edit Pemilik Kost</h2>
        <form method="post" action="" class="form p-4 rounded">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($pemilik['id']); ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" id="name" name="pkname" class="form-control"
                    value="<?php echo htmlspecialchars($pemilik['pkname']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                    value="<?php echo htmlspecialchars($pemilik['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Telepon</label>
                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control"
                    value="<?php echo htmlspecialchars($pemilik['nomor_hp']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                    value="<?php echo htmlspecialchars($pemilik['password']); ?>" required>
            </div>


            <button type="submit" class="btn btn-primary">Update</button>
            <a href="admin-dahsboard-pk.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>