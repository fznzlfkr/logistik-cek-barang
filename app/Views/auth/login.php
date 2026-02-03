<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Romantic Page</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(145deg, #ff69b4, #000000);
            color: #fff;
        }

        .container {
            text-align: center;
            background: rgba(255, 105, 180, 0.1);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 0px 20px 5px rgba(255, 105, 180, 0.5);
        }

        h1 {
            margin-bottom: 20px;
            font-size: 3rem;
            color: #ff69b4;
        }

        button {
            background: #ff69b4;
            color: #000;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            transform: scale(1.1);
            background: #fff;
            color: #ff69b4;
        }

        #pickup-line {
            margin-top: 20px;
            font-size: 1.5rem;
            color: #ffe4e1;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Hello, Sweetie! 😍</h1>
        <p>Click the button below to get a special romantic line!</p>
        <button onclick="showPickupLine()">Get a Pickup Line</button>
        <div id="pickup-line"></div>
    </div>

    <script>
        const pickupLines = [
            "Are you a magician? Because every time I look at you, everyone else disappears. 🪄✨",
            "Do you have a map? I just got lost in your eyes. 🗺️😍",
            "If you were a fruit, you'd be a fineapple. 🍍❤️",
            "Are you the reason for global warming? Because you're hot. 🌡️🔥",
            "Do you believe in love at first sight—or should I walk by again? 👀💘"
        ];

        function showPickupLine() {
            const random = Math.floor(Math.random() * pickupLines.length);
            document.getElementById('pickup-line').innerText = pickupLines[random];
        }
    </script>
</body>

</html>