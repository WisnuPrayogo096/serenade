<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['admin_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda belum login.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmtSelect = $conn->prepare("SELECT img_banner FROM event_konser WHERE id = ?");
    $stmtSelect->bind_param('i', $id);
    $stmtSelect->execute();
    $stmtSelect->bind_result($img_banner);
    $stmtSelect->fetch();
    $stmtSelect->close();

    if ($img_banner) {
        $imagePath = "../../assets/img/slider/" . $img_banner;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $stmtDelete = $conn->prepare("DELETE FROM event_konser WHERE id = ?");
    $stmtDelete->bind_param('i', $id);

    if ($stmtDelete->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event dan gambar berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus event.']);
    }

    $stmtDelete->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
$conn->close();
?>