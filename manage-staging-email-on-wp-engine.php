<?php
/**
 * Plugin Name: Manage Staging Email on WP Engine
 * Plugin URI: http://wordpress.org/plugins/redirect-emails-on-staging/
 * Description: A useful WordPress plugin that allows you to redirect, log or halt all emails on a WP Engine staging environment.
 * Version: 1.0
 * Author: Nate Gay
 * Author URI: https://nategay.me/
 * License: GPL3+
 *
 * Made possible by:
 * Jeremy Pry https://github.com/PrysPlugins/WPE-redirect-emails-on-staging
 * Sal Ferrarello https://github.com/salcode/stop-emails/
 *
 */

require_once("vendor/autoload.php");

use nategay\manage_staging_email_wpe;

//new Main;
new Admin;
new Settings;