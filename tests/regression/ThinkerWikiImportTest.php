<?php
// Executar: php tests/regression/ThinkerWikiImportTest.php
require __DIR__ . '/../../app/Models/Pensador/WikiImport.php';

$root = __DIR__ . '/../../writable/thinker-test-' . bin2hex(random_bytes(5)) . '/';
mkdir($root, 0775, true);
define('FCPATH', $root);

class FixtureWikiImport extends \App\Models\Pensador\WikiImport
{
    public bool $failPhoto = false;
    public bool $emptyDates = false;

    protected function api(string $host, array $params): array
    {
        if (($params['action'] ?? '') === 'wbgetentities') {
            $id = $params['ids'];
            $entity = ['id' => $id, 'labels' => ['pt' => ['value' => $id === 'Q1' ? 'Nome completo' : 'Instituição / local']]];
            if ($id === 'Q2') {
                $entity['claims']['P17'] = [
                    ['rank' => 'normal', 'mainsnak' => ['datavalue' => ['value' => ['id' => 'Q4']]]],
                    ['rank' => 'normal', 'mainsnak' => ['datavalue' => ['value' => ['id' => 'Q4']]]],
                ];
            }
            if ($id === 'Q4') { $entity['labels']['pt']['value'] = 'Brasil'; }
            if ($id === 'Q1') {
                $values = [
                    'P569' => [['time' => '+1900-00-00T00:00:00Z', 'precision' => 9]],
                    'P570' => [['time' => '+1980-02-03T00:00:00Z', 'precision' => 11]],
                    'P19' => [['id' => 'Q2']], 'P108' => [['id' => 'Q3']], 'P1416' => [['id' => 'Q3']],
                    'P18' => ['A.png', 'B.png'],
                ];
                if ($this->emptyDates) { unset($values['P569'], $values['P570']); }
                foreach ($values as $property => $items) {
                    foreach ($items as $value) {
                        $entity['claims'][$property][] = ['rank' => 'normal', 'mainsnak' => ['datavalue' => ['value' => $value]]];
                    }
                }
            }
            return ['entities' => [$id => $entity]];
        }
        if (($params['prop'] ?? '') === 'imageinfo') {
            $name = substr($params['titles'], 5);
            return ['query' => ['pages' => [['imageinfo' => [[
                'url' => 'https://upload.wikimedia.org/' . $name,
                'descriptionurl' => 'https://commons.wikimedia.org/wiki/File:' . $name,
                'extmetadata' => ['Artist' => ['value' => '<b>Fotógrafo</b>'], 'LicenseShortName' => ['value' => 'CC BY']],
            ]]]]]];
        }
        return ['query' => ['pages' => [['title' => 'Artigo', 'extract' => 'Resumo', 'pageprops' => ['wikibase_item' => 'Q1'], 'pageimage' => 'A.png']]]];
    }

    protected function download(string $url, int $limit): string
    {
        if ($this->failPhoto) { throw new RuntimeException('Falha de foto simulada.'); }
        $image = imagecreatetruecolor(3, 3);
        imagefill($image, 0, 0, imagecolorallocate($image, str_contains($url, 'A.png') ? 255 : 0, 0, 100));
        ob_start(); imagepng($image); $bytes = ob_get_clean(); imagedestroy($image);
        return $bytes;
    }
}
function check(bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
}
try {
    $person = ['id' => 1, 'nome' => 'Cadastro', 'link_wikidata' => 'https://www.wikidata.org/wiki/Q1', 'link_wikipedia' => 'https://pt.wikipedia.org/wiki/Artigo'];
    $service = new FixtureWikiImport();
    check($service->read(1)['fotos'] === [], 'Estado inicial');
    check($service->update($person, 'wikidata') === [], 'Importação sem avisos');
    $data = $service->read(1);
    check($data['dados']['nome_completo'] === 'Nome completo', 'Nome');
    check($data['dados']['data_nascimento'] === '1900', 'Preservar precisão de ano');
    check($data['dados']['data_falecimento'] === '03/02/1980', 'Data completa');
    check(count($data['dados']['instituicoes']) === 1, 'Deduplicar instituições');
    check($data['dados']['pais_nascimento'] === 'Brasil', 'País obtido do local de nascimento, sem duplicação');
    check(count($data['fotos']) === 2, 'Todas as fotos');
    foreach ([1, 2] as $number) {
        $file = $service->directory(1) . sprintf('thinker_1_%02d.jpg', $number);
        check(getimagesize($file)['mime'] === 'image/jpeg', 'JPEG real e nome sequencial');
    }
    $service->update($person, 'wikidata');
    check(count($service->read(1)['fotos']) === 2, 'Não duplicar imagens');
    $other = new FixtureWikiImport(); $other->emptyDates = true;
    $other->update($person, 'wikipedia');
    $data = $other->read(1);
    check(isset($data['fontes']['wikidata'], $data['fontes']['wikipedia']), 'Guardar as duas fontes');
    check($data['dados']['data_nascimento'] === '1900', 'Não apagar campos ausentes');
    check($data['fontes']['wikipedia']['resumo'] === 'Resumo', 'Resumo Wikipedia');
    check(count($data['fotos']) === 2, 'Deduplicar entre fontes');
    $bad = $person; $bad['link_wikipedia'] = 'http://127.0.0.1/wiki/Artigo';
    try { $other->update($bad, 'wikipedia'); throw new LogicException('URL local aceita'); } catch (RuntimeException $expected) {}
    $failed = new FixtureWikiImport(); $failed->failPhoto = true; $person['id'] = 2;
    check(count($failed->update($person, 'wikidata')) === 2, 'Avisar falhas de fotos');
    check($failed->read(2)['dados']['nome_completo'] === 'Nome completo', 'Salvar dados mesmo com falha de foto');
    echo "OK: biografia, datas, instituições, fontes, JPEGs, deduplicação, URL e falhas parciais.\n";
    if (in_array('--live', $argv, true)) {
        $live = new \App\Models\Pensador\WikiImport();
        $person = ['id' => 3, 'nome' => 'Albert Einstein', 'link_wikidata' => 'https://www.wikidata.org/wiki/Q937', 'link_wikipedia' => 'https://pt.wikipedia.org/wiki/Albert_Einstein'];
        foreach (['wikidata', 'wikipedia'] as $source) {
            $warnings = $live->update($person, $source);
            $data = $live->read(3);
            echo $source . ': ' . json_encode(['dados' => $data['dados'], 'fotos' => count($data['fotos']), 'avisos' => $warnings], JSON_UNESCAPED_UNICODE) . "\n";
            check($data['dados']['data_nascimento'] === '14/03/1879', 'Data real de nascimento');
            check(count($data['fotos']) >= 1, 'Foto real importada');
        }
    }
} finally {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $file) {
        if ($file->isDir()) { rmdir($file->getPathname()); } else { unlink($file->getPathname()); }
    }
    rmdir($root);
}
