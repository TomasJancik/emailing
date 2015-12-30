<?php
/**
 * Interface for Emailing\Mail object
 * The object should store just the data about the message (Subject, Recipients, Body)
 */

namespace Emailing;

interface iMail {
    
    /**
     * Get the mail's name
     * @return string
     */
    public function getName();
    
    /**
     * Get the subject
     * @return string
     */
    public function getSubject();
    
    /**
     * Get the TO recipients
     * @return array
     */
    public function getTo();
    
    /**
     * Get the CC recipients
     * @return array
     */
    public function getCc();
    
    /**
     * Get the BCC recipients
     * @return array
     */
    public function getBcc();
    
    /**
     * Get the plain text body
     * @return string
     */
    public function getTextBody();
    
    /**
     * Get the HTML body
     * @return string
     */
    public function getHtmlBody();
    
    /**
     * Set the name of the mail - name is just for identification
     * @param string $name
     */
    public function setName($name);
    
    /**
     * Set the subject of the mail
     * @param string $subject
     */
    public function setSubject($subject);
    
    /**
     * Add recipients
     * @param string|array $to
     * @param bool $replace
     */
    public function addTo($to, $replace = false);
    
    /**
     * Add recipients in copy
     * @param string|array $cc
     * @param bool $replace
     */
    public function addCc($cc, $replace = false);
    
    /**
     * Add recipients in hidden copy
     * @param string|array $bcc
     * @param bool $replace
     */
    public function addBcc($bcc, $replace = false);
    
    /**
     * Set the plain text body of the mail
     * @param string $body
     */
    public function setTextBody($body);
    
    /**
     * Set the HTML body of the mail
     * @param string $htmlBody
     */
    public function setHtmlBody($htmlBody);
    
    /**
     * Send the mail
     * @param int $batch How many recipients to send at time. If set CC and BCC are ignored
     */
    public function send($batch = null);
}