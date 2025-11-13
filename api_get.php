<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","biodata_db");

$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    // hobi harus diubah ke array
    $hobiDecode = json_decode($row['hobi'], true);
    $row['hobi'] = is_array($hobiDecode) ? $hobiDecode : [$row['hobi']];

    // foto_url
    if ($row['foto'] != "") {
        $row['foto_url'] = "http://".$_SERVER['HTTP_HOST']."/flutter_api/uploads/".$row['foto'];
    } else {
        $row['foto_url'] = "";
    }

    $data[] = $row;
}

echo json_encode(["status"=>"success","data"=>$data]);
?>
