<?php

namespace App\Models\Io;

use CodeIgniter\Model;

class Mysql extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mysqls';
    protected $primaryKey       = 'id';
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

    function index($d1,$d2,$d3)
        {
            $sx = h("MySQL");
            $sa = '';
            $sb = $this->menu();
            switch($d1)
                {
                    case 'database':
                        $sa = $this->database();
                        break;
                    case 'backup':
                        $sa = $this->database('B');
                        break;
                    default:
                        $sa ='';
                        break;
                }
            $sx = bs(bsc($sx,12).bsc($sa,8).bsc($sb,4));
            return $sx;
        }

    function menu()
        {
            $m = [];
            $m[PATH.'/admin/mysql/database'] = 'Database';
            $m[PATH . '/admin/mysql/backup'] = 'Backup Script';
            return menu($m);
        }

    function database($tp='')
        {
            $sx = '';
            $sql = "SHOW DATABASES;";
            $dt = $this->db->query($sql);
            $dt = $dt->getResult();
            $scr = '';
            foreach($dt as $id=>$line)
                {
                    switch($tp)
                        {
                            case 'B':
                                $sx .= 'mysqldump ' . $line->Database . ' > /home/brapci/backup/' . $line->Database . '.sql<br>';
                                $scr .= 'echo "Backup ' . $line->Database . cr();
                                $scr .= 'mysqldump ' . $line->Database . ' > /home/brapci/backup/' . $line->Database . '.sql'.cr();
                                break;
                            default:
                                $sx .= '<li>' . $line->Database . '</li>';
                                break;
                        }
                }

            if ($scr != '')
                {
                    dircheck("/home/brapci/backup");
                    $file = '/home/brapci/backup/mysql_backup';
                    $scr .= 'echo "Fim do Backup"'.cr();
                    $scr .= 'echo "COPIANDO PARA A REDE"'.cr();
                    $scr .= 'cp /home/brapci/backup/*.sql /home/brapci/rede/pluto/Backup-SQL/.' . cr();

                    file_put_contents($file,$scr);
                }
            return $sx;
        }
}
