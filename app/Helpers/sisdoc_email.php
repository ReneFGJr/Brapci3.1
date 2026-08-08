<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpParser\Node\Stmt\Foreach_;

function email_smtp_test()
{
    $sx = '';

    $smtpHost = get('smtp_host');
    $smtpPort = get('smtp_port');
    $smtpUser = get('smtp_user');
    $smtpPass = get('smtp_pass');
    $fromEmail = get('from_email');
    $toEmail = get('to_email');

    $sx .= '
            <h2>Teste de Configuração SMTP</h2>
            <form action="" method="POST">
                <label for="smtp_host">Servidor SMTP:</label><br>
                <input type="text" class="form-control full border border-secondary big" id="smtp_host" value="'.$smtpHost. '" name="smtp_host" required><br>

                <label for="smtp_port">Porta SMTP:</label><br>
                <select id="smtp_port" class="form-control full border border-secondary" name="smtp_port" required>
                <option value="' . $smtpPort . '">' . $smtpPort . '</option>
                <option value="25">25</option>
                <option value="587">587</option>
                </select><br>

                <label for="smtp_user">Usuário SMTP:</label><br>
                <input type="text" class="form-control full border border-secondary" id="smtp_user"  value="' . $smtpUser . '" name="smtp_user" required><br>

                <label for="smtp_pass">Senha SMTP:</label><br>
                <input type="text" class="form-control full border border-secondary" id="smtp_pass"  value="' . $smtpPass . '" name="smtp_pass" required><br>

                <label for="from_email">Email Remetente:</label><br>
                <input type="email" class="form-control full border border-secondary" id="from_email"  value="' . $fromEmail . '" name="from_email" required><br>

                <label for="to_email">Email Destinatário (para teste):</label><br>
                <input type="email" class="form-control full border border-secondary" id="to_email"  value="' . $toEmail . '" name="to_email" required><br>

                <input type="submit" class="btn btn-secondary" value="Testar Configuração">
            </form>
            <hr>
        ';


    $email = \Config\Services::email();

    $config = [
        'protocol' => 'smtp',
        'SMTPHost' => $smtpHost,
        'SMTPPort' => $smtpPort,
        'SMTPUser' => $smtpUser,
        'SMTPPass' => $smtpPass,
        'mailType'  => 'html',
        'charset'   => 'utf-8',
        'wordWrap'  => true
    ];

    $sx .= '<div class="mt-3"><h4>Log SMTP</h4><pre class="border p-3 bg-light">';

    if ($smtpHost and $smtpPort and $fromEmail and $smtpUser) {

        $email->initialize($config);

        $email->setFrom($fromEmail, 'Teste SMTP');
        $email->setTo($toEmail);
        $email->setSubject('Teste de configuração SMTP');
        $email->setMessage('Este é um email de teste para verificar a configuração do servidor SMTP.');

        $sent = $email->send(false);
        if ($sent) {
            $sx .= bsmessage('Email enviado com sucesso!', 1);
        } else {
            $sx .= bsmessage('Erro ao enviar o email:', 3);
        }

        $sx .= htmlspecialchars($email->printDebugger(['headers', 'subject', 'body']));
    } else {
        $sx .= 'Configuração incompleta. Informe SMTP host, porta, remetente e usuário para testar o envio.';
    }

    $sx .= '</pre></div>';
    $sx = bs(bsc($sx, 12));
    return $sx;
}

function sendmail($to = [], $subject = '', $body = '', $attachs = [], $images = [])
{
    return sendemail($to, $subject, $body, $attachs, $images);
}
function sendemail($to = [], $subject = '', $body = '', $attachs = [], $images = [])
{
    $config = config(\Config\Email::class);
    $email = \Config\Services::email($config, false);
    $recipients = is_array($to) ? array_values(array_filter($to)) : trim((string) $to);

    if ($config->fromEmail === '' || $recipients === '' || $recipients === []) {
        log_message('error', 'E-mail nao enviado: remetente ou destinatario nao configurado.');
        return bsmessage('Erro ao enviar o e-mail: remetente ou destinatario nao configurado.', 3);
    }

    $email->setFrom(
        $config->fromEmail,
        $config->fromName !== '' ? $config->fromName : $config->fromEmail
    );
    $email->setTo($recipients);
    $email->setSubject((string) $subject);

    // Mantem compatibilidade com os templates que usam cid:$image1.
    $defaultImage = FCPATH . 'img/email/bg-email-hL3a.jpg';
    if (is_file($defaultImage)) {
        $email->attach($defaultImage);
        $body = str_replace('$image1', $email->setAttachmentCID($defaultImage), (string) $body);
    } else {
        $body = str_replace('$image1', '', (string) $body);
    }

    // Em $images, a chave pode ser o marcador usado no corpo e o valor o arquivo.
    foreach ((array) $images as $placeholder => $file) {
        if (! is_file($file)) {
            log_message('warning', 'Imagem de e-mail nao encontrada: {file}', ['file' => $file]);
            continue;
        }

        $email->attach($file);
        $cid = $email->setAttachmentCID($file);
        if (is_string($placeholder)) {
            $body = str_replace($placeholder, $cid, $body);
        }
    }

    foreach ((array) $attachs as $file) {
        if (is_file($file)) {
            $email->attach($file);
        } else {
            log_message('warning', 'Anexo de e-mail nao encontrado: {file}', ['file' => $file]);
        }
    }

    $email->setMessage($body);

    try {
        if ($email->send(false)) {
            return bsmessage('E-mail enviado com sucesso!', 1);
        }

        $debug = $email->printDebugger(['headers']);
        log_message('error', 'Falha no envio de e-mail: {debug}', ['debug' => $debug]);
        return bsmessage('Erro ao enviar o e-mail.', 3);
    } catch (\Throwable $exception) {
        log_message('error', 'Excecao no envio de e-mail: {message}', [
            'message' => $exception->getMessage(),
        ]);
        return bsmessage('Erro ao enviar o e-mail.', 3);
    }
}
