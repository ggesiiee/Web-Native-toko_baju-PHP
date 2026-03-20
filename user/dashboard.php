<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$sql = "SELECT * FROM produk ORDER BY id_produk DESC";
$result = mysqli_query($conn, $sql);
?>

<head>
    <meta charset="UTF-8">
    <title>Toko Baju</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<div class="product-grid">
 <?php while($row = mysqli_fetch_assoc($result)): ?>
 <div class="product-card">
  <img src="../admin/uploads/<?= $row['foto']; ?>" alt="<?= $row['nama_baju']; ?>">

  <h3><?= $row['nama_baju']; ?></h3>
  <p class="price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
  <p class="description"><?= substr($row['deskripsi'], 0, 100); ?></p>
  <button>Lihat Detail</button>

<div class="product-actions">
  <a href="pemesanan.php?id_order=<?= $row['id_order']; ?>" class="button-edit">Pesan Sekarang</a>
    </div>
 </div>
 <?php endwhile; ?>
</div>

<div style="text-align: center; padding: 20px;">
    <a href="../logout.php" class="button-delete" style="text-decoration: none;">Logout</a>
</div>
