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
	    $this->to[$to] = new MailRecipient($to);
	} elseif(is_array($to)) {
	    foreach($to as $name => $email) {
		$this->to[$email] = new MailRecipient($email, $name);
	    }
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
	    foreach($this->to as $r) {
		$message->addTo($r->getEmail(), $r->getName());
	    }
	    
	    foreach($this->cc as $r) {
		$message->addTo($r->getEmail(), $r->getName());
	    }
	    
	    foreach($this->bcc as $r) {
		$message->addTo($r->getEmail(), $r->getName());
	    }
	} else {
	    // @todo implement here
	}
    }
}
