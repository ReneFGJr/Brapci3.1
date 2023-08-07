<?php

namespace App\Models\Dci;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'persistent_indicador';
    protected $table            = 'persistent_id';
    protected $primaryKey       = 'id_pi';
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

    function index($d1='',$d2='',$d3='', $d4 ='', $d5 = '')
        {
            $sem = 2;
            $sx = '';

            switch($d1)
                {
                    case 'encargos':
                        if (trim($d3)=='') { $d3 == 0;}
                        $Encargos = new \App\Models\Dci\Encargos();
                        $sx .= $Encargos->edit($d3);
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

                                default:
                                    $sx .= '[[['.$d2.']]]';
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
                        $mn['Departamento'] = PATH . '/dci/';
                        $sx .= breadcrumbs($mn);

                        $menu[PATH . '/dci/docentes/'] = 'Docentes';
                        $menu[PATH . '/dci/cursos/'] = 'Cursos';
                        $menu[PATH . '/dci/disciplinas/'] = 'Disciplinas';
                        $menu[PATH . '/dci/encargos/'] = 'Encargos';
                        $menu[PATH . '/dci/salas/'] = 'Salas de Aula';
                        $menu[PATH . '/dci/semestre/'] = 'Semestre';
                        $sa  = menu($menu);


                        $menu = [];
                        $menu[PATH . '/dci/docentes/report/1'] = 'Relatório Docentes';
                        $menu[PATH . '/dci/semestre/1/'] = 'Relatório Encargos/Dia';
                        $menu[PATH . '/dci/semestre/2/'] = 'Relatório Encargos/Disciplina';
                        $sb  = menu($menu);

                        $sx .= bs(bsc($sa,6).bsc($sb,6));
                        break;
                }
                return $sx;
        }
}
