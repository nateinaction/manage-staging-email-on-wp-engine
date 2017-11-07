<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access to this file
if (!defined('ABSPATH')) {
	die('You can\'t do anything by accessing this file directly.');
}

class Admin
{
	/**
	 * Constructor.
	 *
	 * Hook the methods of this class to the appropriate hooks in WordPress
	 */
	public function __construct()
	{
		if (Main::check_staging()) {
			add_menu_page(
				'Manage Staging Emails',
				'Manage Staging Emails',
				'administrator',
				'manage-staging-emails-wpe',
				array($this,'display_admin_page'),
				'dashicons-email-alt',
				80
			);
		}
	}

	// Name of expected POST values
	public $post_name = 'manage_staging_email_wpe_settings';
	public $selection_name = 'email_preference';

	/**
	 * Display the admin page
	 *
	 * @todo check for email validity
	 * @return string HTML page to render
	 */
	public function admin_page_html()
	{
        $html = '';
        $html .= '<br>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="admin" checked="true"> WordPress Admin Email (blah@blah.com)<br/>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="custom" onclick="document.getElementById(\'custom_address\').focus()"> ';
        $html .= '<input type="text" id="custom_address" name="' . $this->post_name . '[custom_address]" placeholder="custom@email.com"><br/>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="log"> Send emails to PHP error log<br/>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="none"> Halt all emails<br/>';
        $html .= '<input type="submit" value="Save">';
        $html .= $this->post_success_html();
        $html .= '</form>';
        return $html;
	}

	/**
	 * Display success message on admin page if a valid POST request was received
	 *
	 * @return string HTML output of success message
	 */
	public function post_success_html()
	{
		if($this->check_for_post_on_admin()) {
            return '<br><p style="color:green;font-weight:800;">Saved email preference.</p>';
		}
		return '';
	}

	/**
	 * Check to see if a POST request was made, if so, set it in db
	 *
	 * @return bool True if valid POST received
	 */
	public function check_for_post_on_admin()
	{
		if (isset($_POST[$this->post_name])) {
			return $_POST[$this->post_name];
		}
	}

	/**
	 * Check to see if a POST request was made, if so, set it in db
	 *
	 * @todo REALLY NEED TO VALIDATE AND SANITIZE
	 * @return bool True if valid POST received
	 */
	public function send_to_db()
	{
		if ($this->check_for_post_on_admin()) {
			$this->set_preferred_address($_POST[$this->post_name]);
		}
	}
}