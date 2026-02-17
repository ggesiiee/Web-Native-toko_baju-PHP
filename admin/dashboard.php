<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$sql = "SELECT * FROM produk ORDER BY id_produk DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Toko Baju</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>

<body>
<div style="padding: 20px;">
    <a href="tambah_produk.php" style="
        background-color: #28a745; 
        color: white; 
        padding: 12px 20px; 
        text-decoration: none; 
        border-radius: 5px; 
        font-weight: bold;
        display: inline-block;
        margin-bottom: 20px;
    ">
        + Tambah Produk Baru
    </a>
</div>

<div class="product-grid">
 <?php while($row = mysqli_fetch_assoc($result)): ?>

 <div class="product-card">
  <img src="../admin/uploads/<?= $row['foto']; ?>" alt="<?= $row['nama_baju']; ?>">

  <h3><?= $row['nama_baju']; ?></h3>
  <p class="price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
  <p class="description"><?= substr($row['deskripsi'], 0, 100); ?></p>
  <button>Lihat Detail</button>

 <div class="product-actions">
  <a href="edit_produk.php?id_produk=<?= $row['id_produk']; ?>" class="button-edit">Edit</a>
  <a href="delete_produk.php?id_produk=<?= $row['id_produk']; ?>" 
	  onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')" 
	  class="button-delete">Hapus</a>
  </div>
 </div>

 <?php endwhile; ?>
 <br><br>
</div>

<div style="text-align: center; padding: 20px;">
    <a href="../logout.php" class="button-delete" style="text-decoration: none;">Logout</a>
</div>
 </body>