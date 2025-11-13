<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "biodata_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi gagal"]);
    exit;
}

$id = $_POST['id'];

// Ambil data untuk hapus foto
$queryFoto = $conn->query("SELECT foto FROM users WHERE id='$id'");
$row = $queryFoto->fetch_assoc();

if ($row && !empty($row['foto'])) {
    $filePath = "uploads/" . $row['foto'];
    if (file_exists($filePath)) {
        unlink($filePath); // hapus file foto
    }
}

// Hapus data mahasiswa
$sql = "DELETE FROM users WHERE id='$id'";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menghapus data"]);
}

$conn->close();
?>
