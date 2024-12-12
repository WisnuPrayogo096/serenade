<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Anda belum login, silahkan login dahulu!'); window.location.href='login.php';</script>";
    exit;
}

if(isset($_POST['event_id']) && isset($_POST['day_event_id']) && 
   isset($_POST['presale_ticket_id']) && isset($_POST['nama_event']) && 
   isset($_POST['no_day']) && isset($_POST['no_presale']) && 
   isset($_POST['jumlah_tiket']) && isset($_POST['harga_ticket'])) {
    
    $event_id = intval($_POST['event_id']);
    $day_event_id = intval($_POST['day_event_id']);
    $presale_ticket_id = intval($_POST['presale_ticket_id']);
    $nama_event = isset($_POST['nama_event']) ? ($_POST['nama_event']) : 'Unknown Event';
    $no_day = isset($_POST['no_day']) ? ($_POST['no_day']) : 'Unknown Day';
    $no_presale = isset($_POST['no_presale']) ? ($_POST['no_presale']) : 'Unknown Ticket';
    $jumlah_tiket = intval($_POST['jumlah_tiket']);
    $harga_ticket = floatval($_POST['harga_ticket']);

    $subtotal = $harga_ticket * $jumlah_tiket;
    $pajak_daerah = $subtotal * 0.1;
    $biaya_layanan = $subtotal * 0.02;
    $total = $subtotal + $pajak_daerah + $biaya_layanan;
} else {
    echo "<script>
        alert('Data tidak lengkap, silakan lengkapi data Anda!');
    </script>";
    exit();
}


$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $no_identitas = isset($user['no_identitas']) && !empty($user['no_identitas']) ? $user['no_identitas'] : "_";
    list($jenis_identitas, $nomor_identitas) = explode('_', $no_identitas, 2);

    $jenis_identitas = $jenis_identitas ?: '';
    $nomor_identitas = $nomor_identitas ?: '';

    $user['name'] = $user['name'] ?? '';
    $user['jenis_kelamin'] = $user['jenis_kelamin'] ?? '';
    $user['usia'] = $user['usia'] ?? '';
    $user['telp'] = $user['telp'] ?? '';
    $user['email'] = $user['email'] ?? '';
} else {
    echo "<script>
        alert('Data pengguna tidak ditemukan!');
        window.location.href = 'login.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serenade - Confirmation Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <link rel="stylesheet" href="dist/style-dashboard.css" />
</head>

<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php' ?>

    <main
        class="container mx-auto mt-16 px-4 sm:px-6 md:px-8 lg:px-10 sm:mt-20 md:mt-24 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
        <!-- Kolom Kiri-->
        <div class="rounded-xl shadow-md bg-white p-5">
            <div id="timerSection" class="p-4 bg-yellow-400 rounded-lg mb-8 text-center">
                <p>
                    Segera lengkapi data dirimu sebelum <span id="timer" class="font-bold"></span>
                </p>
            </div>
            <div class="flex flex-row gap-4 items-center">
                <div class="p-2 bg-gray-100 text-[#000B58] rounded-xl text-sm text-center">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#003161]">
                    Data Pemesan
                </h1>
            </div>
            <div class="border-dashed-infoticket my-4"></div>
            <form class="space-y-4" method="POST">
                <div class="space-y-2">
                    <label for="nama" class="block font-normal text-gray-700">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama"
                        class="w-full p-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003161]"
                        value="<?php echo htmlspecialchars($user['name']); ?>" required />
                </div>

                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex-none space-y-2">
                        <label for="jenis-identitas" class="block font-normal text-gray-700">Identitas <span
                                class="text-red-500">*</span></label>
                        <select id="jenis-identitas" name="jenis-identitas"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003161]">
                            <option value="" <?php echo ($jenis_identitas === '' ? 'disabled selected' : 'hidden'); ?>>
                                Pilih
                            </option>
                            <option value="KTP" <?php echo ($jenis_identitas === 'KTP' ? 'selected' : ''); ?>>KTP
                            </option>
                            <option value="SIM" <?php echo ($jenis_identitas === 'SIM' ? 'selected' : ''); ?>>SIM
                            </option>
                            <option value="Passport" <?php echo ($jenis_identitas === 'Passport' ? 'selected' : ''); ?>>
                                Passport</option>
                        </select>
                    </div>
                    <div class="flex-1 space-y-2">
                        <label for="nomor-identitas" class="block font-normal text-gray-700">Nomor Identitas <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nomor-identitas" name="nomor-identitas"
                            class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003161]"
                            value="<?php echo htmlspecialchars($nomor_identitas); ?>" required />
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="block font-normal text-gray-700">Jenis Kelamin <span
                            class="text-red-500">*</span></span>
                    <div class="flex gap-4 mt-2 ml-5">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="jenis-kelamin" value="Laki-laki"
                                <?php echo ($user['jenis_kelamin'] === 'Laki-laki' ? 'checked' : ''); ?> required />
                            Laki-laki
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="jenis-kelamin" value="Perempuan"
                                <?php echo ($user['jenis_kelamin'] === 'Perempuan' ? 'checked' : ''); ?> required />
                            Perempuan
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="usia" class="block font-normal text-gray-700">Usia <span
                            class="text-red-500">*</span></label>
                    <input type="number" id="usia" name="usia"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003161]"
                        value="<?php echo htmlspecialchars($user['usia']); ?>" required min="0" />
                </div>

                <div class="space-y-2">
                    <label for="nomor-whatsapp" class="block font-normal text-gray-700">Nomor WhatsApp</label>
                    <input type="text" id="nomor-whatsapp" name="nomor-whatsapp"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003161]"
                        value="<?php echo htmlspecialchars($user['telp']); ?>" />
                </div>

                <div class="flex flex-row justify-end mt-7 mx-3">
                    <button type="submit"
                        class="px-5 py-3 rounded-lg bg-[#000B58] text-white hover:opacity-90 transition-all">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Detail Event -->
        <div class="rounded-xl shadow-md bg-white p-4 sm:p-5 rounded-lg self-start">
            <div class="flex flex-row gap-4 items-center">
                <div class="p-2 bg-gray-100 text-[#000B58] rounded-xl text-sm text-center">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#003161]">
                    Rincian Pemesanan
                </h1>
            </div>
            <div class="border-dashed-infoticket my-4"></div>
            <div class="flex flex-row justify-between items-center px-24">
                <div class="space-y-2 flex flex-col block font-normal text-gray-700 text-left">
                    <label>
                        Nama Event :
                    </label>
                    <label>
                        Day Event :
                    </label>
                    <label>
                        Jenis Tiket :
                    </label>
                    <label>
                        Jumlah tiket :
                    </label>
                </div>
                <div class="space-y-2 flex flex-col block font-semibold text-gray-700 text-right">
                    <p><?php echo $nama_event; ?></p>
                    <p>Day <?php echo $no_day; ?></p>
                    <p>Presale <?php echo $no_presale; ?></p>
                    <p><?php echo $jumlah_tiket; ?> Tiket</p>
                </div>
            </div>
            <div class="w-full max-w-[500px] mx-auto rounded-lg">
                <div class="border-dashed my-4"></div>
                <div class="flex flex-row justify-between px-4">
                    <div class="font-medium text-gray-700 space-y-2">
                        <p>Subtotal</p>
                        <p>Pajak Daerah</p>
                        <p>Biaya Layanan</p>
                    </div>
                    <div class="font-semibold text-gray-700 space-y-2 text-right">
                        <p>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                        <p>Rp <?php echo number_format($pajak_daerah, 0, ',', '.'); ?></p>
                        <p>Rp <?php echo number_format($biaya_layanan, 0, ',', '.'); ?></p>
                    </div>
                </div>
                <div class="border-dashed my-4"></div>
                <div class="flex flex-row justify-between px-4">
                    <div class="font-medium text-gray-700 space-y-2">
                        <p>Total</p>
                    </div>
                    <div class="font-semibold text-xl text-gray-700 space-y-2 text-right">
                        <p>Rp <?php echo number_format($total, 0, ',', '.'); ?></p>
                    </div>
                </div>
                <button onclick="openModal('confirmModal')"
                    class="mt-4 sm:mt-6 w-full bg-[#000B58] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-2xl hover:opacity-90 transition-all text-sm sm:text-base">
                    Lanjutkan
                </button>
            </div>
        </div>
    </main>

    <!-- Section Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="flex justify-between items-center p-4 border-b">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-blue-500">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h2 class="text-lg font-semibold">Informasi Tiket</h2>
                </div>
                <button type="button" onclick="closeModal('confirmModal')" class="text-gray-500 hover:text-gray-700">
                    &times;
                </button>
            </div>

            <!-- Modal Confirm -->
            <form class="p-6" method="POST" action="backend/purchase-user.php">
                <p class="text-center text-gray-600 mb-4">
                    Pastikan data kamu sudah benar yaa!
                </p>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="font-medium">Nama</span>
                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Email</span>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Total</span>
                        <span class="font-bold text-[#000B58]">Rp
                            <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <input type="hidden" name="dataPembelian"
                        value="<?php echo $user_id; ?>|<?php echo $event_id; ?>|<?php echo $day_event_id; ?>|<?php echo $presale_ticket_id; ?>|<?php echo $jumlah_tiket; ?>|<?php echo $total; ?>">
                </div>

                <div class="bg-blue-50 p-3 rounded-lg mb-4 text-sm text-gray-700">
                    <ol class="list-decimal list-inside">
                        <li>
                            Invoice dan e-ticket akan muncul setelah pembayaran
                            berhasil dan di approve oleh admin.
                        </li>
                        <li>
                            Jika belum menerima invoice dan e-ticket harap bersabar
                            menunggu.
                        </li>
                    </ol>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal('confirmModal')"
                        class="flex-none bg-gray-200 text-gray-700 text-sm py-2 px-4 rounded-xl hover:bg-gray-300 transition-all">
                        Edit Data
                    </button>
                    <button type="submit"
                        class="flex-none bg-[#000B58] text-white text-sm py-2 px-4 rounded-xl hover:opacity-90 transition-all text-center">
                        Saya Mengerti
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php /* include 'footer.php' */ ?>
    <script src="dist/btn-user.js"></script>
    <script src="dist/modal.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const timerSection = document.querySelector('#timerSection');
        const timerElement = document.querySelector('#timer');
        const formInputs = document.querySelectorAll('form input, form select');
        let timerDuration = 120; // yang dipakai
        // let timerDuration = 80000;
        let countdown;

        function isFormComplete() {
            return Array.from(formInputs).every(input => {
                if (input.type === 'radio') {
                    return document.querySelector(`input[name="${input.name}"]:checked`);
                }
                return input.value.trim() !== '';
            });
        }

        function startCountdown() {
            countdown = setInterval(() => {
                if (isFormComplete()) {
                    clearInterval(countdown);
                    timerSection.style.display = 'none';
                    return;
                }

                const minutes = Math.floor(timerDuration / 60);
                const seconds = timerDuration % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timerDuration <= 0) {
                    clearInterval(countdown);
                    alert("Waktu habis! Harap lengkapi data.");
                    window.history.back();
                }

                timerDuration--;
            }, 1000);
        }

        startCountdown();
        formInputs.forEach(input => {
            input.addEventListener('input', () => {
                if (isFormComplete()) {
                    clearInterval(countdown);
                    timerSection.style.display = 'none';
                } else {
                    timerSection.style.display = 'block';
                    if (!countdown) startCountdown();
                }
            });
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('backend/update-user.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Kesalahan jaringan, coba lagi.');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                });
        });
    });
    </script>
</body>

</html>