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
    
    public function __construct(\Nette\Mail\IMailer $mailer) {
	$this->mailer = $mailer;
    }
    
    public function getName() {
	return $this->name;
    }
    
    public function getSubject() {
	return $this->subject;
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
    
    public function addTo($to, $replace = false) {
	if($replace) {
	    $this->to = array();
	}
	
	if(is_string($to)) {
	    $this->to[] = $to;
	} elseif(is_array($to)) {
	    $this->to = array_merge($this->to, $to);
	} else {
	    Throw new \InvalidArgumentException(__METHOD__ . ' only accepts string or array as $to parameter.');
	}
    }
    
    public function addCc($cc, $replace = false) {
	if($replace) {
	    $this->cc = array();
	}
	
	if(is_string($cc)) {
	    $this->cc[] = $cc;
	} elseif(is_array($cc)) {
	    $this->cc = array_merge($this->cc, $cc);
	} else {
	    Throw new \InvalidArgumentException(__METHOD__ . ' only accepts string or array as $to parameter.');
	}
    }
    
    public function addBcc($bcc, $replace = false) {
	if($replace) {
	    $this->bcc = array();
	}
	
	if(is_string($bcc)) {
	    $this->bcc[] = $bcc;
	} elseif(is_array($bcc)) {
	    $this->bcc = array_merge($this->bcc, $bcc);
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
    
    public function send($batch) {
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
	
	if(is_null($batch)) {
	    // send to all recipients 
	    foreach($this->to as $name => $mail) {
		$message->addTo($mail, $name);
	    }
	    
	    foreach($this->cc as $name => $mail) {
		$message->addCc($mail, $name);
	    }
	    
	    foreach($this->bcc as $name => $mail) {
		$message->addBcc($mail, $name);
	    }
	} else {
	    // @todo implement here
	}
    }
}
