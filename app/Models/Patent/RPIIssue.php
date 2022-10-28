<?php

namespace App\Models\Patent;

use CodeIgniter\Model;

class RPIIssue extends Model
{
    protected $DBGroup          = 'patent';
    protected $table            = 'rpi_issue';
    protected $primaryKey       = 'id_rpi';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_rpi', 'rpi_nr', 'rpi_data', 'rpi_status'
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

    function register($issue,$status=0)
    {
        $dt = $this->where('rpi_nr', $issue)->findAll();
        if (count($dt) > 0) {
            $data['rpi_status'] = $status;
            $this->set($data)->where('rpi_nr', $issue)->update();
            return $dt[0]['id_rpi'];
        } else {
            $data['rpi_nr'] = $issue;
            $data['rpi_status'] = $status;
            return $this->insert($data);
        }
    }

    function harvesting($id)
    {
        $sx = '';
        $dir = '../.tmp/.inpi/patent/';
        $filename = $dir . 'P' . strzero($id, 4) . '.zip';

        if (file_exists($filename)) {
            $sx .= bsmessage('Cached ' . $filename, 1);
        } else {
            $RPI = new \App\Models\Patent\RPI;
            $url = $RPI->url . 'P' . strzero($id, 4) . '.zip';
            $sx .= h($url);

            $file = read_link($url);
            $sx .= h(strlen($file));

            dircheck('../.tmp/');
            dircheck('../.tmp/.inpi/');
            dircheck('../.tmp/.inpi/patent/');

            if (strlen($file) > 1000) {
                $this->register($id,1);
                file_put_contents($filename, $file);
                $sx .= bsmessage('Harvested ' . $id, 1);
                $this->register($id, 1);
            } else {
                $sx .= bsmessage('Error Harvested ' . $id, 3);
                return $sx;
            }
        }

        $filename_txt = $dir . 'P' . strzero($id, 4) . '.txt';
        if (!file_exists($filename_txt)) {
            $zip = new \ZipArchive();
            $res = $zip->open($filename);
            if ($res === TRUE) {
                $zip->extractTo($dir);
                $zip->close();
            }
            $sx .= bsmessage('Unziped ' . $filename, 1);
            $this->register($id, 2);
        }

        /***************************************************** RETURN */
        return $sx;
    }
}
