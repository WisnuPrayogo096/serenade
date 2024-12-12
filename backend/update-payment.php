<?php
include '../koneksi.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id_payment = $data['id_payment'] ?? null;
$kode_ticket = $data['kode_ticket'] ?? null;

if ($id_payment && $kode_ticket) {
    $stmt = $conn->prepare("UPDATE purchase_user SET id_payment = ? WHERE kode_ticket = ?");
    $stmt->bind_param("is", $id_payment, $kode_ticket);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}

$conn->close();
?>