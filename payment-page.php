<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Anda belum login, silahkan login dahulu!'); window.location.href='login.php';</script>";
    exit;
}

if (isset($_GET['kode_ticket'])) {
    $kode_ticket = htmlspecialchars($_GET['kode_ticket']);
    
    $query = "SELECT id_event, kode_unik, total_akhir, ref_id FROM purchase_user WHERE kode_ticket = '$kode_ticket'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $event_id = $row['id_event'];
        $kode_unik = $row['kode_unik'];
        $clip_total = $row['total_akhir'];
        $total_akhir = number_format($row['total_akhir'], 0, ',', '.');
        $ref_id = $row['ref_id'];
    
        $status_query = "SELECT status_pembayaran FROM purchase_user WHERE kode_ticket = '$kode_ticket'";
        $status_result = mysqli_query($conn, $status_query);
        if ($status_result && mysqli_num_rows($status_result) > 0) {
            $status_row = mysqli_fetch_assoc($status_result);
            if ($status_row['status_pembayaran'] != 'pending') {
                echo "<script>
                    alert('Pembayaran anda telah sukses dan tiket telah terbit. Silahkan pesan event konser lainnya.');
                    window.location.href='index.php';
                </script>";
                exit();
            }
        } else {
            echo "<script>
                alert('Gagal memverifikasi status pembayaran. Silahkan coba lagi.');
                window.location.href='index.php';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('Kode ticket tidak ditemukan di database!');
            window.location.href='detail-event.php?event_id=$event_id';
        </script>";
        exit();
    }
    
} else {
    echo "<script>
        alert('Kode ticket tidak ditemukan!');
        window.location.href='index.php';
    </script>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serenade - Payment Methods</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <link rel="stylesheet" href="dist/style-dashboard.css" />
    <style>
    .hidden {
        display: none;
    }

    .transition-opacity {
        transition: opacity 0.3s ease;
    }

    .rotate {
        transition: transform 0.3s ease;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    .opacity-0 {
        opacity: 0;
    }

    .opacity-100 {
        opacity: 1;
    }
    </style>
</head>

<body class="bg-gray-100 font-sans px-4 sm:px-6 md:px-8 lg:px-10">
    <?php include 'navbar.php' ?>

    <main class="container mx-auto mt-24 max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-center mb-6 text-[#000B58]">
                Metode Pembayaran
            </h2>

            <div class="space-y-4">
                <!-- Total Bayar -->
                <div class="space-y-1">
                    <div class="border rounded-lg p-4 flex flex-col justify-between bg-yellow-400">
                        <div class="flex flex-row flex-wrap justify-between">
                            <h3>Kode Tiket</h3>
                            <p><?php echo $kode_ticket; ?></p>
                        </div>
                        <div class="flex flex-row flex-wrap justify-between">
                            <h3>Ref ID</h3>
                            <p><?php echo $ref_id; ?></p>
                        </div>
                        <div class="border-dashed-white my-4"></div>
                        <div class="flex flex-row flex-wrap justify-between">
                            <h3>Kode Unik</h3>
                            <p class="mr-7"><?php echo $kode_unik; ?></p>
                        </div>
                        <div class="flex flex-row flex-wrap justify-between">
                            <h3>Total Bayar</h3>
                            <div class="flex flex-row font-semibold space-x-3">
                                <p id="totalPaymentAmount">Rp <?php echo $total_akhir; ?></p>
                                <button class="hover:text-white copy-btn"
                                    data-clipboard-text="<?php echo $clip_total; ?>">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row px-2 justify-end text-right text-xs text-red-500">
                        <p>* Harap transfer sesuai nominal yang tertera.</p>
                    </div>
                </div>

                <?php
                $result = $conn->query("SELECT id, name, norek FROM payment WHERE status = '1'");
                while ($payment = $result->fetch_assoc()) {
                    echo "<div class='border rounded-lg p-4 flex flex-col justify-between space-y-4'>
                    <div class='flex flex-row justify-between items-center'>
                        <h3 class='font-semibold'>" . ($payment['name']) . "</h3>
                        <button id='toggleButton" . ($payment['name']) . "'>
                            <i class='fa-solid fa-angle-left mr-3' id='toggleIcon" . ($payment['name']) . "'></i>
                            <i class='fa-solid fa-angle-down mr-3 hidden' id='toggleIconDown" . ($payment['name']) . "'></i>
                        </button>
                    </div>
                    <div id='hiddenContent" . ($payment['name']) . "'
                        class='hidden-content flex flex-row justify-between items-center hidden opacity-0 transition-opacity duration-300'>
                        <div class='flex flex-row gap-2 items-center'>
                            <p class='text-gray-600'>" . ($payment['norek']) . "</p>
                            <button class='text-blue-600 hover:text-blue-800 copy-btn'
                                data-clipboard-text='" . ($payment['norek']) . "'>
                                <i class='far fa-copy'></i>
                            </button>
                        </div>
                        <button class='p-2 bg-[#000B58] text-sm rounded-lg text-white hover:opacity-90 transition-all transfer-btn'
                        data-id-payment='" . ($payment['id']) . "'>
                                    Sudah transfer
                        </button>
            </div>
        </div>";
        };
        ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>
    <script src="dist/btn-user.js"></script>
    <script>
    var clipboard = new ClipboardJS(".copy-btn");

    clipboard.on("success", function(e) {
        const copiedText = e.trigger.getAttribute("data-clipboard-text");
        const alertText = copiedText.startsWith("100.") ?
            "Total pembayaran berhasil disalin" :
            "Nomor rekening berhasil disalin";

        Swal.fire({
            icon: "success",
            title: "Tersalin!",
            text: alertText,
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
        });
        e.clearSelection();
    });

    clipboard.on("error", function(e) {
        Swal.fire({
            icon: "error",
            title: "Gagal menyalin",
            text: "Terjadi kesalahan saat menyalin.",
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500,
        });
    });

    document.querySelectorAll('.transfer-btn').forEach(button => {
        button.addEventListener('click', () => {
            const idPayment = button.getAttribute('data-id-payment');
            const kodeTicket = "<?php echo $kode_ticket; ?>"; // Pastikan $kode_ticket sudah ada di PHP

            fetch('backend/update-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_payment: idPayment,
                        kode_ticket: kodeTicket
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: "info",
                            title: "Konfirmasi Transfer",
                            text: "Transaksi sedang diperiksa admin, Anda akan diarahkan ke halaman Riwayat Transaksi.",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#1d4ed8",
                        }).then(() => {
                            window.location.href = "riwayat-transaksi.php";
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Terjadi kesalahan saat memperbarui data. Silakan coba lagi.",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#d33",
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "Terjadi kesalahan pada jaringan atau server. Silakan coba lagi nanti.",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#d33",
                    });
                });
        });
    });


    document.querySelectorAll('[id^="toggleButton"]').forEach((button) => {
        const buttonId = button.id;
        const contentId = buttonId.replace('toggleButton', 'hiddenContent');
        const iconLeftId = buttonId.replace('toggleButton', 'toggleIcon');
        const iconDownId = buttonId.replace('toggleButton', 'toggleIconDown');

        button.addEventListener('click', () => {
            toggleVA(buttonId, contentId, iconLeftId, iconDownId);
        });
    });

    function toggleVA(buttonId, contentId, iconLeftId, iconDownId) {
        const hiddenContent = document.getElementById(contentId);
        const toggleIconLeft = document.getElementById(iconLeftId);
        const toggleIconDown = document.getElementById(iconDownId);

        const isCurrentlyOpen = !hiddenContent.classList.contains('hidden');

        const allHiddenContents = document.querySelectorAll('.hidden-content');
        const allIconsLeft = document.querySelectorAll('.fa-angle-left');
        const allIconsDown = document.querySelectorAll('.fa-angle-down');

        allHiddenContents.forEach((content) => {
            content.classList.add('hidden');
            content.classList.remove('opacity-100');
        });

        allIconsLeft.forEach((icon) => {
            icon.classList.remove('hidden');
            icon.classList.remove('rotate-180');
        });

        allIconsDown.forEach((icon) => {
            icon.classList.add('hidden');
        });

        if (isCurrentlyOpen) {
            hiddenContent.classList.remove('opacity-100');
            setTimeout(() => {
                hiddenContent.classList.add('hidden');
            }, 300);
            toggleIconLeft.classList.remove('hidden');
            toggleIconDown.classList.add('hidden');
            toggleIconLeft.classList.remove('rotate-180');
        } else {
            hiddenContent.classList.remove('hidden');
            setTimeout(() => {
                hiddenContent.classList.add('opacity-100');
            }, 10);
            toggleIconLeft.classList.add('hidden');
            toggleIconDown.classList.remove('hidden');
            toggleIconLeft.classList.add('rotate-180');
        }
    }
    </script>
</body>

</html>