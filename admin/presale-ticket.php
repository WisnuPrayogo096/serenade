<?php
session_start();
require 'backend/get-event-konser.php';
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
            <!-- Tabel Event -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Daftar Presale Ticket</h2>
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
                                <th class="p-3 text-left">Nama Event</th>
                                <th class="p-3 text-left">List Day</th>
                                <th class="p-3 text-left">No Presale</th>
                                <th class="p-3 text-left">Harga</th>
                                <th class="p-3 text-left">Kuota</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("
                                SELECT p.id, e.nama_event, d.no_day, p.no_presale, p.harga_ticket, p.kuota_ticket 
                                FROM presale_ticket p JOIN event_konser e ON p.id_event = e.id 
                                JOIN day_event d ON p.id_day = d.id ORDER BY p.id ASC
                            ");

                            $no = 1;
                            while ($presale = $result->fetch_assoc()) {
                                echo "
                                    <tr class='border-b'>
                                        <td class='p-3'>{$no}</td>
                                        <td class='p-3'>{$presale['nama_event']}</td>
                                        <td class='p-3'>Day {$presale['no_day']}</td>
                                        <td class='p-3'>Presale {$presale['no_presale']}</td>
                                        <td class='p-3'>Rp {$presale['harga_ticket']}</td>
                                        <td class='p-3'>{$presale['kuota_ticket']}</td>
                                        <td class='p-3 text-center'>
                                            <div class='flex justify-center space-x-2'>
                                                <button
                                                    onclick=\"openEditModalPresale(
                                                        '{$presale['id']}',
                                                        '{$presale['no_presale']}',
                                                        '{$presale['harga_ticket']}',
                                                        '{$presale['kuota_ticket']}'
                                                    )\"
                                                    class='text-green-500 hover:bg-green-100 p-2 rounded'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                                <button onclick=\"openDeleteModalPresale('{$presale['id']}', '{$presale['no_presale']}')\" class='text-red-500 hover:bg-red-100 p-2 rounded'>
                                                    <i class='fa-solid fa-trash'></i>
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

    <!-- Modal Tambah -->
    <div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Tambah Presale</h2>
                <button onclick="closeModal('tambahModal')" class="text-gray-500"></button>
            </div>
            <form method="POST" action="backend/tambah-presale.php">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                    <select id="eventSelect" name="event_id" class="w-full border p-2 rounded" required>
                        <option value="" disabled selected>Pilih event</option>
                        <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id']; ?>"><?= htmlspecialchars($event['nama_event']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">List Day <span class="text-red-500">*</span></label>
                    <select id="daySelect" name="day_id" class="w-full border p-2 rounded" required>
                        <option value="" disabled selected>Pilih event dahulu</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">No Presale <span class="text-red-500">*</span></label>
                    <input type="number" name="no_presale" class="w-full border p-2 rounded"
                        placeholder="Masukkan nomor presale" min="1" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Harga Presale <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_presale" class="w-full border p-2 rounded"
                        placeholder="Masukkan harga presale" min="1" step="0.01"
                        title="Masukkan angka dengan hingga 2 desimal, contoh: 1000.50" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Kuota <span class="text-red-500">*</span></label>
                    <input type="number" name="kuota" class="w-full border p-2 rounded"
                        placeholder="Masukkan kuota presale" min="1" required />
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

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Edit Event</h2>
                <button onclick="closeModal('editModal')" class="text-gray-500"></button>
            </div>
            <form method="POST" action="backend/edit-presale.php">
                <input type="hidden" name="id" id="presaleId" />
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">No Presale <span class="text-red-500">*</span></label>
                    <input type="number" id="editNoPresale" name="no_presale" class="w-full border p-2 rounded"
                        placeholder="Masukkan nomor presale" min="1" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Harga Presale <span class="text-red-500">*</span></label>
                    <input type="number" id="editHargaPresale" name="harga_presale" class="w-full border p-2 rounded"
                        placeholder="Masukkan harga presale" min="1" step="0.01"
                        title="Masukkan angka dengan hingga 2 desimal, contoh: 1000.50" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Kuota <span class="text-red-500">*</span></label>
                    <input type="number" id="editKuotaPresale" name="kuota" class="w-full border p-2 rounded"
                        placeholder="Masukkan kuota presale" min="1" required />
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editModal')"
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

    <!-- Modal Hapus Event -->
    <div id="hapusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96 text-center">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 mx-auto w-10 h-10"></i>
                <h2 class="text-xl font-semibold mb-2">Hapus Presale</h2>
                <p class="text-gray-600 mb-6">
                    Anda yakin ingin menghapus Presale <span id="deletePresale"></span> ?
                </p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('hapusModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                    Batal
                </button>
                <button id="confirmDeletePresaleButton" class="bg-red-500 text-white px-4 py-2 rounded">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script src="../dist/modal.js"></script>
    <script>
    document.getElementById('eventSelect').addEventListener('change', function() {
        const eventId = this.value;
        const daySelect = document.getElementById('daySelect');

        // Clear previous options
        daySelect.innerHTML = '<option value="" disabled selected>Loading...</option>';

        fetch('backend/get-list-day.php?event_id=' + eventId)
            .then(response => response.json())
            .then(data => {
                daySelect.innerHTML = '<option value="" disabled selected>Pilih Day</option>';
                data.forEach(day => {
                    const option = document.createElement('option');
                    option.value = day.id; // id_day sebagai value
                    option.textContent = `Day ${day.no_day} - ${day.tanggal_perform}`;
                    daySelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                daySelect.innerHTML = '<option value="" disabled selected>Error memuat data</option>';
            });
    });
    </script>
</body>

</html>