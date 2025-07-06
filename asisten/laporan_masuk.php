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
<html>
<head>
    <title>Laporan Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <h1 class="text-2xl font-bold mb-4">Laporan Masuk</h1>

    <a href="dashboard.php" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>

    <!-- Filter -->
    <form method="get" class="mb-4">
        <label>Filter berdasarkan modul:</label>
        <select name="modul" class="border p-2 rounded">
            <option value="">-- Semua --</option>
            <?php while ($m = mysqli_fetch_assoc($moduls)) : ?>
                <option value="<?= $m['id'] ?>" <?= (isset($modul_id) && $modul_id == $m['id']) ? 'selected' : '' ?>>
                    <?= $m['judul'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Filter</button>
    </form>

    <!-- Tabel Laporan -->
    <table class="w-full table-auto bg-white shadow rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">Mahasiswa</th>
                <th class="p-2">Modul</th>
                <th class="p-2">Laporan</th>
                <th class="p-2">Nilai</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($laporan = mysqli_fetch_assoc($query)) : ?>
                <tr class="border-t">
                    <td class="p-2"><?= $laporan['mahasiswa_nama'] ?></td>
                    <td class="p-2"><?= $laporan['modul_judul'] ?></td>
                    <td class="p-2">
                        <a href="../uploads/laporan/<?= $laporan['file_laporan'] ?>" class="text-blue-600 underline" target="_blank">Download</a>
                    </td>
                    <td class="p-2 text-center"><?= $laporan['nilai'] ?? '-' ?></td>
                    <td class="p-2 text-center">
                        <a href="nilai_laporan.php?id=<?= $laporan['id'] ?>" class="text-green-600 hover:underline mr-2">Nilai / Update</a>
                        <form action="" method="post" class="inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                            <input type="hidden" name="hapus_id" value="<?= $laporan['id'] ?>">
                            <input type="hidden" name="hapus_file" value="<?= $laporan['file_laporan'] ?>">
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
