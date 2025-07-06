<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$id_mahasiswa = (int) $_SESSION['user_id'];

// Jumlah Praktikum Diikuti = Jumlah praktikum unik dari laporan yang dikumpulkan
$q1 = mysqli_query($conn, "
    SELECT COUNT(DISTINCT m.praktikum_id) AS total
    FROM laporan l
    JOIN modul m ON l.modul_id = m.id
    JOIN praktikum p ON m.praktikum_id = p.id
    WHERE l.user_id = $id_mahasiswa
");
$praktikum_diikuti = mysqli_fetch_assoc($q1)['total'] ?? 0;

// Tugas Selesai = laporan yang sudah diberi nilai
$q2 = mysqli_query($conn, "
    SELECT COUNT(*) AS selesai 
    FROM laporan 
    WHERE user_id = $id_mahasiswa AND nilai IS NOT NULL
");
$tugas_selesai = mysqli_fetch_assoc($q2)['selesai'] ?? 0;

// Tugas Menunggu = laporan yang belum dinilai
$q3 = mysqli_query($conn, "
    SELECT COUNT(*) AS menunggu 
    FROM laporan 
    WHERE user_id = $id_mahasiswa AND nilai IS NULL
");
$tugas_menunggu = mysqli_fetch_assoc($q3)['menunggu'] ?? 0;

// Notifikasi tugas sudah diperiksa
$q4 = mysqli_query($conn, "
    SELECT modul.judul, laporan.nilai 
    FROM laporan 
    JOIN modul ON laporan.modul_id = modul.id
    WHERE user_id = $id_mahasiswa AND nilai IS NOT NULL
    ORDER BY laporan.id DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100 min-h-screen">

    <?php include 'templates/header_mahasiswa.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">📊 Dashboard Mahasiswa</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-500 text-white p-6 rounded-xl shadow hover:scale-[1.02] transition">
                <div class="text-sm uppercase tracking-wide">Praktikum Diikuti</div>
                <div class="text-4xl font-bold mt-2"><?= $praktikum_diikuti ?></div>
            </div>
            <div class="bg-green-500 text-white p-6 rounded-xl shadow hover:scale-[1.02] transition">
                <div class="text-sm uppercase tracking-wide">Tugas Selesai</div>
                <div class="text-4xl font-bold mt-2"><?= $tugas_selesai ?></div>
            </div>
            <div class="bg-yellow-500 text-white p-6 rounded-xl shadow hover:scale-[1.02] transition">
                <div class="text-sm uppercase tracking-wide">Tugas Menunggu</div>
                <div class="text-4xl font-bold mt-2"><?= $tugas_menunggu ?></div>
            </div>
            <div class="bg-indigo-500 text-white p-6 rounded-xl shadow hover:scale-[1.02] transition">
                <div class="text-sm uppercase tracking-wide">Sudah Diperiksa</div>
                <div class="text-4xl font-bold mt-2"><?= $tugas_selesai ?></div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">🔔 Tugas Terakhir yang Sudah Diperiksa</h3>
            <?php if (mysqli_num_rows($q4) > 0): ?>
                <ul class="space-y-3 list-inside list-disc text-gray-700">
                    <?php while ($row = mysqli_fetch_assoc($q4)) : ?>
                        <li>
                            Laporan modul <span class="font-semibold"><?= htmlspecialchars($row['judul']) ?></span> sudah diperiksa.
                            <div class="text-sm text-gray-600">Skor: <span class="font-bold text-green-600"><?= htmlspecialchars($row['nilai']) ?></span></div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500 text-sm">Belum ada laporan yang diperiksa.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'templates/footer_mahasiswa.php'; ?>

</body>
</html>
