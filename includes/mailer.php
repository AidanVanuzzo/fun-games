<?php
// includes/mailer.php

require_once __DIR__ . '/../src/utils/autoloader.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

const MAIL_CONFIGURATION_FILE = __DIR__ . '/../src/config/mail.ini';

/**
 * Envoie un e-mail avec PHPMailer et les paramètres de mail.ini
 */
function send_email(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
{
    $config = parse_ini_file(MAIL_CONFIGURATION_FILE, true);

    if (!$config) {
        throw new Exception("Erreur lors de la lecture du fichier de configuration : " . MAIL_CONFIGURATION_FILE);
    }

    $host           = $config['host'];
    $port           = (int)$config['port'];
    $authentication = filter_var($config['authentication'], FILTER_VALIDATE_BOOLEAN);
    $username       = $config['username'];
    $password       = $config['password'];
    $fromEmail      = $config['from_email'];
    $fromName       = $config['from_name'] ?? 'LSBOWL';
    $secure         = $config['secure'] ?? '';

    if ($textBody === '') {
        $textBody = strip_tags($htmlBody);
    }

    $mail = new PHPMailer(true);

    try {
        // Config SMTP (Mailpit dans ton cas)
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->Port       = $port;
        $mail->SMTPAuth   = $authentication;

        if ($authentication) {
            $mail->Username = $username;
            $mail->Password = $password;
        }

        if (!empty($secure)) {
            $mail->SMTPSecure = $secure; // ex: 'ssl' si un jour tu passes à Infomaniak
        }

        $mail->CharSet  = "UTF-8";
        $mail->Encoding = "base64";

        // Expéditeur / destinataire
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody;

        return $mail->send();
    } catch (Exception $e) {
        // En dev tu peux débugger ici
        // echo "Erreur mail : " . $e->getMessage();
        return false;
    }
}
