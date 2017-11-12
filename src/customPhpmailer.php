<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load PHPMailer class, so we can subclass it.
require_once ABSPATH . WPINC . '/class-phpmailer.php';

class CustomPHPMailer extends \PHPMailer
{
    /**
     * Replacement send() method that does not send.
     *
     * Unlike the PHPMailer send method,
     * this method never calls the method postSend(),
     * which is where the email is actually sent
     *
     * @return bool
     */
    public function send()
    {
        // Get options_array
        $settings = new Settings;
        $options_array = $settings->getPluginOptions();

        // Get named parameters
        $admin = new Admin;
        $selection = $options_array[$admin->selection_name];

        // Get RedirectEmail class
        $redirectEmail = new RedirectEmail;

        if ('log' === $selection) {
            try {
                if (!$this->preSend()) {
                    return false;
                }
                $mock_email = array(
                    'to'     => $this->to,
                    'cc'     => $this->cc,
                    'bcc'    => $this->bcc,
                    'header' => $this->MIMEHeader,
                    'body'   => $this->MIMEBody,
                );
                // send to error log
                $redirectEmail->sendToErrorLog($mock_email);
                return true;
            } catch (\phpmailerException $e) {
                return false;
            }
        } else {
            return true;
        }
    }
}
