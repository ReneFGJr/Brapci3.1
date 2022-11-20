<?php

namespace App\Models\Functions;

use CodeIgniter\Model;

class ViewsRDF extends Model
{
    protected $DBGroup          = 'click';
    protected $table            = 'views_rdf';
    protected $primaryKey       = 'id_av';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'av_views','av_rdf', 'av_last_IP'
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

    function show($id)
    {
        $dt = $this->where('av_rdf', $id)->first();
        if ($dt == '')
            {
                $data['av_rdf'] = $id;
                $data['av_views'] = 1;
                $data['av_last_IP'] = IP();
                $this->insert($data);
                $views = 1;
            } else {
                if ($dt['av_last_IP'] != IP())
                    {
                        $data['av_views'] = $dt['av_views'] + 1;
                        $data['av_last_IP'] = IP();
                        $this->where('av_rdf', $id)->set($data)->update();
                        $views = $dt['av_views'] + 1;

                        $View = new \App\Models\Functions\Views();
                        $View->register($id);
                    } else {
                        $views = $dt['av_views'];
                    }
            }

        $sx = '';
        $sx .= '<div class="btn btn-outline-primary mt-2" style="width: 100%;">';
        $sx .= '<table width="100%">';
        $sx .= '<tr><td class="text-center">';
        $sx .= lang('brapci.views');
        $sx .= ' ';
        $sx .= $views;
        $sx .= ' ';
        if ($views == 1)
            {
                $sx .= lang('brapci.time');
            } else {
                $sx .= lang('brapci.times');
            }

        $sx .= '</td><td>';
        $sx .= '</table>';
        $sx .= '</div>';

        return $sx;
    }
}
