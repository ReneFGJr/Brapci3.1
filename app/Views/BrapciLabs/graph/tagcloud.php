
    <style>
        body {
            background: #f4f6fa;
        }

        .bands-box {
            background: #fff;
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .band {
            margin-bottom: 26px;
        }

        .band-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
            font-size: .9rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .tag {
            padding: 8px 16px;
            border-radius: 999px;
            background: #eef2ff;
            color: #1f3dd6;
            font-weight: 500;
            white-space: nowrap;
            transition: all .2s ease;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
        }

        .tag:hover {
            background: #1f3dd6;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(31, 61, 214, .35);
        }

        .high .tag {
            font-size: 1.35rem;
        }

        .mid .tag {
            font-size: 1.05rem;
        }

        .low .tag {
            font-size: .9rem;
            opacity: .85;
        }
    </style>
</head>


    <div class="content my-5">
        <div class="bands-box">

            <div class="text-center mb-4">
                <h4 class="fw-bold">Distribuição Temática</h4>
                <p class="text-muted">Termos organizados por frequência</p>
            </div>

            <?php
            $max = max(array_column($tags, 'count'));
            $min = min(array_column($tags, 'count'));

            foreach ($tags as &$tag) {
                if ($tag['count'] >= $max * 0.66) {
                    $tag['band'] = 'high';
                } elseif ($tag['count'] >= $max * 0.33) {
                    $tag['band'] = 'mid';
                } else {
                    $tag['band'] = 'low';
                }
            }

            $bands = [
                'high' => 'Alta frequência',
                'mid'  => 'Frequência média',
                'low'  => 'Baixa frequência'
            ];
            ?>

            <?php foreach ($bands as $key => $label): ?>
                <div class="band <?= $key ?>">
                    <div class="band-title"><?= $label ?></div>
                    <div class="tags">
                        <?php foreach ($tags as $tag): ?>
                            <?php if ($tag['band'] === $key): ?>
                                <div class="tag" title="<?= $tag['count'] ?> ocorrências">
                                    <?= esc($tag['label']) ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
