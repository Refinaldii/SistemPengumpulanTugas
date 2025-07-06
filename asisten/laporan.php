<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit;
}

// Hapus laporan jika ada request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $hapus_id = intval($_POST['hapus_id']);
    $hapus_file = $_POST['hapus_file'];

    // Hapus file fisik
    $file_path = "../uploads/laporan/" . $hapus_file;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Hapus dari database
    $stmt = mysqli_prepare($conn, "DELETE FROM laporan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $hapus_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect untuk hindari resubmit
    header("Location: laporan_masuk.php");
    exit;
}

// Filter
$filter = '';
if (isset($_GET['modul']) && $_GET['modul'] != '') {
    $modul_id = intval($_GET['modul']);
    $filter = "WHERE l.modul_id = $modul_id";
}

// Ambil semua laporan + relasi
$query = mysqli_query($conn, "
    SELECT l.*, u.nama as mahasiswa_nama, m.judul as modul_judul
    FROM laporan l
    JOIN users u ON l.user_id = u.id
    JOIN modul m ON l.modul_id = m.id
    $filter
");

// Ambil modul untuk dropdown filter
$moduls = mysqli_query($conn, "SELECT * FROM modul");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6 min-h-screen">

    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <div class="mb-6">
            <a href="dashboard.php" class="text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
            <h1 class="text-2xl font-bold mt-2">📥 Laporan Masuk</h1>
        </div>

        <!-- Filter -->
        <form method="get" class="mb-6 flex flex-wrap items-center gap-3">
            <label class="text-gray-700">Filter berdasarkan modul:</label>
            <select name="modul" class="border border-gray-300 p-2 rounded w-64">
                <option value="">-- Semua --</option>
                <?php while ($m = mysqli_fetch_assoc($moduls)) : ?>
                    <option value="<?= $m['id'] ?>" <?= (isset($modul_id) && $modul_id == $m['id']) ? 'selected' : '' ?>>
                        <?= $m['judul'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Filter
            </button>
        </form>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-300">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="p-3 border">Mahasiswa</th>
                        <th class="p-3 border">Modul</th>
                        <th class="p-3 border">Laporan</th>
                        <th class="p-3 border">Nilai</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($laporan = mysqli_fetch_assoc($query)) : ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3 border"><?= htmlspecialchars($laporan['mahasiswa_nama']) ?></td>
                            <td class="p-3 border"><?= htmlspecialchars($laporan['modul_judul']) ?></td>
                            <td class="p-3 border">
                                <a href="../uploads/laporan/<?= htmlspecialchars($laporan['file_laporan']) ?>" class="text-blue-600 underline" target="_blank">Download</a>
                            </td>
                            <td class="p-3 border text-center"><?= $laporan['nilai'] ?? '-' ?></td>
                            <td class="p-3 border text-center">
                                <a href="nilai_laporan.php?id=<?= $laporan['id'] ?>" class="text-green-600 hover:underline mr-3">Nilai / Update</a>
                                <form method="post" class="inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                                    <input type="hidden" name="hapus_id" value="<?= $laporan['id'] ?>">
                                    <input type="hidden" name="hapus_file" value="<?= htmlspecialchars($laporan['file_laporan']) ?>">
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
