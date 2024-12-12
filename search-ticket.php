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
    <title>Serenade - Search Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <link rel="stylesheet" href="dist/style-dashboard.css" />
</head>

<body class="bg-gray-100 font-sans px-4 sm:px-6 md:px-8 lg:px-10">
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto mt-24 max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-center mb-6 text-[#000B58]">
                Ticket Tracker
            </h2>

            <div class="mb-6">
                <label for="ticketId" class="block text-[#000B58]">ID Ticket</label>
                <div class="flex flex-col gap-3">
                    <input type="text" id="ticketId" placeholder="Masukkan nomor tiket"
                        class="flex-grow p-3 border-2 border-[#000B58] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#000B58]" />
                    <button onclick="checkTicket()"
                        class="bg-[#000B58] text-white px-3 py-2 rounded-lg hover:opacity-90 transition-colors">
                        Cari
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="dist/btn-user.js"></script>
    <script>
    function checkTicket() {
        const ticketId = document.getElementById("ticketId").value;

        if (!ticketId) {
            Swal.fire({
                icon: "warning",
                title: "Peringatan",
                text: "Silakan masukkan nomor tiket",
                confirmButtonColor: "#000B58",
            });
            return;
        }

        fetch('backend/cek-tiket.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `kode_ticket=${encodeURIComponent(ticketId)}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.data.status_pembayaran === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Tiket Ditemukan",
                            html: `
                            <p class="text-center mb-4">Pembayaran berhasil. Tiket Anda siap!</p>
                            <div class="flex justify-center">
                                <img src="assets/img/qrcode/${data.data.qrcode}" alt="QR Code"
                                    style="width: 200px; height: 200px;" class="mx-auto">
                            </div>`,
                            confirmButtonText: "Download E-Ticket",
                            confirmButtonColor: "#000B58",
                            showCloseButton: true,
                            willClose: () => {
                                console.log(
                                    'Modal ditutup tanpa download'
                                );
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = `e-ticket.php?kode_ticket=${data.data.kode_ticket}`;
                            }
                        });
                    } else if (data.data.status_pembayaran === 'pending') {
                        Swal.fire({
                            icon: "info",
                            title: "Pembayaran Pending",
                            text: "Tiket belum tersedia. Silakan selesaikan pembayaran Anda.",
                            confirmButtonColor: "#000B58",
                        });
                    }
                } else if (data.status === 'not_found') {
                    Swal.fire({
                        icon: "error",
                        title: "Tiket Tidak Ditemukan",
                        text: "Nomor tiket tidak ditemukan. Silakan periksa kembali.",
                        confirmButtonColor: "#000B58",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message,
                        confirmButtonColor: "#000B58",
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: "error",
                    title: "Terjadi Kesalahan",
                    text: "Tidak dapat memproses permintaan Anda. Silakan coba lagi.",
                    confirmButtonColor: "#000B58",
                });
            });
    }
    </script>
</body>

</html>