<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside class="w-64 bg-white shadow-xl h-screen fixed left-0 top-0">
    <div class="p-5 border-b border-gray-200 bg-primary-color text-[#003161]">
        <h2 class="text-xl font-bold">serenade.com</h2>
    </div>
    <nav class="p-4">
        <ul>
            <li class="mb-2">
                <a href="dashboard.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fa fa-home mr-3 text-primary-color"></i>Dashboard
                </a>
            </li>
            <li class="mb-2">
                <a href="event-konser.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'event-konser.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-database mr-3 text-primary-color"></i>Event
                    Konser
                </a>
            </li>
            <li class="mb-2">
                <a href="day-event.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'day-event.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-calendar-alt mr-3 text-primary-color"></i>
                    Day Event
                </a>
            </li>
            <li class="mb-2">
                <a href="presale-ticket.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'presale-ticket.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-ticket mr-3 text-primary-color"></i>Presale
                    Ticket
                </a>
            </li>
            <li class="mb-2">
                <a href="manage-user.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'manage-user.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users-gear mr-3 text-primary-color"></i>
                    User
                </a>
            </li>
            <li class="mb-2">
                <a href="launching-ticket.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'launching-ticket.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-receipt mr-3 text-primary-color"></i>
                    Penerbitan
                </a>
            </li>
            <li class="mb-2">
                <a href="payment.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'payment.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-wallet mr-3 text-primary-color"></i>
                    Payments
                </a>
            </li>
            <li class="mb-5">
                <a href="report-sale.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg <?php echo ($page == 'report-sale.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-line mr-3 text-primary-color"></i>Laporan
                    Penjualan
                </a>
            </li>
            <!-- <li class="pt-5 border-t border-[#000b58]">
                <a href="index.php"
                    class="flex items-center py-3 px-4 text-text-color hover:bg-[#006A67] hover:text-white rounded-lg">
                    <i class="fa-solid fa-sign-out mr-3 text-primary-color"></i>Logout
                </a>
            </li> -->
        </ul>
    </nav>
</aside>