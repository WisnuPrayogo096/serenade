<?php
include '../koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, name, password, status FROM user WHERE email = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo "<script>alert('Login berhasil!'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Password salah!'); window.history.back();</script>";
        }
    } else {
        $sql_inactive = "SELECT id, name, password, status FROM user WHERE email = ?";
        $stmt_inactive = $conn->prepare($sql_inactive);
        $stmt_inactive->bind_param('s', $email);
        $stmt_inactive->execute();
        $result_inactive = $stmt_inactive->get_result();

        if ($result_inactive->num_rows > 0) {
            $user_inactive = $result_inactive->fetch_assoc();
            echo "<script>alert('Akun belum tervalidasi oleh admin, mohon bersabar.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Akun tidak ditemukan!'); window.history.back();</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>