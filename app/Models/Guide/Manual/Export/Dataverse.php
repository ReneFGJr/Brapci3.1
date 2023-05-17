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

    <h:body>
        <ui:composition template="/dataverse_template.xhtml">
            <ui:param name="pageTitle" value="Guide"/>
            <ui:param name="showDataverseHeader" value="false"/>
            <ui:param name="loginRedirectPage" value="dataverse.xhtml"/>
            <ui:param name="showMessagePanel" value="#{true}"/>
            <ui:define name="body">
                <h1 class="text-center">Guia do Usuário</h1>
                ' . $context . '
            </ui:define>
        </ui:composition>
    </h:body>
</html>
';
return $xhtml;
    }

    function index($id)
    {
        $Content = new \App\Models\Guide\Manual\Content();
        $dt = $Content
            ->join('guide_content_type', 'gc_type = type_cod')
            ->where('gc_guide', $id)
            ->where('type_header', 1)
            ->where('gc_active', 1)
            ->orderBy('gc_order')
            ->findAll();

        $summary = [];
        $body = '<body id="body">';

        foreach ($dt as $id => $line) {
            $type = $line['type_cod'];
            $name = $line['id_gc'];

            switch ($type) {
                case 'H1':
                    $summary[$name] = $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h1 class="manual">' . $line['gc_title'] . '</h1>';
                    $body .= '</a>';
                    break;
                case 'H2':
                    $summary[$name] = '.' . $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h2 class="manual">' . $line['gc_title'] . '</h2>';
                    $body .= '</a>';
                    break;
                case 'H3':
                    $summary[$name] = '..' . $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h3 class="manual">' . $line['gc_title'] . '</h3>';
                    $body .= '</a>';
                    break;
                case 'H4':
                    $summary[$name] = '...' . $line['gc_title'];
                    $body .= '<a name="' . $name . '" id="' . $name . '">';
                    $body .= '<h4 class="manual">' . $line['gc_title'] . '</h4>';
                    $body .= '</a>';
                    break;
                default:
                    $body .= '<p>' . $type . '</p>';
                    break;
            }
        }
        $body .= '</body>';
        pre($summary, false);
        $dir = '_repository/guide/' . $id . '/export';
        dircheck($dir);
        $guide = $dir . '/guide.xhtml';

        $url = '<a href="' . PATH . '/' . $guide . '" target="_blank">';

        $html = $this->body($body);
        file_put_contents($guide, $html);


        $sx = '<tt>';
        $sx .= 'cd /usr/local/payara5/glassfish/domains/domain1/applications/dataverse';
        $sx .= '<br>';
        $sx .= 'wget ' . $url . PATH . '/_repository/guide/' . $id . '/export/guide.xhtml -O guide.xhtml' . '</a>';
        $sx .= '</tt>';

        $sx = bs(bsc($sx));
        return $sx;
    }
}
