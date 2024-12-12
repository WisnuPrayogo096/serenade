<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nama_event = trim($_POST['nama_event']);
    $lokasi_event = trim($_POST['lokasi_event']);
    $img_banner = null;

    if (empty($nama_event) || empty($lokasi_event)) {
        echo "<script>alert('Nama event dan lokasi tidak boleh kosong!'); window.location.href='../event-konser.php';</script>";
        exit;
    }

    $existingImageQuery = "SELECT img_banner FROM event_konser WHERE id = ?";
    $existingImageStmt = $conn->prepare($existingImageQuery);
    $existingImageStmt->bind_param("i", $id);
    $existingImageStmt->execute();
    $existingImageStmt->bind_result($existingImage);
    $existingImageStmt->fetch();
    $existingImageStmt->close();

    if ($existingImage) {
        $existingImagePath = $uploadDir . $existingImage;
        if (file_exists($existingImagePath)) {
            unlink($existingImagePath);
        }
    }

    if (isset($_FILES['img_banner']) && $_FILES['img_banner']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/img/slider/';
        $fileName = uniqid() . '_' . basename($_FILES['img_banner']['name']);
        $targetFilePath = $uploadDir . $fileName;

        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        $fileMimeType = mime_content_type($_FILES['img_banner']['tmp_name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileMimeType, $allowedMimeTypes) && in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
            $imageInfo = getimagesize($_FILES['img_banner']['tmp_name']);
            if ($imageInfo[0] == 3120 && $imageInfo[1] == 975) {
                if (move_uploaded_file($_FILES['img_banner']['tmp_name'], $targetFilePath)) {
                    $img_banner = $fileName;
                } else {
                    echo "<script>alert('Gagal mengunggah gambar!'); window.location.href='../event-konser.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Resolusi gambar harus 3120x975!'); window.location.href='../event-konser.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('File harus berupa PNG atau JPG/JPEG!'); window.location.href='../event-konser.php';</script>";
            exit;
        }
    }

    $query = "UPDATE event_konser SET nama_event = ?, lokasi_event = ?";
    if ($img_banner) {
        $query .= ", img_banner = ?";
    }
    $query .= " WHERE id = ?";

    $stmt = $conn->prepare($query);

    if ($img_banner) {
        $stmt->bind_param("sssi", $nama_event, $lokasi_event, $img_banner, $id);
    } else {
        $stmt->bind_param("ssi", $nama_event, $lokasi_event, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Event berhasil diperbarui!'); window.location.href='../event-konser.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui event!'); window.location.href='../event-konser.php';</script>";
    }

    $stmt->close();
}
?>