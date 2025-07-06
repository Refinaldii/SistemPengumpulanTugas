<?php
// 1. Definisi Variabel untuk Template
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

require_once '../config.php';

// 2. Panggil Header
require_once 'templates/header.php'; 

// Ambil data dari database
$query1 = "SELECT COUNT(*) AS total FROM modul";
$result1 = mysqli_query($conn, $query1);
$data1 = mysqli_fetch_assoc($result1);
$totalModul = $data1['total'];

$query2 = "SELECT COUNT(*) AS total FROM laporan";
$result2 = mysqli_query($conn, $query2);
$data2 = mysqli_fetch_assoc($result2);
$totalLaporan = $data2['total'];

$query3 = "SELECT COUNT(*) AS total FROM laporan WHERE nilai IS NULL";
$result3 = mysqli_query($conn, $query3);
$data3 = mysqli_fetch_assoc($result3);
$totalBelum = $data3['total'];
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Total Modul Diajarkan -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalModul ?></p>
        </div>
    </div>

    <!-- Total Laporan Masuk -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalLaporan ?></p>
        </div>
    </div>

    <!-- Laporan Belum Dinilai -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalBelum ?></p>
        </div>
    </div>

    <!-- Kelola Akun -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-purple-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.75 9V5.25M8.25 9V5.25M3 13.5h18M4.5 17.25h15M6 21h12"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Manajemen Akun</p>
            <p>
                <a href="users.php" class="text-purple-700 font-bold hover:underline">
                    Kelola Akun
                </a>
            </p>
        </div>
    </div>

</div>

<!-- Aktivitas Terbaru -->
<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php
        // Ambil 5 laporan terbaru
        $queryAktivitas = "
            SELECT l.waktu_kumpul, m.nama AS nama_mahasiswa, mo.nama_modul
            FROM laporan l
            JOIN mahasiswa m ON l.id_mahasiswa = m.id
            JOIN modul mo ON l.id_modul = mo.id
            ORDER BY l.waktu_kumpul DESC
            LIMIT 5
        ";
        $resultAktivitas = mysqli_query($conn, $queryAktivitas);
        while ($row = mysqli_fetch_assoc($resultAktivitas)) {
            $nama = $row['nama_mahasiswa'];
            $inisial = strtoupper(substr($nama, 0, 1)) . strtoupper(substr(explode(" ", $nama)[1] ?? '', 0, 1));
            $modul = $row['nama_modul'];
            $waktu = $row['waktu_kumpul'];
            
            // Buat waktu relatif (misal: "2 jam lalu")
            $datetime1 = new DateTime($waktu);
            $datetime2 = new DateTime();
            $interval = $datetime1->diff($datetime2);

            if ($interval->d > 0) {
                $waktu_ago = $interval->d . ' hari lalu';
            } elseif ($interval->h > 0) {
                $waktu_ago = $interval->h . ' jam lalu';
            } elseif ($interval->i > 0) {
                $waktu_ago = $interval->i . ' menit lalu';
            } else {
                $waktu_ago = 'Baru saja';
            }
        ?>
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                <span class="font-bold text-gray-500"><?= $inisial ?></span>
            </div>
            <div>
                <p class="text-gray-800">
                    <strong><?= htmlspecialchars($nama) ?></strong> mengumpulkan laporan untuk <strong><?= htmlspecialchars($modul) ?></strong>
                </p>
                <p class="text-sm text-gray-500"><?= $waktu_ago ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>


<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>
