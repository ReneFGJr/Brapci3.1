<?php

namespace App\Models\Manuais;

use CodeIgniter\Model;

class Manual extends Model
{
    protected $DBGroup          = 'manuais';
    protected $table            = 'produto';
    protected $primaryKey       = 'id_p';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

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

    function view_manual($id)
        {
            $Scripts = new \App\Models\Manuais\Script();
            $dt = $this->where('id_p',$id)->first();
            $sx = bsc($this->header_manual($dt),12);
            $sx .= $Scripts->show_manual($id);

            $sx = bs($sx);
            return $sx;
        }

        function header_manual($dt)
            {
                $sx = '';
                $sx .= bsc(h('Produto',4));
                $sx .= bsc(h($dt['p_name'],2));
                $sx .= bsc(h('<hr>', 2));

                return $sx;
            }

    function list()
        {
            $sx = '<h2>Tabela com Filtro Dinâmico</h2>
                    <input type="text" id="filterInput" placeholder="Digite para filtrar...">

                    <table class="" style="width: 100%">
                        <thead>
                            <tr>
                                <th width="85%">Produto</th>
                                <th width="15%">Versão</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">';

                            // Exibindo os dados na tabela
                            $data = $this->orderBy('p_name')->findAll();
                            foreach ($data as $row) {
                                $link = '<a href="'.PATH.'manual/view/'. $row['id_p'].'">';
                                $linka = '</a>';
                                $sx .= "<tr>";
                                $sx .= "<td>$link{$row['p_name']}$linka</td>";
                                $sx .= "<td>{$row['p_versão_atual']}</td>";
                                $sx .= "</tr>";
                            }
                        $sx .= '
                        </tbody>
                    </table>

                    <script>
                        // JavaScript para filtrar a tabela
                        document.getElementById(\'filterInput\').addEventListener(\'keyup\', function() {
                            const filterValue = this.value.toLowerCase();
                            const rows = document.querySelectorAll(\'#tableBody tr\');

                            rows.forEach(row => {
                                const nome = row.cells[0].textContent.toLowerCase();
                                const email = row.cells[1].textContent.toLowerCase();
                                const pais = row.cells[2].textContent.toLowerCase();

                                if (nome.includes(filterValue) || email.includes(filterValue) || pais.includes(filterValue)) {
                                    row.style.display = \'\';
                                } else {
                                    row.style.display = \'none\';
                                }
                            });
                        });
                    </script>';
                    $sx = bs(bsc($sx));
                    return $sx;
        }
}
