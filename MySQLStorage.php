<?php

namespace Emailing;

/**
 * Store emails in MySQL database
 *
 * @author t.jancik
 */
class MySQLStorage implements iStorage {
    /** @var \mysqli */
    protected $db;
    
    /** @var \Nette\Mail\IMailer */
    protected $mailer;
    
    /** @var string */
    protected $prefix = 'emailing_';
    
    public function __construct(\mysqli $db) {
	$this->db = $db;
    }
    
    public function setMailer(\Nette\Mail\IMailer $mailer) {
	$this->mailer = $mailer;
    }
    
    /**
     * Create the tables
     * @param bool $drop
     * @throws \mysqli_sql_exception
     */
    public function install($drop = false) {
	$mail = $this->prefix . 'MAIL';
	$mail_recipient = $this->prefix . 'MAIL_RECIPIENT';
	
	$queries = array(
	    'MAIL' => "CREATE TABLE " . $mail . " (" . PHP_EOL
		    . "name varchar(200) PRIMARY KEY, " . PHP_EOL
		    . "subject varchar(255), " . PHP_EOL
		    . "text_body varchar(10000), " . PHP_EOL
		    . "html_body varchar(10000), " . PHP_EOL
		    . "dateCreate datetime default CURRENT_TIMESTAMP, " . PHP_EOL
		    . "dateUpdate datetime ON UPDATE CURRENT_TIMESTAMP, " . PHP_EOL
		    . "all_sent int(1) not null default 0" . PHP_EOL
		    . ")",
	    'MAIL_RECIPIENT' => "CREATE TABLE " . $mail_recipient . " (" . PHP_EOL
			    . "mail varchar(200) REFERENCES " . $mail . "(name), " . PHP_EOL
			    . "name varchar(100), " . PHP_EOL
			    . "email varchar(254) not null, " . PHP_EOL
			    . "field varchar(3) default 'to', " . PHP_EOL
			    . "status varchar(20) not null, " . PHP_EOL
			    . "error varchar(500), " . PHP_EOL
			    . "date datetime, " . PHP_EOL
			    . "PRIMARY KEY (mail, email)"
			    . ")"
	);
	
	foreach($queries as $table => $query) {
	    if($drop) {
		$t = $this->prefix . $table;
		$this->db->query('DROP TABLE ' . $t);
	    }
	    
	    $this->db->query($query);
	    if(!empty($this->db->error)) {
		throw new \mysqli_sql_exception($this->db->errno . ': ' . $this->db->error . PHP_EOL . $query);
	    }
	}
    }
    
    /**
     * Set the prefix
     * @param string $prefix
     */
    public function setPrefix($prefix) {
	// add _ if not included
	if(substr($prefix, -1) != '_') {
	    $prefix = $prefix . '_';
	}
	
	$this->prefix = strtoupper($prefix);
    }
    
    /**
     * Get the prefix
     * @return string
     */
    public function getPrefix() {
	return $this->prefix;
    }
    
    /**
     * Store given mail in Database
     * @param \Emailing\Mail $mail
     * @throws \LengthException
     * @throws \mysqli_sql_exception
     */
    public function save(Mail $mail) {
	if(empty($mail->getName())) {
	    throw new \LengthException("Trying to save mail with empty Name");
	}
	
	$mail_name = $mail->getName();
	$subject = $mail->getSubject();
	$textBody = $mail->getTextBody();
	$htmlBody = $mail->getHtmlBody();
	$to = $mail->getTo();
	$cc = $mail->getCc();
	$bcc = $mail->getBcc();
	unset($mail);
	
	$mail_query = "insert into " . $this->prefix . "MAIL (name, subject, text_body, html_body)" . PHP_EOL
		    . "values (?, ?, ?, ?)" . PHP_EOL
		    . "on duplicate key update subject = values(subject), " . PHP_EOL
					    . "text_body = values(text_body), " . PHP_EOL
					    . "html_body = values(html_body)";
	
	$recipient_query = "insert into " . $this->prefix . "MAIL_RECIPIENT (mail, name, email, field, status, error, date)" . PHP_EOL
							    . "VALUES (?, ?, ?, ?, ?, ?, ?)" . PHP_EOL
							    . "ON DUPLICATE KEY UPDATE name = values(name), " . PHP_EOL
										    . "field = values(field), " . PHP_EOL
										    . "status = values(status), " . PHP_EOL
										    . "error = values(error), " . PHP_EOL
										    . "date = values(date)";
	
	// save mail
	$stm = $this->db->prepare($mail_query);
	if(false == $stm) {
	    throw new \mysqli_sql_exception($this->db->errno . ': ' . $this->db->error . PHP_EOL . $this->db->error);
	}
	
	$stm->bind_param('ssss', $mail_name, $subject, $textBody, $htmlBody);
	$stm->execute();
	$stm->free_result();
	$stm->close();
	
	// save recipient
	$stm = $this->db->prepare($recipient_query);
	if(false == $stm) {
	    throw new \mysqli_sql_exception($this->db->errno . ': ' . $this->db->error . PHP_EOL . $this->db->error);
	}
	
	$field = 'to';
	foreach($to as $r) {
	    $name = $r->getName();
	    $email = $r->getEmail();
	    $status = $r->getStatus();
	    $error = $r->getError();
	    $date = $r->getDate();
	    
	    $stm->bind_param('sssssss', $mail_name, $name, $email, $field, $status, $error, $date);
	    $stm->execute();
	}
	
	$field = 'cc';
	foreach($cc as $r) {
	    $name = $r->getName();
	    $email = $r->getEmail();
	    $status = $r->getStatus();
	    $error = $r->getError();
	    $date = $r->getDate();
	    
	    $stm->bind_param('sssssss', $mail_name, $name, $email, $field, $status, $error, $date);
	    $stm->execute();
	}
	
	$field = 'bcc';
	foreach($bcc as $r) {
	    $name = $r->getName();
	    $email = $r->getEmail();
	    $status = $r->getStatus();
	    $error = $r->getError();
	    $date = $r->getDate();
	    
	    $stm->bind_param('sssssss', $mail_name, $name, $email, $field, $status, $error, $date);
	    $stm->execute();
	}
	
	$stm->free_result();
	$stm->close();
    }
    
    /**
     * Load requested mail from database
     * @param string $name
     * @throws \LogicException
     */
    public function load($name) {
	if(!is_object($this->mailer)) {
	    throw new \LogicException(__METHOD__ . ' called before the Mailer was set');
	}
	
	$mail_query = "SELECT name, subject, text_body, html_body FROM " . $this->prefix . "MAIL WHERE name = ?";
	$recipients_query = "SELECT name, email, field, status, error, date FROM " . $this->prefix . "MAIL_RECIPIENT WHERE mail = ?";
	
	// load mail main content
	$stm = $this->db->prepare($mail_query);
	if(false == $stm) {
	    throw new \mysqli_sql_exception($this->db->errno . ': ' . $this->db->error . PHP_EOL . $this->db->error);
	}
	
	$stm->bind_param('s', $name);
	$stm->execute();
	
	$stm->bind_result($name, $subject, $textBody, $htmlBody);
	$stm->fetch();
	$mail = new Mail($this->mailer);
	$mail->setName($name);
	$mail->setSubject($subject);
	$mail->setTextBody($textBody);
	$mail->setHtmlBody($htmlBody);
	
	$stm->free_result();
	$stm->close();
	
	// load recipients
	$stm = $this->db->prepare($recipients_query);
	if(false == $stm) {
	    throw new \mysqli_sql_exception($this->db->errno . ': ' . $this->db->error . PHP_EOL . $this->db->error);
	}
	$stm->bind_param('s', $name);
	$stm->execute();
	
	$stm->bind_result($name, $email, $field, $status, $error, $date);
	while($stm->fetch()) {
	    $method = 'add' . $field;
	    $r = new MailRecipient($email, $name);
	    
	    if(!empty($error)) {
		$r->setError($error);
	    }
	    
	    if(MailRecipient::$SENT == $status) {
		$r->setSent();
	    }
	    
	    //$r->setDate($date);
	    
	    $mail->$method($r);
	}
    }
}
