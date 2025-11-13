<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🔍 Cek apakah ada data mentah JSON (untuk Flutter Web fallback)
$rawInput = file_get_contents("php://input");
parse_str($rawInput, $formData);

// 🔹 Gabungkan semua kemungkinan sumber data (POST dan parsed body)
$data = array_merge($_POST, $formData);

// 🔹 Ambil semua nilai dengan fallback default
$nama   = $data['nama'] ?? '';
$email  = $data['email'] ?? '';
$umur   = $data['umur'] ?? 0;
$gender = $data['gender'] ?? '';
$status = $data['status'] ?? '';
$hobi   = isset($data['hobi'])
    ? (is_array($data['hobi']) ? implode(',', $data['hobi']) : trim($data['hobi'], '[]"'))
    : '';
$foto   = '';

// 🔹 Upload file (jika ada)
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $foto = $targetFile;
    }
}

// 🔹 Koneksi ke database
$conn = new mysqli("localhost", "root", "", "biodata_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]);
    exit;
}

// 🔹 Simpan data
$stmt = $conn->prepare("
    INSERT INTO users (nama, email, umur, gender, status, hobi, foto)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssissss", $nama, $email, $umur, $gender, $status, $hobi, $foto);
$stmt->execute();

// 🔹 Ambil data terakhir yang tersimpan
$last_id = $conn->insert_id;
$result = $conn->query("SELECT * FROM users WHERE id = $last_id");
$savedData = $result->fetch_assoc();

// 🔹 Kirim respon
echo json_encode([
    "status" => "success",
    "message" => "Data berhasil disimpan",
    "data" => $savedData
]);

$stmt->close();
$conn->close();
?>
