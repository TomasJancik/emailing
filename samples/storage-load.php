<?php

require_once '../vendor/autoload.php';

require_once '../iMailRecipient.php';
require_once '../MailRecipient.php';
require_once '../iMail.php';
require_once '../Mail.php';
require_once '../iStorage.php';
require_once '../MySQLStorage.php';

// build a mail
$smtp_options = array(
    'host' => 'smtp.domain.com',
    'secure' => 'tls',
    'port' => 587,
    'username' => 'sender@domain.com',
    'password' => 'secret_password'
);

$mailer = new Nette\Mail\SmtpMailer($smtp_options);

// prepare storage
$db = new mysqli('localhost', 'root', 'p4ssw0rd', 'test');

$storage = new \Emailing\MySQLStorage($db);
$storage->setPrefix('emailing');

$storage->setMailer($mailer);
$storage->load('Expample of using mail storage');
