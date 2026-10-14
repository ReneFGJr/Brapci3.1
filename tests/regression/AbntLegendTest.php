<?php
// Executar: php tests/regression/AbntLegendTest.php
namespace CodeIgniter { class Model {} }
namespace {
    function sonumero($value) { return preg_replace('/[^0-9]/', '', (string) $value); }
    require __DIR__ . '/../../app/Models/Metadata/Abnt.php';
    set_error_handler(static function ($severity, $message) { throw new \ErrorException($message, 0, $severity); });
    $model = new \App\Models\Metadata\Abnt();
    $legacy = ['journal' => 'Revista', 'vol' => '2', 'nr' => '3', 'year' => '2026'];
    $current = ['publisher' => 'Revista', 'is_vol' => '2', 'is_nr' => '3', 'is_year' => '2026'];
    if ($model->ref_legend($legacy) !== $model->ref_legend($current)) {
        throw new \RuntimeException('Formatos de metadata devem produzir a mesma legenda.');
    }
    if ($model->ref_legend([]) !== '') { throw new \RuntimeException('Issue ausente'); }
    if ($model->ref_legend(['journal' => 'Legado', 'publisher' => 'Atual']) !== '<b>Legado</b>') {
        throw new \RuntimeException('Preservar nome de periódico legado');
    }
    restore_error_handler();
    echo "OK: legendas atuais, legadas e sem periódico.\n";
}
