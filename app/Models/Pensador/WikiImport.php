<?php

namespace App\Models\Pensador;

use RuntimeException;

/** Importa apenas APIs Wikimedia e armazena um retrato local de cada fonte. */
class WikiImport
{
    private array $entities = [];

    public function directory(int $id): string
    {
        if ($id < 1) {
            throw new RuntimeException('Identificador de pensador inválido.');
        }
        return FCPATH . '_repository/thinkers/' . $id . '/';
    }

    public function read(int $id): array
    {
        $path = $this->directory($id) . 'data.json';
        if (!is_file($path)) {
            return ['dados' => [], 'fotos' => [], 'fontes' => []];
        }
        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data)) {
            throw new RuntimeException('Não foi possível ler os dados locais do pensador.');
        }
        return $data;
    }

    /** Importa para o repositório do pensador a foto já existente no perfil BRAPCI. */
    public function importBrapciPhoto(array $pensador): bool
    {
        $id = (int) ($pensador['id'] ?? 0);
        $brapciId = (int) ($pensador['brapci_id'] ?? 0);
        if ($id < 1 || $brapciId < 1) {
            return false;
        }

        $profileBody = $this->download('https://cip.brapci.inf.br/api/brapci/get/v3/' . $brapciId, 4 * 1024 * 1024);
        $profile = json_decode($profileBody, true);
        if (!is_array($profile)) {
            throw new RuntimeException('O perfil BRAPCI retornou um JSON inválido.');
        }
        $photoUrl = trim((string) ($profile['Photo'] ?? $profile['data']['Photo'] ?? ''));
        if ($photoUrl === '') {
            return false;
        }

        $photoParts = parse_url($photoUrl);
        if (($photoParts['scheme'] ?? '') !== 'https'
            || strtolower((string) ($photoParts['host'] ?? '')) !== 'cip.brapci.inf.br'
            || isset($photoParts['user']) || isset($photoParts['port'])
            || !str_starts_with((string) ($photoParts['path'] ?? ''), '/_repository/')) {
            throw new RuntimeException('O perfil BRAPCI retornou um endereço de foto não permitido.');
        }

        $localPhoto = FCPATH . ltrim(rawurldecode((string) $photoParts['path']), '/');
        $bytes = is_file($localPhoto) ? file_get_contents($localPhoto) : $this->download($photoUrl, 15 * 1024 * 1024);
        if ($bytes === false || !function_exists('imagecreatefromstring')) {
            throw new RuntimeException('Não foi possível ler a foto do perfil BRAPCI.');
        }
        $size = @getimagesizefromstring($bytes);
        if (!$size || $size[0] * $size[1] > 20000000) {
            throw new RuntimeException('A foto do perfil BRAPCI possui formato ou tamanho não suportado.');
        }

        $dir = $this->directory($id);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Não foi possível criar a pasta do pensador.');
        }
        $lock = fopen($dir . '.import.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            if ($lock) { fclose($lock); }
            throw new RuntimeException('Já existe uma atualização em andamento para este pensador.');
        }

        try {
            $stored = $this->read($id);
            $origin = $photoUrl;
            $hash = hash('sha256', $bytes);
            foreach ($stored['fotos'] ?? [] as $photo) {
                if (($photo['sha256'] ?? '') === $hash && is_file($dir . ($photo['arquivo'] ?? ''))) {
                    return false;
                }
            }

            // Um perfil BRAPCI possui uma única foto corrente; substitui o registro anterior.
            $stored['fotos'] = array_values(array_filter(
                $stored['fotos'] ?? [],
                static fn (array $photo): bool => ($photo['fonte'] ?? '') !== 'brapci'
                    && !str_starts_with((string) ($photo['origem'] ?? ''), 'brapci:')
            ));

            $image = @imagecreatefromstring($bytes);
            if (!$image) {
                throw new RuntimeException('Não foi possível converter a foto BRAPCI para JPEG.');
            }
            $filename = sprintf('thinker_%d_brapci.jpg', $id);
            try {
                if (!imagejpeg($image, $dir . $filename, 90)) {
                    throw new RuntimeException('Não foi possível salvar a foto BRAPCI.');
                }
            } finally {
                imagedestroy($image);
            }

            $stored['fotos'][] = [
                'arquivo' => $filename,
                'origem' => $origin,
                'fonte' => 'brapci',
                'sha256' => $hash,
                'descricao_url' => 'https://brapci.inf.br/v/' . $brapciId,
                'autor' => '',
                'licenca' => '',
            ];
            $stored['fontes']['brapci'] = [
                'url' => 'https://brapci.inf.br/v/' . $brapciId,
                'atualizado_em' => date(DATE_ATOM),
            ];
            $json = json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            if (file_put_contents($dir . 'data.json.tmp', $json, LOCK_EX) === false
                || !rename($dir . 'data.json.tmp', $dir . 'data.json')) {
                @unlink($dir . $filename);
                throw new RuntimeException('Não foi possível registrar a foto BRAPCI do pensador.');
            }
            return true;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /** Salva uma foto enviada manualmente no repositório do pensador. */
    public function uploadPhoto(int $id, string $temporaryFile): bool
    {
        if ($id < 1 || !is_file($temporaryFile)) {
            throw new RuntimeException('Arquivo de foto inválido.');
        }
        $bytes = file_get_contents($temporaryFile);
        if ($bytes === false || strlen($bytes) > 15 * 1024 * 1024) {
            throw new RuntimeException('A foto deve possuir no máximo 15 MB.');
        }
        if (!function_exists('imagecreatefromstring')) {
            throw new RuntimeException('A extensão GD é necessária para salvar fotos JPEG.');
        }
        $size = @getimagesizefromstring($bytes);
        if (!$size || $size[0] * $size[1] > 20000000) {
            throw new RuntimeException('Formato ou dimensões da imagem não suportados.');
        }
        if (!in_array($size['mime'] ?? '', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
            throw new RuntimeException('Envie uma imagem JPEG, PNG, GIF ou WebP.');
        }

        $dir = $this->directory($id);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Não foi possível criar a pasta do pensador.');
        }
        $lock = fopen($dir . '.import.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            if ($lock) { fclose($lock); }
            throw new RuntimeException('Já existe uma atualização em andamento para este pensador.');
        }

        try {
            $stored = $this->read($id);
            $hash = hash('sha256', $bytes);
            foreach ($stored['fotos'] ?? [] as $photo) {
                if (($photo['sha256'] ?? '') === $hash && is_file($dir . ($photo['arquivo'] ?? ''))) {
                    return false;
                }
            }

            $image = @imagecreatefromstring($bytes);
            if (!$image) {
                throw new RuntimeException('Não foi possível processar a imagem enviada.');
            }
            $number = 1;
            do {
                $filename = sprintf('thinker_%d_upload_%02d.jpg', $id, $number++);
            } while (is_file($dir . $filename));
            try {
                if (!imagejpeg($image, $dir . $filename, 90)) {
                    throw new RuntimeException('Não foi possível salvar a foto enviada.');
                }
            } finally {
                imagedestroy($image);
            }

            $stored['fotos'][] = [
                'arquivo' => $filename,
                'origem' => 'upload:' . $hash,
                'fonte' => 'upload',
                'sha256' => $hash,
                'descricao_url' => '',
                'autor' => '',
                'licenca' => '',
            ];
            $stored['fontes']['upload'] = [
                'atualizado_em' => date(DATE_ATOM),
            ];
            $json = json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            if (file_put_contents($dir . 'data.json.tmp', $json, LOCK_EX) === false
                || !rename($dir . 'data.json.tmp', $dir . 'data.json')) {
                @unlink($dir . $filename);
                throw new RuntimeException('Não foi possível registrar a foto enviada.');
            }
            return true;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    public function update(array $pensador, string $source): array
    {
        if (!in_array($source, ['wikidata', 'wikipedia'], true)) {
            throw new RuntimeException('Fonte inválida.');
        }
        $id = (int) $pensador['id'];
        $url = trim((string) ($pensador['link_' . $source] ?? ''));
        $pictures = [];
        $snapshot = ['url' => $url, 'atualizado_em' => date(DATE_ATOM)];
        if ($source === 'wikidata') {
            if (!preg_match('~^https?://(?:www\.)?wikidata\.org/(?:wiki/|entity/)(Q[1-9][0-9]*)(?:[?#].*)?$~i', $url, $match)) {
                throw new RuntimeException('Informe um link Wikidata válido no cadastro (https://www.wikidata.org/wiki/Q...).');
            }
            $qid = strtoupper($match[1]);
        } else {
            $parts = parse_url($url);
            if (!$parts || !in_array($parts['scheme'] ?? '', ['http', 'https'], true)
                || !preg_match('/^([a-z-]+)(?:\.m)?\.wikipedia\.org$/i', $parts['host'] ?? '', $match)) {
                throw new RuntimeException('Informe um link de artigo da Wikipédia válido no cadastro.');
            }
            $host = strtolower($match[1]) . '.wikipedia.org';
            $title = str_starts_with($parts['path'] ?? '', '/wiki/') ? rawurldecode(substr($parts['path'], 6)) : '';
            if ($title === '') {
                parse_str($parts['query'] ?? '', $query);
                $title = is_string($query['title'] ?? null) ? $query['title'] : '';
            }
            if ($title === '') {
                throw new RuntimeException('O link da Wikipédia não contém o título do artigo.');
            }
            $response = $this->api($host, [
                'action' => 'query', 'prop' => 'pageprops|pageimages|extracts', 'titles' => $title,
                'redirects' => 1, 'exintro' => 1, 'explaintext' => 1, 'piprop' => 'name',
            ]);
            $page = $response['query']['pages'][0] ?? [];
            if (!$page || isset($page['missing']) || isset($page['invalid'])) {
                throw new RuntimeException('Artigo não encontrado na Wikipédia.');
            }
            $snapshot['titulo'] = $page['title'];
            $snapshot['resumo'] = $page['extract'] ?? '';
            $qid = $page['pageprops']['wikibase_item'] ?? '';
            if (!empty($page['pageimage'])) {
                $pictures[] = ['host' => $host, 'title' => $page['pageimage']];
            }
        }
        $data = [];
        if ($qid !== '') {
            $entity = $this->entity($qid);
            $snapshot['wikidata_id'] = $qid;
            $data = $this->biography($entity);
            foreach ($this->values($entity, 'P18') as $filename) {
                if (is_string($filename)) {
                    $pictures[] = ['host' => 'commons.wikimedia.org', 'title' => $filename];
                }
            }
        }
        if (empty($data['nome_completo'])) {
            $data['nome_completo'] = $snapshot['titulo'] ?? $pensador['nome'];
        }
        $snapshot['dados'] = $data;
        $dir = $this->directory($id);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Não foi possível criar a pasta do pensador.');
        }
        $lock = fopen($dir . '.import.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            if ($lock) { fclose($lock); }
            throw new RuntimeException('Já existe uma atualização em andamento para este pensador.');
        }
        try {
            $stored = $this->read($id);
            $warnings = [];
            foreach (array_unique($pictures, SORT_REGULAR) as $picture) {
                try {
                    $this->savePicture($picture, $id, $stored['fotos']);
                } catch (RuntimeException $e) {
                    $warnings[] = $picture['title'] . ': ' . $e->getMessage();
                }
            }
            // Campos ausentes numa fonte não apagam dados já obtidos da outra.
            foreach ($data as $key => $value) {
                if ($value !== null && $value !== '' && $value !== []) {
                    $stored['dados'][$key] = $value;
                }
            }
            $snapshot['avisos'] = $warnings;
            $stored['fontes'][$source] = $snapshot;
            $json = json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            if (file_put_contents($dir . 'data.json.tmp', $json, LOCK_EX) === false || !rename($dir . 'data.json.tmp', $dir . 'data.json')) {
                throw new RuntimeException('Não foi possível salvar os dados do pensador.');
            }
            return $warnings;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    protected function api(string $host, array $params): array
    {
        $body = $this->download('https://' . $host . '/w/api.php?' . http_build_query($params + ['format' => 'json', 'formatversion' => 2]), 8 * 1024 * 1024);
        $json = json_decode($body, true);
        if (!is_array($json) || isset($json['error'])) {
            throw new RuntimeException('A API Wikimedia retornou uma resposta inválida.');
        }
        return $json;
    }

    protected function download(string $url, int $limit): string
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        if (($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['port'])
            || !(in_array($host, ['www.wikidata.org', 'commons.wikimedia.org', 'upload.wikimedia.org', 'thumb.wikimedia.org', 'cip.brapci.inf.br'], true)
                || preg_match('/^[a-z-]+\.wikipedia\.org$/', $host))) {
            throw new RuntimeException('Endereço de download não permitido.');
        }
        $body = '';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'BrapciThinkers/1.0 (https://brapci.inf.br)',
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$body, $limit): int {
                if (strlen($body) + strlen($chunk) > $limit) { return 0; }
                $body .= $chunk;
                return strlen($chunk);
            },
        ]);
        $ok = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        unset($ch);
        if ($ok === false || $status !== 200) {
            throw new RuntimeException('Falha ao consultar o serviço remoto (HTTP ' . $status . '). Tente novamente.');
        }
        return $body;
    }

    private function entity(string $id): array
    {
        if (!preg_match('/^Q[1-9][0-9]*$/', $id)) {
            throw new RuntimeException('Identificador Wikidata inválido.');
        }
        if (!isset($this->entities[$id])) {
            $response = $this->api('www.wikidata.org', ['action' => 'wbgetentities', 'ids' => $id, 'props' => 'labels|claims', 'languages' => 'pt|en', 'languagefallback' => 1]);
            $entity = $response['entities'][$id] ?? [];
            if (!$entity || array_key_exists('missing', $entity)) {
                throw new RuntimeException('Item não encontrado no Wikidata.');
            }
            $this->entities[$id] = $entity;
        }
        return $this->entities[$id];
    }

    private function values(array $entity, string $property): array
    {
        $claims = array_filter($entity['claims'][$property] ?? [], static fn ($claim) => ($claim['rank'] ?? '') !== 'deprecated');
        $values = [];
        foreach ($claims as $claim) {
            if (isset($claim['mainsnak']['datavalue']['value'])) {
                $values[] = $claim['mainsnak']['datavalue']['value'];
            }
        }
        return $values;
    }

    private function label(array $entity): string
    {
        return $entity['labels']['pt']['value'] ?? $entity['labels']['en']['value'] ?? $entity['id'] ?? '';
    }

    private function biography(array $entity): array
    {
        $name = $this->values($entity, 'P1559')[0]['text'] ?? $this->label($entity);
        $data = ['nome_completo' => $name];
        foreach (['P569' => 'data_nascimento', 'P570' => 'data_falecimento'] as $property => $key) {
            $date = $this->values($entity, $property)[0] ?? null;
            if ($date) { $data[$key] = $this->formatDate($date); }
        }
        $places = [];
        $countries = [];
        foreach ($this->values($entity, 'P19') as $place) {
            if (!isset($place['id'])) { continue; }
            $location = $this->entity($place['id']);
            $places[] = $this->label($location);
            // O país vem do local de nascimento, não da cidadania do pensador.
            foreach ($this->values($location, 'P17') as $country) {
                if (isset($country['id'])) {
                    $countries[$country['id']] = $this->label($this->entity($country['id']));
                }
            }
        }
        $data['local_nascimento'] = implode('; ', array_unique($places));
        $data['pais_nascimento'] = implode('; ', array_unique($countries));
        $institutions = [];
        foreach (['P108', 'P1416'] as $property) {
            foreach ($this->values($entity, $property) as $institution) {
                if (isset($institution['id'])) {
                    $institutions[$institution['id']] = ['id' => $institution['id'], 'nome' => $this->label($this->entity($institution['id']))];
                }
            }
        }
        $data['instituicoes'] = array_values($institutions);
        return $data;
    }

    private function formatDate(array $date): string
    {
        if (!preg_match('/^([+-])(\d+)-(\d{2})-(\d{2})T/', $date['time'] ?? '', $match)) { return ''; }
        $year = ltrim($match[2], '0') ?: '0';
        $precision = (int) ($date['precision'] ?? 9);
        $value = $precision >= 11 ? $match[4] . '/' . $match[3] . '/' . $year : ($precision === 10 ? $match[3] . '/' . $year : $year);
        if ($precision < 9) { $value = 'aprox. ' . $value; }
        return $value . ($match[1] === '-' ? ' a.C.' : '');
    }

    private function savePicture(array $picture, int $id, array &$photos): void
    {
        $response = $this->api($picture['host'], [
            'action' => 'query', 'titles' => 'File:' . $picture['title'], 'prop' => 'imageinfo',
            'iiprop' => 'url|extmetadata', 'iiurlwidth' => 1200,
        ]);
        $info = $response['query']['pages'][0]['imageinfo'][0] ?? [];
        $url = $info['thumburl'] ?? $info['url'] ?? '';
        if ($url === '') { throw new RuntimeException('Imagem indisponível.'); }
        foreach ($photos as $photo) {
            if (($photo['origem'] ?? '') === ($info['url'] ?? $url) && is_file($this->directory($id) . $photo['arquivo'])) { return; }
        }
        if (!function_exists('imagecreatefromstring')) { throw new RuntimeException('A extensão GD é necessária para salvar fotos JPEG.'); }
        $bytes = $this->download($url, 15 * 1024 * 1024);
        $size = @getimagesizefromstring($bytes);
        if (!$size || $size[0] * $size[1] > 20000000) { throw new RuntimeException('Formato ou tamanho de imagem não suportado.'); }
        $hash = hash('sha256', $bytes);
        foreach ($photos as $photo) {
            if (($photo['sha256'] ?? '') === $hash && is_file($this->directory($id) . $photo['arquivo'])) { return; }
        }
        $image = @imagecreatefromstring($bytes);
        if (!$image) { throw new RuntimeException('Não foi possível converter a imagem para JPEG.'); }
        $number = 1;
        do { $filename = sprintf('thinker_%d_%02d.jpg', $id, $number++); } while (is_file($this->directory($id) . $filename));
        try {
            if (!imagejpeg($image, $this->directory($id) . $filename, 90)) { throw new RuntimeException('Não foi possível salvar a foto.'); }
        } finally { imagedestroy($image); }
        $meta = $info['extmetadata'] ?? [];
        $photos[] = [
            'arquivo' => $filename, 'origem' => $info['url'] ?? $url, 'sha256' => $hash,
            'descricao_url' => $info['descriptionurl'] ?? '',
            'autor' => trim(strip_tags($meta['Artist']['value'] ?? '')),
            'licenca' => trim(strip_tags($meta['LicenseShortName']['value'] ?? '')),
        ];
    }
}
