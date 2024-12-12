<?php
require '../../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['nama_bank']);
    $norek = trim($_POST['norek']);

    $sql = "INSERT INTO payment (name, norek) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $name, $norek);

    if ($stmt->execute()) {
        echo "<script>alert('Nomor rekening berhasil ditambahkan!'); window.location.href='../payment.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='../payment.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>