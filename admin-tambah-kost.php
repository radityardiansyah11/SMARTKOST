<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $nama_kost = $_POST['kostName'];
    $alamat = $_POST['address'];
    $ukuran_kamar = $_POST['roomSize'];
    $harga = $_POST['price'];
    $diskon = $_POST['discount'];
    $deskripsi = $_POST['description'];
    $jenis_kost = $_POST['jenisKost'];
    $banyak_kasur = $_POST['banyakKasur'];
    $banyak_kamar_mandi = $_POST['banyakKamarMandi'];
    $kategori = $_POST['kategori'];

    // Fasilitas
    $spesifikasi_kamar = $_POST['spesifikasiKamar'] ?? [];
    $fasilitas_kamar = $_POST['fasilitasKamar'] ?? [];
    $fasilitas_kamar_mandi = $_POST['fasilitasKamarMandi'] ?? [];

    // Unggah gambar
    $target_dir = "uploads/";
    $images = [null, null, null, null];  // Initialize with null to handle missing uploads
    for ($i = 1; $i <= 4; $i++) {
        if (!empty($_FILES["kostImage$i"]["name"])) {
            $target_file = $target_dir . basename($_FILES["kostImage$i"]["name"]);
            if (move_uploaded_file($_FILES["kostImage$i"]["tmp_name"], $target_file)) {
                $images[$i - 1] = $target_file; // Menyimpan path gambar
            }
        }
    }

    // Insert data ke dalam database
    $sql = "INSERT INTO kost (nama_kost, alamat, ukuran_kamar, harga, diskon, deskripsi, jenis_kost, banyak_kasur, banyak_kamar_mandi, kategori, gambar_1, gambar_2, gambar_3, gambar_4)
            VALUES ('$nama_kost', '$alamat', '$ukuran_kamar', '$harga', '$diskon', '$deskripsi', '$jenis_kost', '$banyak_kasur', '$banyak_kamar_mandi', '$kategori',
            '{$images[0]}', '{$images[1]}', '{$images[2]}', '{$images[3]}')";

    if ($conn->query($sql) === TRUE) {
        $kost_id = $conn->insert_id;

        // Insert spesifikasi kamar
        foreach ($spesifikasi_kamar as $spesifikasi) {
            $conn->query("INSERT INTO spesifikasi_kamar (kost_id, spesifikasi) VALUES ('$kost_id', '$spesifikasi')");
        }

        // Insert fasilitas kamar
        foreach ($fasilitas_kamar as $fasilitas) {
            $conn->query("INSERT INTO fasilitas_kamar (kost_id, fasilitas) VALUES ('$kost_id', '$fasilitas')");
        }

        // Insert fasilitas kamar mandi
        foreach ($fasilitas_kamar_mandi as $fasilitas_mandi) {
            $conn->query("INSERT INTO fasilitas_kamar_mandi (kost_id, fasilitas) VALUES ('$kost_id', '$fasilitas_mandi')");
        }

        // Setelah data berhasil ditambahkan, redirect ke halaman admin-dashboard-kost.php
        header("Location: admin-dashboard-kost.php?msg=success");
        exit();  // Pastikan tidak ada kode lain yang dieksekusi setelah redirect
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Tambah Kost - SMARTKOST</title>
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
        <div class="container ">
            <div class="form-section">
                <h2 class="text-center">Tambah Kost</h2>
                <form method="POST" action="admin-tambah-kost.php" enctype="multipart/form-data">
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
                                    <i class="fas fa-cloud-upload-alt"></i>

                                    <img id="imagePreview1" src="#" alt="Preview Image">
                                </div>
                                <!-- Upload Area 2 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage2" id="kostImage2" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview2')">
                                    <i class="fas fa-cloud-upload-alt"></i>

                                    <img id="imagePreview2" src="#" alt="Preview Image">
                                </div>
                                <!-- Upload Area 3 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage3" id="kostImage3" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview3')">
                                    <i class="fas fa-cloud-upload-alt"></i>

                                    <img id="imagePreview3" src="#" alt="Preview Image">
                                </div>
                                <!-- Upload Area 4 -->
                                <div class="upload-area">
                                    <input type="file" name="kostImage4" id="kostImage4" accept="image/*"
                                        onchange="previewImage(event, 'imagePreview4')">
                                    <i class="fas fa-cloud-upload-alt"></i>

                                    <img id="imagePreview4" src="#" alt="Preview Image">
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
                                placeholder="Masukkan Nama Kost">
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <input type="text" class="form-control" name="address" id="address"
                                placeholder="Masukkan Alamat">
                        </div>

                        <!-- Room Size -->
                        <div class="col-md-3 mb-3">
                            <label for="roomSize" class="form-label">Ukuran Kamar (m<sup>2</sup>)</label>
                            <input type="text" class="form-control" name="roomSize" id="roomSize"
                                placeholder="Masukkan Ukuran Kamar">
                        </div>

                        <!-- jenis kost -->
                        <div class="col-md-3 mb-3">
                            <label for="jenisKost" class="form-label">Jenis Kost</label>
                            <select class="form-select" name="jenisKost" id="jenisKost">
                                <option selected disabled>Pilih Jenis Kost</option>
                                <option value="laki-laki">Laki-laki</option>
                                <option value="perempuan">Perempuan</option>
                                <option value="campur">Campur</option>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-3 mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="text" class="form-control" name="price" id="price"
                                placeholder="Masukkan Harga">
                        </div>

                        <!-- Discount -->
                        <div class="col-md-3 mb-3">
                            <label for="discount" class="form-label">Diskon (opsional)</label>
                            <input type="text" class="form-control" name="discount" id="discount"
                                placeholder="Masukkan Diskon">
                        </div>

                        <!-- Banyak Kasur -->
                        <div class="col-md-3 mb-3">
                            <label for="banyakKasur" class="form-label">Banyak Kasur</label>
                            <input type="text" class="form-control" name="banyakKasur" id="banyakKasur"
                                placeholder="Masukkan Banyak Kasur">
                        </div>

                        <!-- Banyak Kamar Mandi -->
                        <div class="col-md-3 mb-3">
                            <label for="banyakKamarMandi" class="form-label">Banyak Kamar Mandi</label>
                            <input type="text" class="form-control" name="banyakKamarMandi" id="banyakKamarMandi"
                                placeholder="Masukkan Banyak Kamar Mandi">
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-3 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" name="kategori" id="kategori">
                                <option selected disabled>Pilih Kategori Kost</option>
                                <option value="Standart">Standart</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="4"
                                placeholder="Masukkan Deskripsi"></textarea>
                        </div>

                        <!-- Facilities -->
                        <h5>Fasilitas</h5>
                        <div id="specificationContainer">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="spesifikasi1" class="form-label"><strong>Spesifikasi Tipe
                                            Kamar</strong></label>
                                    <input type="text" class="form-control" name="spesifikasiKamar[]"
                                        placeholder="spesifikasi">
                                </div>
                                <div class="col-md-2 d-flex align-items-center mt-3">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#specificationContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas Kamar -->
                        <div id="roomFacilitiesContainer">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="fasilitasKamar1" class="form-label"><strong>Fasilitas
                                            Kamar</strong></label>
                                    <input type="text" class="form-control" name="fasilitasKamar[]"
                                        placeholder="fasilitas kamar">
                                </div>
                                <div class="col-md-2 d-flex align-items-center mt-3">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#roomFacilitiesContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas Kamar Mandi -->
                        <div id="bathroomFacilitiesContainer">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="fasilitasKamarMandi1" class="form-label"><strong>Fasilitas Kamar
                                            Mandi</strong></label>
                                    <input type="text" class="form-control" name="fasilitasKamarMandi[]"
                                        placeholder="fasilitas kamar mandi">
                                </div>
                                <div class="col-md-2 d-flex align-items-center mt-3">
                                    <button type="button" class="btn btn-primary addSpecBtn"
                                        data-target="#bathroomFacilitiesContainer">Tambah</button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-primary btn-submit"><strong>Tambah
                                    Kost</strong></button>
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
        // Function to dynamically add input fields
        document.querySelectorAll('.addSpecBtn').forEach(button => {
            button.addEventListener('click', function () {
                var target = document.querySelector(this.getAttribute('data-target'));

                // Create new input group
                var newInputGroup = document.createElement('div');
                newInputGroup.className = 'row mb-3';
                newInputGroup.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="${target.id === 'specificationContainer' ? 'spesifikasiKamar[]' : target.id === 'roomFacilitiesContainer' ? 'fasilitasKamar[]' : 'fasilitasKamarMandi[]'}" placeholder="spesifikasi">
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
                preview.parentElement.querySelector('p').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>

</html>