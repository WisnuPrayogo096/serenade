<?php
$page = basename($_SERVER['PHP_SELF']);
?>

<nav class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md">
    <div class="container mx-auto flex justify-between items-center px-10 py-4 sm:px-2 md:px-5 lg:px-8">
        <a href="index.php"><button
                class="text-2xl font-bold text-[#003161] hover:scale-105 transition-transform duration-200 ease-in-out">
                serenade.com
            </button></a>
        <div class="flex flex-row items-center">
            <?php if (!empty($_SESSION['user_name'])): ?>
            <div
                class="text-[#003161] mr-10 font-medium <?php echo ($page != 'index.php' && $page != 'detail-event.php') ? 'hidden' : '' ?>">
                <a href="search-ticket.php" class="hover:text-[#006A67] hover:underline transition-all">Cek Tiket</a>
            </div>
            <div class="flex flex-col items-center">
                <button id="userButton"
                    class="bg-[#000B58] text-white px-6 py-2 rounded-full hover:bg-[#006A67] hover:opacity-90 transition-all flex items-center gap-2">
                    <i class="fa-regular fa-user mr-2"></i>
                    Hai, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </button>
                <div id="dropdownMenu"
                    class="absolute top-full bg-white text-black border border-gray-300 rounded shadow-lg hidden w-48 z-10">
                    <a href="riwayat-transaksi.php" class="block px-4 py-2 hover:bg-[#000B58] hover:text-white">Riwayat
                        Transaksi</a>
                    <a href="backend/logout.php" class="block px-4 py-2 hover:bg-[#000B58] hover:text-white">Logout</a>
                </div>
            </div>
            <?php else: ?>
            <a href="login.php">
                <button
                    class="bg-[#000B58] text-white px-6 py-2 rounded-full hover:bg-[#006A67] hover:opacity-90 transition-all flex items-center gap-2">
                    <i class="fa-regular fa-user mr-2"></i>
                    Login
                </button>
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>