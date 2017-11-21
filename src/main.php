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
     * @var Admin
     */
    private $admin;

    /**
     * @var RedirectEmail
     */
    private $redirectEmail;

    /**
     * Constructor
     *
     * @param Settings $settings
     * @param Admin $admin
     * @param RedirectEmail $redirectEmail
     */
    public function __construct(Settings $settings, Admin $admin, RedirectEmail $redirectEmail)
    {
        $this->settings = $settings;
        $this->admin = $admin;
        $this->redirectEmail = $redirectEmail;
    }

    /**
     * Run plugin only on staging
     *
     * @param bool $isStaging Output of checkStaging()
     * @return bool|null True on staging
     */
    public function runOnStaging($isStaging)
    {
        if ($isStaging) {
            $this->manageEmailBehavior();
            $this->manageAddMenuItem();
            return true;
        }
    }

    public function checkStaging()
    {
        return (function_exists('is_wpe_snapshot') && \is_wpe_snapshot());
    }

    /**
     * Hook into WP to create menu and redirect mail
     *
     * @return string Returns 'redirect' if selection is 'admin' or 'custom',
     *                else will return 'replace'
     */
    public function manageEmailBehavior($selection)
    {
        if ('admin' === $selection || 'custom' === $selection) {
            return 'redirect';
        }
        return 'replace';
    }

    /**
     * Hook into wp_mail to change where email is sent
     *
     * @uses add_filter() Using add_filter to modify wp_mail function
     * @return null
     */
    public function wpHookToRedirectEmail()
    {
        \add_filter('wp_mail', array($this->redirectEmail, 'sendToAddress'), 1000, 1);
    }

    /**
     * Replace PHPMailer with our own class which allows us capture email attempts
     *
     * @uses add_action() To inject into load order
     * @return null
     */
    public function wpHookToReplacePhpMailer()
    {
        \add_action('plugins_loaded', array($this->redirectEmail, 'replacePhpMailer'));
    }

    /**
     * Add plugin menu only if admin
     *
     * @return bool|null True if admin
     */
    public function manageAddMenuItem()
    {
        if ($this->checkAdmin()) {
            $this->wpHookToAddMenuItem();
            return true;
        }
    }

    /**
     * Add menu item to WordPress Dashboard
     *
     * @uses add_action() To add menu WordPress item
     * @return null
     */
    public function wpHookToAddMenuItem()
    {
        \add_action('admin_menu', array($this->admin, 'adminMenuItem'));
    }

    /**
     * Check to see if user is admin
     *
     * @uses is_admin()
     * @return bool|null True if admin
     */
    public function checkAdmin()
    {
        return (function_exists('is_admin') && \is_admin());
    }
}
