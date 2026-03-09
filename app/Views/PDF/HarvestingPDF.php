<?php
$link = get("IDs");
$link = explode(",",$link);
$links = "";
foreach ($link as $l) {
    $links .= "'".$l."',";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Visualizador de Links</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
        }

        .topo {
            background: #4b2e83;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .info {
            padding: 10px;
            background: #e9ecf5;
            display: flex;
            justify-content: space-between;
        }

        .link {
            color: #333;
            font-size: 14px;
        }

        .timer {
            font-weight: bold;
        }

        iframe {
            width: 100%;
            height: 90vh;
            border: none;
        }
    </style>
</head>

<body>

    <div class="topo">
        <h2>Visualizador automático de links</h2>
    </div>

    <div class="info">
        <div class="link" id="linkAtual"></div>
        <div class="timer">Próximo em: <span id="contador">10</span>s</div>
    </div>

    <iframe id="viewer"></iframe>

    <script>
        const links = [<?= $links ?>];

        let indice = 0;
        let tempo = 10;

        function carregarLink() {

            if (indice >= links.length) {
                document.getElementById("linkAtual").innerHTML = "Processo finalizado";
                document.getElementById("contador").innerHTML = "0";
                return;
            }

            const link = 'https://cip.brapci.inf.br//download/'+links[indice];

            document.getElementById("viewer").src = link;
            document.getElementById("linkAtual").innerHTML = link;

            tempo = 10;
            document.getElementById("contador").innerHTML = tempo;

            const intervalo = setInterval(() => {

                tempo--;
                document.getElementById("contador").innerHTML = tempo;

                if (tempo <= 0) {
                    clearInterval(intervalo);
                    indice++;
                    carregarLink();
                }

            }, 1000);

        }

        carregarLink();
    </script>

</body>

</html>