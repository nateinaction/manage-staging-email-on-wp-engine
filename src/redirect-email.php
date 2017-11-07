<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * This class handles redirecting emails when on the WP Engine Staging site.
 *
 * It is usually undesirable for the staging site to send out emails to anyone besides
 * the site administrator. This class hooks into the wp_mail() function in WordPress.
 *
 */
class Redirect_Email
{
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