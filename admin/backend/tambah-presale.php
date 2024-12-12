<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_event = intval(trim($_POST['event_id']));
    $id_day = intval(trim($_POST['day_id']));
    $no_presale = intval(trim($_POST['no_presale']));
    $harga_ticket = floatval(trim($_POST['harga_presale']));
    $kuota_ticket = intval(trim($_POST['kuota']));

    if ($id_event && $id_day && $no_presale && $harga_ticket && $kuota_ticket) {
        $stmt = $conn->prepare("
            INSERT INTO presale_ticket (id_event, id_day, no_presale, harga_ticket, kuota_ticket)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('iiidi', $id_event, $id_day, $no_presale, $harga_ticket, $kuota_ticket);

        if ($stmt->execute()) {
            echo "<script>alert('Data presale berhasil ditambahkan!');</script>";

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
            echo "<script>alert('Gagal menambahkan data presale: '. $conn->error;); window.location.href='../presale-ticket.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Semua bidang wajib diisi!'); window.location.href='../presale-ticket.php';</script>";
    }
}

$conn->close();
?>