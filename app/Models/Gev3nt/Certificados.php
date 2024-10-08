<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;
use TCPDF;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{
    private $background_image;

    public function __construct($background_image, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->background_image = $background_image;
    }
    //Page header
    public function Header()
    {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $this->Image($this->background_image, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}

class Certificados extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'events';
    protected $primaryKey       = 'id_e';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_e',
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

    public function certificado($id=0)
        {
            $dt=$this->first();
            pre($dt);
            $this->emite_certificado("RENE",8,$dt);
        }

    public function emite_certificado($nomeParticipante = 'Fulano de Tal', $cargaHoraria = '8',$dt=[])
    {
        // Define base directory paths
        $baseDir = '/tmp_brapci/';
        $tmpDir = $baseDir.'certificado/';
        $qrcodeDir = $tmpDir . 'qrcode/';
        $certificadoDir = $tmpDir;

        // Create directories if they don't exist
        dircheck($baseDir);
        dircheck($tmpDir);
        dircheck($qrcodeDir);
        dircheck($certificadoDir);

        // Caminho da imagem de fundo do certificado
        $dirImage = '_repository/g3vent/';
        $imagemFundo = $dirImage . $dt['e_background'];

        // Verifica se o arquivo de fundo existe
        if (!file_exists($imagemFundo)) {
            return ['status'=>'404','message' => 'Imagem de fundo não encontrada!'];
        }

        // create new PDF document
        $pdf = new MYPDF($imagemFundo, PDF_PAGE_ORIENTATION, PDF_UNIT,
            PDF_PAGE_FORMAT,
            true,
            'UTF-8',
            false
        );

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 051');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '',
            PDF_FONT_SIZE_MAIN
        ));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT,
            PDF_MARGIN_TOP,
            PDF_MARGIN_RIGHT
        );
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        // remove default footer
        $pdf->setPrintFooter(false);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);




        // Crie um texto para o QR Code
        $textoCertificado = $dt['e_texto'];

        // set font
        $pdf->SetFont('times', '',
            48
        );

        // add a page
        $pdf->AddPage();
        // remove default header
        $pdf->setPrintHeader(false);


        // Print a text
        $html = '<span>&nbsp;PAGE 1&nbsp;</span>
                <p stroke="0.2" fill="true" strokecolor="yellow" color="blue" style="font-family:helvetica;font-weight:bold;font-size:26pt;">
                CERTIFICADO.</p>
                <div style="width: 400px; text-align: justify;">'.$textoCertificado.'</div>';
        $pdf->writeHTML($html, true, false, true, false,
            ''
        );

        // set style for barcode
        $style = array(
                'border' => true,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );

        // QRCODE,H : QR-CODE Best error correction
        $pdf->write2DBarcode('https://cip.brapci.inf.br/api/g3vent/valid', 'QRCODE,H',
            160,
            180,
            40,
            40,
            $style,
            'N'
        );

        //Close and output PDF document
        $pdf->Output();
        exit;



        // Caminho para salvar o certificado
        $caminhoCertificado = $certificadoDir . '/certificado_' . time() . '.pdf';

        // Salvar o PDF no servidor
        $pdf->Output($caminhoCertificado, 'F');

        // Remover o arquivo temporário do QR Code
        unlink($caminhoQrCode);

        $dd = [];
        $dd['link'] = base_url('/certificado/' . basename($caminhoCertificado));
        $dd['message'] = $dd['link'];
        echo '<a href="'.$dd['link'].'" target="_blank">link</a>';
        exit;

        return $dd;

    }
}
