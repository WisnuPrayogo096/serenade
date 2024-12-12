<?php
session_start();
require '../../koneksi.php';

if (!isset($_SESSION['admin_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda belum login.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmtDelete = $conn->prepare("DELETE FROM presale_ticket WHERE id = ?");
    $stmtDelete->bind_param('i', $id);

    if ($stmtDelete->execute()) {
        $update_event_query = "
        UPDATE event_konser ek
        SET 
            ek.waktu_mulai = (
                SELECT MIN(de.jam_mulai)
                FROM day_event de
                WHERE de.id_event = ek.id
            ),
            ek.waktu_selesai = (
                SELECT MAX(de.jam_selesai)
                FROM day_event de
                WHERE de.id_event = ek.id
            ),
            ek.tanggal_mulai = (
                SELECT MIN(de.tanggal_perform)
                FROM day_event de
                WHERE de.id_event = ek.id
            ),
            ek.tanggal_selesai = (
                SELECT MAX(de.tanggal_perform)
                FROM day_event de
                WHERE de.id_event = ek.id
            ),
            ek.kuota = (
                SELECT SUM(pt.kuota_ticket)
                FROM presale_ticket pt
                WHERE pt.id_event = ek.id
            )
        WHERE EXISTS (
            SELECT 1
            FROM day_event de
            WHERE de.id_event = ek.id
        )
        AND EXISTS (
            SELECT 1
            FROM presale_ticket pt
            WHERE pt.id_event = ek.id
        );
        ";
        if ($conn->query($update_event_query)) {
            echo json_encode(['status' => 'success', 'message' => 'Data presale berhasil dihapus, data Event Konser berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data presale dihapus, tetapi gagal memperbarui Event Konser.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data.']);
    }

    $stmtDelete->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
$conn->close();
?>