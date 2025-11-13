<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// 🔹 Koneksi ke database
$conn = new mysqli("localhost", "root", "", "biodata_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]);
    exit;
}

// 🔹 Ambil semua data dari tabel users
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Jika ada path foto, tambahkan URL lengkap agar bisa ditampilkan di Flutter
        if (!empty($row['foto'])) {
            $row['foto_url'] = "http://" . $_SERVER['HTTP_HOST'] . "/flutter_api/uploads" . $row['foto'];
        } else {
            $row['foto_url'] = "";
        }
        $data[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Data ditemukan",
        "total" => count($data),
        "data" => $data
    ]);
} else {
    echo json_encode([
        "status" => "empty",
        "message" => "Belum ada data tersimpan",
        "data" => []
    ]);
}

$conn->close();
?>
