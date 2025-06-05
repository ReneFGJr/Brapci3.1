    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
        }

        td,
        th {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #f5f5f5;
            text-align: left;
        }

        .nav-buttons {
            margin-top: 20px;
        }

        .nav-buttons a {
            text-decoration: none;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            margin-right: 8px;
        }

        .nav-buttons a.disabled {
            background-color: #aaa;
            pointer-events: none;
        }

        input[type="number"] {
            width: 100px;
        }
    </style>
    </head>

    <body>
        <div class="nav-buttons">
            <?php if ($prevId !== null): ?>
                <a href="<?= site_url('capes/view/' . $prevId) ?>">&laquo; Anterior</a>
            <?php else: ?>
                <a class="disabled">&laquo; Anterior</a>
            <?php endif; ?>

            <?php if ($nextId !== null): ?>
                <a href="<?= site_url('capes/view/' . $nextId) ?>">Próximo &raquo;</a>
            <?php else: ?>
                <a class="disabled">Próximo &raquo;</a>
            <?php endif; ?>
        </div>

        <table>
            <tr>
                <th width="25%">Campo</th>
                <th width="75%">Valor</th>
            </tr>
            <tr>
                <td width="25%">ID</td>
                <td><?= esc($registro['ID']) ?> - <?= esc($registro['STATUS']) ?> - <?= esc($registro['ANO_DESTAQUE']) ?> [<?= esc($registro['ORDEM']) ?>]</td>
            </tr>
            <tr>
                <td>Instituíção</td>
                <td><?= esc($registro['SG_INSTITUICAO_ENSINO']) ?> - <?= esc($registro['CD_PROGRAMA']) ?></td>
            </tr>
            <tr>
                <td>Programa</td>
                <td><?= esc($registro['NM_PROGRAMA']) ?> - <?= esc($registro['NM_MODALIDADE']) ?></td>
            </tr>
            <tr>
                <td>Título</td>
                <td><?= esc($registro['TITULO_PRODUCAO_OU_NM_EGRESSO']) ?></td>
            </tr>
            <tr>
                <td>Subtitulo</td>
                <td><?= esc($registro['NM_SUBTIPO_PRODUCAO']) ?> - <?= esc($registro['NM_TIPO_PRODUCAO']) ?></td>
            </tr>
            <tr>
                <td>Grau academico</td>
                <td><?= esc($registro['NM_GRAU_ACADEMICO_EGRESSO']) ?></td>
            </tr>
            <tr>
                <td>Docente</td>
                <td><?= esc($registro['NM_DOCENTE']) ?></td>
            </tr>
            <tr>
                <td>Destaque</td>
                <td><?= esc($registro['TIPO_DE_DESTAQUE']) ?></td>
            </tr>
            <tr>
                <td colspan=2>JUSTIFICATIVA
                    <hr>
                    <?= nl2br(esc($registro['JUSTIFICATIVA'])) ?>
                </td>
            </tr>


        </table>