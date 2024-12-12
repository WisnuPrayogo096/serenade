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
            <!-- Tabel -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Daftar Payment</h2>
                    <button onclick="openModal('tambahModal')"
                        class="bg-[#006A67] text-white text-sm px-4 py-2 rounded-lg hover:opacity-80 flex items-center">
                        <i class="fa-solid fa-plus mr-2"></i>Tambah Data
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">Nama Bank</th>
                                <th class="p-3 text-left">Nomor Rekening</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM payment");

                            $no = 1;
                            while ($payment = $result->fetch_assoc()) {
                                $toggleIcon = $payment['status'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                                $toggleClass = $payment['status'] == 1 ? '' : 'active';
                                echo "
                                <tr class='border-b'>
                                    <td class='p-3'>{$no}</td>
                                    <td class='p-3'>{$payment['name']}</td>
                                    <td class='p-3'>{$payment['norek']}</td>
                                    <td class='p-3 text-center'>
                                        <button class='toggle-btn {$toggleClass}' data-id='{$payment['id']}' data-status='{$payment['status']}' onclick='togglePayment(this)'>
                                            <i class='fa-solid {$toggleIcon}'></i>
                                        </button>
                                    </td>
                                </tr>
                                ";
                                $no++;
                            }
                            
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal Tambah -->
    <div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Tambah Payment Baru</h2>
                <button onclick="closeModal('tambahModal')" class="text-gray-500"></button>
            </div>
            <form method="POST" action="backend/tambah-payment.php">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nama Bank</label>
                    <input type="text" name="nama_bank" class="w-full border p-2 rounded" placeholder="Masukkan nama" />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nomor Rekening</label>
                    <input type="text" name="norek" class="w-full border p-2 rounded" placeholder="Masukkan nomor" />
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('tambahModal')"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../dist/modal.js"></script>
</body>

</html>