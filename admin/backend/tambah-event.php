<?php
require '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = trim($_POST['admin_id']);
    $nama_event = trim($_POST['nama_event']);
    $lokasi_event = trim($_POST['lokasi_event']);
    $img_banner = null;

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
                echo "<script>alert('Dimensi gambar harus 3120x975!'); window.location.href='../event-konser.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('File harus berupa PNG atau JPG/JPEG!'); window.location.href='../event-konser.php';</script>";
            exit;
        }
    }

    if (!empty($admin_id) && !empty($nama_event) && !empty($lokasi_event)) {
        $stmt = $conn->prepare("INSERT INTO event_konser (nama_event, lokasi_event, img_banner, created_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nama_event, $lokasi_event, $img_banner, $admin_id);

        if ($stmt->execute()) {
            echo "<script>alert('Event berhasil dibuat!'); window.location.href='../event-konser.php';</script>";
        } else {
            echo "<script>alert('Gagal membuat event!'); window.location.href='../event-konser.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Nama dan lokasi harus diisi!'); window.location.href='../event-konser.php';</script>";
    }
}
?>