<?php
namespace worldpeaceio\manageStagingEmail;

/**
 * Plugin Name: Manage Staging Email on WP Engine
 * Plugin URI: http://wordpress.org/plugins/redirect-emails-on-staging/
 * Description: A useful plugin that allows you to redirect or halt all emails on a WP Engine staging environment. Make sure your staging site doesn't send out confusing or incorrect emails to your users.
 * Version: 2.0
 * Author: worldpeace.io
 * Author URI: https://worldpeace.io/
 * License: GPL3+
 *
 * Another take on Jeremy Pry's https://github.com/PrysPlugins/WPE-redirect-emails-on-staging
 *
 */

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
if (!class_exists('Redirect_Staging_Email_WPE', false)) :
class Redirect_Staging_Email_WPE
{
	/**
	 * Constructor.
	 *
	 * Hook the methods of this class to the appropriate hooks in WordPress
	 */
	protected function __construct()
	{
		// We're using a high priority to give other plugins room to also modify this filter.
		add_filter('wp_mail', array($this, 'redirect_email'), 1000, 1);
	}

	// Name of valid POST request
	public $post_name = 'preferred_address';

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
	 * Check to see if a POST request was made, if so, set it in db
	 *
	 * @return bool True if valid POST received
	 */
	public function check_for_post_on_admin()
	{
		if (isset($_POST[$this->post_name])) {
			$this->set_preferred_address($_POST[$this->post_name]);
			return true;
		}
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

	/**
	 * Display success message if a valid POST request was received
	 *
	 * @return string HTML output of success message
	 */
	public function display_post_success()
	{
		if($check_for_post_on_admin()) {
            return '<br><p style="color:green;font-weight:800;">Saved preferred email.</p>';
		}
		return '';
	}

	/**
	 * Display the admin page
	 *
	 * @uses get_site_option() Checks to determine if on WPE staging.
	 * @return null
	 */
	public function display_admin_page()
	{
        $html = '';
        $html .= '<br>';
        $html .= '<form method="post">';
        $html .= '<input type="text" name="' . $this->post_name . '" value="' . $this->get_preferred_address() .'">';
        $html .= '<input type="submit" value="Save">';
        $html .= $this->display_post_success();
        $html .= '</form>';
        echo $html;
	}
	
}
endif; // end of if ( class_exists() ) statement