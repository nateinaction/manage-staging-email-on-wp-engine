<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access to this file
if (!defined('ABSPATH')) {
	die('You can\'t do anything by accessing this file directly.');
}

class Admin
{
	// Name of expected POST values
	public $post_name = 'manage_staging_email_wpe_settings';
	public $selection_name = 'email_preference';

	public function admin_menu_item()
	{
		\add_menu_page(
			'Manage Staging Emails',
			'Manage Staging Emails',
			'administrator',
			'manage-staging-emails-wpe',
			array($this, 'render_admin_page'),
			'dashicons-email-alt',
			80
		);
	}

	public function render_admin_page()
	{
		$post = $this->check_for_post_on_admin();
		if ($post) {
			$this->set_email_options($post);
			echo $this->post_success_html();
		}

		$email_options = $this->get_email_options();
		echo $this->admin_page_html($email_options);
	}

	public function get_email_options()
	{
		$settings = new Settings();
		$options = $settings->get_plugin_options();
		$admin_email = $settings->get_admin_email();

		if (!$options) {
			$options = array();
			$options[$this->selection_name] = 'admin';
			$options['custom_address'] = '';
		}
		$options['admin_email'] = $admin_email;
		return $options;
	}

	/**
	 * Send options array to db
	 *
	 * @todo REALLY NEED TO VALIDATE AND SANITIZE
	 * @return null
	 */
	public function set_email_options($options_array)
	{
		$settings = new Settings();
		$settings->set_plugin_options($options_array);
	}

	/**
	 * Display the admin page
	 *
	 * @todo check for email validity
	 * @return string HTML page to render
	 */
	public function admin_page_html($email_options)
	{
        $html = '';
        $html .= '<br>';
        $html .= '<form  method="post">';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="admin" ' . $this->is_checked('admin', $email_options) . '> WordPress Admin Email (' . $email_options['admin_email'] . ')<br/>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="custom" ' . $this->is_checked('custom', $email_options) . ' onclick="document.getElementById(\'custom_address\').focus()"> ';
        $html .= '<input type="text" id="custom_address" name="' . $this->post_name . '[custom_address]" placeholder="custom@email.com" value="' . $email_options['custom_address'] . '"><br/>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="log" ' . $this->is_checked('log', $email_options) . '> Send emails to PHP error log<br/>';
        $html .= '<input type="radio" name="' . $this->post_name . '[' . $this->selection_name . ']" value="none" ' . $this->is_checked('none', $email_options) . '> Halt all emails<br/>';
        $html .= '<input type="submit" value="Save">';
        $html .= '</form>';
        return $html;
	}

	public function is_checked($value, $email_options)
	{
		if ($value === $email_options['email_preference']) {
			return 'checked';
		}
		return '';
	}

	/**
	 * Display success message on admin page if a valid POST request was received
	 *
	 * @return string HTML output of success message
	 */
	public function post_success_html()
	{
        return '<br><p style="color:green;font-weight:800;">Saved email preference.</p>';
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
}