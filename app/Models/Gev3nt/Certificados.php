<?php

namespace App\Models\Gev3nt;

use CodeIgniter\Model;
use TCPDF;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

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
        dircheck('_repository/g3vent/certificados');
        // Caminho da imagem de fundo do certificado
        $imagemFundo = '_repository/g3vent/certificados/feisc4_modelo04.jpg'; // Coloque a imagem no diretório 'writable/uploads'

        // Verifica se o arquivo de fundo existe
        if (!file_exists($imagemFundo)) {
            return ['message'=>'Imagem de fundo não encontrada!'];
        }

        // Crie um texto para o QR Code
        $textoCertificado = "Certificamos que $nomeParticipante participou do evento com uma carga horária de $cargaHoraria horas.";

        // Criar um QR Code
        $qrCode = new QrCode($textoCertificado);
        $qrCode->setSize(100);
        $qrCode->setMargin(10);
        $writer = new PngWriter();
        $qrCodePng = $writer->write($qrCode)->getString();

        // Caminho temporário para salvar o QR Code gerado
        $caminhoQrCode = '.tmp/qr_' . time() . '.png';
        file_put_contents($caminhoQrCode, $qrCodePng);

        // Inicializar o TCPDF
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Certificados');
        $pdf->SetTitle('Certificado de Participação');
        $pdf->SetSubject('Certificado');

        // Adicionar uma página
        $pdf->AddPage();

        // Definir a imagem de fundo
        $pdf->Image($imagemFundo, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), '', '', '', false, 300, '', false, false, 0);

        // Definir a posição e o estilo do texto
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetY(100);
        $pdf->Cell(0, 10, $nomeParticipante, 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 16);
        $pdf->SetY(120);
        $pdf->Cell(0, 10, "Carga Horária: $cargaHoraria horas", 0, 1, 'C');

        // Adicionar o QR Code ao PDF
        $pdf->Image($caminhoQrCode, 10, 250, 30, 30, 'PNG');

        // Caminho para salvar o certificado
        $caminhoCertificado = WRITEPATH . 'uploads/certificado_' . time() . '.pdf';

        // Salvar o PDF no servidor
        $pdf->Output($caminhoCertificado, 'F');

        // Remover o arquivo temporário do QR Code
        unlink($caminhoQrCode);
        print("Certificado gerado com sucesso: <a href='" . base_url('writable/uploads/' . basename($caminhoCertificado)) . "' target='_blank'>Download Certificado</a>");

        return "Certificado gerado com sucesso: <a href='" . base_url('writable/uploads/' . basename($caminhoCertificado)) . "' target='_blank'>Download Certificado</a>";
    }
}
