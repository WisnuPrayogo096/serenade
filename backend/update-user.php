<?php
session_start();
include '../koneksi.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak valid!');
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Anda belum login, silahkan login terlebih dahulu!');
    }

    $user_id = $_SESSION['user_id'];
    $nama = $_POST['nama'] ?? '';
    $jenis_identitas = $_POST['jenis-identitas'] ?? '';
    $nomor_identitas = $_POST['nomor-identitas'] ?? '';
    $jenis_kelamin = $_POST['jenis-kelamin'] ?? '';
    $usia = $_POST['usia'] ?? 0;
    $nomor_whatsapp = $_POST['nomor-whatsapp'] ?? '';

    if (empty($nama) || empty($jenis_identitas) || empty($nomor_identitas) || empty($jenis_kelamin) || empty($usia)) {
        throw new Exception('Semua field yang wajib harus diisi!');
    }

    $no_identitas = $jenis_identitas . '_' . $nomor_identitas;

    $query = "UPDATE user SET name = ?, no_identitas = ?, jenis_kelamin = ?, usia = ?, telp = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Gagal mempersiapkan statement: ' . $conn->error);
    }
    
    $stmt->bind_param("sssssi", $nama, $no_identitas, $jenis_kelamin, $usia, $nomor_whatsapp, $user_id);

    if (!$stmt->execute()) {
        throw new Exception('Terjadi kesalahan saat memperbarui data: ' . $stmt->error);
    }

    echo json_encode(['status' => 'success', 'message' => 'Data berhasil diperbarui!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}