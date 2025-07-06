<?php
session_start();
include '../config.php'; // Pastikan path ini benar dari folder 'mahasiswa'

// Cek apakah user login dan memiliki role mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

// Validasi modul_id dari URL
if (!isset($_POST['modul_id'])) {
    echo "modul_id tidak diberikan.";
    echo "<br><a href='dashboard.php'>Kembali ke Dashboard</a>";
    exit;
}

$modul_id = (int)$_POST['modul_id'];
$user_id = $_SESSION['user_id'];

// Ambil praktikum_id dari tabel modul (untuk redirect nanti)
$result_modul = mysqli_query($conn, "SELECT praktikum_id FROM modul WHERE id = $modul_id");
if (!$result_modul || mysqli_num_rows($result_modul) === 0) {
    echo "Modul tidak ditemukan.";
    echo "<br><a href='dashboard.php'>Kembali ke Dashboard</a>";
    exit;
}
$data_modul = mysqli_fetch_assoc($result_modul);
$praktikum_id = $data_modul['praktikum_id'];

// Proses upload jika ada form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_laporan'])) {
    $file = $_FILES['file_laporan'];
    $upload_dir = '../uploads/laporan/';
    $filename = time() . "_" . basename($file['name']);
    $target = $upload_dir . $filename;

    // Pastikan folder uploads/laporan/ ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        // Cek apakah laporan sudah pernah dikirim
        $cek = mysqli_query($conn, "SELECT * FROM laporan WHERE user_id=$user_id AND modul_id=$modul_id");
        if (mysqli_num_rows($cek) > 0) {
            // Update
            mysqli_query($conn, "UPDATE laporan SET file_laporan='$filename' WHERE user_id=$user_id AND modul_id=$modul_id");
        } else {
            // Insert
            mysqli_query($conn, "INSERT INTO laporan (user_id, modul_id, file_laporan) VALUES ($user_id, $modul_id, '$filename')");
        }

        
    } else {
        echo "<p class='text-red-600'>Gagal upload file.</p>";
    }
}
?>

<?php require_once 'templates/header_mahasiswa.php'; ?>

<h3 class="text-2xl font-bold text-gray-800 mb-4">Upload Laporan</h3>

<form action="" method="post" enctype="multipart/form-data">
    <label for="file_laporan" class="block mb-2">Pilih File Laporan (PDF/Word):</label>
    <input type="file" name="file_laporan" id="file_laporan" required class="mb-4"><br>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
</form>

<a href="modul_praktikum.php?id=<?= $praktikum_id ?>" class="inline-block mt-4 text-blue-600 hover:underline">← Kembali ke Modul</a>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
