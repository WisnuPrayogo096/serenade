<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['admin_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda belum login.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmtUpdate = $conn->prepare("UPDATE user SET status = 'active' WHERE id = ?");
    $stmtUpdate->bind_param('i', $id);

    if ($stmtUpdate->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Status user berhasil diubah menjadi active.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status user.']);
    }

    $stmtUpdate->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
$conn->close();
?>