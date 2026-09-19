<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFclassDomain extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_class_domain';
    protected $primaryKey       = 'id_cd';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cd_property', 'cd_domain','cd_range'
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

    function rules(string $action = '', int $id = 0)
        {
            if ($action === 'create' || $action === 'edit') {
                return $this->ruleForm($action === 'edit' ? $id : 0);
            }

            $rules = $this
                ->select("id_cd, C1.c_class as domain, C2.c_class as prop, C3.c_class as range, COALESCE(rf_group, 'Sem grupo') as form_group, COALESCE(rf_order, 0) as form_order")
                ->join('brapci_rdf.rdf_class as C1', 'C1.id_c = cd_domain', 'left')
                ->join('brapci_rdf.rdf_class as C2', 'C2.id_c = cd_property', 'left')
                ->join('brapci_rdf.rdf_class as C3', 'C3.id_c = cd_range', 'left')
                ->join('brapci_rdf.rdf_form', 'rf_class = cd_property', 'left')
                ->orderBy('C1.c_class, rf_group, rf_order, C2.c_class, C3.c_class')
                ->findAll();

            $groups = [];
            foreach ($rules as $rule) {
                $domain = (string) ($rule['domain'] ?? 'Sem domínio');
                $formGroup = trim((string) ($rule['form_group'] ?? '')) ?: 'Sem grupo';
                $groups[$domain][$formGroup][] = $rule;
            }
            $availableGroups = [];
            foreach ($groups as $formGroups) {
                foreach (array_keys($formGroups) as $formGroup) {
                    $availableGroups[$formGroup] = $formGroup;
                }
            }
            natcasesort($availableGroups);

            $sx = '<div class="d-flex justify-content-between align-items-center mb-4">';
            $sx .= '<div><h1 class="mb-1">Regras da ontologia</h1><p class="text-muted mb-0">Relatório agrupado por domínio.</p></div>';
            $sx .= '<div><a class="btn btn-outline-secondary me-2" href="' . PATH . '/rdf">Voltar ao RDF</a><a class="btn btn-primary" href="' . PATH . '/rdf/rules/create">Nova regra</a></div></div>';
            $sx .= '<div class="alert alert-light border">' . count($rules) . ' regra(s) em ' . count($groups) . ' domínio(s).</div>';
            $sx .= '<div class="mb-4"><label class="form-label" for="rdf-rule-group-filter">Filtrar por grupo</label>';
            $sx .= '<select class="form-select" id="rdf-rule-group-filter"><option value="">Todos os grupos</option>';
            foreach ($availableGroups as $formGroup) {
                $sx .= '<option value="' . esc($formGroup, 'attr') . '">' . esc($formGroup) . '</option>';
            }
            $sx .= '</select></div><div id="rdf-rule-filter-empty" class="alert alert-info d-none">Nenhuma regra encontrada neste grupo.</div>';
            foreach ($groups as $domain => $formGroups) {
                $domainTotal = array_sum(array_map('count', $formGroups));
                $sx .= '<section class="card mb-4 shadow-sm rdf-rule-domain"><div class="card-header bg-dark text-white d-flex justify-content-between"><strong>' . esc($domain) . '</strong>';
                $sx .= '<span class="badge bg-light text-dark">' . $domainTotal . ' regra(s)</span></div><div class="card-body">';
                foreach ($formGroups as $formGroup => $domainRules) {
                    $sx .= '<div class="rdf-rule-group" data-group="' . esc($formGroup, 'attr') . '"><h2 class="h5 mt-2 mb-2 text-primary">Grupo: ' . esc($formGroup) . '</h2>';
                    $sx .= '<div class="table-responsive mb-3"><table class="table table-striped table-hover mb-0"><thead><tr><th style="width:10%">Ordem</th><th>Propriedade</th><th>Alcance</th><th class="text-end">Ações</th></tr></thead><tbody>';
                    foreach ($domainRules as $rule) {
                        $sx .= '<tr><td>' . (int) $rule['form_order'] . '</td><td>' . esc((string) ($rule['prop'] ?? '')) . '</td><td>' . esc((string) ($rule['range'] ?? '')) . '</td>';
                        $sx .= '<td class="text-end"><a class="btn btn-sm btn-outline-primary" href="' . PATH . '/rdf/rules/edit/' . (int) $rule['id_cd'] . '">Editar</a></td></tr>';
                    }
                    $sx .= '</tbody></table></div></div>';
                }
                $sx .= '</div></section>';
            }
            if ($groups === []) {
                $sx .= '<div class="alert alert-info">Nenhuma regra cadastrada.</div>';
            }
            $sx .= '<script>(function(){const filter=document.getElementById("rdf-rule-group-filter");if(!filter)return;const domains=Array.from(document.querySelectorAll(".rdf-rule-domain"));const empty=document.getElementById("rdf-rule-filter-empty");filter.addEventListener("change",function(){const selected=this.value;let visibleDomains=0;domains.forEach(function(domain){let visibleGroups=0;domain.querySelectorAll(".rdf-rule-group").forEach(function(group){const show=!selected||group.dataset.group===selected;group.classList.toggle("d-none",!show);if(show)visibleGroups++;});domain.classList.toggle("d-none",visibleGroups===0);if(visibleGroups>0)visibleDomains++;});empty.classList.toggle("d-none",visibleDomains!==0);});})();</script>';
            return bs(bsc($sx, 12));
        }

    private function ruleForm(int $id = 0): string
        {
            $request = service('request');
            $record = $id > 0 ? $this->find($id) : null;
            if ($id > 0 && $record === null) {
                return bs(bsc('<div class="alert alert-danger">Regra não encontrada.</div>', 12));
            }
            $values = $record ?? ['cd_domain' => '', 'cd_property' => '', 'cd_range' => ''];
            $values['rf_group'] = '';
            $values['rf_order'] = 0;
            if ($record !== null) {
                $formConfig = db_connect('rdf2')->table('rdf_form')->where('rf_class', $record['cd_property'])->get()->getRowArray();
                if ($formConfig !== null) {
                    $values['rf_group'] = $formConfig['rf_group'] ?? '';
                    $values['rf_order'] = (int) ($formConfig['rf_order'] ?? 0);
                }
            }
            $error = '';
            if (strtoupper((string) $request->getMethod()) === 'POST') {
                foreach (['cd_domain', 'cd_property', 'cd_range'] as $field) {
                    $values[$field] = (int) $request->getPost($field);
                }
                $values['rf_group'] = trim((string) $request->getPost('rf_group'));
                $values['rf_order'] = max(0, (int) $request->getPost('rf_order'));
                if (min($values['cd_domain'], $values['cd_property'], $values['cd_range']) <= 0) {
                    $error = 'Selecione o domínio, a propriedade e o alcance.';
                } else {
                    $duplicate = $this->where('cd_domain', $values['cd_domain'])->where('cd_property', $values['cd_property'])->where('cd_range', $values['cd_range']);
                    if ($id > 0) {
                        $duplicate->where('id_cd !=', $id);
                    }
                    if ($duplicate->first() !== null) {
                        $error = 'Esta regra já está cadastrada.';
                    } else {
                        $id > 0 ? $this->update($id, $values) : $this->insert($values);
                        $formTable = db_connect('rdf2')->table('rdf_form');
                        $existingForm = $formTable->where('rf_class', $values['cd_property'])->get()->getRowArray();
                        $formData = ['rf_class' => $values['cd_property'], 'rf_group' => $values['rf_group'], 'rf_order' => $values['rf_order']];
                        if ($existingForm !== null) {
                            $formTable->where('id_f', $existingForm['id_f'])->update($formData);
                        } else {
                            $formTable->insert($formData);
                        }
                        return $this->rules();
                    }
                }
            }

            $classModel = new \App\Models\RDF2\RDFclass();
            $classes = $classModel->select('id_c, c_class')->where('c_type', 'C')->orderBy('c_class')->findAll();
            $properties = (new \App\Models\RDF2\RDFclass())->select('id_c, c_class')->where('c_type', 'P')->orderBy('c_class')->findAll();
            $formAction = $id > 0 ? PATH . '/rdf/rules/edit/' . $id : PATH . '/rdf/rules/create';
            $sx = '<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0">' . ($id > 0 ? 'Editar regra' : 'Nova regra') . '</h1><a class="btn btn-outline-secondary" href="' . PATH . '/rdf/rules">Voltar ao relatório</a></div>';
            if ($error !== '') {
                $sx .= '<div class="alert alert-danger">' . esc($error) . '</div>';
            }
            $sx .= '<form method="post" action="' . esc($formAction, 'attr') . '">' . csrf_field();
            $sx .= $this->ruleSelect('cd_domain', 'Domínio', $classes, (int) $values['cd_domain']);
            $sx .= $this->ruleSelect('cd_property', 'Propriedade', $properties, (int) $values['cd_property']);
            $sx .= $this->ruleSelect('cd_range', 'Alcance', $classes, (int) $values['cd_range']);
            $sx .= '<div class="row"><div class="col-md-8 mb-3"><label class="form-label" for="rf_group">Grupo do formulário</label><input class="form-control" id="rf_group" name="rf_group" value="' . esc((string) $values['rf_group'], 'attr') . '"></div>';
            $sx .= '<div class="col-md-4 mb-3"><label class="form-label" for="rf_order">Ordem</label><input class="form-control" type="number" min="0" id="rf_order" name="rf_order" value="' . (int) $values['rf_order'] . '"></div></div>';
            $sx .= '<button class="btn btn-primary me-2" type="submit">Salvar regra</button><a class="btn btn-outline-secondary" href="' . PATH . '/rdf/rules">Cancelar</a></form>';
            return bs(bsc($sx, 12));
        }

    private function ruleSelect(string $name, string $label, array $options, int $selected): string
        {
            $sx = '<div class="mb-3"><label class="form-label" for="' . $name . '">' . $label . '</label><select class="form-select" required id="' . $name . '" name="' . $name . '"><option value="">Selecione</option>';
            foreach ($options as $option) {
                $selectedAttribute = (int) $option['id_c'] === $selected ? ' selected' : '';
                $sx .= '<option value="' . (int) $option['id_c'] . '"' . $selectedAttribute . '>' . esc((string) $option['c_class']) . '</option>';
            }
            return $sx . '</select></div>';
        }

    function getForm($class = '')
        {
        $cp = 'Prop.c_class as prop, ';
        $cp .= 'Range.c_class as range, ';
        $cp .= 'id_cd, cd_domain, rf_group, rf_order';
        $dt = $this
            ->select($cp)
            ->join('brapci_rdf.rdf_class as Prop', 'cd_property = Prop.id_c')
            ->join('brapci_rdf.rdf_class as Range', 'cd_range = Range.id_c')
            ->join('brapci_rdf.rdf_form','rf_class = cd_domain','left')
            ->where('cd_domain', $class)
            ->findAll();
        return $dt;
        }


    function getResources($class = '', $prop = '')
    {
        $cp = 'c_class as Class, id_c as ClassID, "" as selected  ';
        $dt = $this
            ->select($cp)
            ->join('brapci_rdf.rdf_class','cd_range = id_c')
            ->where('cd_domain',$class)
            ->where('cd_property',$prop)
        ->findAll(1);
        //-findAll()
        return $dt;
    }

    function register($class, $prop, $range)
    {
        $this->where('cd_property', $prop);
        $this->where('cd_domain', $class);
        $this->where('cd_range', $range);
        $dt = $this->first();
        if ($dt == null) {
            $d = [];
            $d['cd_property'] = $prop;
            $d['cd_domain'] = $class;
            $d['cd_range'] = $range;

            return $this->set($d)->insert();
        } else {
            return $dt['id_cd'];
        }
    }


    function listDomain($id)
        {
            $dt = $this
                ->join('rdf_class', 'cd_domain = id_c')
                ->where('cd_property',$id)
                ->findAll();
            return $dt;
        }
}
