<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    // ESTOS VALORES SON SÓLO DEFENSA/DEFECTO.
    // SERÁN SOBRESCRITOS POR LA CONFIGURACIÓN DE TU ARCHIVO .env
    
    // Eliminado getenv() para evitar el error 'Constant expression contains invalid operations'.
    public string $fromEmail  = ''; 
    public string $fromName   = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     * Dejar en blanco o 'localhost'. El valor de .env lo sobrescribirá.
     */
    public string $SMTPHost = ''; 

    /**
     * SMTP Username
     * Dejar en blanco para que el .env lo controle.
     */
    public string $SMTPUser = ''; 

    /**
     * SMTP Password
     * ¡CRÍTICO! DEBE ESTAR VACÍO ('') AQUÍ para no exponer la clave en Git.
     */
    public string $SMTPPass = ''; 

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587; 

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 60;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     */
    public string $SMTPCrypto = 'tls'; 

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
     * Set to true to use Delivery Status Notification
     */
    public bool $DSN = false;
}