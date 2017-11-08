<?php

namespace nategay\manage_staging_email_wpe;

// Prevent direct access
if (!defined('ABSPATH')) exit;

class Admin
{
	// Name of expected POST values
	public $post_name = 'manage_staging_email_wpe_settings';
	public $selection_name = 'email_preference';
	public $custom_address = 'custom_address';

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
			$valid_post = $this->check_for_valid_post($post);
			if ($valid_post['success']) {
				$this->set_email_options($post);
			}
			$post_success_html = $valid_post['html'];
		}

		$email_options = $this->get_email_options();
		$form = $this->admin_page_html($email_options);
		
		echo $form . $post_success_html;
	}

	/**
	 * Display success message on admin page if a valid POST request was received
	 *
	 * @return string HTML output of success message
	 */
	public function check_for_valid_post($post)
	{
		if ('custom' === $post[$this->selection_name]) {
			$email_address = $post[$this->custom_address];
			if(!$this->check_for_valid_email($email_address)) {
				return array(
					'success' => false,
					'html' => '<p style="color:red;font-weight:800;">Please enter a valid email.</p>',
				);
			}
		}
		return array(
			'success' => true,
			'html' => '<p style="color:green;font-weight:800;">Saved email preference.</p>',
		);
	}

	public function check_for_valid_email($email_address)
	{
		if (is_email($email_address)) {
      		return true;
		}
	}

	public function get_email_options()
	{
		$settings = new Settings();
		$options = $settings->get_plugin_options();
		$admin_email = $settings->get_admin_email();

		if (!$options) {
			$options = array();
			$options[$this->selection_name] = 'admin';
			$options[$this->custom_address] = '';
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
        $html .= '<h2>Manage Staging Emails</h2>';
        $html .= '<p>Where would you like your staging emails to be directed?</p>';
        $html .= '<form  method="post">';

        $html .= $this->radio_option_html('admin', $email_options);
        $html .= 'WordPress Admin Email (' . $email_options['admin_email'] . ')<br/>';

        $html .= $this->radio_option_html('custom', $email_options);
        $html .= $this->text_box_html($email_options) . '<br/>';

        $html .= $this->radio_option_html('log', $email_options);
        $html .= 'Send emails to PHP error log<br/>';

        $html .= $this->radio_option_html('none', $email_options); 
        $html .= 'Halt all emails<br/>';

        $html .= '<p><input type="submit" value="Save"></p>';
        $html .= '</form>';
        return $html;
	}

	public function radio_option_html($option_name, $email_options)
	{
		$attribute_array = array(
			'type' => 'radio',
			'name' => $this->post_name . '[' . $this->selection_name . ']',
			'value' => $option_name,
			'onclick' => '',
		);

		if ($option_name === 'custom') {
			$attribute_array['onclick'] = 'document.getElementById(\'' . $this->custom_address . '\').focus()';
		}

		return '<input ' . $this->get_attributes_html($attribute_array) . ' ' . $this->is_checked($option_name, $email_options) . '> ';
	}

	public function text_box_html($email_options)
	{
		$attribute_array = array(
			'type' => 'text',
			'id' => $this->custom_address,
			'name' => $this->post_name . '[' . $this->custom_address . ']',
			'placeholder' => 'custom@email.com',
			'value' => $email_options[$this->custom_address],
		);
		return '<input ' . $this->get_attributes_html($attribute_array) . '>';
	}

	public function get_attributes_html($attribute_array)
	{
		$html = '';
		foreach ($attribute_array as $key => $value) {
			$html .= $key . '="' . $value . '" ';
		}
		return $html;
	}

	public function is_checked($value, $email_options)
	{
		if ($value === $email_options[$this->selection_name]) {
			return 'checked';
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
}