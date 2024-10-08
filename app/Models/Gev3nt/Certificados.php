<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;
use TCPDF;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{
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
        $img_file = K_PATH_IMAGES . 'image_demo.jpg';;
        $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}

class Certificados extends Model
{
    protected $DBGroup          = 'gev3nt';
    protected $table            = 'event';
    protected $primaryKey       = 'id_e';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_e',
        'e_name',
        'e_url',
        'e_description',
        'e_active',
        'e_logo',
        'e_sigin_until'
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



    public function certificado($nomeParticipante = 'Fulano de Tal', $cargaHoraria = '8')
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
        $imagemFundo = '_repository/g3vent/certificados/feisc4_modelo01.jpg'; // Coloque a imagem no diretório 'writable/uploads'

        // Verifica se o arquivo de fundo existe
        if (!file_exists($imagemFundo)) {
            return ['status'=>'404','message' => 'Imagem de fundo não encontrada!'];
        }

        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT,
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
        $textoCertificado = "Certificamos que $nomeParticipante participou do evento com uma carga horária de $cargaHoraria horas.";

        // set font
        $pdf->SetFont('times', '',
            48
        );

        // add a page
        $pdf->AddPage();
        // remove default header
        $pdf->setPrintHeader(false);
        $img_file = $imagemFundo;
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

        // Print a text
        $html = '<span style="background-color:yellow;color:blue;">&nbsp;PAGE 1&nbsp;</span>
<p stroke="0.2" fill="true" strokecolor="yellow" color="blue" style="font-family:helvetica;font-weight:bold;font-size:26pt;">You can set a full page background.</p>';
        $pdf->writeHTML($html, true, false, true, false,
            ''
        );

        // add a page
        $pdf->AddPage();
        // get the current page break margin

        // disable auto-page-break
        // set bacground image
        $img_file = K_PATH_IMAGES . 'image_demo.jpg';;
        $pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        // set the starting point for the page content
        $pdf->setPageMark();

        //Close and output PDF document
        $pdf->Output();
        exit;



        // --- example with background set on page ---

        // remove default header
        $pdf->setPrintHeader(false);

        // add a page
        $pdf->AddPage();


        // -- set new background ---

        // get the current page break margin
        $bMargin = $pdf->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $pdf->getAutoPageBreak();
        // disable auto-page-break
        $pdf->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = $imagemFundo;
        $pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();


        // Print a text
        $html = '<span style="color:white;text-align:center;font-weight:bold;font-size:80pt;">PAGE 3</span>';
        $pdf->writeHTML($html, true, false, true, false,
            ''
        );

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output();
        exit;

        // Criar um QR Code
        $qrCode = new QrCode($textoCertificado);
        $qrCode->setSize(100);
        $qrCode->setMargin(10);
        $writer = new PngWriter();
        $qrCodePng = $writer->write($qrCode)->getString();

        // Caminho temporário para salvar o QR Code gerado
        $caminhoQrCode = $qrcodeDir . 'qr_' . time() . '.png';
        file_put_contents($caminhoQrCode, $qrCodePng);

        // Inicializar o TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4');
        $pdf->SetCreator(PDF_CREATOR);
        // remove default footer
        $pdf->setPrintFooter(false);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT,
            PDF_MARGIN_TOP,
            PDF_MARGIN_RIGHT
        );
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        $pdf->SetAuthor('Sistema de Certificados');
        $pdf->SetTitle('Certificado de Participação');
        $pdf->SetSubject('Certificado');

        // Adicionar uma página
        $pdf->AddPage();

        // Definir a imagem de fundo
        $pdf->Image($imagemFundo, 0, 0, 230, 340, '', '', '', true, 300, '', false, false, 0);

        // Print a text
        $html = '<span style="background-color:yellow;color:blue;">&nbsp;PAGE 1&nbsp;</span>
<p stroke="0.2" fill="true" strokecolor="yellow" color="blue" style="font-family:helvetica;font-weight:bold;font-size:26pt;">You can set a full page background.</p>';
        $pdf->writeHTML($html, true, false, true, false,
            ''
        );

        // Definir a posição e o estilo do texto
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetY(100);
        $pdf->Cell(0, 10, $nomeParticipante . $pdf->getPageHeight(), 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 16);
        $pdf->SetY(120);
        $pdf->Cell(0, 10, "Carga Horária: $cargaHoraria horas", 0, 1, 'C');

        // Adicionar o QR Code ao PDF
        //$pdf->Image($caminhoQrCode, 10, 250, 30, 30, 'PNG');

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
