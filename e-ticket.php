<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['user_name'])) {
    echo "<script>alert('Anda belum login, silahkan login dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$kode_ticket = isset($_GET['kode_ticket']) ? $_GET['kode_ticket'] : '';
if (empty($kode_ticket)) {
    echo "<script>alert('Kode ticket tidak ditemukan!'); window.location.href='search-tiket.php';</script>";
    exit;
}

$query = "
    SELECT 
        pu.id AS purchase_id,
        pu.id_user,
        pu.id_event,
        pu.id_day,
        pu.id_presale,
        pu.id_payment,
        pu.kode_ticket,
        pu.qrcode,
        us.name AS buyer_name,
        ek.nama_event,
        de.tanggal_perform,
        de.jam_mulai,
        pt.no_presale,
        pu.jumlah_tiket,
        pu.ref_id,
        py.name AS payment_method,
        pu.status_pembayaran
    FROM 
        purchase_user pu
    LEFT JOIN 
        user us ON pu.id_user = us.id    
    LEFT JOIN 
        event_konser ek ON pu.id_event = ek.id
    LEFT JOIN 
        day_event de ON pu.id_day = de.id    
    LEFT JOIN 
        presale_ticket pt ON pu.id_presale = pt.id    
    LEFT JOIN 
        payment py ON pu.id_payment = py.id
    WHERE pu.kode_ticket = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $kode_ticket);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $ticket = $result->fetch_assoc();
} else {
    header("Location: search-tiket.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serenade.com - E-Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 flex flex-col justify-center items-center min-h-screen">
    <div id="ticket-container" class="bg-white shadow-lg rounded-lg p-6 w-full max-w-md">
        <div class="text-center mb-4">
            <a href="index.php"><button
                    class="text-2xl font-bold text-[#003161] hover:scale-105 transition-transform duration-200 ease-in-out">
                    serenade.com
                </button></a>
            <p class="text-md  text-gray-500">E-Ticket</p>
        </div>

        <!-- Barcode -->
        <div class="w-full flex items-center justify-center mb-4">
            <img src="assets/img/qrcode/<?php echo $ticket['qrcode']; ?>" alt="QR Code" class="mx-auto"
                style="max-height: 150px;">
        </div>

        <div class="space-y-3">
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Kode Tiket:</span>
                <span class="col-span-2" id="ticketCode"><?php echo $ticket['kode_ticket']; ?></span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Nama Pembeli:</span>
                <span class="col-span-2" id="buyerName"><?php echo $ticket['buyer_name']; ?></span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Nama Event:</span>
                <span class="col-span-2" id="eventName"><?php echo $ticket['nama_event']; ?></span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Tanggal Event:</span>
                <span class="col-span-2"
                    id="eventDate"><?php echo date('d F Y', strtotime($ticket['tanggal_perform'])); ?></span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Waktu Mulai:</span>
                <span class="col-span-2" id="eventTime"><?php echo date('H:i', strtotime($ticket['jam_mulai'])); ?>
                    WIB</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Presale:</span>
                <!-- <span class="col-span-2" id="presaleInfo"><?php /* echo $ticket['no_presale'] ? 'Ya' : 'Tidak'; */ ?></span> -->
                <span class="col-span-2" id="presaleInfo">Presale <?php echo $ticket['no_presale']; ?></span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Jumlah:</span>
                <span class="col-span-2" id="purchaseQuantity"><?php echo $ticket['jumlah_tiket']; ?> Tiket</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Ref ID:</span>
                <span class="col-span-2" id="paymentRefId"><?php echo $ticket['ref_id']; ?></span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <span class="font-semibold">Payment:</span>
                <span class="col-span-2" id="paymentMethod"><?php echo $ticket['payment_method']; ?></span>
            </div>
        </div>

        <div class="mt-4 text-center text-xs text-gray-500 space-y-1">
            <p>E-Ticket ini resmi diterbitkan oleh <span class="font-bold text-[#003161]">serenade.com</span></p>
            <p>Dicetak pada: <span id="printDateTime"></span></p>
        </div>
    </div>

    <button id="downloadBtn"
        class="mt-4 px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300">
        Download E-Ticket
    </button>

    <script>
    // Set print date and time
    document.getElementById('printDateTime').textContent =
        new Date().toLocaleString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

    // Download ticket as PNG
    document.getElementById('downloadBtn').addEventListener('click', function() {
        html2canvas(document.getElementById('ticket-container'), {
            scale: 2, // Increases resolution
            useCORS: true // Helps with rendering external images if needed
        }).then(canvas => {
            // Create a link element, download the image
            const link = document.createElement('a');
            link.download = `Serenade-Ticket-${document.getElementById('ticketCode').textContent}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });
    </script>
</body>

</html>