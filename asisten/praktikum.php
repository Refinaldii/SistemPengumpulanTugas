<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit;
}

// Tambah Praktikum
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    mysqli_query($conn, "INSERT INTO praktikum (nama, deskripsi) VALUES ('$nama', '$deskripsi')");
    header("Location: praktikum.php");
    exit;
}

// Update Praktikum
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    mysqli_query($conn, "UPDATE praktikum SET nama='$nama', deskripsi='$deskripsi' WHERE id=$id");
    header("Location: praktikum.php");
    exit;
}

// Hapus Praktikum
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM praktikum WHERE id=$id");
    header("Location: praktikum.php");
    exit;
}

// Ambil semua praktikum
$praktikum = mysqli_query($conn, "SELECT * FROM praktikum");

// Jika edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM praktikum WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Mata Praktikum</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <h1 class="text-2xl font-bold mb-4">Kelola Mata Praktikum</h1>

    <!-- Form Tambah / Edit -->
    <div class="bg-white p-4 shadow rounded mb-6">
        <form method="post">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            <div class="mb-2">
                <label class="block">Nama Praktikum</label>
                <input type="text" name="nama" class="border p-2 w-full" value="<?= $edit_data['nama'] ?? '' ?>" required>
            </div>
            <div class="mb-2">
                <label class="block">Deskripsi</label>
                <textarea name="deskripsi" class="border p-2 w-full" required><?= $edit_data['deskripsi'] ?? '' ?></textarea>
            </div>
            <button type="submit" name="<?= $edit_data ? 'update' : 'tambah' ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <?= $edit_data ? 'Update' : 'Tambah' ?>
            </button>
        </form>
    </div>

    <!-- Tabel Data Praktikum -->
    <table class="w-full table-auto bg-white shadow rounded">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 text-left">Nama</th>
                <th class="p-2 text-left">Deskripsi</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($praktikum)) : ?>
                <tr class="border-t">
                    <td class="p-2"><?= $row['nama'] ?></td>
                    <td class="p-2"><?= $row['deskripsi'] ?></td>
                    <td class="p-2 text-center">
                        <a href="?edit=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a> |
                        <a href="?hapus=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
