<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "biodata_db");

if ($conn->connect_error) {
    echo json_encode(["status"=>"error","message"=>"Koneksi gagal"]);
    exit;
}

$nama   = $_POST['nama'] ?? '';
$email  = $_POST['email'] ?? '';
$umur   = $_POST['umur'] ?? '';
$gender = $_POST['gender'] ?? '';
$status = $_POST['status'] ?? '';
$hobi   = $_POST['hobi'] ?? '[]';

$fotoName = "";

// upload foto
if (!empty($_FILES['image']['name'])) {
    $cleanName = str_replace(' ', '_', $_FILES['image']['name']);
    $fotoName = time() . "_" . $cleanName;

    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $fotoName);
}

$sql = "INSERT INTO users (nama,email,umur,gender,status,hobi,foto)
        VALUES ('$nama','$email','$umur','$gender','$status','$hobi','$fotoName')";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success","message"=>"Data berhasil disimpan"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Gagal menyimpan"]);
}
?>
