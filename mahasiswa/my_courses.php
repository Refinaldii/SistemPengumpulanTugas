<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil semua praktikum yang diikuti mahasiswa
$praktikum_query = mysqli_query($conn, "
    SELECT p.id, p.nama
    FROM praktikum p
    JOIN pendaftaran d ON d.praktikum_id = p.id
    WHERE d.user_id = $user_id
");

$praktikum_list = [];
while ($row = mysqli_fetch_assoc($praktikum_query)) {
    $praktikum_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Modul Praktikum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen text-gray-800">

    <div class="mb-6">
        <a href="dashboard.php" class="text-blue-600 hover:underline flex items-center gap-1">
            &larr; <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <?php if (empty($praktikum_list)): ?>
        <p class="text-gray-600">Anda belum mendaftar praktikum apa pun.</p>
    <?php else: ?>
        <?php foreach ($praktikum_list as $praktikum): ?>
            <div class="mb-8">
                <h2 class="text-2xl font-bold border-b border-gray-300 pb-2 mb-4 mt-6"><?= htmlspecialchars($praktikum['nama']) ?></h2>

                <?php
                $praktikum_id = $praktikum['id'];
                $modul_query = mysqli_query($conn, "
                    SELECT m.*, l.file_laporan, l.nilai, l.feedback
                    FROM modul m
                    LEFT JOIN laporan l ON l.modul_id = m.id AND l.user_id = $user_id
                    WHERE m.praktikum_id = $praktikum_id
                ");
                ?>

                <?php if (mysqli_num_rows($modul_query) === 0): ?>
                    <p class="text-gray-600 italic">Belum ada modul untuk praktikum ini.</p>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php while ($modul = mysqli_fetch_assoc($modul_query)) : ?>
                            <div class="bg-white p-6 rounded-lg shadow">
                                <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($modul['judul']) ?></h3>

                                <!-- Unduh Materi -->
                                <?php if (!empty($modul['materi_file'])) : ?>
                                    <a href="../uploads/materi/<?= urlencode($modul['materi_file']) ?>" class="text-blue-600 underline text-sm" download>📄 Unduh Materi</a>
                                <?php else : ?>
                                    <p class="text-gray-500 text-sm">Belum ada file materi.</p>
                                <?php endif; ?>

                                <!-- Upload Laporan -->
                                <form method="post" action="upload_laporan.php" enctype="multipart/form-data" class="mt-4 space-y-2">
                                    <input type="hidden" name="modul_id" value="<?= $modul['id'] ?>">
                                    <input type="file" name="file_laporan" required class="block w-full text-sm text-gray-600 border border-gray-300 rounded px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-4 py-2 rounded transition">Kirim Laporan</button>
                                </form>

                                <!-- Status Upload -->
                                <?php if (!empty($modul['file_laporan'])) : ?>
                                    <p class="mt-3 text-sm text-gray-700">✅ Sudah mengirim laporan: <strong><?= htmlspecialchars($modul['file_laporan']) ?></strong></p>
                                <?php endif; ?>

                                <!-- Nilai dan Feedback -->
                                <?php if ($modul['nilai'] !== null) : ?>
                                    <p class="mt-3 text-sm text-blue-700">🎯 Nilai: <strong><?= $modul['nilai'] ?></strong></p>
                                    <p class="text-sm italic text-gray-600">💬 Feedback: <?= htmlspecialchars($modul['feedback']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
