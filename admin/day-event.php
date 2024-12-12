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
                    <h2 class="text-xl font-semibold">Daftar Day Event</h2>
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
                                <th class="p-3 text-left">Tanggal Perform</th>
                                <th class="p-3 text-left">Jam</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("
                                SELECT day_event.*, event_konser.nama_event 
                                FROM day_event
                                INNER JOIN event_konser ON day_event.id_event = event_konser.id
                            ");

                            $no = 1;
                            while ($day = $result->fetch_assoc()) {
                                $tanggalPerform = $day['tanggal_perform'] 
                                    ? date_format(date_create($day['tanggal_perform']), "d M Y") 
                                    : "-";

                                $jamMulai = date("H:i", strtotime($day['jam_mulai']));
                                $jamSelesai = date("H:i", strtotime($day['jam_selesai']));
                                $jamPerform = $jamMulai . "-" . $jamSelesai;

                                echo "<tr class='border-b'>
                                    <td class='p-3'>{$no}</td>
                                    <td class='p-3'>". htmlspecialchars($day['nama_event']) ."</td>
                                    <td class='p-3'>Day ". htmlspecialchars($day['no_day']) ."</td>                                    
                                    <td class='p-3'>". htmlspecialchars($tanggalPerform) ."</td>
                                    <td class='p-3'>". htmlspecialchars($jamPerform) ."</td>
                                    <td class='p-3 text-center'>
                                        <div class='flex justify-center space-x-2'>
                                            <button 
                                                onclick='openDetailModal(this)' 
                                                class='text-blue-500 hover:bg-blue-100 p-2 rounded'
                                                data-deskripsi='". htmlspecialchars($day['deskripsi'], ENT_QUOTES, 'UTF-8') ."'>
                                                <i class='fa-regular fa-eye'></i>
                                            </button>
                                            <button 
                                                onclick=\"openEditModalDay(
                                                    '{$day['id']}', 
                                                    '{$day['id_event']}', 
                                                    '{$day['no_day']}',
                                                    '{$day['jam_mulai']}',
                                                    '{$day['jam_selesai']}',
                                                    '{$day['tanggal_perform']}',
                                                    '" . htmlspecialchars($day['deskripsi'], ENT_QUOTES, 'UTF-8') . "', 
                                                    '{$day['img_day']}'
                                                )\" 
                                                class='text-green-500 hover:bg-green-100 p-2 rounded'>
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <button onclick=\"openDeleteModalDay('{$day['id']}', '{$day['no_day']}')\" class='text-red-500 hover:bg-red-100 p-2 rounded'>
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
    <div id="tambahModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white p-8 rounded-lg w-[500px] md:w-[700px]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Tambah Day</h2>
                <button onclick="closeModal('tambahModal')" class="text-gray-500">×</button>
            </div>
            <form method="POST" action="backend/tambah-day.php" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kolom 1 -->
                    <div>
                        <label class="block text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                        <select name="event_id" class="w-full border p-2 rounded" required>
                            <option value="" disabled selected>Pilih Event</option>
                            <?php foreach ($events as $event): ?>
                            <option value="<?= $event['id']; ?>"><?= htmlspecialchars($event['nama_event']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Event Day <span class="text-red-500">*</span></label>
                        <input type="number" name="event_day" class="w-full border p-2 rounded"
                            placeholder="Masukkan day" required />
                    </div>

                    <!-- Kolom 2 -->
                    <div>
                        <label class="block text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_mulai" class="w-full border p-2 rounded" required />
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_selesai" class="w-full border p-2 rounded" />
                    </div>

                    <!-- Kolom 3 -->
                    <div>
                        <label class="block text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" class="w-full border p-2 rounded" required />
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Upload Gambar</label>
                        <input type="file" name="img_day" class="w-full border p-2 rounded" accept="image/*" />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Deskripsi Event Day <span
                            class="text-red-500">*</span></label>
                    <div class="flex mb-2 space-x-2">
                        <button type="button" onclick="formatText('bold', 'deskripsiEventDayTambah')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            <strong>B</strong>
                        </button>
                        <button type="button" onclick="formatText('italic', 'deskripsiEventDayTambah')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            <em>I</em>
                        </button>
                        <button type="button" onclick="formatText('underline', 'deskripsiEventDayTambah')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            <u>U</u>
                        </button>
                        <button type="button" onclick="formatText('paragraph', 'deskripsiEventDayTambah')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            ¶
                        </button>
                    </div>
                    <textarea id="deskripsiEventDayTambah" name="deskripsi" class="w-full border p-2 rounded" rows="5"
                        required></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('tambahModal')"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Deskripsi</h2>
                <button onclick="closeModal('detailModal')" class="text-gray-500">×</button>
            </div>
            <div class="space-y-4">
                <p></p>
            </div>
            <div class="mt-6 text-right">
                <button onclick="closeModal('detailModal')" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
        <div class="bg-white p-8 rounded-lg w-[500px] md:w-[700px]">
            <div class="flex justify-between items-center my-4">
                <h2 class="text-xl font-semibold">Edit Day</h2>
                <button onclick="closeModal('editModal')" class="text-gray-500">×</button>
            </div>
            <form method="POST" action="backend/edit-day.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Kolom 1 -->
                    <div>
                        <label class="block text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                        <select name="event_id" class="w-full border p-2 rounded" id="editNamaEvent" required>
                            <option value="" disabled selected>Pilih Event</option>
                            <?php foreach ($events as $event): ?>
                            <option value="<?= $event['id']; ?>"><?= htmlspecialchars($event['nama_event']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Event Day <span class="text-red-500">*</span></label>
                        <input type="number" id="editNoDay" name="event_day" class="w-full border p-2 rounded"
                            placeholder="Masukkan day" required />
                    </div>

                    <!-- Kolom 2 -->
                    <div>
                        <label class="block text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" id="editJamMulai" name="jam_mulai" class="w-full border p-2 rounded"
                            required />
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" id="editJamSelesai" name="jam_selesai" class="w-full border p-2 rounded" />
                    </div>

                    <!-- Kolom 3 -->
                    <div>
                        <label class="block text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" id="editTanggalDay" name="tanggal" class="w-full border p-2 rounded"
                            required />
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Upload Gambar</label>
                        <input type="file" name="img_day" class="w-full border p-2 rounded" accept="image/*" />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Deskripsi Event Day <span
                            class="text-red-500">*</span></label>
                    <div class="flex mb-2 space-x-2">
                        <button type="button" onclick="formatText('bold', 'deskripsiEventDayEdit')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            <strong>B</strong>
                        </button>
                        <button type="button" onclick="formatText('italic', 'deskripsiEventDayEdit')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            <em>I</em>
                        </button>
                        <button type="button" onclick="formatText('underline', 'deskripsiEventDayEdit')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            <u>U</u>
                        </button>
                        <button type="button" onclick="formatText('paragraph', 'deskripsiEventDayEdit')"
                            class="bg-gray-200 px-3 py-1 rounded">
                            ¶
                        </button>
                    </div>
                    <textarea id="deskripsiEventDayEdit" name="deskripsi" class="w-full border p-2 rounded" rows="5"
                        required></textarea>
                </div>

                <div class="mb-4 flex justify-center">
                    <img id="currentImage" class="w-32 h-32 object-cover" />
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

    <!-- Modal Hapus -->
    <div id="hapusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96 text-center">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 mx-auto w-10 h-10"></i>
                <h2 class="text-xl font-semibold mb-2">Hapus Day Event</h2>
                <p class="text-gray-600 mb-6">
                    Anda yakin ingin menghapus Day <span id="deleteDay"></span> ?
                </p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('hapusModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                    Batal
                </button>
                <button id="confirmDeleteDayButton" class="bg-red-500 text-white px-4 py-2 rounded">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script src="../dist/modal.js"></script>
    <script src="../dist/quill.js"></script>
</body>

</html>