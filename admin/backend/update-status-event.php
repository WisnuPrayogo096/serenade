<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    if (!is_numeric($id) || !is_numeric($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $query = $conn->prepare("UPDATE event_konser SET status = ? WHERE id = ?");
    $query->bind_param("ii", $status, $id);

    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }

    $query->close();
    $conn->close();
}