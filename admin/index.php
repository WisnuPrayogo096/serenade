<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../dist/font-style.css" />
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #006a67, #000b58);
    }
    </style>
    <title>Serenade - Admin Area</title>
</head>

<body class="gradient-bg h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden w-[480px] max-w-full min-h-[350px] relative">
        <div class="absolute inset-0 flex items-center justify-center transition-all duration-500">
            <form method="POST" action="backend/login.php"
                class="flex flex-col items-center justify-center px-10 h-full w-full">
                <h1 class="text-2xl font-bold mb-6 text-[#000b58]">Admin Area</h1>
                <input type="email" name="email" placeholder="Email"
                    class="bg-gray-200 rounded-lg px-4 py-2 mb-4 w-full outline-none text-sm focus:ring-2 focus:ring-[#006a67]"
                    required />
                <input type="password" name="password" placeholder="Password"
                    class="bg-gray-200 rounded-lg px-4 py-2 mb-4 w-full outline-none text-sm focus:ring-2 focus:ring-[#006a67]"
                    required />
                <button type="submit"
                    class="bg-[#000b58] text-white font-semibold uppercase text-xs py-3 px-12 rounded-lg hover:bg-gradient-to-r from-[#006a67] to-[#000b58] hover:opacity-90 transition-all duration-300 mt-4">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>

</html>