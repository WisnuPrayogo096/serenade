<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'], $_POST['name'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO user (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Pengguna berhasil terdaftar. Harap tunggu validasi admin.'); window.location.href='../login.php';</script>";
    } else {
        echo "<script>alert('Error' . $stmt->error); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>