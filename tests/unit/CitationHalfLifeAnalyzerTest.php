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
        $this->assertSame(7, $data['referencias_com_ano']);
        $this->assertSame(1, $data['referencias_sem_ano']);
        $this->assertSame(2016.0, $data['ano_mediano']);
        $this->assertSame(10.0, $data['meia_vida']);
        $this->assertSame(1, $data['tipologias']['artigos']['quantidade']);
        $this->assertSame(1, $data['tipologias']['eventos']['quantidade']);
        $this->assertSame(1, $data['tipologias']['livros']['quantidade']);
        $this->assertSame(1, $data['tipologias']['capitulos_de_livros']['quantidade']);
        $this->assertSame(1, $data['tipologias']['sites']['quantidade']);
        $this->assertSame(1, $data['tipologias']['teses']['quantidade']);
        $this->assertSame(1, $data['tipologias']['dissertacoes']['quantidade']);
        $this->assertSame(1, $data['tipologias']['outras_tipologias']['quantidade']);
    }

    public function testReturnsNullHalfLifeWhenNoValidYearExists(): void
    {
        $data = (new CitationHalfLifeAnalyzer())->analyze("Referência sem ano\nOutra referência", 2026);

        $this->assertNull($data['ano_mediano']);
        $this->assertNull($data['meia_vida']);
        $this->assertSame(2, $data['referencias_sem_ano']);
    }
}
