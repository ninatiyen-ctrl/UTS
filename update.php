<?php
$conn = new mysqli("localhost", "root", "", "biodata_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id=$id");
if ($result->num_rows == 0) {
    die("Data tidak ditemukan!");
}
$data = $result->fetch_assoc();

// UPDATE - Simpan perubahan data
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $umur = $_POST['umur'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $hobi = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";

    $foto = $data['foto'];
    if (!empty($_FILES['foto']['name'])) {
        // Hapus foto lama
        if (!empty($foto) && file_exists("uploads/" . $foto)) {
            unlink("uploads/" . $foto);
        }

        // Upload foto baru
        $target_dir = "uploads/";
        $foto_name = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto_name;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
        $foto = $foto_name;
    }

    $conn->query("UPDATE users 
                  SET nama='$nama', email='$email', umur='$umur', gender='$gender', status='$status', hobi='$hobi', foto='$foto' 
                  WHERE id=$id");
    header("Location: index.php");
    exit;
}

$hobi_tersimpan = explode(", ", $data['hobi']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; margin: 40px; }
        form {
            background: white; padding: 20px; width: 90%; margin: 20px auto;
            border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        input, select {
            padding: 8px; margin: 5px; border: 1px solid #ccc; border-radius: 4px;
        }
        input[type="text"], input[type="email"], input[type="number"] { width: 200px; }
        button {
            background: #0078D7; color: white; border: none;
            padding: 8px 16px; border-radius: 4px; cursor: pointer;
        }
        button:hover { background: #005fa3; }
        img { width: 120px; height: 120px; object-fit: cover; border-radius: 10px; margin: 10px; border: 2px solid #ccc; }
        .preview { display: flex; align-items: center; gap: 20px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">✏️ Edit Data Mahasiswa</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nama" value="<?= $data['nama'] ?>" required>
    <input type="email" name="email" value="<?= $data['email'] ?>" required>
    <input type="number" name="umur" value="<?= $data['umur'] ?>" required>

    <select name="gender" required>
        <option value="">--Pilih Gender--</option>
        <option value="Laki-laki" <?= ($data['gender'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
        <option value="Perempuan" <?= ($data['gender'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
    </select>

    <select name="status" required>
        <option value="">--Pilih Status--</option>
        <option value="Aktif" <?= ($data['status'] == 'Aktif') ? 'selected' : '' ?>>Aktif</option>
        <option value="Cuti" <?= ($data['status'] == 'Cuti') ? 'selected' : '' ?>>Cuti</option>
    </select>

    <br><br>
    <label><strong>Pilih Hobi:</strong></label><br>
    <label><input type="checkbox" name="hobi[]" value="Membaca" <?= in_array("Membaca", $hobi_tersimpan) ? 'checked' : '' ?>> Membaca</label>
    <label><input type="checkbox" name="hobi[]" value="Menulis" <?= in_array("Menulis", $hobi_tersimpan) ? 'checked' : '' ?>> Menulis</label>
    <label><input type="checkbox" name="hobi[]" value="Olahraga" <?= in_array("Olahraga", $hobi_tersimpan) ? 'checked' : '' ?>> Olahraga</label>
    <label><input type="checkbox" name="hobi[]" value="Gaming" <?= in_array("Gaming", $hobi_tersimpan) ? 'checked' : '' ?>> Gaming</label>

    <br><br>
    <div class="preview">
        <?php if (!empty($data['foto'])): ?>
            <div>
                <p><strong>Foto Lama:</strong></p>
                <img src="uploads/<?= $data['foto'] ?>" alt="Foto Lama">
            </div>
        <?php endif; ?>

        <div>
            <p><strong>Foto Baru (Preview):</strong></p>
            <img id="preview-img" src="https://via.placeholder.com/120?text=Preview" alt="Preview">
        </div>
    </div>

    <br>
    <input type="file" name="foto" accept="image/*" onchange="previewFoto(event)">
    <br><br>

    <button type="submit" name="update">💾 Simpan Perubahan</button>
    <a href="index.php"><button type="button" style="background:#6c757d;">🔙 Kembali</button></a>
</form>

<script>
function previewFoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview-img');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>

<?php $conn->close(); ?>
