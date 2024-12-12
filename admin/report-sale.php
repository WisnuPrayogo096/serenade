<?php
session_start();
require '../koneksi.php';
if (!isset($_SESSION['admin_name'])) {
    echo "<script>alert('Anda belum login!'); window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serenade - Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../dist/style-dashboard-admin.css" />
    <link rel="stylesheet" href="../dist/font-style.css" />
</head>

<body class="bg-gray-100 text-[#000b58] flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 w-full">
        <?php include 'navbar.php'; ?>

        <div class="p-6">
            <!-- Tabel Event -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Laporan Penjualan</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">User</th>
                                <th class="p-3 text-left">Event</th>
                                <th class="p-3 text-left">Kode Tiket</th>
                                <th class="p-3 text-left">Jumlah Tiket</th>
                                <th class="p-3 text-left">Total Bayar</th>
                                <th class="p-3 text-left">Payment</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">QRCode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT 
                                    pu.id AS purchase_id, 
                                    pu.id_user, 
                                    us.name,
                                    pu.id_event, 
                                    ek.nama_event, 
                                    pu.kode_ticket, 
                                    pu.jumlah_tiket, 
                                    pu.total_akhir, 
                                    pu.status_pembayaran, 
                                    pu.id_payment, 
                                    pu.qrcode, 
                                    py.name AS payment
                                FROM 
                                    purchase_user pu
                                LEFT JOIN 
                                    user us ON pu.id_user = us.id
                                LEFT JOIN 
                                    event_konser ek ON pu.id_event = ek.id
                                LEFT JOIN 
                                    payment py ON pu.id_payment = py.id
                                WHERE
                                    pu.status_pembayaran ='success'
                            ");
                            $stmt->execute();
                            $result = $stmt->get_result();                        
                            
                            $no = 1;
                            while($ticket = $result->fetch_assoc()) {
                                $total = "Rp " . number_format($ticket['total_akhir'], 0, ',', '.');
                                $payment = !empty($ticket['payment']) ? htmlspecialchars($ticket['payment']) : '-';
                                $qrcode = !empty($ticket['qrcode']) ? '../assets/img/qrcode/' . htmlspecialchars($ticket['qrcode']) : '';
                                
                                $class_status = ($ticket['status_pembayaran'] == 'success') ? 'text-green-500' : 'text-red-500';
                                $hover_button = ($ticket['status_pembayaran'] == 'success') ? 'hover:bg-blue-100' : '';
                                $disabled_button = ($ticket['status_pembayaran'] == 'success') ? '' : 'opacity-50 cursor-not-allowed';
                                $barcode = ($ticket['status_pembayaran'] == 'success') ? 'data-qrcode="'. $qrcode .'"' : '';
                                $modal = ($ticket['status_pembayaran'] == 'success') ? 'onclick="openQrcodeModal(this)"' : '';
                                
                                echo "<tr class='border-b'>
                                <td class='p-3'>{$no}</td>
                                <td class='p-3'>". ucfirst(htmlspecialchars($ticket['name'])) ."</td>
                                <td class='p-3'>". htmlspecialchars($ticket['nama_event']) ."</td>
                                <td class='p-3'>". htmlspecialchars($ticket['kode_ticket']) ."</td>
                                <td class='p-3'>". htmlspecialchars($ticket['jumlah_tiket']) ." Tiket</td>
                                <td class='p-3'>". $total ."</td>
                                <td class='p-3'>". $payment ."</td>
                                <td class='p-3'><span class='". $class_status ."'>". ucfirst(htmlspecialchars($ticket['status_pembayaran'])) ."</span></td>
                                <td class='p-3 text-center'>
                                    <button ". $modal ."
                                        class='text-blue-500 p-2 rounded ". $hover_button . $disabled_button ."'
                                        ". $barcode .">
                                        <i class='fa-solid fa-qrcode'></i>
                                    </button>
                                </td>
                            </tr>";
                            $no++;
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal QRCode -->
    <div id="qrcodeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96 text-center">
            <div class="mb-4">
                <i class="fas fa-qrcode text-blue-500 mx-auto w-10 h-10"></i>
                <h2 class="text-xl font-semibold mb-2">Detail QRCode</h2>
                <!-- QRCode Image -->
                <div id="qrcodeImageContainer">
                    <img id="qrcodeImage" src="" alt="QRCode" class="mx-auto w-40 h-40" />
                </div>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('qrcodeModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script src="../dist/modal.js"></script>
</body>

</html>