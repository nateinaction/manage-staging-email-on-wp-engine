<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * The Settings class handles getting and storing plugin settings.
 *
 */
class Settings
{
	public $option_name = 'manage_staging_email_wpe';

	/**
	 * Sanitize options before setting in DB
	 *
	 * @return array Status will be a bool, true for success
	 */
	public function set_plugin_options($options_array)
	{
		$current_options = $this->get_plugin_options();

		$admin = new Admin;
		$selected = $options_array[$admin->selection_name];
		$email_address = $options_array[$admin->custom_address];
			
		if ('custom' === $selected) {
			if(!$this->check_for_valid_email($email_address)) {
				return array(
					'status' => false,
					'message' => 'Please enter a valid email.',
				);
			}
		} else {
			$options_array[$admin->custom_address] = $current_options[$admin->custom_address];
		}
		
		$this->set_options_in_db($options_array);
		return array(
			'status' => true,
			'message' => 'Saved email preference.',
		);
	}

	/**
	 * Checks if email address is valid
	 *
	 * @uses is_email() WordPress function to check if email address is valid
	 * @return bool True if valid, else null
	 */
	public function check_for_valid_email($email_address)
	{
		if (\is_email($email_address)) {
      		return true;
		}
	}

	/**
	 * Sets plugin options in WordPress' database
	 *
	 * @uses update_option() Sets plugin settings in db
	 * @return null
	 */
	public function set_options_in_db($options_array)
	{
		\update_option($this->option_name, $options_array);
	}

	/**
	 * Provides a valid options array even if options have not yet been set
	 *
	 * @return array Array with settings 
	 */
	public function get_plugin_options() {
		$options = $this->get_options_from_db();
		if (!$options) {
			$admin = new Admin;
			$options = array();
			$options[$admin->selection_name] = 'admin';
			$options[$admin->custom_address] = '';
		}
		return $options;
	}

	/**
	 * Gets plugin options from WordPress' database
	 *
	 * @uses get_site_option() Grabs plugin settings from db
	 * @return array|bool Will be false if no settings exist yet, else array with settings 
	 */
	public function get_options_from_db()
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