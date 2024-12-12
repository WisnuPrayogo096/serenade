<nav class="bg-white shadow-md mb-2 p-5 flex justify-between items-center relative">
    <h1 class="text-xl font-bold text-primary-color">Dashboard Admin</h1>
    <div class="flex items-center relative">
        <button id="userButton" class="flex items-center gap-3 text-text-color">
            <i class="fa-solid fa-user text-primary-color"></i>
            <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        </button>
        <div id="dropdownMenu"
            class="absolute bg-white text-black border border-gray-300 rounded shadow-lg hidden w-48">
            <a href="backend/logout.php" class="block px-4 py-2 hover:bg-[#000B58] hover:text-white">Logout</a>
        </div>
    </div>
</nav>