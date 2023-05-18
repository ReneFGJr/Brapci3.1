<?php

namespace App\Models\Guide\Manual\Export;

use CodeIgniter\Model;

class Dataverse extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'dataverses';
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

    function body($context)
    {
        $xhtml = '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:h="http://java.sun.com/jsf/html"
      xmlns:f="http://java.sun.com/jsf/core"
      xmlns:ui="http://java.sun.com/jsf/facelets"
      xmlns:o="http://omnifaces.org/ui"
      xmlns:p="http://primefaces.org/ui">
    <h:head>
    </h:head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <h:body>
        <ui:composition template="/dataverse_template.xhtml">
            <ui:param name="pageTitle" value="Guide"/>
            <ui:param name="showDataverseHeader" value="false"/>
            <ui:param name="loginRedirectPage" value="dataverse.xhtml"/>
            <ui:param name="showMessagePanel" value="#{true}"/>
            <ui:define name="body">
                <div class="row">
                <div class="col-md-12">
                <h1 class="text-center">Guia do Usuário</h1>
                </div>
                ' . $context . '

                </div>
            </ui:define>
        </ui:composition>
    </h:body>
</html>
';
return $xhtml;
    }

    function index($id)
    {
        $erro = '';
        $Content = new \App\Models\Guide\Manual\Content();
        $dt = $Content
            ->join('guide_content_type', 'gc_type = type_cod')
            ->where('gc_guide', $id)
            //->where('type_header', 1)
            ->where('gc_active', 1)
            ->orderBy('gc_order')
            ->findAll();

        $files = '';
        $summary = [];
        $summary_name = [];
        $body =  '<body id="body">' . cr();;
        $body .= '<div id="summary">{SUMMARY}</div>'.cr();
        $nv = [0,0,0,0];
        $pause = '<br>';

        foreach ($dt as $idx => $line) {
            $type = $line['type_cod'];
            $name = $line['id_gc'];
            $cont = trim($line['gc_title']).trim($line['gc_content']);

            switch ($type) {
                case 'H1':
                    $summary_name[$name] = $line['gc_title'];
                    $summary[$name] = [];
                    $nv = [$name, 0, 0, 0];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h1 class="manual">' . $line['gc_title'] . '</h1>';
                    $body .= '</a>' . cr();
                    break;
                case 'H2':
                    $nv[1] = $name;
                    $item = $summary[$nv[0]];
                    $item[$name] = [];
                    $summary[$nv[0]] = $item;
                    $summary_name[$name] = $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h2 class="manual">' . $line['gc_title'] . '</h2>';
                    $body .= '</a>' . cr();
                    break;
                case 'H3':
                    $nv[2] = $name;
                    $item = $summary[$nv[0]][$nv[1]];
                    $item[$name] = [];
                    $summary[$nv[0]][$nv[1]]= $item;

                    $summary_name[$name] = $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h3 class="manual">' . $line['gc_title'] . '</h3>';
                    $body .= '</a>' . cr();
                    break;
                case 'H4':
                    $nv[3] = $name;
                    $item = $summary[$nv[0]][$nv[1]][$nv[2]];
                    $item[$name] = [];
                    $summary[$nv[0]][$nv[1]][$nv[2]] = $item;

                    $summary_name[$name] = $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h4 class="manual">' . $line['gc_title'] . '</h4>';
                    $body .= '</a>' . cr();
                    break;
                case 'IMG':
                    $file = '_repository/guide/' . $id . '/' . $cont;
                    if (!file_exists($file))
                        {
                            $erro .= '<br>' . $file . ' not found';
                            $file = '<span class="text-danger">img/guide/noimage.jpg</span>';
                        }

                    $files .= 'wget ' . PATH . '/'. $file . ' -O ' . $cont . ' -q</a><br>';
                    $body .= '<div class="col-sm-12"><img src="/img/'.$cont. '" class="img-fluid"/></div>'.cr();
                    //pre($line);
                    //exit;
                    break;
                case 'P':
                    $body .= '<div class="col-sm-12"><p class="p guide">'.$line['gc_content']. '</p></div>' . cr();
                    break;
                default:
                    $body .= '<div class="col-sm-12"><p>NOT: ' . $type . '</p></div>'.cr();
                    break;
            }
        }
        $body .= '</body>';

        $nr = 0;

        $sm = $this->summary($summary,$summary_name);

        echo $sm;

        $body = troca($body,'{SUMMARY}',$sm);
        $dir = '_repository/guide/' . $id . '/export';
        dircheck($dir);
        $guide = $dir . '/guide.xhtml';



        $url = '<a href="' . PATH . '/' . $guide . '" target="_blank">';
        $body .= '<div id="guide_version" style="font-size: 0.7em;">Guide Version v: 0.' . date("y.md.Hm") . '</div>';

        $html = $this->body($body);


        file_put_contents($guide, $html);


        $sx = '<tt>';
        $sx .= 'export PAYARA=/usr/local/payara5/glassfish';
        $sx .= '<br>';
        $sx .= 'cd $PAYARA/domains/domain1/applications/dataverse';
        $sx .= '<br>';
        $sx .= 'echo "Copiando Guia"';
        $sx .= '<br>';
        $sx .= '<br>';
        $sx .= 'wget ' . $url . PATH . '/_repository/guide/' . $id . '/export/guide.xhtml -O guide.xhtml -q' . '</a>';
        $sx .= '<br>';
        $sx .= $pause;
        $sx .= 'echo "Copiando Imagens"';
        $sx .= '<br>';
        $sx .= 'mkdir $PAYARA/domains/domain1/applications/dataverse/img';
        $sx .= '<br>';
        $sx .= 'cd $PAYARA/domains/domain1/applications/dataverse/img';
        $sx .= '<br>';
        $sx .= $files;
        $sx .= '<br>';
        $sx .= $pause;
        $sx .= 'echo "Reinicializando o Payara"';
        $sx .= '<br>';
        $sx .= '$PAYARA/bin/asadmin stop-domain';
        $sx .= '<br>';
        $sx .= '$PAYARA/bin/asadmin start-domain';
        $sx .= '<br>';
        $sx .= $pause;
        $sx .= 'echo "Fim do processo"';
        $sx .= '<br>';
        $sx .= '</tt>';

        $sx .= '<br>';
        $sx .= bsmessage($erro,3);

        $sx = bs(bsc($sx));
        return $sx;
    }

    function summary($summary,$label)
        {
            $sx = '<ol>';

            foreach($summary as $idn1=>$subn1)
                {
                    $link = '<a href="#'.$idn1.'">';
                    $linka = '</a>';
                    $sx .= '<li>'.$link.$label[$idn1].$linka;
                    if (is_array($subn1))
                        {
                            $sx .= $this->summary($subn1, $label);
                        }
                    $sx .= '</li>';
                }
            $sx .= '</ol>';
            return $sx;
        }
}
