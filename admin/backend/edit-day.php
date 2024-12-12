<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $event_id = intval($_POST['event_id']);
    $event_day = intval($_POST['event_day']);
    $tanggal = trim($_POST['tanggal']);
    $jam_mulai = trim($_POST['jam_mulai']);
    $jam_selesai = trim($_POST['jam_selesai']);
    $deskripsi = trim($_POST['deskripsi']);
    $img_day = null;

    if (empty($event_id) || empty($event_day) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai) || empty($deskripsi)) {
        echo "<script>alert('Field harus diisi tidak boleh kosong!'); window.location.href='../day-event.php';</script>";
        exit;
    }

    if (isset($_FILES['img_day']) && $_FILES['img_day']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/img/content/';
        $fileName = uniqid() . '_' . basename($_FILES['img_day']['name']);
        $targetFilePath = $uploadDir . $fileName;

        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        $fileMimeType = mime_content_type($_FILES['img_day']['tmp_name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileMimeType, $allowedMimeTypes) && in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
            $imageInfo = getimagesize($_FILES['img_day']['tmp_name']);
            if ($imageInfo[0] == 700 && $imageInfo[1] == 700) {
                if (move_uploaded_file($_FILES['img_day']['tmp_name'], $targetFilePath)) {
                    $img_day = $fileName;
                } else {
                    echo "<script>alert('Gagal mengunggah gambar!'); window.location.href='../day-event.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Resolusi gambar harus 700x700!'); window.location.href='../day-event.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('File harus berupa PNG atau JPG/JPEG!'); window.location.href='../day-event.php';</script>";
            exit;
        }
    }

    $query = "UPDATE day_event SET id_event = ?, no_day = ?, tanggal_perform = ?, jam_mulai = ?, jam_selesai = ?, deskripsi = ?";
    if ($img_day) {
        $query .= ", img_day = ?";
    }
    $query .= " WHERE id = ?";

    $stmt = $conn->prepare($query);

    if ($img_day) {
        $stmt->bind_param("iisssssi", $event_id, $event_day, $tanggal, $jam_mulai, $jam_selesai, $deskripsi, $img_day, $id);
    } else {
        $stmt->bind_param("iissssi", $event_id, $event_day, $tanggal, $jam_mulai, $jam_selesai, $deskripsi, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Event Day berhasil diperbarui!');</script>";
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
            echo "<script>alert('Data event konser berhasil diperbarui!'); window.location.href='../day-event.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data event konser!'); window.location.href='../day-event.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal memperbarui Event Day!'); window.location.href='../day-event.php';</script>";
    }

    $stmt->close();
}
?>