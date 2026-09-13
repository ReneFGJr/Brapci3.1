<?php

namespace Tests\Unit;

use App\Services\CitationHalfLifeAnalyzer;
use CodeIgniter\Test\CIUnitTestCase;

final class CitationHalfLifeAnalyzerTest extends CIUnitTestCase
{
    public function testCalculatesHalfLifeAndGroupsTypologies(): void
    {
        $text = implode("\n", [
            'SILVA, A. Artigo científico. Revista Exemplo, v. 1, n. 2, 2020.',
            'SOUZA, B. Trabalho. Anais do Congresso Nacional, 2018.',
            'COSTA, C. Livro exemplo. Editora Teste, 2016. ISBN 123.',
            'LIMA, D. Capítulo exemplo. In: Livro coletivo. Editora Teste, 2014.',
            'Portal Brapci. Disponível em: https://brapci.inf.br. Acesso em: 2022.',
            'ALVES, E. Pesquisa. Tese (Doutorado), 2012.',
            'MORAES, F. Estudo. Dissertação (Mestrado), 2010.',
            'Referência sem tipologia e sem ano.',
        ]);

        $data = (new CitationHalfLifeAnalyzer())->analyze($text, 2026);

        $this->assertSame(8, $data['total_referencias']);
        $this->assertSame(6, $data['referencias_com_ano']);
        $this->assertSame(2, $data['referencias_sem_ano']);
        $this->assertSame(2015.0, $data['ano_mediano']);
        $this->assertSame(11.0, $data['meia_vida']);
        $this->assertSame(1, $data['tipologias']['artigos']['quantidade']);
        $this->assertSame(1, $data['tipologias']['eventos']['quantidade']);
        $this->assertSame(1, $data['tipologias']['livros']['quantidade']);
        $this->assertSame(1, $data['tipologias']['capitulos_de_livros']['quantidade']);
        $this->assertSame(0, $data['tipologias']['sites']['quantidade']);
        $this->assertSame(1, $data['tipologias']['teses']['quantidade']);
        $this->assertSame(1, $data['tipologias']['dissertacoes']['quantidade']);
        $this->assertSame(2, $data['tipologias']['outras_tipologias']['quantidade']);
        $this->assertArrayHasKey('portugues', $data['idiomas']);
        $this->assertArrayHasKey('ingles', $data['idiomas']);
        $this->assertArrayHasKey('espanhol', $data['idiomas']);
        $this->assertArrayHasKey('frances', $data['idiomas']);
        $this->assertSame(8, array_sum(array_column($data['idiomas'], 'quantidade')));
    }

    public function testIdentifiesFourSupportedLanguages(): void
    {
        $text = implode("\n", [
            'Artigo em português. Disponível em: exemplo. Acesso em: 2020.',
            'Research article. Available at: example. Accessed 2021.',
            'Artículo de investigación. Disponible en: ejemplo. Universidad, 2022.',
            'Article scientifique. Disponible sur: exemple. Revue française, 2023.',
        ]);

        $data = (new CitationHalfLifeAnalyzer())->analyze($text, 2026);

        $this->assertSame(1, $data['idiomas']['portugues']['quantidade']);
        $this->assertSame(1, $data['idiomas']['ingles']['quantidade']);
        $this->assertSame(1, $data['idiomas']['espanhol']['quantidade']);
        $this->assertSame(1, $data['idiomas']['frances']['quantidade']);
    }

    public function testReturnsNullHalfLifeWhenNoValidYearExists(): void
    {
        $data = (new CitationHalfLifeAnalyzer())->analyze("Referência sem ano\nOutra referência", 2026);

        $this->assertNull($data['ano_mediano']);
        $this->assertNull($data['meia_vida']);
        $this->assertSame(2, $data['referencias_sem_ano']);
    }

    public function testUsesPublicationYearAndIgnoresAvailabilityData(): void
    {
        $reference = 'CANDELA, L. et al. Data journals: A survey. Journal of the Association for '
            . 'Information Science and Technology, v. 66, n. 9, p. 1747-1762, 2015. '
            . 'Disponível em: https://example.org/doi/23358. Acesso em: 28 jul. 2026';

        $data = (new CitationHalfLifeAnalyzer())->analyze($reference, 2026);

        $this->assertSame(2015, $data['referencias'][0]['ano']);
        $this->assertSame(11, $data['referencias'][0]['idade']);
        $this->assertSame(11.0, $data['meia_vida']);
        $this->assertStringNotContainsString('Disponível em', $data['referencias'][0]['referencia']);
        $this->assertStringNotContainsString('2026', $data['referencias'][0]['referencia']);
        $this->assertSame('ingles', $data['referencias'][0]['idioma']);
    }
}
