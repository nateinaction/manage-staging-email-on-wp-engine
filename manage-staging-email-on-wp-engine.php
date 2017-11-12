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

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

nategay\manage_staging_email_wpe\Main::run();
