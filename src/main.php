<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Main
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
            $admin = new Admin;
            $settings = new Settings;
            $redirectEmail = new RedirectEmail;

            $options_array = $settings->getPluginOptions();
            $selection = $options_array[$admin->selection_name];
        }
        $main->init($selection, $redirectEmail, $admin);
    }

    /**
     * Initialize plugin only on staging
     *
     * @return null
     */
    public function init($selection, $redirectEmail, $admin)
    {
        if ($this->checkStaging()) {
            $this->addHooks($selection, $redirectEmail, $admin);
            return true;
        }
    }

    /**
     * Hook into WP to create menu and redirect mail
     *
     * @return null
     */
    public function addHooks($selection, $redirectEmail, $admin)
    {
        if ('admin' === $selection || 'custom' === $selection) {
            \add_filter('wp_mail', array($redirectEmail, 'sendToAddress'), 1000, 1);
        } else {
            \add_action('plugins_loaded', array($redirectEmail, 'replacePhpmailer'));
        }

        \add_action('admin_menu', array($admin, 'adminMenuItem'));
    }

    /**
     * Check to see if we're on WP Engine's staging environment
     *
     * @uses is_wpe_snapshot() Checks to determine if on WPE staging.
     * @return bool True if on WPE staging or null
     */
    public function checkStaging()
    {
        if (function_exists('is_wpe_snapshot') && is_wpe_snapshot()) {
            return true;
        }
    }
}
