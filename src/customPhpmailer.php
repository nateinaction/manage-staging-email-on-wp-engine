<?php

namespace NateGay\ManageStagingEmailWPE;

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
        // Get options_array and named params
        $redirectEmail = $this->redirectEmail();
        $selection = $redirectEmail->getSelection();

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
                $mock_email_text = \print_r($mock_email, true);
                $redirectEmail->sendToErrorLog($mock_email_text);
                $redirectEmail->logWhenEmailManaged('sent to PHP error log');
                return true;
            } catch (\phpmailerException $e) {
                return false;
            }
        } else {
            $redirectEmail->logWhenEmailManaged('halted');
            return true;
        }
    }

    /**
     * Helper function to initialize RedirectEmail class
     *
     * @return RedirectEmail
     */
    public function redirectEmail()
    {
        return new RedirectEmail;
    }
}
