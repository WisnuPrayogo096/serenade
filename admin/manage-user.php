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
                    <h2 class="text-xl font-semibold">Daftar User</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">User</th>
                                <th class="p-3 text-left">Registrasi</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-center">Validasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("
                            SELECT
                                id,
                                name,
                                created_at,
                                status
                            FROM 
                                user;
                            ");

                            $no = 1;
                            while($user = $result->fetch_assoc()) {
                                if ($user['status'] == 'active') {
                                    $class = 'text-green-500 font-semibold';
                                    $text = 'Active';
                                } else {
                                    $class = 'text-red-500 font-semibold';
                                    $text = 'Inactive';
                                }
                                $dateRegist = date('d-m-y H:i:s', strtotime($user['created_at']));
                                
                                echo "<tr class='border-b'>
                                <td class='p-3'>{$no}</td>
                                <td class='p-3'>". htmlspecialchars($user['name']) ."</td>
                                <td class='p-3'>". htmlspecialchars($dateRegist) ."</td>
                                <td class='p-3'><span class='". htmlspecialchars($class) ."'>". htmlspecialchars($text) ."</span></td>
                                <td class='p-3 text-center'>
                                    <div class='flex justify-center space-x-2'>
                                        <button onclick=\"openVerifUserModal('{$user['id']}', '{$user['name']}')\"
                                            class='text-green-500 hover:bg-green-100 p-2 rounded'>
                                            <i class='fa-solid fa-check'></i>
                                        </button>
                                        <button onclick=\"openBannedUserModal('{$user['id']}', '{$user['name']}')\"
                                            class='text-red-500 hover:bg-red-100 p-2 rounded'>
                                            <i class='fa-solid fa-ban'></i>
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

    <!-- Modal Confirm User -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96 text-center">
            <div class="mb-4">
                <i class="fa-solid fa-circle-check text-green-500 mx-auto w-10 h-10"></i>
                <h2 class="text-xl font-semibold mb-2">Confirm user</h2>
                <p class="text-gray-600 mb-6">
                    Anda yakin ingin mengaktifkan <span id="nameUser"></span> ?
                </p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('confirmModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                    Batal
                </button>
                <button id="confirmBtn" class="bg-green-500 text-white px-4 py-2 rounded">
                    Yakin
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Confirm User -->
    <div id="disabledModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-96 text-center">
            <div class="mb-4">
                <i class="fa-solid fa-ban text-red-500 mx-auto w-10 h-10"></i>
                <h2 class="text-xl font-semibold mb-2">Disabled user</h2>
                <p class="text-gray-600 mb-6">
                    Anda yakin ingin menonaktifkan <span id="name"></span> ?
                </p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('disabledModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">
                    Batal
                </button>
                <button id="bannedBtn" class="bg-red-500 text-white px-4 py-2 rounded">
                    Yakin
                </button>
            </div>
        </div>
    </div>

    <script src="../dist/modal.js"></script>
</body>

</html>