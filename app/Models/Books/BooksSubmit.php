<?php

namespace App\Models\Books;

use CodeIgniter\Model;

class BooksSubmit extends Model
{
    protected $DBGroup          = 'books';
    protected $table            = 'books_submit';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs', 'bs_post', 'bs_status',
        'bs_title', 'b_isbn', 'bs_rdf',
        'bs_arquivo'
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

    function registerPDF()
        {
            $tmp = $_FILES['file']['tmp_name'];
            $name = $_FILES['file']['name'];
            $md5 = md5_file($tmp);
            $dir = $this->directory();
            $filename = $dir . $md5 . '.pdf';
            $exist = file_exists($filename);
            move_uploaded_file($tmp,$filename);

            $dt = $this
                ->where('bs_arquivo',$md5)
                ->first();
            if ($dt == [])
                {
                    $dd['bs_arquivo'] = $md5;
                    $idc = $this->set($dd)->insert();
                    $status = 0;
                } else {
                    $idc = $dt['id_bs'];
                    $status = $dt['bs_status'];
                }
            return [$md5,$exist,$idc,$status];
        }

    function directory()
        {
            $dir = '.tmp/books/';
            dircheck($dir);
            return $dir;
        }

    function view($id)
        {
            $sx = '';
            $dt = $this->find($id);

            if ($dt['bs_status'] == 2)
                {
                    if ($dt['bs_rdf'] > 0)
                        {
                            $url = PATH . 'a/' . $dt['bs_rdf'];
                            echo metarefresh($url,0);
                            exit;
                        }
                }

            $sx .= bsc($this->action($dt),12);

            if ($dt != [])
                {
                    $js = (array)json_decode($dt['bs_post']);

                    foreach($js as $key=>$value)
                        {
                            $sx .= bsc(msg('brapci.'.$key), 3, 'small mt-2');
                            $sx .= bsc($value.'&nbsp;', 9,'border-top border-secondary');
                        }
                } else {
                    $sx .= 'Registro não localizado '.$id;
                }
            $sx = bsc($sx,5);
            $iframe = $this->show_pdf($dt);
            $sx .= bsc($iframe,7);
            return bs($sx);
        }

    function chache_status($id,$sta)
        {
            $dd['bs_status'] = $sta;
            $this->set($dd)->where('id_bs',$id)->update();
            return True;
        }

    function action($dt)
        {
            $sx = '';
            $sta = $dt['bs_status'];
            $id = $dt['id_bs'];
            $btn = '<a href="'.PATH.'admin/book/status/0" class="btn btn-outline-warning ms-2">' . lang('brapci.return') . '</a>';
            switch($sta)
                {
                    case '0':
                        $sx .= '<a href="'.PATH.'admin/book/change/'.$id.'/1" class="btn btn-outline-primary">'.lang('brapci.accept').'</a>';
                        $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/9"  class="btn btn-outline-danger ms-2">' . lang('brapci.reject') . '</btn>';
                        $sx .= $btn;
                        break;
                    case '1':
                        $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/2" class="btn btn-outline-primary">' . lang('brapci.create_book') . '</a>';
                        $sx .= '<a href="' . PATH . 'admin/book/change/' . $id . '/9"  class="btn btn-outline-danger ms-2">' . lang('brapci.reject') . '</btn>';
                        $sx .= $btn;
                    default:
                        $sx .= 'No actions';
                    break;
                }
            return $sx;
        }

    function show_pdf($dt)
        {
            $file = $dt['bs_arquivo'];
            $html = PATH.'.tmp/books/'.$file.'.pdf';
            $sx = $html.'
            <iframe src="'.$html.'" style="width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;">
                Your browser doesnt support iframes
            </iframe>';
            return $sx;
        }

    function savePDF($id)
        {

        }

    function list($sta)
        {
            $sx = '';
            $dt = $this
                ->where('bs_status',$sta)
                ->findAll();
            foreach($dt as $id=>$line)
                {
                    $link = '<a href="'.PATH.'admin/book/view/'.$line['id_bs'].'">';
                    $linka = '</a>';
                    $js = (array)$line['bs_post'];
                    $sx .= '<li>';
                    $js = $js[0];
                    $js = (array)json_decode($js);

                    if (isset($js['b_titulo']))
                        {
                            $sx .= '<b>';
                            $sx .= $link . (string)$js['b_titulo'].$linka;
                            $sx .= '<br><i>' . $js['b_autor'] . '</i>';
                            $sx .= '</b>';
                        } else {
                            $sx .= '<b>';
                            $sx .= $link . 'Não informado' . $linka;
                            $sx .= '<br><i>' . 'sem autoria registrada' . '</i>';
                            $sx .= '</b>';
                        }

                    $sx .= '</li>';
                }
            return $sx;
        }

    function resume()
        {
            $sx = '';
            $dt = $this
                ->select("count(*) as total, bs_status")
                ->where('bs_status',1)
                ->ORwhere('bs_status', 2)
                ->groupBy('bs_status')
                ->orderBy('bs_status')
                ->findAll();
            foreach($dt as $id=>$line)
                {
                    $link = '<a class="text-danger" href="'.PATH.'admin/book/status/'.$line['bs_status'].'">';
                    $linka = '</a>';
                    $sx .= '<li class="text-danger" style="font-size: 0.7em;">';
                    $sx .= $link.lang('brapci.book_status_'.$line['bs_status']).$linka;
                    $sx .= ' <b>';
                    $sx .= '('.$line['total'].')';
                    $sx .= '</b>';
                    $sx .= '</li>';
                }
            if ($sx != '')
                {
                    $sx = '<b>Livros submetidos</b>'.$sx;
                }
            return $sx;
        }

    function sendEmail($id)
        {
            $dt = $this->where('id_bs',$id)->first();
            $email = 'renefgj@gmail.com';
            $subject = 'Submissão de livro';
            $name = 'Rene Faustino Gabriel Junior';
            $to = [$email];

            $btn_concordancia = '<a href="https://brapci.inf.br/#/books/disclaimer/'.$id.'/'.md5($id.'brapci_livros').'" style="padding: 5px 10px; border:1px solid #000; border-radius: 10px;">Concordo com os termos</a>';

            /* Enviar e-mail */
            $txt = '';
            $txt .= '<table width="600" border=0>';
            $txt .= '<tr><td><img src="cid:$image1" style="width: 100%;"></td></tr>';
            $txt .= '<tr><td>';
            $txt .= 'Prezado autor ' . $name . ',<br>';
            $txt .= '<br>';
            $txt .= 'Sua submissão foi registrada e será analisada, porém é necessário que concorde com os termos.';
            $txt .= '<br><br>';
            $txt .=
        '<h2>Disclaimer - Brapci-Livros</h2><br>
            A Brapci-Livros tem como objetivo promover o acesso gratuito a livros e materiais educativos de domínio público ou disponibilizados sob licenças abertas.
            Todos os conteúdos disponíveis nesta plataforma foram selecionados para garantir que estejam em conformidade com as leis de direitos autorais e licenças aplicáveis.
            Não existe cobrança para registrar ou acessar as obras.
            <br><br>
            <b>Direitos Autorais e Licenças:</b>
            <br>
            Os livros e materiais disponíveis nesta base de dados são de domínio público ou licenciados sob termos que permitem sua livre distribuição.
            Podem também ser disponibilizados com as dividas autorizações dos autores e da editora.
            <br>
            No entanto, é responsabilidade dos usuários verificar a licença específica de cada obra antes de utilizá-la para fins comerciais ou redistribuição. Quaisquer usos fora do escopo permitido pela licença exigem a obtenção de permissão prévia do(s) titular(es) dos direitos autorais.
            <br><br>
            <b>Limitação de Responsabilidade:</b>
            <br>
            Embora nos esforcemos para garantir a precisão das informações e a conformidade legal dos materiais incluídos nesta base de dados, não nos responsabilizamos por eventuais erros, omissões, ou pela interpretação dos conteúdos pelos usuários. O uso dos materiais disponibilizados é de total responsabilidade do usuário.
            <br><br>
            <b>Atualizações e Alterações:</b>
            <br>
            Reservamo-nos o direito de atualizar ou remover qualquer material desta base de dados sem aviso prévio, a fim de garantir a conformidade com as leis de direitos autorais e as políticas da plataforma.
            <br><br>
            <b>Contato:</b>
            <br><br>
            Caso identifique qualquer material que não deva estar disponível na base de dados ou tenha dúvidas sobre os termos de uso, entre em contato conosco pelo e-mail brapcici@gmail.com.<br>
            <a href="https://brapci.inf.br/#/books">https://brapci.inf.br/#/books</a>
            <br><br>
            '.$btn_concordancia.'
            ';
            $txt .= '</td></tr></table>';
            $subject = '[BRAPCI-LIVROS] ';
            $subject .= 'Termo de submissão';

            sendemail($email,$subject,$txt);
        }

    function register()
        {
            $PS = array_merge($_POST, $_GET);
            $PSj = json_encode($PS);
            $RSP = [];
            $dt = [];
            if (isset($PS['id_b']))
                {
                    if (isset($PS['id_b'])) {
                        $dt['bs_title'] = "";
                        $dt['bs_post'] = $PSj;
                        $dt['bs_status'] = 1;
                        $this->set($dt)->where('id_bs', $PS['id_b'])->update();
                        $dt['id_b'] = $PS['id_b'];
                    }
                    $RSP['ID'] = $dt['id_b'];
                    $RSP['st'] = 1;
                    $RSP['status'] = '200';
                    $this->sendEmail($dt['id_b']);
                } else {
                    $RSP['status'] = '500';
                    $RSP['message'] = 'ID do arquivo inválido';
                    $RSP['post'] = $PSj;
                }

                return $RSP;
        }
}
