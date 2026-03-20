<?php
session_start();
include_once '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$product_id = (int)($_GET['id_produk'] ?? 0);
if ($product_id <= 0) {
    $_SESSION['error_message'] = 'ID produk tidak valid.';
    header("Location: dashboard.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    $_SESSION['error_message'] = 'Produk tidak ditemukan.';
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_order = trim($_POST['tanggal_order']);
    $id_user = (int)($_SESSION['id_users'] ?? 0);

    if ($id_user <= 0) {
        $_SESSION['error_message'] = 'User tidak valid. Login ulang.';
        header("Location: ../index.php");
        exit();
    }

    $errors = [];
    if ($jumlah <= 0) {
        $errors[] = "Jumlah harus lebih dari 0.";
    }
    if ($jumlah > $product['stok']) {
        $errors[] = "Stok tidak mencukupi. Stok tersedia: " . $product['stok'];
    }
    if (empty($tanggal_order)) {
        $errors[] = "Tanggal order tidak boleh kosong.";
    } else {
        $order_date = new DateTime($tanggal_order);
        $current_date = new DateTime();
        if ($order_date < $current_date) {
            $errors[] = "Tanggal order tidak boleh hari ini atau sebelumnya.";
        }
    }

    if (empty($errors)) {
        $new_stok = $product['stok'] - $jumlah;
        $stmt_update = $conn->prepare("UPDATE produk SET stok = ? WHERE id_produk = ?");
        $stmt_update->bind_param("ii", $new_stok, $product_id);
        if (!$stmt_update->execute()) {
            $errors[] = "Gagal update stok: " . $conn->error;
        }
        $stmt_update->close();

        if (empty($errors)) {
            $stmt_insert = $conn->prepare("INSERT INTO orders (id_user, id_produk, jumlah, tanggal_order) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("iiis", $id_user, $product_id, $jumlah, $tanggal_order);
            if ($stmt_insert->execute()) {
                $_SESSION['success_message'] = "Pemesanan berhasil dibuat!";
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Gagal membuat pemesanan: " . $conn->error;
            }
            $stmt_insert->close();
        }
    }

    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pemesanan - <?= htmlspecialchars($product['nama_baju'] ?? 'Produk'); ?></title>

</head>
<body>
    <div>
        <?php if ($success_message): ?>
            <div class="alert-success">
                <?= $success_message ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert-error">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <h2>Pesan Produk: <?= htmlspecialchars($product['nama_baju']); ?></h2>
            <p><strong>Harga:</strong> Rp <?= number_format($product['harga'], 0, ',', '.'); ?></p>
            <p><strong>Stok Tersedia:</strong> <?= $product['stok']; ?></p>

            <input type="hidden" name="id_produk" value="<?= $product['id_produk']; ?>">

            <label>Nama:</label><br>
            <input type="text" name="nama" value="<?= htmlspecialchars($_SESSION['username'] ?? ''); ?>" readonly><br><br>

            <label>Jumlah:</label><br>
            <input type="number" name="jumlah" min="1" max="<?= $product['stok']; ?>" value="1" required><br><br>

            <label>Tanggal Order (pilih tanggal besok atau setelahnya):</label><br>
            <input type="date" name="tanggal_order" required><br><br>

            <button type="submit">Pesan Sekarang</button>
        </form>

        <br>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
