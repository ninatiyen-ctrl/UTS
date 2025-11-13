<?php
$conn = new mysqli("localhost", "root", "", "biodata_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// CREATE
if (isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $umur = $_POST['umur'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $hobi = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "";

    $foto_name = "";
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $foto_name = time() . "_" . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $foto_name);
    }

    $conn->query("INSERT INTO users (nama, email, umur, gender, status, hobi, foto) 
                  VALUES ('$nama', '$email', '$umur', '$gender', '$status', '$hobi', '$foto_name')");
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $result = $conn->query("SELECT foto FROM users WHERE id=$id");
    if ($result->num_rows > 0) {
        $foto = $result->fetch_assoc()['foto'];
        if ($foto && file_exists("uploads/$foto")) unlink("uploads/$foto");
    }
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: index.php");
    exit;
}

// SEARCH
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// SORTING
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : "id";
$order = isset($_GET['order']) ? $_GET['order'] : "DESC";

// Validasi kolom agar tidak disalahgunakan
$valid_columns = ["id", "nama", "umur", "status"];
if (!in_array($sort_by, $valid_columns)) $sort_by = "id";

// Query
$query = "SELECT * FROM users WHERE 
          nama LIKE '%$search%' OR email LIKE '%$search%' OR hobi LIKE '%$search%'
          ORDER BY $sort_by $order";
$result = $conn->query($query);

// Untuk toggle ASC/DESC di link
function sort_link($column, $label, $current_sort, $current_order, $search) {
    $next_order = ($current_sort == $column && $current_order == "ASC") ? "DESC" : "ASC";
    $arrow = "";
    if ($current_sort == $column) $arrow = $current_order == "ASC" ? "↑" : "↓";
    $url = "?sort_by=$column&order=$next_order&search=" . urlencode($search);
    return "<a href='$url' style='color:white; text-decoration:none;'>$label $arrow</a>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>📋 Data Mahasiswa</title>
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; margin: 40px; }
    h2 { text-align:center; color: #333; }

    /* Form */
    form {
        background: white; padding: 20px; border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 30px;
    }
    input, select { margin: 5px; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
    button {
        background: #0078D7; color: white; border: none;
        padding: 8px 16px; border-radius: 4px; cursor: pointer;
    }
    button:hover { background: #005fa3; }

    /* Search Bar */
    .search-bar {
        text-align: right; margin-bottom: 15px;
    }
    .search-bar input {
        padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc;
    }

    /* Tabel */
    table {
        width: 100%; border-collapse: collapse; background: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
        border: 1px solid #ddd; padding: 10px; text-align: center;
    }
    th { background-color: #0078D7; color: white; cursor: pointer; }
    tr:hover { background-color: #f1f1f1; }
    img { width: 60px; height: 60px; border-radius: 8px; cursor: pointer; }

    /* Modal Preview */
    .modal {
        display: none; position: fixed; z-index: 1000;
        left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.7);
        justify-content: center; align-items: center;
    }
    .modal img {
        max-width: 80%; max-height: 80%;
        border-radius: 10px; box-shadow: 0 0 15px #fff;
    }
    .close {
        position: absolute; top: 20px; right: 40px;
        font-size: 40px; color: white; cursor: pointer;
    }
</style>
</head>
<body>

<h2>📋 Manajemen Data Mahasiswa</h2>

<!-- Form Tambah -->
<form method="POST" enctype="multipart/form-data">
    <h3>Tambah Data Baru</h3>
    <input type="text" name="nama" placeholder="Nama" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="number" name="umur" placeholder="Umur" required>

    <select name="gender" required>
        <option value="">--Pilih Gender--</option>
        <option value="Laki-laki">Laki-laki</option>
        <option value="Perempuan">Perempuan</option>
    </select>

    <select name="status" required>
        <option value="">--Pilih Status--</option>
        <option value="Aktif">Aktif</option>
        <option value="Cuti">Cuti</option>
    </select>

    <br><br>
    <label><strong>Pilih Hobi:</strong></label><br>
    <label><input type="checkbox" name="hobi[]" value="Membaca"> Membaca</label>
    <label><input type="checkbox" name="hobi[]" value="Menulis"> Menulis</label>
    <label><input type="checkbox" name="hobi[]" value="Olahraga"> Olahraga</label>
    <label><input type="checkbox" name="hobi[]" value="Gaming"> Gaming</label>

    <br><br>
    <input type="file" name="foto" accept="image/*">
    <button type="submit" name="add">Tambah</button>
</form>

<!-- Search Bar -->
<div class="search-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Cari nama, email, atau hobi..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍 Cari</button>
        <a href="index.php"><button type="button" style="background:#6c757d;">Reset</button></a>
    </form>
</div>

<!-- Tabel -->
<table>
    <tr>
        <tr>
        <th>No</th>
        <th><?= sort_link("id", "ID", $sort_by, $order, $search) ?></th>
        <th>Foto</th>
        <th><?= sort_link("nama", "Nama", $sort_by, $order, $search) ?></th>
        <th>Email</th>
        <th><?= sort_link("umur", "Umur", $sort_by, $order, $search) ?></th>
        <th>Gender</th>
        <th><?= sort_link("status", "Status", $sort_by, $order, $search) ?></th>
        <th>Hobi</th>
        <th>Aksi</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>$no</td>
            <td>{$row['id']}</td>
                <td>";
                if ($row['foto'])
                    echo "<img src='uploads/{$row['foto']}' onclick='showModal(\"uploads/{$row['foto']}\")'>";
                else
                    echo "❌";
                echo "</td>
                <td>{$row['nama']}</td>
                <td>{$row['email']}</td>
                <td>{$row['umur']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['status']}</td>
                <td>{$row['hobi']}</td>
                <td>
                    <a href='update.php?id={$row['id']}'><button>✏️ Edit</button></a>
                    <a href='?delete={$row['id']}' onclick='return confirm(\"Yakin hapus data ini?\")'>
                        <button style=\"background:#dc3545;\">🗑 Hapus</button>
                    </a>
                </td>
            </tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='9'>Tidak ada data ditemukan</td></tr>";
    }
    ?>
</table>

<!-- Modal -->
<div id="fotoModal" class="modal" onclick="closeModal()">
    <span class="close">&times;</span>
    <img id="modalImg" src="">
</div>

<script>
function showModal(src) {
    const modal = document.getElementById('fotoModal');
    const modalImg = document.getElementById('modalImg');
    modalImg.src = src;
    modal.style.display = "flex";
}
function closeModal() {
    document.getElementById('fotoModal').style.display = "none";
}
</script>

</body>
</html>

<?php $conn->close(); ?>
