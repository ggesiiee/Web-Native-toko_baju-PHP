<?php
session_start();
include_once "../includes/koneksi.php"; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_baju'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    $gambar = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($gambar);

    if (move_uploaded_file($tmp_name, $target_file)) {
        $sql = "INSERT INTO produk (nama_baju, harga, stok, deskripsi, foto) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "siiss", $nama, $harga, $stok, $deskripsi, $gambar);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Produk berhasil ditambahkan!'); window.location='dashboard.php';</script>";
                exit();
            } else {
                echo "Error saat menyimpan: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "<script>alert('Gagal mengupload gambar. Cek folder uploads!');</script>";
    }
}
?>

<h2>Tambah Produk Baru</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <label>Nama Produk:</label><br>
    <input type="text" name="nama_baju" required><br><br>

    <label>Harga:</label><br>
    <input type="number" name="harga" required><br><br>

    <label>Stok:</label><br>
    <input type="number" name="stok" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi"></textarea><br><br>

    <label>Gambar Produk:</label><br>
    <input type="file" name="foto" required><br><br>

    <button type="submit" name="tambah">Tambah Produk</button>
</form>
