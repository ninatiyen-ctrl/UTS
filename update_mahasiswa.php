<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "biodata_db");

$id     = $_POST['id'];
$nama   = $_POST['nama'];
$email  = $_POST['email'];
$umur   = $_POST['umur'];
$gender = $_POST['gender'];
$status = $_POST['status'];
$hobi   = $_POST['hobi'];

$fotoName = "";

// 🔹 Jika user upload foto baru
if (!empty($_FILES['foto']['name'])) {

    $originalName = str_replace(' ', '_', $_FILES['foto']['name']);
    $fotoName = time() . "_" . $originalName;

    move_uploaded_file(
        $_FILES['foto']['tmp_name'],
        "uploads/" . $fotoName
    );

    // update foto
    $conn->query("UPDATE users SET foto='$fotoName' WHERE id='$id'");
}

// 🔹 update data lain
$sql = "UPDATE users SET 
        nama='$nama',
        email='$email',
        umur='$umur',
        gender='$gender',
        status='$status',
        hobi='$hobi'
        WHERE id='$id'";

$conn->query($sql);

echo json_encode(["status"=>"success","message"=>"Data diperbarui"]);
?>
