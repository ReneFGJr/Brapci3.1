<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Importação CSV OJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-lg p-4 mb-4">
            <h2 class="mb-4">Resultado da Importação CSV</h2>


            <?php if (isset($totais) && is_array($totais)): ?>
                <div class="mb-4">
                    <div class="row align-items-center">
                        <div class="col-auto"><strong>Filtrar por status:</strong></div>
                        <?php foreach ($totais as $status => $total): ?>
                            <div class="col-auto">
                                <form method="get" action="" style="display:inline">
                                    <input type="hidden" name="status" value="<?= esc($status) ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm<?= (isset($_GET['status']) && $_GET['status'] == $status) ? ' active' : '' ?>">
                                        Status <?= esc($status) ?> (<?= esc($total) ?>)
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        <div class="col-auto">
                            <a href="?" class="btn btn-outline-secondary btn-sm<?= !isset($_GET['status']) ? ' active' : '' ?>">Todos</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (is_string($result)): ?>
                <div class="alert alert-danger"> <?= esc($result) ?> </div>
            <?php elseif (is_array($result) && count($result) > 0): ?>
                <?php
                // Ordena por Year do mais antigo para o mais novo
                $colYear = null;
                $cols = array_keys($result[0]);

                // Remove BOM (Byte Order Mark) se existir e atualiza nomes dos campos nas linhas
                foreach ($cols as $idx => $c) {
                    $c = str_replace(chr(239) . chr(187) . chr(191), '', $c);
                    $cols[$idx] = trim($c);
                }
                // Atualiza os nomes dos campos em cada linha
                foreach ($result as $i => $row) {
                    $novo = [];
                    $j = 0;
                    foreach ($row as $v) {
                        $novo[$cols[$j]] = $v;
                        $j++;
                    }
                    $result[$i] = $novo;
                }
                // Descobre coluna Year
                $colYear = null;
                foreach ($cols as $c) {
                    if (strtolower($c) == 'year') {
                        $colYear = $c;
                        break;
                    }
                }
                if ($colYear) {
                    usort($result, function ($a, $b) use ($colYear) {
                        return ($a[$colYear] ?? 0) <=> ($b[$colYear] ?? 0);
                    });
                }
                ?>
                <table class="table table-bordered table-hover bg-white" style="width:100%">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="50%">title</th>
                            <th width="35%">authors</th>
                            <th width="5%">Year</th>
                            <th width="5%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $i => $row): ?>
                            <tr>
                                <td><?= esc($row['ID'] ?? $row['id'] ?? '-') ?> / <?= esc($row['submit_id'] ?? '-') ?></td>
                                <td><?= esc($row['Title'] ?? '-') ?></td>
                                <td><?= esc($row['Authors'] ?? '-') ?></td>
                                <td>
                                    <?= esc($row['Year'] ?? $row['year'] ?? '-') ?>
                                    n. <?= esc($row['ID1'] ?? $row['num'] ?? '-') ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $row['status'] ?? 0;
                                    $btn = 'Confirmar Submissão';
                                    $btnClass = 'btn-success';
                                    $action = base_url('ojs/send');
                                    if ($status == 1) {
                                        $btn = 'Atualizar título';
                                        $btnClass = 'btn-warning';
                                        $action = base_url('ojs/send/1');
                                    } elseif ($status == 2) {
                                        $btn = 'Atualizar autores';
                                        $btnClass = 'btn-info';
                                        $action = base_url('ojs/send/2');
                                    } elseif ($status == 3) {
                                        $btn = 'Enviar Arquivo';
                                        $btnClass = 'btn-secondary';
                                        $action = base_url('ojs/send/3');
                                    } elseif ($status == 4) {
                                        $btn = 'Upload de arquivo';
                                        $btnClass = 'btn-dark';
                                        $action = base_url('ojs/send/4');
                                    }
                                    ?>
                                    <form method="post" action="<?= $action ?>" style="display:inline">
                                        <?php foreach ($row as $k => $v): ?>
                                            <input type="hidden" name="csv[<?= esc($k) ?>]" value="<?= esc($v) ?>">
                                        <?php endforeach; ?>
                                        <?php
                                        $status = $row['status'] ?? 0;
                                        $btn = 'Confirmar Submissão';
                                        $btnClass = 'btn-success';
                                        if ($status == 1) {
                                            $btn = 'Atualizar título';
                                            $btnClass = 'btn-warning';
                                        } elseif ($status == 2) {
                                            $btn = 'Atualizar autores';
                                            $btnClass = 'btn-info';
                                        } elseif ($status == 3) {
                                            $btn = 'Enviar Arquivo';
                                            $btnClass = 'btn-secondary';
                                        } elseif ($status == 4) {
                                            $btn = 'Upload de arquivo';
                                            $btnClass = 'btn-dark';
                                        }
                                        ?>
                                        <button type="submit" class="btn <?= $btnClass ?> btn-sm"><?= $btn ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">Nenhum dado encontrado no CSV.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>