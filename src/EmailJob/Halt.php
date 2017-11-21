<?php

namespace ManageStagingEmailWPE\EmailJob;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load PHPMailer class, so we can subclass it.
require_once ABSPATH . WPINC . '/class-phpmailer.php';

use \ManageStagingEmailWPE\CustomPHPMailer;

class Halt extends \PHPMailer implements CustomPHPMailer
{
    /**
     * Replace PHPMailers send() method with one that does not send.
     *
     * @return bool
     */
    public function send()
    {
        return true;
    }
}