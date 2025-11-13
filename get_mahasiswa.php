<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "biodata_db");

$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    // 🔹 Perbaikan hobi
    $hobiRaw = $row['hobi'];
    $decoded = json_decode($hobiRaw, true);
    $row['hobi'] = is_array($decoded) ? $decoded : [$hobiRaw];

    // 🔹 Perbaikan foto_url
    if (!empty($row['foto'])) {

        // Hilangkan spasi pada nama file (jaga-jaga)
        $row['foto'] = str_replace(' ', '_', $row['foto']);

        $row['foto_url'] =
            "http://" . $_SERVER['HTTP_HOST'] . "/flutter_api/uploads/" . $row['foto'];
    } else {
        $row['foto_url'] = "";
    }

    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
?>
