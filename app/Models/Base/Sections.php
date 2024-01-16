<?php

namespace App\Models\Base;

use CodeIgniter\Model;

class Sections extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sections';
    protected $primaryKey       = 'id_sc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_sc', 'sc_name', 'sc_index', 's_section'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function showHTML($dt)
    {
        $sx = view('RDF/sections', $dt);
        return $sx;
    }

    function getSection($name)
    {
        $dt = $this->where('sc_name', $name)->first();
        if ($dt != []) {
            return $dt['id_sc'];
        }
        echo "Não existe $name";
        pre($dt);
    }

    function identify($n, $se)
    {
        $n = mb_strtolower(ascii($n));
        $se = ' ' . $se;
        $n = ' ' . $n;
        echo $n . '=> '.$se. '<br>';

        /************************************* Artigo traduzido */
        if (((strpos($n, 'traducao')))
            or (strpos($n, 'traduaaes'))
            or (strpos($n, 'traducoes'))
            or (strpos($n, 'traducciones'))

        ) {
            return $this->getSection('Artigo traduzido');
        }

        /************************************* TCC */
        if (((strpos($n, 'trabalhos de conclusao de curso')))
            or (strpos($n, 'trabalhos academicos'))
        ) {
            return $this->getSection('TCC');
        }

        /************************************* Anais */
        if (((strpos($n, 'anais')))
            or (strpos($n, 'trabalhos apresentados em eventos'))
            or (strpos($n, 'seminario'))
            or (strpos($n, 'congresso'))
            or (strpos($n, 'enancib'))
            or (strpos($n, 'ebbc'))
        ) {
            return $this->getSection('Anais');
        }

        /************************************* Editorial */
        if (((strpos($se, ':ED')))
            //or (strpos($n, 'archeion online'))
        ) {
            return $this->getSection('Editorial');
        }

        /************************************* Documentos */
        if (((strpos($n, 'documentos')))
            or (strpos($n, 'documento'))
            //or (strpos($n, 'comissao editorial'))
            //or (strpos($n, 'creditos'))
        ) {
            return $this->getSection('Documentos');
        }

        /************************************* Relatório */
        if (((strpos($n, 'relatorio de forum')))
            or (strpos($n, 'relatorios'))
            //or (strpos($n, 'comissao editorial'))
            //or (strpos($n, 'creditos'))
        ) {
            return $this->getSection('Relatório');
        }

        /************************************* Entrevistas */
        if (((strpos($n, 'entrevista')))
            or (strpos($n, 'depoimento'))
            or (strpos($n, 'interviews'))
            or (strpos($n, 'podcast'))
            //or (strpos($n, 'creditos'))
        ) {
            return $this->getSection('Entrevistas');
        }

        /************************************* Expediente */
        if (((strpos($n, 'apoio')))
            or (strpos($n, 'avaliadores'))
            or (strpos($n, 'comissao editorial'))
            or (strpos($n, 'creditos'))
            or (strpos($n, 'expediente'))
            or (strpos($n, 'institucionales'))
            or (strpos($n, 'nominata'))
            or (strpos($n, 'parecerista'))
            or (strpos($n, 'programacao'))
            or (strpos($n, 'revisores'))
         ) {
            return $this->getSection('Expediente');
        }

        /************************************* Relato de experiência */
        if (((strpos($n, 'relatos de experiencia')))
            or (strpos($n, 'relatos de pesquisa'))
            or (strpos($n, 'relato de experiencia'))
            or (strpos($n, 'relato de experiancia'))
            or (strpos($n, 'relatos de experiencia'))
            or (strpos($n, 'relato de estagio'))
            or (strpos($n, 'relatos'))
        ) {
            return $this->getSection('Relato de experiência');
        }

        /************************************* Estudo de Caso */
        if (((strpos($n, 'estudo de caso')))
            or (strpos($n, 'estudos de caso'))
            or (strpos($n, 'estudio de caso'))
            or (strpos($n, 'estudios de caso'))
            or (strpos($n, 'reportes de casos'))

        ) {
            return $this->getSection('Estudo de Caso');
        }

        /************************************* Multimídia */
        if (((strpos($n, 'registro audiovisual')))
            //or (strpos($n, 'ensayo'))
            //or (strpos($n, 'essays'))
            //or (strpos($n, 'reflexiones'))
        ) {
            return $this->getSection('Multimídia');
        }

        /************************************* Ensaio */
        if (((strpos($n, 'ensaio')))
            or (strpos($n, 'ensayo'))
            or (strpos($n, 'essays'))
            or (strpos($n, 'reflexiones'))
        ) {
            return $this->getSection('Ensaio');
        }

        /************************************* Palestra */
        if (((strpos($n, 'palestra')))
            //or (strpos($n, 'opinion'))
            //or (strpos($n, 'opinion'))
        ) {
            return $this->getSection('Palestra');
        }

        /************************************* Ponto de vista */
        if (((strpos($n, 'opiniao')))
            or (strpos($n, 'opinion'))
            or (strpos($n, 'opinion'))
            or (strpos($n, 'pontos de vista'))
        ) {
            return $this->getSection('Ponto de vista');
        }

        /************************************* Revista Completa */
        if (((strpos($n, 'edicao completa')))
                or (strpos($n, 'edicoes anteriores'))
                or (strpos($n, 'revista completa'))
        ) {
            return $this->getSection('Revista Completa');
        }

        /************************************* Cartas ao editor */
        if (((strpos($n, 'carta')))
//            or (strpos($n, 'boletin'))
//            or (strpos($n, 'resenha'))
        ) {
            return $this->getSection('Cartas ao editor');
        }

        /************************************* Resumo expandido */
        if (((strpos($n, 'resumo expandido')))
            or (strpos($n, 'resumos expandidos'))
            //            or (strpos($n, 'resenha'))
        ) {
            return $this->getSection('Resumo expandido');
        }

        /************************************* Recensão */
        if (((strpos($n, 'recensao')))
              or (strpos($n, 'recensoes'))
            //            or (strpos($n, 'resenha'))
        ) {
            return $this->getSection('Recensão');
        }

        /************************************* Resenha */
        if (((strpos($n, 'book reviews')))
            or (strpos($n, 'boletin'))
            or (strpos($n, 'resenha'))
            or (strpos($n, 'livros'))
            or (strpos($n, 'presentacian de obras'))
            or (strpos($n, 'reseaas'))
            or (strpos($n, 'resenhas'))
            or (strpos($n, 'resea'))
            or (strpos($n, 'reseas'))
            or (strpos($n, 'resumenes'))


        ) {
            return $this->getSection('Resenha');
        }

        /************************************* Boletim */
        if (((strpos($n, 'boletim')))
            or (strpos($n, 'boletin'))
        ) {
            return $this->getSection('Boletim');
        }

        /************************************* Suplementos */
        if (((strpos($n, 'anexos')))
            or (strpos($n, 'suplement'))
            or (strpos($n, 'supplement'))
        ) {
            return $this->getSection('Suplementos');
        }

        /************************************* Anuário */
        if (((strpos($n, 'anuario')))
            //or (strpos($n, 'archeion online'))
        ) {
            return $this->getSection('Anuário');
        }

        /************************************* Apresentação */
        if (((strpos($n, 'apresentacao')))
            or (strpos($n, 'presentacion'))
            or (strpos($n, 'presentacian'))
        ) {
            return $this->getSection('Apresentação');
        }

        /************************************* Normas para publicação */
        if (((strpos($n, 'diretrizes para autores')))
            or (strpos($n, 'normas editoriais'))
            or (strpos($n, 'normas da revista'))
            or (strpos($n, 'normas para publica'))
            or (strpos($n, 'normas editoriais'))
            or (strpos($n, 'normas editoriais'))
            or (strpos($n,  'reglamento, instrucciones e indices analiticos'))
            or (strpos($n, 'template'))

        ) {
            return $this->getSection('Normas para publicação');
        }

        /************************************* Dados de pesquisa */
        if (((strpos($n, 'dados de pesquisa')))
            //or (strpos($se, ':EDT'))
        ) {
            return $this->getSection('Dados de pesquisa');
        }

        /************************************* Premiados  */
        if (((strpos($n, 'premiados')))
            or (strpos($n, 'premio'))
        ) {
            return $this->getSection('Premiados');
        }

        /************************************* Defesas  */
        if (((strpos($n, 'defensas')))
            or (strpos($n, 'resumos de dissertacoes'))
            or (strpos($n, 'resumos de monografias'))
            or (strpos($n, 'dissertacao'))
            or (strpos($n, 'tese'))
        ) {
            return $this->getSection('Teses e Dissertações');
        }

        /************************************* Editorial */
        if (((strpos($n, 'editorial')))
            or (strpos($n, 'nota editorial'))
            or (strpos($n, 'notas del editor'))
            or (strpos($n, 'nota editorial'))
            //or (strpos($se, ':EDT'))

        ) {
            return $this->getSection('Editorial');
        }

        /************************************* Prefácioa */
        if (((strpos($n, 'prefacio')))
            or (strpos($n, 'posfacio'))
        ) {
            return $this->getSection('Prefácio');
        }

        /************************************* Pecha kucha */
        if (((strpos($n, 'pecha kucha')))
            //or (strpos($n, 'debatiendo'))
        ) {
            return $this->getSection('Pecha kucha');
        }

        /************************************* pPcha kucha */
        if (((strpos($n, 'posters')))
            or (strpos($n, 'poster'))
        ) {
            return $this->getSection('Pôster');
        }

        /************************************* Sumário */
        if (((strpos($n, 'sumario')))
            //or (strpos($n, 'debatiendo'))
        ) {
            return $this->getSection('Sumário');
        }

        /************************************* Debates */
        if (((strpos($n, 'debate')))
            or (strpos($n, 'debatiendo'))
        ) {
            return $this->getSection('Debates');
        }

        /************************************* Dossie */
        if (((strpos($n, 'dossie')))
            or (strpos($n, 'dossier'))
            or (strpos($n, 'dosier'))
            or (strpos($n, 'dossier'))
            or (strpos($n, 'dossia'))
        ) {
            return $this->getSection('Dossiê');
        }


        /************************************* Artigo de revisão */
        if (
            (strpos($n, 'review article'))
            or (strpos($n, 'revision'))
            or (strpos($n, 'revisao'))
            or (strpos($n, 'revisao de literatura'))
            or (strpos($n, 'revisoes de literatura'))


        ) {
            return $this->getSection('Artigo de revisão');
        }

        /************************************* Comunicações */
        if (((strpos($n, 'comunicacoes')))
            or (strpos($n, 'comunicacao'))
            or (strpos($n, 'comunicacion'))
            or (strpos($n, 'comunicaciones'))
            or (strpos($n, 'pesquisa em andamento'))
            or (strpos($n, 'relato de pesquisa'))
            or (strpos($n, 'researches in progress'))
            or (strpos($n, 'research in progress'))
            or (strpos($n, 'short communication'))
            or (strpos($n, 'short papers'))


        ) {
            return $this->getSection('Comunicações');
        }

        /************************************* Notícias e Informação */
        if (((strpos($n, 'actualidades')))
            or (strpos($n, 'noticias'))
            or (strpos($n, 'informativo'))
            or (strpos($n, 'notieventos'))
            ) {
            return $this->getSection('Notícias e Informação');
        }

        /************************************* Nota */
        if (strpos($n, 'nota')) {
            return $this->getSection('Notas e Registros');
        }

        /************************************* Nota */
        if ((strpos($se, 'teste'))
            or (strpos($n, 'teste'))
            or (strpos($n, 'secao excluida'))
            ) {
            return $this->getSection('Testes');
        }

        /************************************* Nota */
        if ((strpos($se, 'open access driverset')) or (strpos($n, 'open access driverset'))) {
            return $this->getSection('Testes');
        }

        /************************************* In memoriam */
        if (((strpos($n, 'in memoriam')))
            or (strpos($n, 'in memorium'))
            or (strpos($n, 'obituario'))
        ) {
            return $this->getSection('In memoriam');
        }

        /************************************* Homenagem */
        if (((strpos($n, 'homenage')))
            //or (strpos($n, 'in memorium'))
        ) {
            return $this->getSection('Homenagem');
        }

        /************************************* Índice */
        if (((strpos($n, 'indice')))
            or (strpos($n, 'andice'))
        ) {
            return $this->getSection('Índice');
        }

        /************************************* Advertência */
        if (((strpos($n, 'advertencia')))
            or (strpos($n, 'retratacao'))
            or (strpos($n, 'retration'))
        ) {
            return $this->getSection('Retratação & Advertência');
        }

        /************************************* Nota */
        if (
            (strpos($n, 'agradecimentos'))
            or (strpos($se, ':ACK'))
        ) {
            return $this->getSection('Agradecimentos');
        }
        return 0;
        return $this->getSection('Artigo');
    }

    function list_not_group()
    {
        $sx = '';
        $SetSpec = new \App\Models\Oaipmh\SetSpec();
        $dt = $this
            ->join('brapci_oaipmh.oai_setspec', 'id_sc = s_section', 'right')
            ->where('id_sc is null')
            ->where('s_name <> ""')
            ->orderby('s_name')
            ->findAll(40);

        foreach ($dt as $id => $line) {
            $name = $line['s_name'];
            $setspec = $line['s_id'];
            $sx .= '<li>'.$name.' ('.$setspec.')';
            $ids = $this->identify($name, $setspec);
            if ($ids > 0) {
                $d['s_section'] = $ids;
                $SetSpec->set($d)->where('id_s', $line['id_s'])->update();
            }
            $sx .= '</li>';
        }
        $sx .= '<a href="?confirm=True">Confirm</a>';
        return bs(bsc($sx,12));
    }

    function normalize($sec, $idj)
    {
        //echo h($sec.'=='.$idj);
        switch ($idj) {
            case 75:
                $sec = explode(':', trim($sec));
                $sec = $sec[count($sec) - 1];
                return $sec;
            default:
                return $sec;
                break;
        }
        exit;
    }


    function index_sections($sect = array(), $id = '')
    {
    }
}
