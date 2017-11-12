<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * This class handles redirecting emails when on the WP Engine Staging site.
 *
 * It is usually undesirable for the staging site to send out emails to anyone besides
 * the site administrator. This class hooks into the wp_mail() function in WordPress.
 *
 */
class RedirectEmail
{
    /**
     * Redirect email to preferred address and remove CC and BCC headers
     *
     * @param array $mail_args Array of settings for sending the message.
     * @return array The args to use for the mail message
     */
    public function sendToAddress($mail_args)
    {
        $settings = new Settings;
        $mail_args['to'] = $settings->getPreferredAddress();
        $mail_args['headers'] = array();
        return $mail_args;
    }

    /**
     * Replace the global $phpmailer with fake phpmailer.
     *
     * @return CustomPHPMailer instance, the object that replaced the global $phpmailer
     *
     */
    public function replacePhpmailer()
    {
        global $phpmailer;
        return $this->replaceWithCustomPhpmailer($phpmailer);
    }

    /**
     * Replace the parameter object with an instance of CustomPHPMailer.
     *
     * @param PHPMailer $obj WordPress PHPMailer object.
     * @return CustomPHPMailer $obj
     */
    public function replaceWithCustomPhpmailer(&$obj = null)
    {
        $obj = new CustomPHPMailer;
        return $obj;
    }

    /**
     * Convert mock email to text.
     *
     * @param $mock_email array Represents the email that was stopped.
     * @return string Text version of email
     */
    public function mockEmailToText($mock_email)
    {
        return \print_r($mock_email, true);
    }

    /**
     * Hooked function for email logging.
     *
     * Checks if email should be logged and logs it if necessary
     *
     * @param array $mock_email represents the email that was stopped.
     */
    public function sendToErrorLog($mock_email)
    {
        $text = $this->mockEmailToText($mock_email);
        \error_log($text);
    }
}
