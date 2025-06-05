<?php

namespace App\Models\Capes;

use CodeIgniter\Model;
helper(['url', 'form', 'nbr', 'sessions', 'cookie']);

class Capes extends Model
{
    protected $DBGroup          = 'capes';
    protected $table            = 'capes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'C1',
        'C2',
        'C3',
        'C4',
        'C5',
        'C6',
        'C7',
        'C8',
        'C9',
        'C10',
        'STATUS'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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
    public $data = [];

    public function getNextId(int $currentId): ?int
    {
        $row = $this->select('min(ID) as ID')
            ->where('ID >', $currentId)
            ->first();

        return $row['ID'] ?? null;
    }

    /**
     * Retorna o maior ID que seja menor que $currentId.
     * Se não existir, retorna null.
     */
    public function getPrevId(int $currentId): ?int
    {
        $row = $this->select('max(ID) as ID')
            ->where('ID <', $currentId)
            ->first();

        return $row['ID'] ?? null;
    }


    function view($id = '')
    {
        if ($this->getPpg() == '') { return ""; }
        // Se nenhum ID foi passado, recupera o primeiro registro (menor ID)
        if ($id === '') {
            $first = $this
                ->select('min(ID) as ID')
                ->where('CD_PROGRAMA', $this->getPpg())
                ->first();
            if (empty($first)) {
                return "VOLTAR";
            }
            $id = $first['ID'];
        }

        $this->saved();
        // Tenta buscar o registro atual
        $registro = $this
            ->where('CD_PROGRAMA', $this->getPpg())
            ->find($id);
        if (!$registro) {
            // Se não existir esse ID, redireciona para o primeiro registro
            return "VOLTAR 2";
        }

        $this->data = $registro;
        // Busca IDs do registro anterior e próximo
        $prevId = $this->getPrevId($id);
        $nextId = $this->getNextId($id);

        // Dados a enviar para a view
        $data = [
            'registro' => $registro,
            'prevId'   => $prevId,
            'nextId'   => $nextId,
        ];
        return view('Capes/view', $data);
    }

    public function setPpg($ppg)
    {
        set_cookie('ppg', $ppg, 3600 * 24 * 30); // 30 dias
    }
    public function getPpg()
    {
        $vlr = get_cookie('ppg');
        return $vlr ?? '';
    }

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '', $d5 = '')
    {
        $sx = '';
        switch ($d1) {
            case 'search':
                $sx = $this->search($d2, $d3, $d4, $d5);
                break;
            case 'list':
                $sx = $this->list($d2, $d3, $d4, $d5);
                break;
            default:
                $dd = [];
                $dd['ppg'] = get("ppg");
                $dd['sf'] = $this->view();
                $dd['form']  = view('Capes/form_item', $this->data);

                $sx = view('Capes/form_ppg', $dd);
                break;
        }
        return $sx;
    }

    function saved()
    {
        $action = get("action");
        if ($action == 'save') {
            $id = get("id");
            if ($id != '') {
                $dd = [];
                $dd['C1'] = get("C1");
                $dd['C2'] = get("C2");
                $dd['C3'] = get("C3");
                $dd['C4'] = get("C4");
                $dd['C5'] = get("C5");
                $dd['C6'] = get("C6");

                $TOTAL = 0;
                $set = 0;
                for($r=1;$r <= 6; $r++) {
                    $C = 'C'.$r;
                    if (isset($dd[$C]) && is_numeric($dd[$C])) {
                        $TOTAL += $dd[$C];
                        $set++;
                    }
                    if ($set == 6)
                        {
                            $dd['C7'] = $TOTAL;
                            $dd['STATUS'] = 1;
                        } else 	{
                            $dd['C7'] = 0;
                            $dd['STATUS'] = 0;
                        }
                }

                // Salva os dados
                $this->set($dd)->where('ID',$id)->update();
            }
        }
        $ppg = get("ppg");
        if ($ppg == '') {
            $ppg = $this->getPpg();
        }
        if ($ppg != '') {
            $this->setPpg($ppg);
        }
        $this->data = [];
    }
}
