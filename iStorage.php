<?php

namespace Emailing;

interface iStorage {
    /**
     * Store the given mail
     * @param \Emailing\Mail $mail
     */
    public function save(Mail $mail);
    
    /**
     * Load mails by name
     * @param string $name
     */
    public function load($name);
}