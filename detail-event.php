<?php
session_start();
include 'koneksi.php';

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    
    $query = "
    SELECT 
        ek.id AS event_id,
        ek.nama_event,
        ek.lokasi_event,
        ek.img_banner,
        de.id AS day_event_id,
        de.no_day,
        de.tanggal_perform,
        de.jam_mulai,
        de.jam_selesai,
        de.deskripsi AS day_event_deskripsi,
        pt.id AS presale_ticket_id,
        pt.no_presale,
        pt.harga_ticket,
        pt.kuota_ticket
    FROM 
        event_konser ek
    JOIN 
        day_event de ON ek.id = de.id_event
    JOIN 
        presale_ticket pt ON de.id = pt.id_day
    WHERE 
        de.id = ?
    ORDER BY 
        ek.id, de.no_day, pt.no_presale;
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($event = $result->fetch_assoc()) {
        // $tanggal_mulai = date("d M Y", strtotime($event['tanggal_mulai']));
        // $tanggal_selesai = date("d M Y", strtotime($event['tanggal_selesai']));
        // $harga_ticket_termurah = "Rp " . number_format($event['harga_ticket_termurah'], 0, ',', '.');
        // $harga_ticket_termahal = "Rp " . number_format($event['harga_ticket_termahal'], 0, ',', '.');
        $jam_mulai = date("H:i", strtotime($event['jam_mulai']));
        $jam_selesai = date("H:i", strtotime($event['jam_selesai']));
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Event - <?php echo htmlspecialchars($event['nama_event']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <link rel="stylesheet" href="dist/style-dashboard.css" />
</head>

<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php' ?>

    <main
        class="container mx-auto my-16 px-4 sm:px-6 md:px-8 lg:px-10 sm:mt-20 md:mt-24 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
        <!-- Kolom Kiri-->
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#003161] mb-3 sm:mb-4">
                <?php echo htmlspecialchars($event['nama_event']) . " - Day " . htmlspecialchars($event['no_day']); ?>
            </h1>

            <img src="assets/img/slider/<?php echo htmlspecialchars($event['img_banner']); ?>" alt="Event"
                class="w-full h-[250px] sm:h-[300px] md:h-[350px] object-cover rounded-lg mb-3 sm:mb-4" />

            <div class="flex gap-3 sm:gap-4 mb-3 sm:mb-4">
                <button id="descriptionBtn" onclick="toggleSection('description')"
                    class="bg-[#000B58] text-white px-4 sm:px-6 py-2 rounded-full text-sm sm:text-base">
                    <i class="fa-solid fa-newspaper mr-1"></i>
                    Deskripsi
                </button>
                <button id="ticketBtn" onclick="toggleSection('ticket')"
                    class="bg-[#EBF1FF] text-[#6A7EFF] px-4 sm:px-6 py-2 rounded-full text-sm sm:text-base">
                    <i class="fa-solid fa-ticket mr-1"></i>
                    Tiket
                </button>
            </div>

            <div id="descriptionContent" class="mb-40 text-sm sm:text-base">
                <p class="font-bold mt-6 text-[#003161]">Deskripsi Event</p>
                <div class="text-gray-500 space-y-4 mt-3">
                    <?php echo html_entity_decode($event['day_event_deskripsi'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>

            <div id="ticketContent" class="hidden">
                <div class="mb-40 space-y-3 sm:space-y-4">
                    <?php
                    $query = "
                    SELECT 
                        ek.id AS event_id,
                        de.id AS day_event_id,
                        pt.id AS presale_ticket_id,
                        ek.nama_event,
                        de.no_day,
                        pt.no_presale,
                        pt.harga_ticket,
                        pt.kuota_ticket
                    FROM 
                        event_konser ek
                    JOIN 
                        day_event de ON ek.id = de.id_event
                    JOIN 
                        presale_ticket pt ON de.id = pt.id_day
                    WHERE 
                        de.id = ?
                    ORDER BY 
                        ek.id, de.no_day, pt.no_presale;
                    ";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $event_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($ticket = $result->fetch_assoc()) {
                        $harga_ticket = "Rp " . number_format($ticket['harga_ticket'], 0, ',', '.');
                        if ($ticket['kuota_ticket'] != 0) {
                            $class = 'py-2 px-3 rounded-lg bg-blue-100 text-blue-500 text-sm';
                            $status = 'On Sale';
                            $hide = '';
                        } else {
                            $class = 'py-2 px-3 rounded-lg bg-red-100 text-red-500 text-sm';
                            $status = 'Sold Out';
                            $hide = 'hidden';
                        }

                    echo "<div
                        class='ticket-container bg-white py-5 px-6 text-sm sm:text-base sm:py-6 sm:px-7 rounded-lg border border-1'
                        data-event-id='" . htmlspecialchars($ticket['event_id']) . "'
                        data-day-event-id='" . htmlspecialchars($ticket['day_event_id']) . "'
                        data-presale-ticket-id='" . htmlspecialchars($ticket['presale_ticket_id']) . "'
                        data-nama-event='" . htmlspecialchars($ticket['nama_event']) . "'
                        data-no-day='" . htmlspecialchars($ticket['no_day']) . "'
                        data-no-presale='" . htmlspecialchars($ticket['no_presale']) . "'
                        data-harga-ticket='" . $ticket['harga_ticket'] . "'>
                        
                        <div class='flex flex-row justify-between items-center'>
                            <h3 class='font-bold text-[#003161]'>
                                Presale ". htmlspecialchars($ticket['no_presale']) ." - Day ". htmlspecialchars($ticket['no_day']) ."
                            </h3>
                            <div class='". htmlspecialchars($class) ."'>
                                <p>". htmlspecialchars($status) ."</p>
                            </div>
                        </div>
                        <p class='text-gray-400 text-sm'>
                            Pajak dan biaya layanan ditanggung penonton.
                        </p>
                        <div class='border-dashed my-4'></div>
                        <div class='price-section'>
                            <div class='original-section flex flex-row justify-between items-center'>
                                <div>
                                    <p class='text-gray-400'>Harga</p>
                                    <p class='text-orange-500 font-bold'>". htmlspecialchars($harga_ticket) ."</p>
                                </div>
                                <button
                                    class='select-ticket-btn bg-[#000B58] px-5 py-2 rounded-lg text-white text-sm hover:opacity-90 hover:shadow-md ". htmlspecialchars($hide) ."'>
                                    Pilih
                                </button>
                            </div>
                        </div>
                    </div>";
                    }    
                    ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan-->
        <div id="buyTicketSection" class="w-[500px] p-4 sm:p-6 rounded-lg self-start">
            <h2 class="text-xl sm:text-2xl font-bold text-[#003161] mb-3 mt-5 sm:mb-7">
                Detail Event
            </h2>

            <div class="space-y-4 sm:space-y-5">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="p-2 rounded-xl bg-[#EBF1FF] text-[#6A7EFF] text-sm sm:text-base">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="text-sm sm:text-base">
                        <p class="text-gray-500">Tanggal</p>
                        <p class="font-bold"><?php echo date('j F Y', strtotime($event['tanggal_perform'])); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="p-2 rounded-xl bg-[#EBF1FF] text-[#6A7EFF] text-sm sm:text-base">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="text-sm sm:text-base">
                        <p class="text-gray-500">Waktu</p>
                        <p class="font-bold"><?php echo $jam_mulai . " - " . $jam_selesai; ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="p-2 rounded-xl bg-[#EBF1FF] text-[#6A7EFF] text-sm sm:text-base">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="text-sm sm:text-base">
                        <p class="text-gray-500">Lokasi</p>
                        <p class="font-bold"><?php echo htmlspecialchars($event['lokasi_event']); ?></p>
                    </div>
                </div>
            </div>

            <button id="buyTicketBtn" onclick="scrollToBuyTicket()"
                class="mt-4 sm:mt-6 w-2/3 bg-[#000B58] text-white px-4 sm:px-6 py-2 sm:py-3 rounded-2xl hover:opacity-90 transition-all text-sm sm:text-base">
                Beli Tiket
            </button>
        </div>
    </main>
    <script src="dist/btn-user.js"></script>
    <script src="dist/user-ticket.js"></script>
    <?php /* include 'footer.php' */ ?>
</body>

</html>