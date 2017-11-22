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
use \ManageStagingEmailWPE\Settings;

class Redirect extends \PHPMailer implements CustomPHPMailer
{
    private $preferredAddress;

    private $sendToLog;

    public function __construct(SendToLog $sendToLog, Settings $settings)
    {
        $selection = $settings->getSelection();
        $this->preferredAddress = $settings->getPreferredAddress($selection);
        $this->sendToLog = $sendToLog;
    }

    /**
     * Replace PHPMailers send() method with one that sends only to preferred address.
     *
     * @return string Output of email or error
     */
    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }
            $this->to = array(
                array($this->preferredAddress),
            );
            $this->cc = $this->bcc = array();
            $this->sendToLog->sendByline('sent to ' . $this->preferredAddress);
            $this->postSend();
            return true;
        } catch (\phpmailerException $e) {
            return false;
        }
    }
}
