<?php

/**
 * Fake logging class - here just prints 
 */
class FakeLogger {
    private static $levels = array(
	LOG_EMERG => 'EMERGENCY',
	LOG_ALERT => 'ALERT',
	LOG_CRIT => 'CRITICAL',
	LOG_ERR => 'ERROR',
	LOG_WARNING => 'WARNING',
	LOG_NOTICE => 'NOTICE',
	LOG_INFO => 'INFO',
	LOG_DEBUG => 'DEBUG'
    );
    
    public function log($level, $msg) {
	echo self::$levels[$level] . ': ' . $msg . PHP_EOL;
    }
    
}

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

/** optional - only for logging purposes */
$logger = new FakeLogger();
$mail->setLogger(array($logger, 'log'));
/** minimum level to log */
$mail->setLogLimit(LOG_INFO);

$mail->setSubject('Sending in batch');
$mail->setTextBody('This mail was sent in batches');

$mail->addTo('email1@domain.com');
$mail->addTo('email2@domain.com');
$mail->addTo('email3@domain.com');
$mail->addTo('email4@domain.com');
$mail->addTo('email5@domain.com');

$mail->send(1);