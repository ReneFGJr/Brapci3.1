<?php

namespace Tests\Unit\Metadata;

use App\Models\Metadata\Bibtex;
use App\Models\Metadata\RIS;
use CodeIgniter\Test\CIUnitTestCase;

final class ExportFormatTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper(['sisdoc_forms', 'nbr']);
    }

    public function testRisAcceptsAnIncompleteRecord(): void
    {
        $result = (new RIS())->short(['ID' => '123']);

        $this->assertStringContainsString('TY  - JOUR', $result);
        $this->assertStringContainsString('ER  -', $result);
    }

    public function testBibtexAcceptsAnIncompleteRecord(): void
    {
        $result = (new Bibtex())->short(['ID' => '123']);

        $this->assertStringContainsString('@article{unknown,', $result);
        $this->assertStringContainsString('brapci/123', $result);
    }

    public function testExportersIgnoreInvalidAuthorValues(): void
    {
        $record = [
            'ID' => '123',
            'Authors' => [null, ['invalid'], 'Autor Válido'],
        ];

        $this->assertStringContainsString('AU  -', (new RIS())->short($record));
        $this->assertStringContainsString('author = {', (new Bibtex())->short($record));
    }
}
