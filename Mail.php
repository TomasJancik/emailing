<?php

namespace Emailing;

/**
 * Mail object hodling the information about the mail
 *
 * @author t.jancik
 */
class Mail implements iMail {
    /** @var \Nette\Mail\IMailer */
    private $mailer;
    
    /** @var string */
    private $name;
    
    /** @var string */
    private $subject;
    
    /** @var sting */
    private $from;
    
    /** @var array */
    private $to = array();
    
    /** @var array */
    private $cc = array();
    
    /** @var array */
    private $bcc = array();
    
    /** @var string */
    private $textBody;
    
    /** @var string */
    private $htmlBody;
    
    /** @var callable */
    protected $logger = false;
    
    /** @var type int */
    protected $log_limit = LOG_ERR;
    
    public function __construct(\Nette\Mail\IMailer $mailer) {
	$this->mailer = $mailer;
    }
    
    public function getName() {
	return $this->name;
    }
    
    public function getSubject() {
	return $this->subject;
    }
    
    public function getFrom() {
	return $this->from;
    }
    
    public function getTo() {
	return $this->to;
    }
    
    public function getCc() {
	return $this->cc;
    }
    
    public function getBcc() {
	return $this->bcc;
    }
    
    public function getTextBody() {
	return $this->textBody;
    }
    
    public function getHtmlBody() {
	return $this->htmlBody;
    }
    
    public function setName($name) {
	$this->name = $name;
    }
    
    public function setSubject($subject) {
	$this->subject = $subject;
    }
    
    public function setFrom($from) {
	$this->from = $from;
    }
    
    public function addTo($to, $replace = false) {
	if($replace) {
	    $this->to = array();
	}
	
	if(is_string($to)) {
	    $this->to[$to] = new MailRecipient($to);
	} elseif(is_array($to)) {
	    foreach($to as $name => $email) {
		$this->to[$email] = new MailRecipient($email, $name);
	    }
	} elseif($to instanceof iMailRecipient) {
	    $this->to[$to->getEmail()] = $to;
	} else {
	    Throw new \InvalidArgumentException(__METHOD__ . ' only accepts string or array as $to parameter.');
	}
    }
    
    public function addCc($cc, $replace = false) {
	if($replace) {
	    $this->cc = array();
	}
	
	if(is_string($cc)) {
	    $this->cc[] = new MailRecipient($cc);
	} elseif(is_array($cc)) {
	    foreach($cc as $name => $email) {
		$this->cc[$email] = new MailRecipient($email, $name);
	    }
	} elseif($cc instanceof iMailRecipient) {
	    $this->cc[$cc->getEmail()] = $cc;
	} else {
	    Throw new \InvalidArgumentException(__METHOD__ . ' only accepts string or array as $to parameter.');
	}
    }
    
    public function addBcc($bcc, $replace = false) {
	if($replace) {
	    $this->bcc = array();
	}
	
	if(is_string($bcc)) {
	    $this->bcc[] = new MailRecipient($bcc);
	} elseif(is_array($bcc)) {
	    foreach($bcc as $name => $email) {
		$this->bcc[$email] = new MailRecipient($email, $name);
	    }
	} elseif($bcc instanceof iMailRecipient) {
	    $this->bcc[$bcc->getEmail()] = $bcc;
	} else {
	    Throw new \InvalidArgumentException(__METHOD__ . ' only accepts string or array as $to parameter.');
	}
    }
    
    public function setTextBody($body) {
	$this->textBody = $body;
    }
    
    public function setHtmlBody($htmlBody) {
	$this->htmlBody = $htmlBody;
    }
    
    /**
     * Set the mailer
     * @param \Nette\Mail\IMailer $mailer
     */
    public function setMailer(\Nette\Mail\IMailer $mailer) {
        $this->mailer = $mailer;
    }
    
    public function setLogger(callable $logger) {
	$this->logger = $logger;
    }
    
    /**
     * Set the minimum log level to be logged
     * @param int $limit
     * @throws InvalidArgumentException
     */
    public function setLogLimit($limit) {
	if(is_int($limit)) {
	    $this->log_limit = $limit;
	} else {
	    throw new \InvalidArgumentException(__METHOD__ . ' only accepts INT as an argument');
	}
	
    }
    
    /**
     * Log a message using the $logger callback
     * @param int $level
     * @param string $msg
     */
    protected function log($level, $msg) {
	if(is_callable($this->logger)) {
	    if($level <= $this->log_limit) {
		call_user_func_array($this->logger, array($level, $msg));
	    }
	}
    }
    
    public function send($batch = null) {
	$message = $this->prepareMessage();
	
	if(is_null($batch)) {
	    // send to all recipients 
	    foreach($this->to as $r) {
		$message->addTo($r->getEmail(), $r->getName());
		$r->setSent();
	    }
	    
	    foreach($this->cc as $r) {
		$message->addCc($r->getEmail(), $r->getName());
		$r->setSent();
	    }
	    
	    foreach($this->bcc as $r) {
		$message->addBcc($r->getEmail(), $r->getName());
		$r->setSent();
	    }
	    
	    try {
		$this->mailer->send($message);
	    } catch(Nette\Mail\SendException $e) {
		foreach($this->to as $r) {
		    $r->setError($e->getMessage());
		}

		foreach($this->cc as $r) {
		    $r->setError($e->getMessage());
		}

		foreach($this->bcc as $r) {
		    $r->setError($e->getMessage());
		}
		
		$this->log(LOG_ERR, $e->getMessage());
	    }
	    
	} else {
	    $idx = 0;
	    
	    $this->log(LOG_INFO, 'Sending email in batches by ' . $batch . ' recipients');
	    
	    do {
		$to = array_slice($this->to, $idx, $batch);
		$idx += $batch;
		
		foreach($to as $r) {
		    $message->addTo($r->getEmail(), $r->getName());
		    $this->to[$r->getEmail()]->setSent();
		}
		
		try {
		    $this->mailer->send($message);
		} catch (\Nette\Mail\SendException $e) {
		    foreach($to as $r) {
			$this->to[$r->getEmail()]->setError($e->getMessage());
		    }
		    
		    $this->log(LOG_ERR, $e->getMessage());
		}
	    } while($idx < count($this->to));
	}
    }
    
    /**
     * Prepare the Message object and return it
     * @return \Nette\Mail\Message
     */
    protected function prepareMessage() {
	if(empty($this->subject) && empty($this->textBody) && empty($this->htmlBody)) {
	    throw new \LogicException('Subject and body cannot be empty');
	}
	
	if(empty($this->to) && empty($this->cc) && empty($this->bcc)) {
	    throw new \LogicException('No recipients were specified for this email');
	}
	
	$message = new \Nette\Mail\Message();
	
	$message->setSubject($this->subject);
	
	if(!empty($this->htmlBody)) {
	    $message->setHtmlBody($this->htmlBody);
	}
	
	if(!empty($this->textBody)) {
	    $message->setBody($this->textBody);
	}
	
	return $message;
    }
}
