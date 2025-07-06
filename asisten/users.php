<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: ../login.php");
    exit;
}

// Tambah
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')");
    header("Location: users.php");
    exit;
}

// Edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', password='$password', role='$role' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', role='$role' WHERE id=$id");
    }
    header("Location: users.php");
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

// Ambil semua user
$users = mysqli_query($conn, "SELECT * FROM users");

// Edit data
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $edit = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-4 text-gray-800">Kelola Akun Pengguna</h1>

        <a href="dashboard.php" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>

        <!-- Form Tambah/Edit -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <form method="post">
                <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Nama</label>
                    <input type="text" name="nama" class="border border-gray-300 p-2 w-full rounded" value="<?= $edit['nama'] ?? '' ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" class="border border-gray-300 p-2 w-full rounded" value="<?= $edit['email'] ?? '' ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Password <?= $edit ? '(Kosongkan jika tidak diubah)' : '' ?></label>
                    <input type="password" name="password" class="border border-gray-300 p-2 w-full rounded" <?= $edit ? '' : 'required' ?>>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Role</label>
                    <select name="role" class="border border-gray-300 p-2 w-full rounded" required>
                        <option value="mahasiswa" <?= ($edit['role'] ?? '') == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                        <option value="asisten" <?= ($edit['role'] ?? '') == 'asisten' ? 'selected' : '' ?>>Asisten</option>
                    </select>
                </div>
                
                <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <?= $edit ? 'Update' : 'Tambah' ?>
                </button>
            </form>
        </div>

        <!-- Tabel Users -->
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($users)) : ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-3"><?= $row['nama'] ?></td>
                            <td class="p-3"><?= $row['email'] ?></td>
                            <td class="p-3 capitalize"><?= $row['role'] ?></td>
                            <td class="p-3 text-center space-x-2">
                                <a href="?edit=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="?hapus=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
