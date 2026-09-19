<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFclass extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class';
    protected $primaryKey       = 'id_c';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'c_class', 'c_equivalent', 'c_prefix',
        'c_type', 'c_description', 'c_url',
        'c_url_update','id_c', 'c_class_main'
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

    function register($Prefix,$Class)
        {
            $RDFprefix = new \App\Models\RDF2\RDFprefix();
            $prefix = $RDFprefix->getPrefixID((string)$Prefix);
            $ClassID = 0;

            if($prefix > 0)
                {
                    $ClassID = $this->getClass($Class, $prefix);
                    if ($ClassID == 0)
                        {
                            $d = [];
                            $d['c_class'] = $Class;
                            $d['c_prefix'] = $prefix;
                            $d['c_type'] = 'C';
                            $d['c_description'] = '';
                            $d['c_url'] = '';
                            $d['c_url_update'] = date("Y-m-d");
                            $d['c_class_main'] = 0;
                            $d['c_equivalent'] = 0;
                            $ClassID = $this->set($d)->insert();
                        }
                }
            return $ClassID;
        }

    function getClass($Class,$prefix='')
        {

            $Class = trim($Class);
            $this
                ->join('rdf_prefix', 'id_prefix = c_prefix')
                ->where('c_class',$Class);
            if ($prefix != '')
                {
                    $this->where('c_prefix', $prefix);
                }
            $dt = $this->first();

            if ($dt != null)
                {
                    if ($dt['c_equivalent'] != 0) {
                        return $dt['c_equivalent'];
                    } else {
                        return $dt['id_c'];
                    }
                } else {
                    return 0;
                }

        }

    function get($id)
        {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();

        $cp = 'id_c as id, prefix_ref as prefix,c_class as Class,
                        c_type as Type, CONCAT(prefix_url,c_class) as url';

        $dt = $this
            ->select($cp)
            ->join('rdf_prefix', 'id_prefix = c_prefix')
            ->where('c_class',$id)
            ->orderBy('c_class')
            ->first();


        $dt['classTotal'] = $RDFconcept->totalClass($id);
        $dt['propTotal'] =  $RDFconcept->totalProp($id);;

        if (!isset($dt['Class']))
            {
                $RSP['status'] = '400';
                $RSP['message'] = 'Class or Property not found';
                return $RSP;
            }

        switch ($dt['Class'])
            {
                case 'C':
                    $dt['Class'] = 'Classe';
                    break;
                case 'P':
                    $dt['Class'] = 'Property';
                    break;
            }

        /******** Domain */
        $RDFdomain = new \App\Models\RDF2\RDFclassDomain();
        $dt['Domain'] = $RDFdomain->listDomain($dt['id']);

        /********* Range */
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $dt['Range'] = $RDFclass->listRange($dt['id']);

        $RDFclassDomain = new \App\Models\RDF2\RDFclassDomain();
        $dt['Form'] = $RDFclassDomain->getForm($dt['id']);


        return $dt;
        }

    function listRange()
        {
            return [];
        }

    function getClasses()
        {
            $cp = 'id_c as id, prefix_ref as prefix,c_class as Class,
                        c_type as Type, CONCAT(prefix_url,c_class) as url';
            //$cp = '*';
            $dt = $this
                ->select($cp)
                ->join('rdf_prefix','id_prefix = c_prefix')
                ->where('c_type','C')
                ->orderBy('c_class')
                ->findAll();
            return $dt;
        }

    /** CRUD used by /rdf/Class. */
    public function crudTable(string $message = '', string $messageType = 'success', string $type = 'C'): string
    {
        $type = $type === 'P' ? 'P' : 'C';
        $isProperty = $type === 'P';
        $basePath = PATH . '/rdf/' . ($isProperty ? 'Property' : 'Class');
        $singular = $isProperty ? 'propriedade' : 'classe';
        $rows = $this
            ->select('id_c, c_class, c_description, c_url, prefix_ref')
            ->join('rdf_prefix', 'id_prefix = c_prefix', 'left')
            ->where('c_type', $type)
            ->orderBy('c_class')
            ->findAll();

        $sx = '<div class="d-flex justify-content-between align-items-center mb-3">';
        $sx .= '<h2 class="mb-0">' . ($isProperty ? 'Propriedades RDF' : 'Classes RDF') . '</h2>';
        $sx .= '<div><a class="btn btn-outline-secondary me-2" href="' . PATH . '/rdf">Voltar ao RDF</a>';
        $sx .= '<a class="btn btn-primary" href="' . $basePath . '/create">Nova ' . $singular . '</a></div></div>';
        if ($message !== '') {
            $sx .= '<div class="alert alert-' . esc($messageType, 'attr') . '">' . esc($message) . '</div>';
        }
        $filterId = $isProperty ? 'rdf-property-filter' : 'rdf-class-filter';
        $tableBodyId = $isProperty ? 'rdf-property-rows' : 'rdf-class-rows';
        $sx .= '<div class="mb-3"><label class="form-label" for="' . $filterId . '">Buscar</label>';
        $sx .= '<input class="form-control" type="search" id="' . $filterId . '" placeholder="Digite um ID, prefixo, nome, descrição ou URL" autocomplete="off"></div>';
        $sx .= '<div class="table-responsive"><table class="table table-striped table-hover align-middle">';
        $sx .= '<thead class="table-dark"><tr><th>ID</th><th>Prefixo</th><th>' . ($isProperty ? 'Propriedade' : 'Classe') . '</th><th>Descrição</th><th>URL</th><th class="text-end">Ações</th></tr></thead><tbody id="' . $tableBodyId . '">';
        foreach ($rows as $line) {
            $id = (int) $line['id_c'];
            $name = esc((string) $line['c_class']);
            $detailUrl = PATH . '/rdf/Class/' . rawurlencode((string) $line['c_class']);
            $sx .= '<tr class="rdf-filter-row"><td>' . $id . '</td><td>' . esc((string) ($line['prefix_ref'] ?? '')) . '</td>';
            $sx .= '<td><a href="' . esc($detailUrl, 'attr') . '">' . $name . '</a></td>';
            $sx .= '<td>' . esc((string) ($line['c_description'] ?? '')) . '</td>';
            $url = trim((string) ($line['c_url'] ?? ''));
            $safeUrl = preg_match('~^https?://~i', $url) ? '<a href="' . esc($url, 'attr') . '" target="_blank" rel="noopener noreferrer">' . esc($url) . '</a>' : esc($url);
            $sx .= '<td>' . $safeUrl . '</td>';
            $sx .= '<td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary me-1" href="' . esc($detailUrl, 'attr') . '">Visualizar</a>';
            $sx .= '<a class="btn btn-sm btn-outline-primary' . ($isProperty ? ' me-1' : '') . '" href="' . $basePath . '/edit/' . $id . '">Editar</a>';
            if ($isProperty) {
                $sx .= '<form class="d-inline" method="post" action="' . $basePath . '/delete/' . $id . '" onsubmit="return confirm(\'Confirma a exclusão desta propriedade?\');">';
                $sx .= csrf_field() . '<button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button></form>';
            }
            $sx .= '</td></tr>';
        }
        if ($rows === []) {
            $sx .= '<tr><td colspan="6" class="text-center text-muted py-4">Nenhuma classe cadastrada.</td></tr>';
        }
        $sx .= '<tr id="' . $tableBodyId . '-empty" class="d-none"><td colspan="6" class="text-center text-muted py-4">Nenhum resultado encontrado.</td></tr>';
        $sx .= '</tbody></table></div>';
        $sx .= '<script>(function(){const input=document.getElementById(' . json_encode($filterId) . ');const body=document.getElementById(' . json_encode($tableBodyId) . ');if(!input||!body)return;const rows=Array.from(body.querySelectorAll(".rdf-filter-row"));const empty=document.getElementById(' . json_encode($tableBodyId . '-empty') . ');input.addEventListener("input",function(){const query=this.value.trim().toLocaleLowerCase();let visible=0;rows.forEach(function(row){const show=!query||row.textContent.toLocaleLowerCase().includes(query);row.classList.toggle("d-none",!show);if(show)visible++;});empty.classList.toggle("d-none",visible!==0);});})();</script>';
        return $sx;
    }

    public function crudForm(int $id = 0, string $type = 'C'): string
    {
        $type = $type === 'P' ? 'P' : 'C';
        $isProperty = $type === 'P';
        $basePath = PATH . '/rdf/' . ($isProperty ? 'Property' : 'Class');
        $singular = $isProperty ? 'propriedade' : 'classe';
        $request = service('request');
        $requestMethod = strtoupper((string) $request->getMethod());
        $record = $id > 0 ? $this->find($id) : null;
        if ($id > 0 && $record === null) {
            return $this->crudTable(ucfirst($singular) . ' não encontrada.', 'danger', $type);
        }

        $error = '';
        if ($requestMethod === 'POST') {
            $name = trim((string) $request->getPost('c_class'));
            $prefix = (int) $request->getPost('c_prefix');
            if ($name === '' || $prefix <= 0) {
                $error = 'Informe o nome e o prefixo da ' . $singular . '.';
            } elseif (!preg_match('/^[A-Za-z_][A-Za-z0-9_.-]*$/', $name)) {
                $error = 'O nome deve começar com letra ou sublinhado e não pode conter espaços.';
            } elseif (trim((string) $request->getPost('c_url')) !== '' && !filter_var(trim((string) $request->getPost('c_url')), FILTER_VALIDATE_URL)) {
                $error = 'Informe uma URL válida.';
            } else {
                $duplicate = $this->where('c_class', $name)->where('c_prefix', $prefix);
                if ($id > 0) {
                    $duplicate->where('id_c !=', $id);
                }
                if ($duplicate->first() !== null) {
                    $error = 'Já existe uma ' . $singular . ' com esse nome e prefixo.';
                } else {
                    $data = [
                        'c_class' => $name,
                        'c_prefix' => $prefix,
                        'c_type' => $type,
                        'c_description' => trim((string) $request->getPost('c_description')),
                        'c_url' => trim((string) $request->getPost('c_url')),
                        'c_url_update' => date('Y-m-d'),
                    ];
                    if ($id > 0) {
                        $this->update($id, $data);
                        return $this->crudTable(ucfirst($singular) . ' atualizada com sucesso.', 'success', $type);
                    }
                    $data['c_equivalent'] = 0;
                    $data['c_class_main'] = 0;
                    $this->insert($data);
                    return $this->crudTable(ucfirst($singular) . ' criada com sucesso.', 'success', $type);
                }
            }
        }

        $values = $record ?? [];
        foreach (['c_class', 'c_prefix', 'c_description', 'c_url'] as $field) {
            if ($requestMethod === 'POST') {
                $values[$field] = $request->getPost($field);
            }
        }
        $prefixes = (new RDFprefix())->orderBy('prefix_ref')->findAll();
        $action = $id > 0 ? $basePath . '/edit/' . $id : $basePath . '/create';
        $sx = '<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="mb-0">' . ($id > 0 ? 'Editar ' . $singular . ' RDF' : 'Nova ' . $singular . ' RDF') . '</h2>';
        $sx .= '<a class="btn btn-outline-secondary" href="' . PATH . '/rdf">Voltar ao RDF</a></div>';
        if ($error !== '') {
            $sx .= '<div class="alert alert-danger">' . esc($error) . '</div>';
        }
        $sx .= '<form method="post" action="' . esc($action, 'attr') . '">' . csrf_field();
        $sx .= '<div class="mb-3"><label class="form-label" for="c_class">Nome da ' . $singular . '</label><input required class="form-control" id="c_class" name="c_class" maxlength="255" value="' . esc((string) ($values['c_class'] ?? ''), 'attr') . '"></div>';
        $sx .= '<div class="mb-3"><label class="form-label" for="c_prefix">Prefixo</label><select required class="form-select" id="c_prefix" name="c_prefix"><option value="">Selecione</option>';
        foreach ($prefixes as $prefix) {
            $selected = (int) ($values['c_prefix'] ?? 0) === (int) $prefix['id_prefix'] ? ' selected' : '';
            $sx .= '<option value="' . (int) $prefix['id_prefix'] . '"' . $selected . '>' . esc((string) $prefix['prefix_ref']) . '</option>';
        }
        $sx .= '</select></div>';
        $sx .= '<div class="mb-3"><label class="form-label" for="c_description">Descrição</label><textarea class="form-control" id="c_description" name="c_description" rows="3">' . esc((string) ($values['c_description'] ?? '')) . '</textarea></div>';
        $sx .= '<div class="mb-3"><label class="form-label" for="c_url">URL</label><input type="url" class="form-control" id="c_url" name="c_url" value="' . esc((string) ($values['c_url'] ?? ''), 'attr') . '"></div>';
        $sx .= '<button class="btn btn-primary me-2" type="submit">Salvar</button><a class="btn btn-outline-secondary" href="' . $basePath . '">Cancelar</a></form>';
        return $sx;
    }

    public function crudDelete(int $id, string $type = 'C'): string
    {
        $type = $type === 'P' ? 'P' : 'C';
        if (strtoupper((string) service('request')->getMethod()) !== 'POST') {
            return $this->crudTable('Método não permitido para exclusão.', 'danger', $type);
        }
        $record = $this->find($id);
        if ($record === null) {
            return $this->crudTable('Registro não encontrado.', 'danger', $type);
        }

        $db = db_connect('rdf2');
        $dependencies = $db->table('rdf_concept')->where('cc_class', $id)->countAllResults();
        $dependencies += $db->table('rdf_class_domain')->groupStart()->where('cd_domain', $id)->orWhere('cd_property', $id)->orWhere('cd_range', $id)->groupEnd()->countAllResults();
        $dependencies += $this->groupStart()->where('c_equivalent', $id)->orWhere('c_class_main', $id)->groupEnd()->countAllResults();
        if ($dependencies > 0) {
            return $this->crudTable('O registro não pode ser excluído porque possui vínculos com conceitos ou regras da ontologia.', 'warning', $type);
        }
        $this->delete($id);
        return $this->crudTable('Registro excluído com sucesso.', 'success', $type);
    }
}
