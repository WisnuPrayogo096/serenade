<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                session_start();
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                echo "<script>alert('Login berhasil!'); window.location.href='../dashboard.php';</script>";
            } else {
                echo "<script>alert('Password salah!'); window.location.href='../index.php';</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!'); window.location.href='../index.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Mohon isi semua kolom!'); window.location.href='../index.php';</script>";
    }
}
?>