<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'againsafegas.ascii@gmail.com';
    public string $fromName   = 'ASG';
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';
    public string $protocol = 'smtp';
    public string $mailPath = '/usr/sbin/sendmail';
    
    // SMTP Configuration para Gmail
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPUser = 'againsafegas.ascii@gmail.com';
    public string $SMTPPass = 'ywbn dvza fiew hcir';
    public int $SMTPPort = 587; // 🔄 Cambia a 587 con TLS
    public int $SMTPTimeout = 30;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls'; // 🔄 Cambia a tls

    // Email settings
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType = 'html';
    public string $charset = 'UTF-8';
    public bool $validate = true;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;
}