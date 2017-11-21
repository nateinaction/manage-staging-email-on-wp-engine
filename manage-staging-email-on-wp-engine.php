<?php
/**
 * Plugin Name: Manage Staging Email on WP Engine
 * Plugin URI: http://wordpress.org/plugins/redirect-emails-on-staging/
 * Description: Redirect, log, or halt all emails on a WP Engine staging environment.
 * Version: 1.0
 * Author: Nate Gay
 * Author URI: https://nategay.me/
 * License: GPL3+
 *
 * This is a modern PHP take based on Jeremy Pry's original plugin,
 * Redirect Emails on Staging. I also found Sal Ferrarello's plugin,
 * Stop Emails, a helpful reference.
 *
 * https://github.com/PrysPlugins/WPE-redirect-emails-on-staging
 * https://github.com/salcode/stop-emails/
 *
 */

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class Factory
{
    private $settings;
    private $admin;
    private $replacePhpMailer;
    private $redirectEmail;
    private $logEmail;
    private $haltEmail;

    public function __construct()
    {
        $this->settings = new Settings;
        $this->logEmail = new EmailJob\Log;
        $this->haltEmail = new EmailJob\Halt;
        //$this->admin = new Admin($this->settings);
        //$this->redirectEmail = new EmailJob\Redirect($this->settings);
        //$this->main = new Main($settings, $admin, $redirectEmail);

        $selection = $this->settings->getSelection();
        $isLogOrHalt = $this->isLogOrHalt($selection, $this->logEmail, $this->haltEmail);
        if ($isLogOrHalt) {
            $replacePhpMailer = new ReplacePHPMailer($isLogOrHalt);
            $this->hookReplacePhpMailer($replacePhpMailer);
        } else {
            $redirectEmail = new EmailJob\Redirect($this->preferred_address);
            $this->hookRedirectEmail($redirectEmail);
        }
    }

    /**
     * Should we log, or halt email?
     *
     * @param PHPMailer $logEmail
     * @param PHPMailer $haltEmail
     * @return PHPMailer|null If selection is to log or halt then will return PHPMailer else null
     */
    public function isLogOrHalt($selection, CustomPHPMailer $logEmail, CustomPHPMailer $haltEmail)
    {
        switch ($selection) {
            case 'halt':
                return $haltEmail;
            case 'log':
                return $logEmail;
        }
    }

    /**
     * Hook into wp_mail to change where email is sent
     *
     * @uses add_filter() Using add_filter to modify wp_mail function
     * @return null
     */
    public function hookRedirectEmail(EmailJob\Redirect $redirectEmail)
    {
        \add_filter('wp_mail', array($redirectEmail, 'sendToAddress'), 1000, 1);
    }

    /**
     * Replace PHPMailer with our own class which allows us capture email attempts
     *
     * @uses add_action() To inject into load order
     * @return null
     */
    public function hookReplacePhpMailer(ReplacePHPMailer $replacePHPMailer)
    {
        \add_action('plugins_loaded', array($replacePHPMailer, 'doReplace'));
    }
}

/**
 * Only run on WP Engine's staging environment
 *
 * @uses is_wpe_snapshot() Checks to determine if on WPE staging.
 */
if (function_exists('is_wpe_snapshot') && \is_wpe_snapshot()) {
    new Factory;
}
