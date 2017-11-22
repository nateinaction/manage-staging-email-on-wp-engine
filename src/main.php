<?php

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Main
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var SendToLog
     */
    private $sendToLog;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settings = new Settings;
        $this->sendToLog = new SendToLog;

        $customPHPMailer = $this->getCustomPHPMailer();
        $replacePhpMailer = new ReplacePHPMailer($customPHPMailer);
        $this->hookReplacePhpMailer($replacePhpMailer);
    }

    /**
     * Should we log, halt, or redirect email? Redirecting is default.
     *
     * @return PHPMailer
     */
    public function getCustomPHPMailer()
    {
        $selection = $this->settings->getSelection();
        switch ($selection) {
            case 'halt':
                return new CustomPHPMailer\Halt($this->sendToLog);
            case 'log':
                return new CustomPHPMailer\Log($this->sendToLog);
            default:
                return new CustomPHPMailer\Redirect($this->sendToLog, $this->settings);
        }
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

    /**
     * Adds Manage Staging Emails menu item to dashboard
     *
     * @uses add_menu_page() To add menu item to dashboard
     * @return null
     */
    public function hookAdminMenu(Admin $admin)
    {
        \add_menu_page(
            'Manage Staging Emails',
            'Manage Staging Emails',
            'administrator',
            'manage-staging-emails-wpe',
            array($admin, 'renderAdminPage'),
            'dashicons-email-alt',
            80
        );
    }
}
