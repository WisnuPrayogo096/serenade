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
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Daftar Event Konser</h2>
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
                                <th class="p-3 text-left">Lokasi</th>
                                <th class="p-3 text-left">Tanggal</th>
                                <th class="p-3 text-left">Waktu</th>
                                <th class="p-3 text-left">Kuota</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM event_konser");
                            $no = 1;
                            while ($event = $result->fetch_assoc()) {
                                echo "
                                <tr class='border-b'>
                                    <td class='p-3'>{$no}</td>
                                    <td class='p-3'>" . ($event['nama_event'] ?? '-') . "</td>
                                    <td class='p-3'>" . ($event['lokasi_event'] ?? '-') . "</td>
                                    <td class='p-3'>" . 
                                        (!empty($event['tanggal_mulai']) && !empty($event['tanggal_selesai']) 
                                            ? date('d M Y', strtotime($event['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($event['tanggal_selesai'])) 
                                            : '-') . "
                                    </td>
                                    <td class='p-3'>" . 
                                        (!empty($event['waktu_mulai']) && !empty($event['waktu_selesai']) 
                                            ? $event['waktu_mulai'] . ' - ' . $event['waktu_selesai'] 
                                            : '-') . "
                                    </td>
                                    <td class='p-3'>" . ($event['kuota'] ?? '-') . "</td>
                                    <td class='p-3 text-center'>
                                        <div class='flex justify-center space-x-2'>
                                            <button onclick=\"openEditModal(
                                                '{$event['id']}', 
                                                '" . htmlspecialchars($event['nama_event'], ENT_QUOTES) . "',
                                                '" . htmlspecialchars($event['lokasi_event'], ENT_QUOTES) . "',
                                                '{$event['img_banner']}'
                                            )\" class='text-green-500 hover:bg-green-100 p-2 rounded'>
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <button onclick=\"openDeleteModal('{$event['id']}', '{$event['nama_event']}')\" class='text-red-500 hover:bg-red-100 p-2 rounded'>
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

    <!-- Modal Tambah Event -->
    <div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Tambah Event</h2>
                <button onclick="closeModal('tambahModal')" class="text-gray-500"></button>
            </div>
            <form method="POST" action="backend/tambah-event.php" enctype="multipart/form-data">
                <input type="hidden" name="admin_id" value="<?php echo $_SESSION['admin_id']; ?>" />
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_event" class="w-full border p-2 rounded" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_event" class="w-full border p-2 rounded" required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Upload Gambar</label>
                    <input type="file" name="img_banner" class="w-full border p-2 rounded" accept="image/*" />
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

    <!-- Modal Edit Event -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Edit Event</h2>
                <button onclick="closeModal('editModal')" class="text-gray-500">&times;</button>
            </div>
            <form method="POST" action="backend/edit-event.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId" />
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_event" id="editNamaEvent" class="w-full border p-2 rounded"
                        required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_event" id="editLokasiEvent" class="w-full border p-2 rounded"
                        required />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Upload Gambar</label>
                    <input type="file" name="img_banner" class="w-full border p-2 rounded" accept="image/*" />
                </div>
                <div class="mb-4 flex justify-center">
                    <img id="currentImage" class="w-64 h-32 object-cover" />
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
                <h2 class="text-xl font-semibold mb-2">Hapus Event</h2>
                <p class="text-gray-600 mb-6">
                    Anda yakin ingin menghapus event <span id="deleteEventName"></span> ?
                </p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('hapusModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                    Batal
                </button>
                <button id="confirmDeleteButton" class="bg-red-500 text-white px-4 py-2 rounded">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script src="../dist/modal.js"></script>
</body>

</html>