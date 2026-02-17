<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$product_id = $_GET['id_produk'] ?? 0;

$sql_select = "SELECT * FROM produk WHERE id_produk = $product_id";
$result_select = mysqli_query($conn, $sql_select);
$product = mysqli_fetch_assoc($result_select);

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_baju'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    $gambar_lama = $product['foto'];
    $gambar_baru = $_FILES['foto']['name'];
    $target_dir = "uploads/"; 
    $gambar_final = $gambar_lama;

    if (!empty($gambar_baru)) {
        $target_file = $target_dir . basename($gambar_baru);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $gambar_final = $gambar_baru;
        } else {
            echo "Gagal mengupload gambar baru.";
        }
    }

    $sql_update = "UPDATE produk SET
        nama_baju = '$nama_produk',
        harga = '$harga',
        stok = '$stok',
        deskripsi = '$deskripsi',
        foto = '$gambar_final'
        WHERE id_produk = $product_id";
    
    if (mysqli_query($conn, $sql_update)) {
        echo "Produk berhasil diperbarui!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<form action="edit_produk.php?id_produk=<?= $product_id; ?>" method="post" enctype="multipart/form-data">

    <label>Nama Produk:</label>
    <input type="text" name="nama_baju" value="<?= htmlspecialchars($product['nama_baju']); ?>" required>
    <br><br>

    <label>Harga:</label>
    <input type="number" name="harga" value="<?= htmlspecialchars($product['harga']); ?>" required>
    <br><br>

    <label>Stok:</label>
    <input type="number" name="stok" value="<?= htmlspecialchars($product['stok']); ?>" required>
    <br><br>

    <label>Deskripsi:</label>
    <textarea name="deskripsi"><?= htmlspecialchars($product['deskripsi']); ?></textarea>
    <br><br>

    <label>Gambar Produk:</label><br>
    <p>Gambar saat ini: <br>
       <img src="uploads/<?= htmlspecialchars($product['foto']); ?>" width="150" style="border:1px solid #ccc; padding:5px;">
    </p>
    <input type="file" name="foto" accept="image/*">
    <p><small>*Biarkan kosong jika tidak ingin mengubah gambar</small></p>

    <button type="submit">Perbarui Produk</button>
</form>