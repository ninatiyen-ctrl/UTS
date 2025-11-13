<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost","root","","biodata_db");

$id = $_POST['id'];

$old = $conn->query("SELECT foto FROM users WHERE id=$id")->fetch_assoc();

if ($old['foto'] != "" && file_exists("uploads/".$old['foto'])) {
    unlink("uploads/".$old['foto']);
}

$conn->query("DELETE FROM users WHERE id=$id");

echo json_encode(["status"=>"success"]);
?>
