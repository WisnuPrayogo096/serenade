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
    <style>

    </style>
</head>

<body class="bg-gray-100 text-[#000b58] flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 w-full">
        <?php include 'navbar.php'; ?>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                $query = "SELECT SUM(jumlah_tiket) AS total_terjual FROM purchase_user WHERE status_pembayaran = 'success'";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                $total_terjual = !empty($row['total_terjual'] ? $row['total_terjual'] : '0');
                ?>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center hover:shadow-lg">
                    <div class="bg-blue-100 p-3 rounded-xl mr-4 text-blue-500 hover:scale-105">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500">Total Tiket Terjual</h3>
                        <p class="text-2xl font-bold"><?php echo number_format($total_terjual, 0, ',', ','); ?></p>
                    </div>
                </div>
                <?php
                $query = "SELECT SUM(total_akhir) AS pendapatan FROM purchase_user WHERE status_pembayaran = 'success'";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                $pendapatan = !empty($row['pendapatan']) ? $row['pendapatan'] : 0;
                $show_pendapatan = number_format($pendapatan, 0,',','.');
                ?>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center hover:shadow-lg">
                    <div class="bg-purple-100 p-3 rounded-xl mr-4 text-purple-500 hover:scale-105">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500">Pendapatan</h3>
                        <p class="text-2xl font-bold">Rp. <?php echo $show_pendapatan; ?></p>
                    </div>
                </div>
                <?php
                $query = "SELECT COUNT(status) AS user_aktif FROM user WHERE status = 'active'";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                $user_aktif = $row['user_aktif'];
                $show_user_aktif = number_format($user_aktif, 0,',',',');
                ?>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center hover:shadow-lg">
                    <div class="bg-green-100 p-3 rounded-xl mr-4 text-green-500 hover:scale-105">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500">User Terverifikasi</h3>
                        <p class="text-2xl font-bold"><?php echo $show_user_aktif; ?></p>
                    </div>
                </div>
                <?php
                $query = "SELECT COUNT(*) AS total_event FROM event_konser";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                $total_event = $row['total_event'];
                $show_total_event = number_format($total_event, 0,',',',');
                ?>
                <div class="bg-white p-6 rounded-lg shadow-md flex items-center hover:shadow-lg">
                    <div class="bg-red-100 p-3 rounded-xl mr-4 text-red-500 hover:scale-105">
                        <i class="fa-solid fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500">Total Event</h3>
                        <p class="text-2xl font-bold"><?php echo $show_total_event; ?></p>
                    </div>
                </div>
            </div>

            <!-- Tabel Event -->
            <div class="bg-white mt-8 rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Daftar Event</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">Nama Event</th>
                                <th class="p-3 text-left">Tanggal</th>
                                <th class="p-3 text-left">Lokasi</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("
                            SELECT 
                                ek.*, 
                                ad.id AS id_admin, 
                                ad.name AS nama_admin
                            FROM 
                                event_konser ek
                            LEFT JOIN 
                                admin ad ON ek.created_by = ad.id
                            ");

                            $no = 1;
                            while ($event = $result->fetch_assoc()) {
                                $tanggal = !empty($event['tanggal_mulai']) && $event['tanggal_selesai'] ? date('d M Y', strtotime($event['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($event['tanggal_selesai'])) : '-';
                                $jam = !empty($event['waktu_mulai']) && $event['waktu_selesai'] ? date('H:i', strtotime($event['waktu_mulai'])) . ' - ' . date('H:i', strtotime($event['waktu_selesai'])) : '-';
                                $kuota = !empty($event['kuota']) ? htmlspecialchars($event['kuota']) : '-';
                                $status_detail = $event['status'] == 1 ? 'Active' : 'Not Active'; 
                                $toggleIcon = $event['status'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                                $toggleClass = $event['status'] == 1 ? '' : 'active';
                                
                                echo "<tr class='border-b'>
                                    <td class='p-3'>{$no}</td>
                                    <td class='p-3'>{$event['nama_event']}</td>
                                    <td class='p-3'>{$tanggal}</td>
                                    <td class='p-3'>{$event['lokasi_event']}</td>
                                    <td class='p-3 text-center'>
                                        <button class='toggle-btn {$toggleClass}' data-id='{$event['id']}' data-status='{$event['status']}' onclick='toggleEvent(this)'>
                                            <i class='fa-solid {$toggleIcon}'></i>
                                        </button>
                                    </td>
                                    <td class='p-3 text-center'>
                                        <div class='flex justify-center space-x-2'>
                                            <button onclick='openDetailEventModal(this)'
                                                class='text-blue-500 hover:bg-blue-100 p-2 rounded'
                                                data-nama-event='". htmlspecialchars($event['nama_event']) ."'
                                                data-lokasi-event='". htmlspecialchars($event['lokasi_event']) ."'
                                                data-jam-event='". $jam ."'
                                                data-tanggal-event='". $tanggal ."'
                                                data-kuota-event='". $kuota ."'
                                                data-status-event='". $status_detail ."'
                                                data-nama-admin='". htmlspecialchars($event['nama_admin']) ."'>
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

    <!-- Modal Detail -->
    <div id="detailEventModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Detail Event Konser</h2>
                <button onclick="closeModal('detailEventModal')" class="text-gray-500">×</button>
            </div>
            <div class="space-y-4">
                <table class="w-full text-left border-collapse border border-gray-300">
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Nama Event</td>
                            <td class="border border-gray-300 px-4 py-2" id="namaEvent"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Lokasi Event</td>
                            <td class="border border-gray-300 px-4 py-2" id="lokasiEvent"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Jam</td>
                            <td class="border border-gray-300 px-4 py-2" id="jam"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Tanggal</td>
                            <td class="border border-gray-300 px-4 py-2" id="tanggal"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Kuota</td>
                            <td class="border border-gray-300 px-4 py-2" id="kuota"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Status</td>
                            <td class="border border-gray-300 px-4 py-2" id="status"></td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold">Dibuat oleh</td>
                            <td class="border border-gray-300 px-4 py-2" id="admin"></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="mt-6 text-right">
                <button onclick="closeModal('detailEventModal')" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    <script src="../dist/modal.js"></script>
    <script src="../dist/btn-admin.js"></script>
</body>

</html>