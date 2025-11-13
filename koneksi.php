<?php
// ====== FILE: koneksi.php ======
$servername = "localhost";
$username = "root";
$password = "";
$database = "biodata_db"; // ⚠️ pastikan ini sudah dibuat di phpMyAdmin

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi gagal: " . mysqli_connect_error()
    ]);
    exit();
}
?>
