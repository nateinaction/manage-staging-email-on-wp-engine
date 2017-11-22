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

class Halt extends \PHPMailer implements CustomPHPMailer
{
    private $sendToLog;

    public function __construct(SendToLog $sendToLog)
    {
        $this->sendToLog = $sendToLog;
    }

    /**
     * Replace PHPMailers send() method with one that does not send.
     *
     * @return bool
     */
    public function send()
    {
        $this->sendToLog->sendByline('halted');
        return true;
    }
}
