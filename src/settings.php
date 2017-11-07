<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access to this file
if (!defined('ABSPATH')) {
	die('You can\'t do anything by accessing this file directly.');
}

/**
 * The Settings class handles getting and storing plugin settings.
 *
 */
class Settings
{
	/**
	 * Set new preferred email address
	 *
	 * @uses update_option() Set new preferred email address
	 * @return null
	 */
	public function set_preferred_address($preferred_address)
	{
		update_option('wpe_staging_email', $preferred_address);
	}

	/**
	 * Get preferred email addresss
	 *
	 * @uses get_site_option() Grabs preferred email address from db
	 * @return string Value of preferred address
	 */
	public function get_preferred_address()
	{
		return get_site_option('wpe_staging_email') || get_site_option('admin_email');
	}
}