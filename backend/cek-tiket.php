<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_ticket = $_POST['kode_ticket'] ?? '';

    if (empty($kode_ticket)) {
        echo json_encode(['status' => 'error', 'message' => 'Nomor tiket tidak boleh kosong.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT kode_ticket, status_pembayaran, qrcode FROM purchase_user WHERE kode_ticket = ?");
    $stmt->bind_param("s", $kode_ticket);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'data' => [
                'kode_ticket' => $data['kode_ticket'],
                'status_pembayaran' => $data['status_pembayaran'],
                'qrcode' => $data['qrcode']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'not_found', 'message' => 'Tiket tidak ditemukan.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>