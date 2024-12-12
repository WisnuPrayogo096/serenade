<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Anda belum login, silahkan login dahulu!'); window.location.href='login.php';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serenade - Riwayat Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <link rel="stylesheet" href="dist/style-dashboard.css" />
</head>

<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php' ?>

    <main class="mt-10 w-full">
        <div class="p-6">
            <div class="bg-white mt-8 rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Riwayat transaksi</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">Event</th>
                                <th class="p-3 text-left">Day</th>
                                <th class="p-3 text-left">Presale</th>
                                <th class="p-3 text-left">Kode Tiket</th>
                                <th class="p-3 text-left">Total bayar</th>
                                <th class="p-3 text-left">Payment</th>
                                <th class="p-3 text-left">Status Bayar</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                                SELECT 
                                    pu.id AS purchase_id, 
                                    pu.id_user, 
                                    pu.id_event, 
                                    ek.nama_event, 
                                    pu.id_day, 
                                    de.no_day, 
                                    pu.id_presale, 
                                    pt.no_presale, 
                                    pu.kode_ticket, 
                                    pu.jumlah_tiket, 
                                    pu.total, 
                                    pu.kode_unik, 
                                    pu.total_akhir, 
                                    pu.ref_id, 
                                    pu.status_pembayaran, 
                                    pu.id_payment, 
                                    pu.qrcode, 
                                    py.name
                                FROM 
                                    purchase_user pu
                                LEFT JOIN 
                                    event_konser ek ON pu.id_event = ek.id
                                LEFT JOIN 
                                    day_event de ON pu.id_day = de.id
                                LEFT JOIN 
                                    presale_ticket pt ON pu.id_presale = pt.id
                                LEFT JOIN 
                                    payment py ON pu.id_payment = py.id
                                WHERE 
                                    pu.id_user = ?
                            ");
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();                        
                            
                            $no = 1;
                            while ($recent = $result->fetch_assoc()) {
                                $class_status = ($recent['status_pembayaran'] == 'success') ? 'text-green-500' : 'text-red-500';
                                $hover_button = ($recent['status_pembayaran'] == 'success') ? 'hover:bg-blue-100' : '';
                                $disabled_button = ($recent['status_pembayaran'] == 'success') ? '' : 'opacity-50 cursor-not-allowed';
                                
                                $payment = !empty($recent['name']) ? htmlspecialchars($recent['name']) : '-';
                                $total_bayar = "Rp " . number_format($recent['total_akhir'], 0, ',', '.');
                                $qrcode = !empty($recent['qrcode']) ? 'assets/img/qrcode/' . htmlspecialchars($recent['qrcode']) : '';
                                $barcode = ($recent['status_pembayaran'] == 'success') ? 'data-qrcode="'. $qrcode .'"' : '';
                                $modal = ($recent['status_pembayaran'] == 'success') ? 'onclick="openQrcodeModal(this)"' : '';
                                
                                echo "<tr class='border-b'>
                                <td class='p-3'>{$no}</td>
                                <td class='p-3'>". htmlspecialchars($recent['nama_event']) ."</td>
                                <td class='p-3'>Day ". htmlspecialchars($recent['no_day']) ."</td>
                                <td class='p-3'>Presale ". htmlspecialchars($recent['no_presale']) ."</td>
                                <td class='p-3'>". htmlspecialchars($recent['kode_ticket']) ."</td>
                                <td class='p-3'>". $total_bayar ."</td>
                                <td class='p-3'>". $payment ."</td>
                                <td class='p-3 ". $class_status ."'>". ucfirst(htmlspecialchars($recent['status_pembayaran'])) ."</td>
                                <td class='p-3 text-center'>
                                    <div class='flex justify-center space-x-2'>
                                        <button ". $modal ."
                                            class='text-blue-500 p-2 rounded ". $hover_button . $disabled_button ."'
                                            ". $barcode .">
                                            <i class='fa-solid fa-qrcode'></i>
                                        </button>
                                        <button
                                        onclick='openDetailTiketModal(this)'
                                        class='text-blue-500 p-2 rounded hover:bg-blue-100'
                                            data-nama-event='". htmlspecialchars($recent['nama_event']) ."'
                                            data-no-day='". htmlspecialchars($recent['no_day']) ."'
                                            data-no-presale='". htmlspecialchars($recent['no_presale']) ."'
                                            data-kode-ticket='". htmlspecialchars($recent['kode_ticket']) ."'
                                            data-jumlah-ticket='". htmlspecialchars($recent['jumlah_tiket']) ."'
                                            data-total='". htmlspecialchars($recent['total']) ."'
                                            data-kode-unik='". htmlspecialchars($recent['kode_unik']) ."'
                                            data-total-akhir='". htmlspecialchars($recent['total_akhir']) ."'
                                            data-ref-id='". htmlspecialchars($recent['ref_id']) ."'
                                            data-nama-payment='". $payment ."'
                                            data-status-bayar='". ucfirst(htmlspecialchars($recent['status_pembayaran'])) ."'>
                                            <i class='fa-regular fa-eye'></i>
                                        </button>
                                    </div>
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

    <!-- modal detail -->
    <div id="detailTiketModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Detail transaksi</h2>
                <button onclick="closeModal('detailTiketModal')" class="text-gray-500">×</button>
            </div>
            <div class="space-y-4">
                <table class="w-full text-left border-collapse border border-gray-300">
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Nama Event</td>
                            <td class="border border-gray-300 px-4 py-2" id="namaEvent"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">No Day</td>
                            <td class="border border-gray-300 px-4 py-2" id="noDay"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">No Presale</td>
                            <td class="border border-gray-300 px-4 py-2" id="noPresale"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Kode Ticket</td>
                            <td class="border border-gray-300 px-4 py-2" id="kodeTicket"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Jumlah Tiket</td>
                            <td class="border border-gray-300 px-4 py-2" id="jumlahTicket"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Total Harga Tiket</td>
                            <td class="border border-gray-300 px-4 py-2" id="total"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Kode Unik</td>
                            <td class="border border-gray-300 px-4 py-2" id="kodeUnik"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Total Pembayaran</td>
                            <td class="border border-gray-300 px-4 py-2" id="totalAkhir"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Ref ID</td>
                            <td class="border border-gray-300 px-4 py-2" id="refId"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Payment</td>
                            <td class="border border-gray-300 px-4 py-2" id="namaPayment"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Status Bayar</td>
                            <td class="border border-gray-300 px-4 py-2" id="statusBayar"></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="mt-6 text-right">
                <button onclick="closeModal('detailTiketModal')" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>

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

    <script src="dist/btn-user.js"></script>
    <script src="dist/modal.js"></script>
</body>

</html>