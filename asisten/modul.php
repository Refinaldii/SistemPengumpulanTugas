<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$praktikumList = mysqli_query($conn, "SELECT * FROM praktikum");

// Tambah
if (isset($_POST['tambah'])) {
    $praktikum_id = $_POST['praktikum_id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);

    $file = $_FILES['materi_file'];
    $filename = time() . "_" . basename($file['name']);
    $target = "../uploads/materi/" . $filename;
    move_uploaded_file($file['tmp_name'], $target);

    mysqli_query($conn, "INSERT INTO modul (praktikum_id, judul, materi_file) VALUES ('$praktikum_id', '$judul', '$filename')");
    header("Location: modul.php");
    exit;
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $praktikum_id = $_POST['praktikum_id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $old_file = $_POST['old_file'];
    $new_file = $old_file;

    if ($_FILES['materi_file']['name']) {
        $file = $_FILES['materi_file'];
        $filename = time() . "_" . basename($file['name']);
        $target = "../uploads/materi/" . $filename;
        move_uploaded_file($file['tmp_name'], $target);

        if (file_exists("../uploads/materi/" . $old_file)) {
            unlink("../uploads/materi/" . $old_file);
        }

        $new_file = $filename;
    }

    mysqli_query($conn, "UPDATE modul SET praktikum_id='$praktikum_id', judul='$judul', materi_file='$new_file' WHERE id=$id");
    header("Location: modul.php");
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($conn, "SELECT materi_file FROM modul WHERE id=$id");
    $f = mysqli_fetch_assoc($q);
    if ($f && file_exists("../uploads/materi/" . $f['materi_file'])) {
        unlink("../uploads/materi/" . $f['materi_file']);
    }

    mysqli_query($conn, "DELETE FROM modul WHERE id=$id");
    header("Location: modul.php");
    exit;
}

// Edit
$editData = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM modul WHERE id=$edit_id");
    $editData = mysqli_fetch_assoc($result);
}

$modulList = mysqli_query($conn, "
    SELECT m.*, p.nama as praktikum_nama
    FROM modul m
    JOIN praktikum p ON m.praktikum_id = p.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Modul</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Modul Praktikum</h1>
        <a href="../asisten/dashboard.php" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded shadow">
            ← Kembali
        </a>
    </div>

    <div class="bg-white p-6 rounded shadow mb-8">
        <form method="post" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id" value="<?= $editData ? $editData['id'] : '' ?>">
            <input type="hidden" name="old_file" value="<?= $editData ? $editData['materi_file'] : '' ?>">

            <div>
                <label class="block font-medium text-gray-700 mb-1">Pilih Praktikum</label>
                <select name="praktikum_id" class="border border-gray-300 rounded w-full p-2" required>
                    <option value="">-- Pilih --</option>
                    <?php
                    mysqli_data_seek($praktikumList, 0);
                    while ($p = mysqli_fetch_assoc($praktikumList)) :
                        $selected = $editData && $editData['praktikum_id'] == $p['id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $p['id'] ?>" <?= $selected ?>><?= $p['nama'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Judul Modul</label>
                <input type="text" name="judul" class="border border-gray-300 rounded w-full p-2" required value="<?= $editData ? $editData['judul'] : '' ?>">
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Upload Materi (PDF/DOCX)</label>
                <input type="file" name="materi_file" accept=".pdf,.doc,.docx" class="block">
                <?php if ($editData): ?>
                    <p class="text-sm text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
                <?php endif; ?>
            </div>

            <div class="pt-2">
                <button type="submit" name="<?= $editData ? 'update' : 'tambah' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    <?= $editData ? 'Update Modul' : 'Tambah Modul' ?>
                </button>
                <?php if ($editData): ?>
                    <a href="modul.php" class="ml-3 text-sm text-gray-600 underline">Batal Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white shadow rounded text-sm">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Praktikum</th>
                    <th class="px-4 py-2 text-left">Judul Modul</th>
                    <th class="px-4 py-2 text-left">File</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($modulList)) : ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2"><?= $row['praktikum_nama'] ?></td>
                        <td class="px-4 py-2"><?= $row['judul'] ?></td>
                        <td class="px-4 py-2">
                            <a href="../uploads/materi/<?= $row['materi_file'] ?>" class="text-blue-600 hover:underline" target="_blank">Unduh</a>
                        </td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <a href="?edit=<?= $row['id'] ?>" class="text-green-600 hover:underline">Edit</a>
                            <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus modul ini?')" class="text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
