<?php

require_once '../vendor/autoload.php';

require_once '../iMailRecipient.php';
require_once '../MailRecipient.php';
require_once '../iMail.php';
require_once '../Mail.php';

$smtp_options = array(
    'host' => 'smtp.domain.com',
    'secure' => 'tls',
    'port' => 587,
    'username' => 'sender@domain.com',
    'password' => 'secret_password'
);

$mailer = new Nette\Mail\SmtpMailer($smtp_options);
$mail = new Emailing\Mail($mailer);

$mail->setSubject('Sending in batch');
$mail->setTextBody('This mail was sent in batches');

$mail->addTo('email@domain.com');
$mail->addTo('email2@domain.com');
$mail->addTo('email3@domain.com');
$mail->addTo('email4@domain.com');
$mail->addTo('email5@domain.com');

$mail->send(1);