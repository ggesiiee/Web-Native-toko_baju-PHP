<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
 header("Location: ../index.php");
 exit();
}

include_once '../includes/koneksi.php';

if (isset($_GET['id_produk'])) {
 $product_id = $_GET['id_produk'];
 $sql_delete = "DELETE FROM produk WHERE id_produk = ?";

if ($stmt = mysqli_prepare($conn, $sql_delete)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php?status=deleted");
        exit();
    } else {
        echo "Error: Gagal menghapus produk. " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "ID Produk tidak ditemukan.";
}
}

mysqli_close($conn);
?>