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
 * This is a derivative work based on Jeremy Pry's original plugin,
 * Redirect Emails on Staging. The method which emails are redirected
 * is based on Sal Ferrarello's plugin, Stop Emails.
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
    public function __construct()
    {
        $settings = new Settings;
        $admin = new Admin;
        $redirectEmail = new RedirectEmail;
        $main = new Main($settings, $admin, $redirectEmail);

        $this->initialize($main);
    }

    public function initialize(Main $main)
    {
        $isStaging = $main->checkStaging();
        $main->runOnStaging($isStaging);
    }
}

new Factory;
