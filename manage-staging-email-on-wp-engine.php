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

/**
 * Only run on WP Engine's staging environment
 *
 * @uses is_wpe_snapshot() Checks to determine if on WPE staging.
 */
if (function_exists('is_wpe_snapshot') && \is_wpe_snapshot()) {
    new Main;
}
