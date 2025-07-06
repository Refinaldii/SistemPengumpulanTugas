<?php
session_start();
include '../config.php';

// Ambil semua praktikum
$query = mysqli_query($conn, "SELECT * FROM praktikum");

// Cek apakah user sudah login dan role-nya mahasiswa
$isMahasiswa = isset($_SESSION['role']) && $_SESSION['role'] == 'mahasiswa';
$user_id = $_SESSION['user_id'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Katalog Praktikum</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <!-- Tombol Kembali -->
    <div class="mb-6">
        <a href="dashboard.php" class="inline-flex items-center text-sm text-blue-600 hover:underline">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">📚 Katalog Mata Praktikum</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                <div class="bg-white rounded-lg shadow p-5 hover:shadow-lg transition">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($row['nama']) ?></h2>
                    <p class="text-gray-600 mb-4"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>

                    <?php if ($isMahasiswa): ?>
                        <?php
                        $praktikum_id = $row['id'];
                        $cek = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE user_id=$user_id AND praktikum_id=$praktikum_id");
                        ?>
                        <?php if (mysqli_num_rows($cek) == 0): ?>
                            <form method="post" action="daftar_praktikum.php">
                                <input type="hidden" name="praktikum_id" value="<?= $praktikum_id ?>">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition w-full">
                                    Daftar
                                </button>
                            </form>
                        <?php else: ?>
                            <p class="text-green-600 font-medium">✅ Sudah terdaftar</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
