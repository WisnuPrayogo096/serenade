<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $no_presale = $_POST['no_presale'];
    $harga_ticket = $_POST['harga_presale'];
    $kuota_ticket = $_POST['kuota'];

    // Update query
    $query = "UPDATE presale_ticket 
            SET no_presale = ?, harga_ticket = ?, kuota_ticket = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiii', $no_presale, $harga_ticket, $kuota_ticket, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data presale berhasil diperbarui!');</script>";

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
            echo "<script>alert('Data event konser berhasil diperbarui!'); window.location.href='../presale-ticket.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data event konser!'); window.location.href='../presale-ticket.php';</script>";
        }        
    } else {
        echo "<script>alert('Gagal memperbarui data presale!'); window.location.href='../presale-ticket.php';</script>";
    }

    $conn->close();
}
?>