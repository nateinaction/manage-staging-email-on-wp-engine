<?php

namespace ManageStagingEmailWPE\EmailJob;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Redirect
{
    private $preferred_address;

    public function __construct($preferred_address)
    {
        $this->preferred_address = $preferred_address;
    }

    /**
     * Redirect email to preferred address and remove CC and BCC headers
     *
     * @param array $mail_args Array of settings for sending the message.
     * @return array The args to use for the mail message
     */
    public function sendToAddress($mail_args)
    {
        $mail_args['to'] = $this->preferredAddress;
        $mail_args['headers'] = array();
        //$this->logWhenEmailManaged('redirected to ' . $this->preferredAddress);
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
}
