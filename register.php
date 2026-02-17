<?php
include_once 'includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['passwords'];
    $role = $_POST['role']; 
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_user = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $check_user->store_result();

    if ($check_user->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan! Silakan pilih nama lain.'); window.history.back();</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, passwords, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='index.php';</script>";
        } else {
            echo "Gagal mendaftar: " . $conn->error;
        }
        $stmt->close();
    }
    $check_user->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css"> </head>
<body>
    <form action="register.php" method="POST">
        <h2>Register</h2>
        
        <label>Username</label><br>
        <input type="text" name="username" required>
        <br><br>

        <label>Password</label><br>
        <input type="password" name="passwords" required>
        <br><br>

        <label>Daftar Sebagai:</label><br>
        <select name="role" required>
            <option value="user">User / Pelanggan</option>
            <option value="admin">Admin / Penjual</option>
        </select>
        <br><br>

        <button type="submit">Register</button>
        <br><br>

        <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>
    </form>
</body>
</html>