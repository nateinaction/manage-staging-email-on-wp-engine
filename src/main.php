<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Main extends Settings
{
    /**
     * Static method to initialize class
     *
     * @return null
     */
    public static function run()
    {
        if (!($main instanceof Main)) {
            $main = new Main;
        }
        $main->init();
    }

    /**
     * Initialize plugin only on staging
     *
     * @return bool|null True on staging
     */
    public function init()
    {
        if ($this->checkStaging()) {
            $this->manageEmailBehavior();
            $this->manageAddMenuItem();
            return true;
        }
    }

    /**
     * Check to see if we're on WP Engine's staging environment
     *
     * @uses is_wpe_snapshot() Checks to determine if on WPE staging.
     * @return bool True if on WPE staging or null
     */
    public function checkStaging()
    {
        if (function_exists('is_wpe_snapshot') && \is_wpe_snapshot()) {
            return true;
        }
    }

    /**
     * Hook into WP to create menu and redirect mail
     *
     * @return string Returns 'redirect' if selection is 'admin' or 'custom',
     *                else will return 'replace'
     */
    public function manageEmailBehavior()
    {
        $selection = $this->getSelection();
        if ('admin' === $selection || 'custom' === $selection) {
            $this->wpHookToRedirectEmail();
            return 'redirect';
        }
        $this->wpHookToReplacePhpMailer();
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
        $redirectEmail = $this->redirectEmail();
        \add_filter('wp_mail', array($redirectEmail, 'sendToAddress'), 1000, 1);
    }

    /**
     * Replace PHPMailer with our own class which allows us capture email attempts
     *
     * @uses add_action() To inject into load order
     * @return null
     */
    public function wpHookToReplacePhpMailer()
    {
        $redirectEmail = $this->redirectEmail();
        \add_action('plugins_loaded', array($redirectEmail, 'replacePhpMailer'));
    }

    /**
     * Add plugin menu only if admin
     *
     * @return bool|null True if admin
     */
    public function manageAddMenuItem() {
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
        $admin = $this->admin();
        \add_action('admin_menu', array($admin, 'adminMenuItem'));
    }

    /**
     * Check to see if user is admin
     *
     * @uses is_admin()
     * @return bool|null True if admin
     */
    public function checkAdmin()
    {
        if (function_exists('is_admin') && \is_admin()) {
            return true;
        }
    }

    /**
     * Helper function to initialize Admin class
     *
     * @return Admin
     */
    public function admin()
    {
        return new Admin;
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
