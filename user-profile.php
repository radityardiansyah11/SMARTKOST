<?php
include 'config.php';
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari database
$username_session = $_SESSION['username'];
$sql = "SELECT username, email, profile_image FROM login_system WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username_session);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];
    $profile_image = $row['profile_image'];
} else {
    echo "Pengguna tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses upload file
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Pastikan folder uploads ada
        $uploadDir = 'uploads/';
        $userFolder = $uploadDir . $username_session . '/'; // Inisialisasi variabel $userFolder
        
        // Pastikan folder untuk pengguna ada sebelum mencoba upload file
        if (!is_dir($userFolder)) {
            mkdir($userFolder, 0777, true);  // Buat folder jika belum ada
        }

        // Buat nama file unik untuk mencegah konflik
        $uploadFile = $userFolder . time() . '_' . basename($_FILES['profile_image']['name']);

        // Memindahkan file yang diupload ke folder pengguna
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            // Simpan path file ke dalam database
            $sql = "UPDATE login_system SET profile_image = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $uploadFile, $username_session);

            // Cek apakah eksekusi berhasil
            if ($stmt->execute()) {
                $_SESSION['profile_image'] = $uploadFile; // Simpan ke sesi untuk langsung memperbarui tampilan
                echo "Profile image updated successfully.";
            } else {
                echo "Terjadi kesalahan saat menyimpan gambar ke database.";
            }

            // Update variabel profil
            $profile_image = $uploadFile;
        } else {
            echo "Terjadi kesalahan saat mengupload file.";
        }
    }

    // Update username dan email
    if (!empty($_POST['username']) && !empty($_POST['email'])) {
        $new_username = htmlspecialchars($_POST['username']);
        $new_email = htmlspecialchars($_POST['email']);

        // Simpan perubahan ke dalam database
        $sql = "UPDATE login_system SET username = ?, email = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $new_username, $new_email, $username_session);
        if ($stmt->execute()) {
            // Update variabel lokal dan session
            $_SESSION['username'] = $new_username;
            $username = $new_username;
            $email = $new_email;
        } else {
            echo "Terjadi kesalahan saat memperbarui data.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Ganti Profil - SMARTKOST</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

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

    <!-- Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Heebo', sans-serif;
        }

        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #343a40;
        }

        .form-control {
            border-radius: 30px;
        }

        .btn-primary {
            background-color: #0D6EFD;
            border-radius: 30px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .profile-container img {
            display: block;
            margin: 0 auto 20px;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #0D6EFD;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container profile-container">
        <h2>Profil Anda</h2>

        <?php if (isset($profile_image) && file_exists($profile_image)): ?>
            <img src="<?php echo $profile_image; ?>" alt="Profile Image">
        <?php else: ?>
            <img src="img2/Bulat.png" alt="Default Profile Image">
        <?php endif; ?>

        <form action="user-profile.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="profile_image" class="form-label">Change Profile Image</label>
                <input type="file" name="profile_image" id="profile_image" class="form-control">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>"
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>"
                    class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
        </form>

        <a href="user-home.php" class="back-link">Back to Home</a>
    </div>

    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    

</body>

</html>