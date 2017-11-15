<?php

namespace NateGay\ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class RedirectEmail extends Settings
{
    /**
     * Redirect email to preferred address and remove CC and BCC headers
     *
     * @param array $mail_args Array of settings for sending the message.
     * @return array The args to use for the mail message
     */
    public function sendToAddress($mail_args)
    {
        $preferredAddress = $this->getPreferredAddress();
        $mail_args['to'] = $preferredAddress;
        $mail_args['headers'] = array();
        $this->logWhenEmailManaged('redirected to ' . $preferredAddress);
        return $mail_args;
    }

    /**
     * A standard message to the error log when emails are managed
     *
     * A love letter to my fellow technicians.
     *
     * @param string Past tense statement to indicate what happened with the email
     */
    public function logWhenEmailManaged($past_tense_action)
    {
        $message = 'Email ' . $past_tense_action . ' by the ' . $this->plugin_title . ' plugin.';
        $this->sendToErrorLog($message);
        return $message;
    }

    /**
     * Sends message to error log
     *
     * @param string Message to send to log
     */
    public function sendToErrorLog($message)
    {
        \error_log($message);
    }

    /**
     * Replace the global $phpmailer with fake phpmailer.
     *
     * @return CustomPHPMailer instance, the object that replaced the global $phpmailer
     *
     */
    public function replacePhpMailer()
    {
        global $phpmailer;
        return $this->replaceWithCustomPhpMailer($phpmailer);
    }

    /**
     * Replace the parameter object with an instance of CustomPHPMailer.
     *
     * @param PHPMailer $obj WordPress PHPMailer object.
     * @return CustomPHPMailer $obj
     */
    public function replaceWithCustomPhpMailer(&$obj = null)
    {
        $obj = new CustomPHPMailer;
        return $obj;
    }
}
