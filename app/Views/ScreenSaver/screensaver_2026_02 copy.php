<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>BRAPCI ScreenSaver</title>
    <style>
        body {
            margin: 0;
            background: black;
            overflow: hidden;
        }

        .logo {
            position: absolute;
            width: 300px;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(70vw, 20vh) rotate(90deg);
            }

            50% {
                transform: translate(40vw, 70vh) rotate(180deg);
            }

            75% {
                transform: translate(10vw, 40vh) rotate(270deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <img src="https://brapci.inf.br/assets/img/brand_brapci_shadown.png" class="logo">
</body>

</html>