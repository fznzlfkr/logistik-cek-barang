<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }

        .background-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -3;
        }

        .background-slider,
        .background-slider.next {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(80%) saturate(90%);
            transition: opacity 1s ease-in-out;
        }

        .background-slider.next {
            opacity: 0;
            z-index: -2;
        }

        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(134, 136, 139, 0.6);
            z-index: -1;
        }

        .auth-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: auto;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }

        .logo {
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 18px;
            color: #172b4d;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-checkbox {
            text-align: left;
            margin: 10px 0 20px;
            font-size: 14px;
            color: #555;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #0052cc;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
        }

        .btn:hover {
            background-color: #0747a6;
        }

        .footer-links {
            margin-top: 20px;
            font-size: 14px;
        }

        .footer-links a {
            color: #0052cc;
            text-decoration: none;
            margin: 0 5px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .copyright {
            margin-top: 30px;
            font-size: 12px;
            color: #7a869a;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
            height: 20px;
            fill: #777;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }
    </style>

</head>

<body>
    <div class="background-wrapper">
        <div class="background-slider" id="bg1"></div>
        <div class="background-slider next" id="bg2"></div>
    </div>
    <div class="background-overlay"></div>

    <div class="auth-container">
        <div class="logo">
            <img src="../assets/img/logo.jpg" alt="Logo" width="60" />
        </div>
        <h2>Log in to continue</h2>
        <?= session()->getFlashdata('error') ?>
        <?= session()->getFlashdata('success') ?>
        <form action="<?= base_url('loginProcess') ?>" method="post">
            <input class="form-input" name="email" type="email" placeholder="Enter your email" required />

            <div class="password-wrapper">
                <input id="passwordInput" class="form-input" name="password" type="password" placeholder="Enter your password" required />
                <span id="togglePassword" class="toggle-password" title="Show password">
                    <!-- eye icon -->
                    <svg viewBox="0 0 24 24">
                        <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 
                        5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                    </svg>
                </span>
            </div>

            <div class="form-checkbox">
                <label><input type="checkbox" name="remember" /> Remember me</label>
            </div>
            <button class="btn" type="submit">Continue</button>
        </form>

        <div class="footer-links">
            <a href="<?= base_url('register')?>">Create an account</a>
        </div>

        <div class="copyright">
            © CargoWing – Powered by your logistics in flight
        </div>
    </div>

    <script>
        // Background image slider
        const backgrounds = [
            'assets/img/bg1.jpg',
            'assets/img/bg2.jpg',
            'assets/img/bg3.jpg'
        ];

        let current = 0;
        const bg1 = document.getElementById('bg1');
        const bg2 = document.getElementById('bg2');

        function changeBackground() {
            const next = (current + 1) % backgrounds.length;

            bg2.style.backgroundImage = `url('${backgrounds[next]}')`;
            bg2.style.opacity = '1';

            setTimeout(() => {
                bg1.style.backgroundImage = `url('${backgrounds[next]}')`;
                bg2.style.opacity = '0';
                current = next;
            }, 1000);
        }

        bg1.style.backgroundImage = `url('${backgrounds[0]}')`;
        setInterval(changeBackground, 5000);

        // Toggle show/hide password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');

        const eyeIcon = `
            <svg viewBox="0 0 24 24">
                <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 
                5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
            </svg>`;

        const eyeOffIcon = `
            <svg viewBox="0 0 24 24">
                <path d="M12 6a9.77 9.77 0 018.94 6A9.77 9.77 0 0112 18a9.77 9.77 0 01-8.94-6A9.77 9.77 0 0112 6m0-2C6 4 
                2 12 2 12s4 8 10 8 10-8 10-8-4-8-10-8zm0 5a3 3 0 100 6 3 3 0 000-6z"/>
            </svg>`;

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.innerHTML = isPassword ? eyeOffIcon : eyeIcon;
            togglePassword.title = isPassword ? 'Hide password' : 'Show password';
        });
    </script>
</body>

</html>
