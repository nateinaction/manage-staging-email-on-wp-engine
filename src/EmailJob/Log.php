<?php

namespace ManageStagingEmailWPE\EmailJob;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load PHPMailer class, so we can subclass it.
require_once ABSPATH . WPINC . '/class-phpmailer.php';

use \ManageStagingEmailWPE\CustomPHPMailer;

class Log extends \PHPMailer implements CustomPHPMailer
{
    /**
     * Replace PHPMailers send() method with one that sends to the error log.
     *
     * @return string Output of email or error
     */
    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }
            $email = array(
                'to'     => $this->to,
                'cc'     => $this->cc,
                'bcc'    => $this->bcc,
                'header' => $this->MIMEHeader,
                'body'   => $this->MIMEBody,
            );
            $poke = \print_r($email, true);
            \error_log($poke);
        } catch (\phpmailerException $e) {
            return $e;
        }
    }
}