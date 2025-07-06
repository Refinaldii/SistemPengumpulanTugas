    <?php
    // File: mahasiswa/modul_praktikum.php
    session_start();
    include '../../config.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
        header("Location: ../../login.php");
        exit;
    }

    $praktikum_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $user_id = $_SESSION['user_id'];

    // Cek apakah mahasiswa memang mengikuti praktikum ini
    $check = mysqli_query($conn, "SELECT * FROM peserta_praktikum WHERE user_id = $user_id AND praktikum_id = $praktikum_id");
    if (mysqli_num_rows($check) === 0) {
        echo "<p class='text-red-600'>Praktikum tidak valid atau tidak diikuti oleh Anda.</p>";
        echo "<a href='dashboard.php' class='text-blue-600 underline'>Kembali ke Dashboard</a>";
        exit;
    }

    // Ambil modul dan laporan
    $query = mysqli_query($conn, "
        SELECT m.*, l.file_laporan, l.nilai, l.feedback
        FROM modul m
        LEFT JOIN laporan l ON l.modul_id = m.id AND l.user_id = $user_id
        WHERE m.praktikum_id = $praktikum_id
    ");

    require_once 'templates/header_mahasiswa.php';
    ?>

    <a href="dashboard.php" class="inline-block mb-4 text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>

    <h3 class="text-2xl font-bold text-gray-800 mb-4">Modul Praktikum</h3>

    <?php if (mysqli_num_rows($query) > 0): ?>
        <ul class="space-y-4">
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <li class="p-4 border rounded shadow">
                    <strong class="text-lg text-blue-700"><?= htmlspecialchars($row['judul']) ?></strong><br>
                    <a href="../../uploads/<?= htmlspecialchars($row['materi_file']) ?>" target="_blank" class="text-blue-600 hover:underline">Download Modul</a><br>

                    <?php if ($row['file_laporan']): ?>
                        <p class="text-green-600 mt-1">Laporan sudah diunggah</p>
                        <p class="text-sm">Nilai: <strong><?= $row['nilai'] ?? '-' ?></strong></p>
                        <p class="text-sm">Feedback: <em><?= $row['feedback'] ?? '-' ?></em></p>
                    <?php else: ?>
                        <form action="upload_laporan.php" method="POST" enctype="multipart/form-data" class="mt-2 space-y-2">
                            <input type="hidden" name="modul_id" value="<?= $row['id'] ?>">
                            <input type="file" name="file_laporan" required class="block">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Upload</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-gray-600">Belum ada modul untuk praktikum ini.</p>
    <?php endif; ?>

    <?php require_once 'templates/footer_mahasiswa.php'; ?>
