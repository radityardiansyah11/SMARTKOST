<?php
include 'config.php';
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_username'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari database
$username_session = $_SESSION['user_username'];
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
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            // Pastikan folder uploads ada
            $uploadDir = 'uploads/';
            $userFolder = $uploadDir . $username_session . '/';

            if (!is_dir($userFolder)) {
                mkdir($userFolder, 0777, true); // Buat folder jika belum ada
            }

            // Buat nama file unik
            $uploadFile = $userFolder . time() . '_' . basename($_FILES['profile_image']['name']);

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                // Simpan path file ke dalam database
                $sql = "UPDATE login_system SET profile_image = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $uploadFile, $username_session);

                if ($stmt->execute()) {
                    $_SESSION['profile_image'] = $uploadFile;
                    echo "Profile image updated successfully.";
                    header("Location: user-profile.php");
                    exit();
                } else {
                    echo "Terjadi kesalahan saat menyimpan gambar ke database.";
                }
            } else {
                echo "Terjadi kesalahan saat mengupload file.";
            }
        } else {
            echo "Hanya file gambar (JPEG, PNG, GIF) yang diperbolehkan.";
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .profile-container {
            max-width: 60%;
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

        .profile-wrapper {
            display: flex;
            align-items: center;
            gap: 50px;
        }

        .profile-image-container {
            text-align: center;
            cursor: pointer;
            margin-top: -50px;
        }

        .profile-image {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
        }


        .form-control {
            border-radius: 30px;
        }

        .form-section {
            flex: 1;
        }

        .button-group {
            display: flex;
            margin-top: 40px;
        }

        .btn-primary {
            border-radius: 30px;
            padding: 10px 20px;
        }

        .btn-secondary {
            margin-left: 10px;
            border-radius: 30px;
            padding: 10px 20px;
            text-decoration: none;
            text-align: center;
        }

        /* Sembunyikan input file */
        #profile_image_input {
            display: none;
        }
    </style>
</head>

<body>

    <div class="container profile-container">
        <h2>Profil Anda</h2>
        <div class="profile-wrapper">
            <div class="profile-image-container">
                <!-- Gambar profil yang dapat diklik untuk mengganti gambar -->
                <?php if (isset($profile_image) && file_exists($profile_image)): ?>
                    <img id="profile_image_preview" src="<?php echo $profile_image; ?>" alt="Profile Image"
                        class="profile-image" onclick="document.getElementById('profile_image_input').click();">
                <?php else: ?>
                    <img src="img2/Bulat.png" alt="Default Profile Image" class="profile-image"
                        onclick="document.getElementById('profile_image_input').click();">
                <?php endif; ?>
            </div>

            <!-- Form input di sebelah kanan gambar -->
            <div class="form-section">
                <form action="user-profile.php" method="POST" enctype="multipart/form-data">

                    <!-- Input File gambar yang disembunyikan -->
                    <input type="file" name="profile_image" id="profile_image_input" class="form-control"
                        accept="image/*">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username"
                            value="<?php echo htmlspecialchars($username); ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>"
                            class="form-control" required>
                    </div>

                    <!-- Tombol Save Changes dan Back to Home -->
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary text-white">Save Changes</button>
                        <a href="user-home.php" class="btn btn-secondary text-white">Back to Home</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Scripts -->
    <script>
        // JavaScript to preview the image immediately after choosing a file
        document.getElementById('profile_image_input').addEventListener('change', function (event) {
            const file = event.target.files[0]; // Get the selected file
            if (file) {
                const reader = new FileReader(); // Create a new FileReader to read the file
                reader.onload = function (e) {
                    // Set the src of the preview image to the loaded file data
                    document.getElementById('profile_image_preview').src = e.target.result;
                }
                reader.readAsDataURL(file); // Read the file as a data URL
            }
        });
    </script>

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