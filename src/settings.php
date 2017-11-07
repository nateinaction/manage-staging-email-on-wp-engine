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
	public $option_name = 'manage_staging_email_wpe';

	/**
	 * Sets plugin options in WordPress' database
	 *
	 * @uses update_option() Sets plugin settings in db
	 * @return null
	 */
	public function set_plugin_options($options_array)
	{
		\update_option($this->option_name, $options_array);
	}

	/**
	 * Gets plugin options from WordPress' database
	 *
	 * @uses get_site_option() Grabs plugin settings from db
	 * @return array|bool Will be false if no settings exist yet, else array with settings 
	 */
	public function get_plugin_options()
	{
		return \get_site_option($this->option_name);
	}

	/**
	 * Gets admin email from WordPress' database
	 *
	 * @uses get_site_option() Grabs preferred email address from db
	 * @return string Value of admin email
	 */
	public function get_admin_email()
	{
		return \get_site_option('admin_email');
	}
}