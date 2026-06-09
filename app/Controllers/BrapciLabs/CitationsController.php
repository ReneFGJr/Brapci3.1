<?php
/**
 * Exemplo de uso da view de citações por autor
 * 
 * Adicione este método em seu Controller
 */

namespace App\Controllers\BrapciLabs;

use App\Models\BrapciLabs\BrapciWorksModel;

class CitationsController extends BaseController
{
    /**
     * Exibe citações por autor em tabela
     * 
     * Uso: GET /citations/by-authors/{id}
     * 
     * @param int $id ID do projeto
     * @return string HTML com tabela de citações
     */
    public function citationsByAuthors(int $id)
    {
        $BrapciWorksModel = new BrapciWorksModel();
        
        // Obtém os dados de citações
        $citations = $BrapciWorksModel->getCitationsByAuthors($id);
        
        // Passa os dados para a view
        return view('BrapciLabs/CitationsByAuthorsView', [
            'citations' => $citations,
            'projectId' => $id
        ]);
    }

    /**
     * Alternativa: com método formatado (opcional)
     */
    public function citationsByAuthorsFormatted(int $id)
    {
        $BrapciWorksModel = new BrapciWorksModel();
        
        // Obtém os dados já formatados
        $citations = $BrapciWorksModel->getCitationsByAuthorsFormatted($id);
        
        return view('BrapciLabs/CitationsByAuthorsView', [
            'citations' => $citations,
            'projectId' => $id
        ]);
    }

    /**
     * Com busca por termo (opcional)
     */
    public function searchCitations(int $id, string $searchTerm = '')
    {
        $BrapciWorksModel = new BrapciWorksModel();
        
        $citations = $BrapciWorksModel->getCitationsByAuthors($id, $searchTerm);
        
        // Filtra apenas citações que contêm o termo de busca
        if (!empty($searchTerm)) {
            $searchLower = strtolower($searchTerm);
            $citations = array_map(function($items) use ($searchLower) {
                return array_filter($items, function($item) use ($searchLower) {
                    return str_contains(strtolower($item[1] ?? ''), $searchLower);
                });
            }, $citations);
            
            // Remove autores sem citações após filtro
            $citations = array_filter($citations, function($items) {
                return !empty($items);
            });
        }
        
        return view('BrapciLabs/CitationsByAuthorsView', [
            'citations' => $citations,
            'projectId' => $id,
            'searchTerm' => $searchTerm
        ]);
    }
}
