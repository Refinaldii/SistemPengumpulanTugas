<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $praktikum_id = $_POST['praktikum_id'];

    if ($user_id) {
        // Insert ke tabel pendaftaran
        $sql = "INSERT INTO pendaftaran (user_id, praktikum_id) VALUES ($user_id, $praktikum_id)";
        mysqli_query($conn, $sql);
        header("Location: katalog.php");
        exit;
    } else {
        echo "Silakan login terlebih dahulu.";
    }
}
?>
