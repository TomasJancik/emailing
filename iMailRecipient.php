<?php

namespace Emailing;

interface iMailRecipient {
    public function __construct($email, $name = null);
    
    /**
     * Set the name of recipient
     * @param string $name
     */
    public function setName($name);
    
    /**
     * Set the mail address
     * @param string $email
     */
    public function setEmail($email);
    
    /**
     * Mark this recipient as already sent
     */
    public function setSent();
    
    /**
     * Set the error message and status
     * @param string $err
     */
    public function setError($err);
    
    /**
     * Get the recipient mail
     */
    public function getName();
    
    /**
     * Get the email address
     */
    public function getEmail();
    
    /**
     * Get current status
     */
    public function getStatus();
    
    /**
     * Get error message
     */
    public function getError();
    
    /**
     * Get date when message was sent to recipient
     */
    public function getDate();
    
    public function isSent();
}