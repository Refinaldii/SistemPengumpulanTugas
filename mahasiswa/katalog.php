<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT p.id, p.nama, p.deskripsi
    FROM pendaftaran dp
    JOIN praktikum p ON dp.praktikum_id = p.id
    WHERE dp.user_id = $user_id
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Praktikum Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold mb-4">Praktikum yang Saya Ikuti</h1>

    <?php if (mysqli_num_rows($query) === 0): ?>
        <p class="text-gray-600">Anda belum mendaftar ke praktikum manapun.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                <div class="bg-white p-4 shadow rounded">
                    <h2 class="text-xl font-semibold"><?= $row['nama'] ?></h2>
                    <p class="text-gray-600"><?= $row['deskripsi'] ?></p>
                    <a href="detail_praktikum.php?id=<?= $row['id'] ?>" class="inline-block mt-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Lihat Detail</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</body>
</html>
