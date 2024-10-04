<?php
include 'config.php';
session_start();

$successMessage = '';
$errorMessage = '';

// Fetch kost details from the database based on a specific ID or parameter
$kost_id = $_GET['id']; // Assuming you pass the ID in the URL as a query string

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $id = $_POST['id'];
    $kostName = $_POST['kostName'];
    $address = $_POST['address'];
    $roomSize = $_POST['roomSize'];
    $jenisKost = $_POST['jenisKost'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $banyakKasur = $_POST['banyakKasur'];
    $banyakKamarMandi = $_POST['banyakKamarMandi'];
    $kategori = $_POST['kategori'];
    $description = $_POST['description'];
    $spesifikasiKamar = $_POST['spesifikasiKamar'];
    $fasilitasKamar = $_POST['fasilitasKamar'];
    $fasilitasKamarMandi = $_POST['fasilitasKamarMandi'];
    $fasilitasUmum = $_POST['fasilitasUmum'];
    $peraturanKost = $_POST['peraturanKost'];

    // Handle file uploads
    $uploadDir = 'uploads/';
    $gambar = [];
    for ($i = 1; $i <= 4; $i++) {
        $fileKey = "kostImage$i";
        $currentFile = $_POST["currentImage$i"] ?? '';

        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $fileName = basename($_FILES[$fileKey]['name']);
            $targetFile = $uploadDir . $fileName;

            // Debugging information
            echo "Uploading file: " . $fileName . "<br>";
            echo "Target path: " . $targetFile . "<br>";

            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
                echo "File successfully uploaded: " . $fileName . "<br>";
                $gambar[$i] = $fileName;
            } else {
                echo "Failed to move uploaded file: " . $fileName . "<br>";
                $gambar[$i] = $currentFile;
            }
        } else {
            $gambar[$i] = $currentFile;
        }
    }



    // Update kost details
    $updateKostSql = "UPDATE kost SET
        nama_kost = '$kostName',
        alamat = '$address',
        ukuran_kamar = '$roomSize',
        jenis_kost = '$jenisKost',
        harga = '$price',
        diskon = '$discount',
        banyak_kasur = '$banyakKasur',
        banyak_kamar_mandi = '$banyakKamarMandi',
        kategori = '$kategori',
        deskripsi = '$description',
        gambar_1 = '{$gambar[1]}',
        gambar_2 = '{$gambar[2]}',
        gambar_3 = '{$gambar[3]}',
        gambar_4 = '{$gambar[4]}'
    WHERE id = '$id'";

    if ($conn->query($updateKostSql) === TRUE) {
        // Update spesifikasi_kamar
        $conn->query("DELETE FROM spesifikasi_kamar WHERE kost_id = '$id'");
        foreach ($spesifikasiKamar as $spesifikasi) {
            if (!empty(trim($spesifikasi))) {
                $conn->query("INSERT INTO spesifikasi_kamar (kost_id, spesifikasi) VALUES ('$id', '$spesifikasi')");
            }
        }

        // Update fasilitas_kamar
        $conn->query("DELETE FROM fasilitas_kamar WHERE kost_id = '$id'");
        foreach ($fasilitasKamar as $fasilitas) {
            if (!empty(trim($fasilitas))) {
                $conn->query("INSERT INTO fasilitas_kamar (kost_id, fasilitas) VALUES ('$id', '$fasilitas')");
            }
        }

        // Update fasilitas_kamar_mandi
        $conn->query("DELETE FROM fasilitas_kamar_mandi WHERE kost_id = '$id'");
        foreach ($fasilitasKamarMandi as $fasilitas_mandi) {
            if (!empty(trim($fasilitas_mandi))) {
                $conn->query("INSERT INTO fasilitas_kamar_mandi (kost_id, fasilitas) VALUES ('$id', '$fasilitas_mandi')");
            }
        }

        // Update fasilitas_umum
        $conn->query("DELETE FROM fasilitas_umum WHERE kost_id = '$id'");
        foreach ($fasilitasUmum as $fasilitas_umum) {
            if (!empty(trim($fasilitas_umum))) {
                $conn->query("INSERT INTO fasilitas_umum (kost_id, fasilitas) VALUES ('$id', '$fasilitas_umum')");
            }
        }

        // Update peraturan_kost
        $conn->query("DELETE FROM peraturan_kost WHERE kost_id = '$id'");
        foreach ($peraturanKost as $peraturan) {
            if (!empty(trim($peraturan))) {
                $conn->query("INSERT INTO peraturan_kost (kost_id, peraturan) VALUES ('$id', '$peraturan')");
            }
        }


        // Redirect to the dashboard page
        header("Location: admin-dashboard-kost.php"); // Update this URL to match your dashboard URL
        exit(); // Make sure to exit to stop further script execution
    } else {
        $errorMessage = "Error updating kost: " . $conn->error;
    }
} else {
    // Fetch kost details for display
    $sql = "SELECT * FROM kost WHERE id = $kost_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("No kost found.");
    }

    // Ambil data spesifikasi
    $sqlSpesifikasi = "SELECT spesifikasi FROM spesifikasi_kamar WHERE kost_id = $kost_id";
    $spesifikasiResult = $conn->query($sqlSpesifikasi);

    // Ambil data fasilitas kamar
    $sqlFasilitas = "SELECT fasilitas FROM fasilitas_kamar WHERE kost_id = $kost_id";
    $fasilitasResult = $conn->query($sqlFasilitas);

    // Ambil data fasilitas kamar mandi
    $sqlFasilitasMandi = "SELECT fasilitas FROM fasilitas_kamar_mandi WHERE kost_id = $kost_id";
    $fasilitasMandiResult = $conn->query($sqlFasilitasMandi);

    // Ambil data fasilitas umum
    $sqlFasilitasUmum = "SELECT fasilitas FROM fasilitas_umum WHERE kost_id = $kost_id";
    $fasilitasUmumResult = $conn->query($sqlFasilitasUmum);

    // Ambil data peraturan kost
    $sqlPeraturan = "SELECT peraturan FROM peraturan_kost WHERE kost_id = $kost_id";
    $peraturanResult = $conn->query($sqlPeraturan);

}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Kost - SMARTKOST</title>
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

    <!-- css -->
    <link href="css/tambahkost.css" rel="stylesheet">

    <style>
        .form-select {
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            border: none;
        }
    </style>
</head>

<body class="bg-white">
    <div class="container-xxl bg-white p-0">
        <div class="container">
            <div class="form-section">
                <h2 class="text-center">Edit Kost</h2>
                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
                <?php endif; ?>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

                    <div class="row">
                        <!-- Image Uploads -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <h5>Gambar Kost</h5>
                            </label>
                            <div class="upload-container">

                                <!-- Upload Area 1 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage1" id="kostImage1" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview1')">
                                    <img id="imagePreview1" src="<?php echo htmlspecialchars($row['gambar_1']); ?>"
                                        alt="Preview Image"
                                        style="display: <?php echo !empty($row['gambar_1']) ? 'block' : 'none'; ?>">
                                    <input type="hidden" name="currentImage1"
                                        value="<?php echo htmlspecialchars($row['gambar_1']); ?>">
                                </div>

                                <!-- Upload Area 2 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage2" id="kostImage2" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview2')">
                                    <img id="imagePreview2" src="<?php echo htmlspecialchars($row['gambar_2']); ?>"
                                        alt="Preview Image"
                                        style="display: <?php echo !empty($row['gambar_2']) ? 'block' : 'none'; ?>">
                                    <input type="hidden" name="currentImage2"
                                        value="<?php echo htmlspecialchars($row['gambar_2']); ?>">
                                </div>

                                <!-- Upload Area 3 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage3" id="kostImage3" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview3')">
                                    <img id="imagePreview3" src="<?php echo htmlspecialchars($row['gambar_3']); ?>"
                                        alt="Preview Image"
                                        style="display: <?php echo !empty($row['gambar_3']) ? 'block' : 'none'; ?>">
                                    <input type="hidden" name="currentImage3"
                                        value="<?php echo htmlspecialchars($row['gambar_3']); ?>">
                                </div>

                                <!-- Upload Area 4 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage4" id="kostImage4" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview4')">
                                    <img id="imagePreview4" src="<?php echo htmlspecialchars($row['gambar_4']); ?>"
                                        alt="Preview Image"
                                        style="display: <?php echo !empty($row['gambar_4']) ? 'block' : 'none'; ?>">
                                    <input type="hidden" name="currentImage4"
                                        value="<?php echo htmlspecialchars($row['gambar_4']); ?>">
                                </div>
                            </div>
                        </div>

                        <label class="form-label">
                            <h5 class="mt-2">Deskripsi Kost</h5>
                        </label>
                        <!-- Kost Name -->
                        <div class="col-md-6 mb-3">
                            <label for="kostName" class="form-label">Nama Kost</label>
                            <input type="text" class="form-control" name="kostName" id="kostName"
                                placeholder="Masukkan Nama Kost"
                                value="<?php echo htmlspecialchars($row['nama_kost']); ?>">
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <input type="text" class="form-control" name="address" id="address"
                                placeholder="Masukkan Alamat" value="<?php echo htmlspecialchars($row['alamat']); ?>">
                        </div>

                        <!-- Room Size -->
                        <div class="col-md-3 mb-3">
                            <label for="roomSize" class="form-label">Ukuran Kamar (m<sup>2</sup>)</label>
                            <input type="text" class="form-control" name="roomSize" id="roomSize"
                                placeholder="Masukkan Ukuran Kamar"
                                value="<?php echo htmlspecialchars($row['ukuran_kamar']); ?>">
                        </div>

                        <!-- jenis kost -->
                        <div class="col-md-3 mb-3">
                            <label for="jenisKost" class="form-label">Jenis Kost</label>
                            <select class="form-select" name="jenisKost" id="jenisKost">
                                <option value="laki-laki" <?php echo $row['jenis_kost'] === 'laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="perempuan" <?php echo $row['jenis_kost'] === 'perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                                <option value="campur" <?php echo $row['jenis_kost'] === 'campur' ? 'selected' : ''; ?>>
                                    Campur</option>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-3 mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="text" class="form-control" name="price" id="price" placeholder="Masukkan Harga"
                                value="<?php echo htmlspecialchars($row['harga']); ?>">
                        </div>

                        <!-- Discount -->
                        <div class="col-md-3 mb-3">
                            <label for="discount" class="form-label">Diskon (opsional)</label>
                            <input type="text" class="form-control" name="discount" id="discount"
                                placeholder="Masukkan Diskon" value="<?php echo htmlspecialchars($row['diskon']); ?>">
                        </div>

                        <!-- Banyak Kasur -->
                        <div class="col-md-3 mb-3">
                            <label for="banyakKasur" class="form-label">Banyak Kasur</label>
                            <input type="text" class="form-control" name="banyakKasur" id="banyakKasur"
                                placeholder="Masukkan Banyak Kasur"
                                value="<?php echo htmlspecialchars($row['banyak_kasur']); ?>">
                        </div>

                        <!-- Banyak Kamar Mandi -->
                        <div class="col-md-3 mb-3">
                            <label for="banyakKamarMandi" class="form-label">Banyak Kamar Mandi</label>
                            <input type="text" class="form-control" name="banyakKamarMandi" id="banyakKamarMandi"
                                placeholder="Masukkan Banyak Kamar Mandi"
                                value="<?php echo htmlspecialchars($row['banyak_kamar_mandi']); ?>">
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-3 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" name="kategori" id="kategori">
                                <option value="Standart" <?php echo $row['kategori'] === 'Standart' ? 'selected' : ''; ?>>
                                    Standart</option>
                                <option value="Premium" <?php echo $row['kategori'] === 'Premium' ? 'selected' : ''; ?>>
                                    Premium</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="4"
                                placeholder="Masukkan Deskripsi"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
                        </div>

                        <!-- Facilities -->
                        <h5>Fasilitas</h5>
                        <div id="specificationContainer">
                            <?php if ($spesifikasiResult && $spesifikasiResult->num_rows > 0): ?>
                                <?php while ($spesifikasi = $spesifikasiResult->fetch_assoc()): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="spesifikasiKamar[]"
                                                placeholder="spesifikasi"
                                                value="<?php echo htmlspecialchars($spesifikasi['spesifikasi']); ?>">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger removeSpecBtn">Hapus</button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="spesifikasiKamar[]"
                                        placeholder="spesifikasi">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#specificationContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div id="roomFacilitiesContainer">
                            <?php if ($fasilitasResult && $fasilitasResult->num_rows > 0): ?>
                                <?php while ($fasilitas = $fasilitasResult->fetch_assoc()): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="fasilitasKamar[]"
                                                placeholder="fasilitas kamar"
                                                value="<?php echo htmlspecialchars($fasilitas['fasilitas']); ?>">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger removeSpecBtn">Hapus</button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="fasilitasKamar[]"
                                        placeholder="fasilitas kamar">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#roomFacilitiesContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <div id="bathroomFacilitiesContainer">
                            <?php if ($fasilitasMandiResult && $fasilitasMandiResult->num_rows > 0): ?>
                                <?php while ($fasilitasMandi = $fasilitasMandiResult->fetch_assoc()): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="fasilitasKamarMandi[]"
                                                placeholder="fasilitas kamar mandi"
                                                value="<?php echo htmlspecialchars($fasilitasMandi['fasilitas']); ?>">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger removeSpecBtn">Hapus</button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="fasilitasKamarMandi[]"
                                        placeholder="fasilitas kamar mandi">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#bathroomFacilitiesContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas Umum -->
                        <div id="generalFacilitiesContainer">
                            <h5>Fasilitas Umum</h5>
                            <?php if ($fasilitasUmumResult && $fasilitasUmumResult->num_rows > 0): ?>
                                <?php while ($fasilitasUmum = $fasilitasUmumResult->fetch_assoc()): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="fasilitasUmum[]"
                                                placeholder="Fasilitas Umum"
                                                value="<?php echo htmlspecialchars($fasilitasUmum['fasilitas']); ?>">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger removeSpecBtn">Hapus</button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="fasilitasUmum[]"
                                        placeholder="Fasilitas Umum">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#generalFacilitiesContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <!-- Peraturan Kost -->
                        <div id="rulesContainer">
                            <h5>Peraturan Kost</h5>
                            <?php if ($peraturanResult && $peraturanResult->num_rows > 0): ?>
                                <?php while ($peraturan = $peraturanResult->fetch_assoc()): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="peraturanKost[]"
                                                placeholder="Peraturan Kost"
                                                value="<?php echo htmlspecialchars($peraturan['peraturan']); ?>">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-danger removeSpecBtn">Hapus</button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="peraturanKost[]"
                                        placeholder="Peraturan Kost">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#rulesContainer">Tambah</button>
                                </div>
                            </div>
                        </div>


                        <!-- Submit Button -->
                        <div class="col-md-12 d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-primary btn-submit"><strong>Simpan
                                    Perubahan</strong></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer Start -->
        <footer class="text-light py-4 mt-5" style="background-color: #000;">
            <div class="container text-center">
                <p class="mb-0">&copy; 2024 SMARTKOST. All rights reserved.</p>
            </div>
        </footer>
        <!-- Footer End -->
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-chevron-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template JavaScript -->
    <script src="js/main.js"></script>
    <script>
        document.querySelectorAll('.addSpecBtn').forEach(button => {
            button.addEventListener('click', function () {
                var target = document.querySelector(this.getAttribute('data-target'));

                // Create new input group
                var newInputGroup = document.createElement('div');
                newInputGroup.className = 'row mb-3';
                newInputGroup.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="${target.id === 'generalFacilitiesContainer' ? 'fasilitasUmum[]' : 'peraturanKost[]'}" placeholder="spesifikasi">
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-danger removeSpecBtn">Hapus</button>
            </div>
        `;

                target.appendChild(newInputGroup);

                // Add event listener for remove button
                newInputGroup.querySelector('.removeSpecBtn').addEventListener('click', function () {
                    target.removeChild(newInputGroup);
                });
            });
        });

        function previewImage(event, previewId) {
            var input = event.target;
            var file = input.files[0];
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
                // Hide the upload area when preview is displayed
                preview.parentElement.querySelector('.fas').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>

</html>