<?php

namespace ManageStagingEmailWPE\CustomPHPMailer;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load PHPMailer class, so we can subclass it.
require_once ABSPATH . WPINC . '/class-phpmailer.php';

use \ManageStagingEmailWPE\CustomPHPMailer;
use \ManageStagingEmailWPE\SendToLog;

class Log extends \PHPMailer implements CustomPHPMailer
{
    private $sendToLog;

    public function __construct(SendToLog $sendToLog)
    {
        $this->sendToLog = $sendToLog;
    }

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
            $message = \print_r($email, true);
            $this->sendToLog->send($message);
            $this->sendToLog->sendByline('sent to error log');
            return true;
        } catch (\phpmailerException $e) {
            return false;
        }
    }
}
