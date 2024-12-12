<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serenade - Booking Tiket Konser</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="dist/font-style.css" />
    <link rel="stylesheet" href="dist/style-dashboard.css" />
</head>

<body class="bg-gray-100 font-sans">
    <?php include 'navbar.php' ?>

    <main class="pt-28 px-10">
        <section class="carousel-container">
            <?php
            $images = scandir('assets/img/slider');
            $no = 1;
            foreach ($images as $image) {
                if ($image !== '.' && $image !== '..') {
                    $activeClass = ($image === 'slider-1.png') ? 'active' : '';
                    echo '<img src="assets/img/slider/' . htmlspecialchars($image) . '" alt="Konser ' . $no . '" class="carousel-slide ' . $activeClass . '" />';
                    $no++;
                }
            }
            ?>

            <button id="prevSlide"
                class="absolute left-5 top-1/2 transform -translate-y-1/2 bg-[#000B58]/50 hover:bg-[#006A67]/70 text-white p-3 rounded-full transition-all z-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button id="nextSlide"
                class="absolute right-5 top-1/2 transform -translate-y-1/2 bg-[#000B58]/50 hover:bg-[#006A67]/70 text-white p-3 rounded-full transition-all z-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </section>

        <!-- event -->
        <section class="container mx-auto py-12 px-4">
            <h1 class="text-3xl font-bold text-[#000B58] mb-5">
                Event Konser Terbaru
            </h1>
            <div class="flex flex-wrap justify-center gap-6">
                <?php
                $result = $conn->query("
                SELECT 
                    ek.id AS event_id,
                    ek.nama_event,
                    ek.lokasi_event,
                    ek.tanggal_mulai,
                    ek.tanggal_selesai,
                    ek.kuota,
                    MIN(pt.harga_ticket) AS harga_ticket_termurah,
                    de.no_day,
                    de.img_day,
                    de.id AS day_event_id
                FROM 
                    presale_ticket pt
                JOIN 
                    event_konser ek ON pt.id_event = ek.id
                JOIN 
                    day_event de ON de.id_event = ek.id
                WHERE 
                    ek.status = 1
                GROUP BY 
                    ek.id, ek.nama_event, ek.lokasi_event, ek.tanggal_mulai, ek.tanggal_selesai, ek.kuota, de.no_day, de.img_day, de.id
                ORDER BY 
                    ek.id, de.no_day;
                ");
                while ($event = $result->fetch_assoc()) {
                    $tanggal_mulai = date("d M Y", strtotime($event['tanggal_mulai']));
                    $tanggal_selesai = date("d M Y", strtotime($event['tanggal_selesai']));
                    $tanggalPerform = $tanggal_mulai . "-" . $tanggal_selesai;
                    $harga_ticket_termurah = "Rp " . number_format($event['harga_ticket_termurah'], 0, ',', '.');
                    if ($event['kuota'] == 0) {
                        $kuota_class = 'bg-red-500';
                    } elseif ($event['kuota'] <= 20) {
                        $kuota_class = 'bg-yellow-500';
                    } else {
                        $kuota_class = 'bg-green-500';
                    }

                    echo "<div
                    class='bg-white shadow-lg rounded-xl overflow-hidden w-[350px] transform hover:scale-105 transition-transform duration-300 ease-in-out'>
                    <a href='detail-event.php?event_id=". htmlspecialchars($event['day_event_id']) ."'>
                        <img src='assets/img/content/". htmlspecialchars($event['img_day']) ."' alt='Event'
                            class='w-full h-[350px] object-cover' />
                        <div class='p-5 bg-gray-50'>
                            <h3 class='text-base font-light text-gray-500 mb-2'>
                                <i class='fa-solid fa-location-dot mr-2'></i>
                                ". htmlspecialchars($event['lokasi_event']) ."
                            </h3>
                            <h3 class='text-base font-light text-gray-500 mb-2'>
                                <i class='fa-regular fa-calendar-days mr-2'></i>
                                ". htmlspecialchars($tanggalPerform) ."
                            </h3>
                            <h3 class='text-base font-light text-gray-500 mb-5'>
                                <i class='fa-regular fa-clock mr-2'></i>
                                Day ". htmlspecialchars($event['no_day']) ."
                            </h3>
                            <h3 class='text-xl font-semibold text-[#000B58] mb-2'>
                                ". htmlspecialchars($event['nama_event']) ."
                            </h3>
                            <span class='text-gray-500 font-light'>Mulai dari</span>
                            <div class='flex justify-between items-center'>
                                <span class='text-orange-500 font-bold'>". htmlspecialchars($harga_ticket_termurah) ."</span>
                                <span class='text-white px-3 py-1 rounded-full text-sm ". htmlspecialchars($kuota_class) ."'>". htmlspecialchars($event['kuota']) ." Tiket</span>
                            </div>
                        </div>
                    </a>
                </div>";                }
                ?>
            </div>
        </section>
    </main>
    <?php include 'footer.php' ?>

    <script src="dist/btn-user.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll(".carousel-slide");
        const prevButton = document.getElementById("prevSlide");
        const nextButton = document.getElementById("nextSlide");
        let currentSlide = 1;

        function showSlide(index) {
            slides.forEach((slide) => slide.classList.remove("active"));
            slides[index].classList.add("active");
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        showSlide(currentSlide);

        if (slides.length > 1) {
            setInterval(nextSlide, 8000);
        }

        nextButton.addEventListener("click", nextSlide);
        prevButton.addEventListener("click", prevSlide);
    });

    document.addEventListener("DOMContentLoaded", function() {
        const navbar = document.querySelector("nav");

        window.addEventListener("scroll", function() {
            const scrollTop = window.scrollY;

            if (scrollTop > 0) {
                navbar.style.opacity = "0.8";
            } else {
                navbar.style.opacity = "1";
            }
        });
    });
    </script>
</body>

</html>