<?php

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SendToLog
{
    private $pluginName = 'Manage Staging Email on WP Engine';
    
    /**
     * A standard message to the error log when emails are managed
     *
     * A love letter to my fellow technicians.
     *
     * @param string Past tense statement to indicate what happened with the email
     */
    public function sendByline($action)
    {
        \error_log('Email ' . $action . ' by the ' . $this->pluginName . ' plugin.');
    }

    /**
     * Sends message to error log
     *
     * @param string Message to send to log
     */
    public function send($message)
    {
        \error_log($message);
    }
}
