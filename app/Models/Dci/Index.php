<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'dci';
    protected $table            = 'docentes';
    protected $primaryKey       = 'id_dc';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pi ', 'pi_id', 'pi_url',
        'pi_json', 'pi_active', 'pi_status',
        'pi_citation', 'pi_creators', 'pi_title',
        'updated_at'
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

    public $semestreID = 0;
    public $semestre = '';

    function index($d1='',$d2='',$d3='', $d4 ='', $d5 = '')
        {
            $Semestre = new \App\Models\Dci\Semestre();
            $this->semestreID = $Semestre->getSemestre('ID');
            $this->semestre = $Semestre->getSemestre('');

            if ($this->semestre == '')
                {
                    $d1 = 'semestre';
                }

            $sem = 1;
            $sx = '';
            switch($d1)
                {
                    case 'encargos':
                        if (trim($d2)=='') { $d2 == 0;}
                        $Encargos = new \App\Models\Dci\Encargos();
                        switch($d2)
                            {
                                case 'edit':
                                    $sx .= $Encargos->edit($d3);
                                    break;
                                case 'new':
                                    $sx .= $Encargos->new();
                                    break;
                                default:
                                    $sx .= "MENU";
                                    break;
                            }
                        break;
                    case 'report':
                        switch($d2)
                            {
                                case 'docentes':
                                    $Docentes = new \App\Models\Dci\Docentes;
                                    $sx .= $Docentes->index('report_encargos',$sem);
                                    break;
                            }
                        break;
                    case 'semestre':

                        $Disciplinas = new \App\Models\Dci\Disciplinas();
                        switch($d2)
                            {
                                case '1':
                                    $sx .= $Disciplinas->show_semestre($sem);
                                break;
                                case '2':
                                    $sx .= $Disciplinas->show_semestre_row($sem);
                                break;
                                case '3':
                                    $sx .= $Disciplinas->show_semestre_disciplinas($sem);
                                break;

                                default:
                                    $Semestre = new \App\Models\Dci\Semestre();
                                    $sx .= $Semestre->index($d1,$d2,$d3,$d4);
                                    break;
                            }


                        break;
                    case 'docentes':
                        $Docentes = new \App\Models\Dci\Docentes;
                        $sx .= $Docentes->index($d2, $d3, $d4);
                        break;

                    case 'salas':
                        $Salas = new \App\Models\Dci\Salas;
                        $sx .= $Salas->index($d2, $d3, $d4, $d5);
                        break;

                    case 'cursos':
                        $Cursos = new \App\Models\Dci\Cursos;
                        $sx .= $Cursos->index($d2, $d3, $d4, $d5);
                        break;

                    case 'disciplinas':
                        $Disciplinas = new \App\Models\Dci\Disciplinas;
                        $sx .= $Disciplinas->index($d2, $d3, $d4);
                        return $sx;
                        break;

                    default:
                        $mn = [];
                        $mn['Departamento'] = base_url('/dci/');
                        $sx .= breadcrumbs($mn);

                        $menu[base_url('/dci/docentes/')] = 'Docentes';
                        $menu[base_url('/dci/cursos/')] = 'Cursos';
                        $menu[base_url('/dci/disciplinas/')] = 'Disciplinas';
                        $menu[base_url('/dci/encargos/')] = 'Encargos';
                        $menu[base_url('/dci/salas/')] = 'Salas de Aula';
                        $menu[base_url('/dci/semestre/')] = 'Semestre';
                        $sa  = menu($menu);


                        $menu = [];
                        $menu[base_url('/dci/docentes/report/1')] = 'Relatório Docentes';
                        $menu[base_url('/dci/semestre/1/')] = 'Relatório Encargos/Dia';
                        $menu[base_url('/dci/semestre/2/')] = 'Relatório Encargos/Disciplina';
                        $sb  = menu($menu);

                        $sx .= bs(bsc($sa,6).bsc($sb,6));
                        break;
                }
                return $sx;
        }
}
