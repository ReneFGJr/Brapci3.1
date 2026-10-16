<?php

namespace Tests\Unit;

use App\Models\Metadata\NET;
use CodeIgniter\Test\CIUnitTestCase;

final class MetadataNetTest extends CIUnitTestCase
{
    public function testSubjectsBuildsWeightedPajekNetwork(): void
    {
        $network = (new NET())->subjects([
            ['KEYWORDS' => 'Ciencia da informacao; Bibliometria; Bibliometria'],
            ['KEYWORDS' => 'bibliometria; Recuperacao da informacao'],
        ]);

        $this->assertStringContainsString("*Vertices 3\r\n", $network);
        $this->assertStringContainsString('"Bibliometria" ellipse x_fact 10.00 y_fact 10.00', $network);
        $this->assertSame(2, substr_count($network, " 1\r\n"));
        $this->assertStringEndsWith("\r\n", $network);
    }

    public function testSubjectsAcceptsNestedKeywordArrays(): void
    {
        $network = (new NET())->subjects([
            ['KEYWORDS' => ['pt' => ['Arquivologia', 'Memoria']]],
        ]);

        $this->assertStringContainsString("*Vertices 2\r\n", $network);
        $this->assertStringContainsString('"Arquivologia"', $network);
        $this->assertStringContainsString('"Memoria"', $network);
        $this->assertStringContainsString("1 2 1\r\n", $network);
    }

    public function testSubjectsReturnsEmptyNetworkWithoutKeywords(): void
    {
        $this->assertSame(
            "*Vertices 0\r\n*Edges\r\n",
            (new NET())->subjects([['KEYWORDS' => '']]),
        );
    }

    public function testSubjectsNormalizesCaseAccentsAndSpecialCharacters(): void
    {
        $network = (new NET())->subjects([
            ['KEYWORDS' => 'CIÊNCIA-DA/INFORMAÇÃO; ciência da informação; @DADOS#ABERTOS'],
        ]);

        $this->assertStringContainsString("*Vertices 2\r\n", $network);
        $this->assertStringContainsString('"Ciencia da informacao"', $network);
        $this->assertStringContainsString('"Dados abertos"', $network);
    }

    public function testSubjectTermsNormalizesAndCreatesNetworkPerLine(): void
    {
        $network = (new NET())->subject_terms([
            ['KEYWORDS' => 'Ciência da Informação; Dados-Abertos!'],
            ['KEYWORDS' => 'INFORMAÇÃO e recuperação'],
        ]);

        $this->assertStringContainsString("*Vertices 5\r\n", $network);
        $this->assertStringContainsString('"ciencia"', $network);
        $this->assertStringContainsString('"informacao" ellipse x_fact 10.00 y_fact 10.00', $network);
        $this->assertStringContainsString('"dados"', $network);
        $this->assertStringContainsString('"abertos"', $network);
        $this->assertStringContainsString('"recuperacao"', $network);
        $this->assertStringNotContainsString('"da"', $network);
        $this->assertStringNotContainsString('"e"', $network);
        $this->assertSame(7, substr_count($network, " 1\r\n"));
    }

    public function testSubjectTermsReturnsEmptyNetworkForStopwordsOnly(): void
    {
        $this->assertSame(
            "*Vertices 0\r\n*Edges\r\n",
            (new NET())->subject_terms([['KEYWORDS' => 'a; de; e; para']]),
        );
    }
}
