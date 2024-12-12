<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="dist/style-login.css" />
    <link rel="stylesheet" href="dist/font-style.css" />
    <title>Serenade - User Page</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="backend/register.php" method="POST">
                <h1 style="margin-bottom: 1rem; color: #006a67">Create Account</h1>
                <input type="text" name="name" placeholder="Name" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit" class="button-su">Sign Up</button>
            </form>

        </div>
        <div class="form-container sign-in">
            <form action="backend/login.php" method="POST">
                <h1 style="margin-bottom: 1rem; color: #000b58">Sign In</h1>
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit" class="button-si">Sign In</button>
                <a href="admin/">Admin?</a>
            </form>

        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h2>serenade.com</h2>
                    <p>Silahkan masuk dengan data yang sudah terdaftar.</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h2>serenade.com</h2>
                    <p>
                        Daftar akun terlebih dahulu sebelum melakukan pembelian tiket.
                    </p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="dist/script-login.js"></script>
</body>

</html>