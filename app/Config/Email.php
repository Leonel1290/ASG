<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // LECTURA DE VARIABLES DE ENTORNO PARA REMITENTE
    // Asegura que se usen las variables SENDGRID_FROM_EMAIL/NAME de Render
    public string $fromEmail  = getenv('SENDGRID_FROM_EMAIL');
    public string $fromName   = getenv('SENDGRID_FROM_NAME');
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    // LECTURA DE VARIABLES DE ENTORNO PARA PROTOCOLO
    public string $protocol = getenv('email.protocol') ?: 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    // LECTURA DE VARIABLES DE ENTORNO PARA HOST
    public string $SMTPHost = getenv('email.SMTPHost') ?: 'localhost';

    /**
     * SMTP Username (Debe ser 'apikey' para SendGrid)
     */
    // LECTURA DE VARIABLES DE ENTORNO PARA USUARIO
    public string $SMTPUser = getenv('email.SMTPUser') ?: '';

    /**
     * SMTP Password (Tu API Key de SendGrid)
     */
    // LECTURA DE VARIABLES DE ENTORNO PARA CONTRASEÑA
    public string $SMTPPass = getenv('email.SMTPPass') ?: '';

    /**
     * SMTP Port
     */
    // LECTURA DE VARIABLES DE ENTORNO PARA PUERTO (587 para SendGrid)
    public int $SMTPPort = (int)getenv('email.SMTPPort') ?: 25;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = (int)getenv('email.SMTPTimeout') ?: 60;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption. (Debe ser 'tls' para el puerto 587 de SendGrid)
     */
    // LECTURA DE VARIABLES DE ENTORNO PARA ENCRIPTACIÓN
    public string $SMTPCrypto = getenv('email.SMTPCrypto') ?: 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = true;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * The mail MIME version number. Recommended: 1.0
     */
    public string $newline = "\n";

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Sets the most preferred way to send email
     */
    public string $DSN = '';
}