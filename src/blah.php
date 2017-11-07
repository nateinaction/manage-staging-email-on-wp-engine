<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access to this file
if (!defined('ABSPATH')) {
	die('You can\'t do anything by accessing this file directly.');
}


/**
 * This class handles redirecting emails when on the WP Engine Staging site.
 *
 * It is usually undesirable for the staging site to send out emails to anyone besides
 * the site administrator. This class hooks into the wp_mail() function in WordPress.
 *
 */
class Main
{
	/**
	 * Constructor.
	 *
	 * Hook the methods of this class to the appropriate hooks in WordPress
	 */
	public function __construct()
	{
		// We're using a high priority to give other plugins room to also modify this filter.
		add_filter('wp_mail', array($this, 'redirect_email'), 1000, 1);
	}

	/**
	 * Check to see if we're on WP Engine's staging environment
	 *
	 * @uses is_wpe_snapshot() Checks to determine if on WPE staging.
	 * @return bool True if on WPE staging or null
	 */
	public function check_staging()
	{
		if (function_exists('is_wpe_snapshot') && is_wpe_snapshot()) {
			return true;
		}
	}

	/**
	 * Redirect email to preferred address and remove CC and BCC headers
	 *
	 * @param array $mail_args Array of settings for sending the message.
	 * @return array The args to use for the mail message
	 */
	public function redirect_email($mail_args)
	{
		if ($this->check_staging()) {
			$mail_args['to'] = $this->get_preferred_address();
			$mail_args['headers'] = array();
		}
		return $mail_args;
	}	
}