<?php

namespace App\Models\Dataverse\ApiDOC;

use CodeIgniter\Model;

class Metric extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'metrics';
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

    var $Dataverse = '';

    function __construct()
    {
        $this->Dataverse = new \App\Models\Dataverse\Index();
    }

    function all()
    {
        $bread = ['Dataverse' => PATH . '/dados/dataverse/', 'Indicatores' => PATH . '/dados/dataverse/indicators'];
        $sx = breadcrumbs($bread);
        $sx .= $this->info();
        $sx .= $this->counter();
        $sx .= $this->bySubject();
        $sx .= $this->researchrs();

        return $sx;
    }

    function info()
    {
        $sx = h('dataverse.indicatores_info');
        $url = $this->Dataverse->server();
        $url .= 'api/info';

        $sx .= $url;
        return $sx;
    }

    function researchrs()
        {
            $server = $this->Dataverse->server();
            $url = $server . '/api/search/?';
            $url .= 'q=*&type=dataset&sort=dateSort&order=desc&fq=categoryOfDataverse:%20Research Project%20';
            $sx = anchor($url);
            return $sx;
        }

    function tree()
        {
            $server = $this->Dataverse->server();
            $url = $server. '/api/info/metrics/tree';
        }

    function byCategory()
        {
            $server = $this->Dataverse->server();
            $url = $server. '/api/info/metrics/datasets/byCategory';
        }

    function bySubject()
    {
        $sx = h('dataverse.indicatores_bySubject');
        $url = $this->Dataverse->server();
        $sx .= '<tt>' . anchor($url . '/api/info/metrics/datasets/bySubject') . '</tt>';
        $sx .= '<div id="dataversesBySubject" class="col-xs-12">XX</div>';
        $sx .= '
            <script>
            function querySubject(elm) {
                var html = "";
                    trad = ["Medicina, Saúde e Ciências da Vida","Ciências Sociais","Ciências da Terra e do meio ambiente","Artes e Humanidades","Computação e Ciência da Informação","Ciências Agrárias"];
                    t = 0;
                    $.get("'.$url. '" + "/api/info/metrics/dataverses/bySubject", function(data) {
                        var allRows = data.split(/\r?\n|\r/);

                        for (var singleRow = 1; singleRow < allRows.length; singleRow++) {
                            var rowCells = allRows[singleRow].split(",");
                            if (rowCells.length == 3)
                                {
                                    link = "<a href="'. $this->Dataverse->server(). 'dataverse/root?q=&fq1=subject_ss%3A%22" + rowCells[0] + "%22&fq0=dvObjectType%3A%28dataverses+OR+datasets%29&types=dataverses%3Adatasets&sort=dateSort&order=
                                    linka = "</a>"
                                    html = html + "<li>" + link + trad[t] + " = "+rowCells[0] + ", "+ rowCells[1] + " ("+rowCells[2]+")</li>";
                                }
                            if (rowCells.length == 2)
                                {
                                    html = html + "<li>" + trad[t] + " == " + rowCells[0] + " ("+rowCells[1]+")</li>";
                                }
                            t++;
                        }
                        document.getElementById(elm).innerHTML = html;
                    });
                };
                querySubject("dataversesBySubject");
                </script>';
        return $sx;

    }

    function byType()
    {
        $url = $server . '/api/info/metrics/files/byType';
    }

    function remoteLocalData()
        {
            //https://arcadados.fiocruz.br/api/info/metrics/datasets/?dataLocation=remote
        }

    function counter()
    {
        $sx = h('dataverse.indicatores_counters');
        $url = $this->Dataverse->server();

        $sx .= '<tt>'.anchor($url.'/api/info/metrics/datasets').'</tt>';

        $sa = '<div>Dataverses: <span id="ID_dataverse">0</span></div>' . cr();
        $sa .= '<div>Datasets: <span id="ID_datasets">0</span></div>' . cr();
        $sa .= '<div>Files: <span id="ID_files">0</span></div>' . cr();
        $sa .= '<div>Donwloads: <span id="ID_downloads">0</span></div>' . cr();

        $js = "
/************************************************************* Total - Script */
url_metrics = '$url/api/info/metrics/';
function getvals(url, div, expr, param) {
    fetch(url)
        .then((response) => { return response.json(); })
        .then((myJson) => {
            switch (expr) {
                case 'Total':
                    document.getElementById(div).innerHTML = myJson.data.count;
                    break;
            }
        });
}

/* Para chamar as metricas coloque esses comandos na custom-homepage.html */
/* Script para chamar as metricas */

getvals(url_metrics + 'dataverses', 'ID_dataverse', 'Total');
getvals(url_metrics + 'datasets', 'ID_datasets', 'Total');
getvals(url_metrics + 'files', 'ID_files', 'Total');
getvals(url_metrics + 'downloads', 'ID_downloads', 'Total ');
";
        $sx .= '<script>' . $js . '</script>';

        $sb = '';

        $sb .= 'file: indicatores.html
              <textarea class="form-control full" rows=5>' . $sa . '</textarea>';


        $sb .= 'file: dataverse.js
            <textarea class="form-control full" rows=15>' . $js . '</textarea>';

        $sx .= bsc(h('brapci.result', 3) . $sa, 4) . bsc(h('brapci.codes', 3) . $sb, 8);
        $sx = bs($sx);
        return $sx;
    }
}
