<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($conn, "
    SELECT l.*, u.nama as mahasiswa_nama, m.judul as modul_judul
    FROM laporan l
    JOIN users u ON l.user_id = u.id
    JOIN modul m ON l.modul_id = m.id
    WHERE l.id = $id
");

$laporan = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = intval($_POST['nilai']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    mysqli_query($conn, "UPDATE laporan SET nilai = $nilai, feedback = '$feedback' WHERE id = $id");
    header("Location: laporan_masuk.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Beri Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6 min-h-screen">
    <!-- Tombol Kembali -->
    <div class="mb-6">
        <a href="dashboard.php" class="inline-block bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400 transition">
            ← Kembali ke Dashboard
        </a>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-4">Nilai Laporan Mahasiswa</h1>

    <div class="bg-white p-6 rounded shadow-md max-w-2xl mx-auto">
        <div class="mb-4">
            <p class="mb-1"><span class="font-semibold">Mahasiswa:</span> <?= $laporan['mahasiswa_nama'] ?></p>
            <p class="mb-1"><span class="font-semibold">Modul:</span> <?= $laporan['modul_judul'] ?></p>
            <p class="mb-1"><span class="font-semibold">Laporan:</span>
                <a href="../uploads/laporan/<?= $laporan['file_laporan'] ?>" class="text-blue-600 underline hover:text-blue-800 transition" target="_blank">Download</a>
            </p>
        </div>

        <form method="post" class="space-y-4">
            <div>
                <label class="block font-medium">Nilai (0-100):</label>
                <input type="number" name="nilai" min="0" max="100" value="<?= $laporan['nilai'] ?>" class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div>
                <label class="block font-medium">Feedback:</label>
                <textarea name="feedback" class="border border-gray-300 rounded p-2 w-full h-32 resize-none focus:outline-none focus:ring-2 focus:ring-blue-400" required><?= $laporan['feedback'] ?></textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-green-500 text-white px-5 py-2 rounded hover:bg-green-600 transition">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
