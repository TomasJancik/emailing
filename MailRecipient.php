<?php

namespace Emailing;

/**
 * MailRecipient
 *
 * @author t.jancik
 */
class MailRecipient implements iMailRecipient {
    
    public static $WAITING = 'WAITING';
    public static $SENT = 'SENT';
    public static $ERROR = 'ERROR';
    
    /** @var string */
    private $name;
    
    /** @var string */
    private $email;
    
    /** @var string */
    private $status;
    
    /** @var string */
    private $error;
    
    /** @var string */
    private $date;
    
    public function __construct($mail, $name = null) {
	$this->email = $email;
	$this->name = $name;
	$this->status = self::$WAITING;
    }
    
    public function setName($name) {
	$this->name = $name;
    }
    
    public function setEmail($email) {
	$this->email = $email;
    }
    
    public function setSent() {
	$this->status = self::$SENT;
	$this->date = date('Y-m-d H:i:s');
    }
    public function setError($err){
	$this->error = $err;
    }
    
    public function getName() {
	return $this->name;
    }
    
    public function getEmail() {
	return $this->email;
    }
    
    public function getStatus() {
	return $this->status;
    }
    
    public function getError() {
	return $this->error;
    }
    
    public function getDate() {
	return $this->date;
    }
    
    /**
     * Tell if the recipient was already mailed or not
     * @return bool
     */
    public function isSent() {
	return $this->status != self::$WAITING;
    }
}
