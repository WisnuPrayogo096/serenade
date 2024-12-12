<?php
session_start();
require '../../koneksi.php';
// require 'phpqrcode/qrlib.php';
require 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!isset($_SESSION['admin_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda belum login.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['namaUser']) && isset($_POST['kodeTicket'])) {
    $id = $_POST['id'];
    $namaUser = $_POST['namaUser'];
    $kodeTicket = $_POST['kodeTicket'];

    $stmtCheck = $conn->prepare("SELECT id, kode_ticket FROM purchase_user WHERE id = ? AND kode_ticket = ?");
    $stmtCheck->bind_param('is', $id, $kodeTicket);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID atau Kode Ticket tidak valid.']);
        exit;
    }

    $stmtUpdatePayment = $conn->prepare("UPDATE purchase_user SET status_pembayaran = 'success' WHERE id = ? AND kode_ticket = ?");
    $stmtUpdatePayment->bind_param('is', $id, $kodeTicket);

    if ($stmtUpdatePayment->execute()) {
        $queryQrData = "
            SELECT 
                pu.kode_ticket,
                us.name,
                ek.nama_event,
                de.no_day,
                pt.no_presale,
                pu.status_pembayaran,
                py.name AS payment
            FROM 
                purchase_user pu
            LEFT JOIN 
                user us ON pu.id_user = us.id    
            LEFT JOIN 
                event_konser ek ON pu.id_event = ek.id
            LEFT JOIN 
                day_event de ON pu.id_day = de.id    
            LEFT JOIN 
                presale_ticket pt ON pu.id_presale = pt.id    
            LEFT JOIN 
                payment py ON pu.id_payment = py.id
            WHERE pu.kode_ticket = ?";
        
        $stmtQrData = $conn->prepare($queryQrData);
        $stmtQrData->bind_param('s', $kodeTicket);
        $stmtQrData->execute();
        $resultQrData = $stmtQrData->get_result();
        $qrData = $resultQrData->fetch_assoc();

        if ($qrData) {
            $qrContent = json_encode([
                'kode_ticket' => $qrData['kode_ticket'],
                'name' => $qrData['name'],
                'nama_event' => $qrData['nama_event'],
                'no_day' => $qrData['no_day'],
                'no_presale' => $qrData['no_presale'],
                'status_pembayaran' => $qrData['status_pembayaran'],
                'payment' => $qrData['payment']
            ]);

            // $qrFileName = uniqid('qrcode_', true) . '.png';
            // $qrFilePath = "../../assets/img/qrcode/" . $qrFileName;

            // QRcode::png($qrContent, $qrFilePath, QR_ECLEVEL_L, 10, 2);

            // $stmtUpdateQr = $conn->prepare("UPDATE purchase_user SET qrcode = ? WHERE id = ?");
            // $stmtUpdateQr->bind_param('si', $qrFileName, $id);
            // $stmtUpdateQr->execute();

            // config QR Code
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_H, // Menggunakan tingkat koreksi kesalahan yang tinggi
                'scale' => 10,
            ]);

            $qrDir = "../../assets/img/qrcode/";
            $qrCode = new QRCode($options);
            $qrFileName = uniqid('qrcode_', true) . '.png';
            $qrFilePath = $qrDir . $qrFileName;

            $qrCode->render($qrContent, $qrFilePath);

            $stmtUpdateQr = $conn->prepare("UPDATE purchase_user SET qrcode = ? WHERE id = ?");
            $stmtUpdateQr->bind_param('si', $qrFileName, $id);
            $stmtUpdateQr->execute();

            $queryKuota = "
                SELECT 
                    pu.id_presale,
                    pt.kuota_ticket,
                    pu.jumlah_tiket
                FROM 
                    purchase_user pu
                LEFT JOIN 
                    presale_ticket pt ON pu.id_presale = pt.id
                WHERE pu.kode_ticket = ?";
            
            $stmtKuota = $conn->prepare($queryKuota);
            $stmtKuota->bind_param('s', $kodeTicket);
            $stmtKuota->execute();
            $resultKuota = $stmtKuota->get_result();
            $kuotaData = $resultKuota->fetch_assoc();

            if ($kuotaData) {
                $newKuota = $kuotaData['kuota_ticket'] - $kuotaData['jumlah_tiket'];
                $stmtUpdateKuota = $conn->prepare("UPDATE presale_ticket SET kuota_ticket = ? WHERE id = ?");
                $stmtUpdateKuota->bind_param('ii', $newKuota, $kuotaData['id_presale']);
                $stmtUpdateKuota->execute();
            }

            $updateEventQuery = "
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
                )";

            $conn->query($updateEventQuery);

            echo json_encode(['status' => 'success', 'message' => 'Berhasil tervalidasi dan sudah terbit QRCode.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data untuk QR code tidak ditemukan.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate status pembayaran.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}

$conn->close();
?>