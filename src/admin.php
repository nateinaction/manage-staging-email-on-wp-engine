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

	/**
	 * Adds Manage Staging Emails menu item to dashboard
	 *
	 * @return null
	 */
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

	/**
	 * Check if a POST exists, set settings and retrieve success/failure message, echo form and message
	 *
	 * @todo refactor this
	 * @return null
	 */
	public function render_admin_page()
	{
		$post = $this->check_for_post_on_admin();
		if ($post) {
			$settings = new Settings;
			$set_plugin_options = $settings->set_plugin_options($post);
			$post_success_html = $this->post_success_html($set_plugin_options);
		}

		$form = $this->admin_page_html();
		
		echo $form . $post_success_html;
	}

	/**
	 * Display success/failure message on admin page if a valid POST request was received
	 *
	 * @return string HTML output of success message
	 */
	public function post_success_html($set_plugin_options)
	{
		$attribute_array['style'] = 'color:green;font-weight:800;';
		if (!$set_plugin_options['status']) {
			$attribute_array['style'] = 'color:red;font-weight:800;';
		}

		$html = '<p ' . $this->get_attributes_html($attribute_array) . '>';
		$html .= $set_plugin_options['message'];
		$html .= '</p>';
		return $html;
	}

	/**
	 * Returns HTML form for admin page
	 *
	 * @return string HTML for form
	 */
	public function admin_page_html()
	{
		$settings = new Settings();

        $html = '';
        $html .= '<h2>Manage Staging Emails</h2>';
        $html .= '<p>Where would you like your staging emails to be directed?</p>';
        $html .= '<form  method="post">';

        $html .= $this->radio_option_html('admin', $settings->get_plugin_options());
        $html .= 'WordPress admin email: ' . $settings->get_admin_email() . '<br/>';

        $html .= $this->radio_option_html('custom', $settings->get_plugin_options());
        $html .= 'Custom email: ';
        $html .= $this->text_box_html($settings->get_plugin_options()) . '<br/>';

        $html .= $this->radio_option_html('log', $settings->get_plugin_options());
        $html .= 'Send emails to PHP error log<br/>';

        $html .= $this->radio_option_html('none', $settings->get_plugin_options()); 
        $html .= 'Halt all emails<br/>';

        $html .= '<p><input type="submit" value="Save"></p>';
        $html .= '</form>';
        return $html;
	}

	/**
	 * Returns HTML for radio button
	 *
	 * @param $option_name string Name of radio button
	 * @param $plugin_options array Plugin options array
	 * @return string HTML for radio button
	 */
	public function radio_option_html($option_name, $plugin_options)
	{
		$attribute_array = array(
			'type' => 'radio',
			'id' => $option_name . '-radio',
			'name' => $this->post_name . '[' . $this->selection_name . ']',
			'value' => $option_name,
			'onclick' => '',
		);

		if ($option_name === 'custom') {
			$attribute_array['onclick'] = 'document.getElementById(\'' . $this->custom_address . '\').focus()';
		}

		return '<input ' . $this->get_attributes_html($attribute_array) . ' ' . $this->is_checked($option_name, $plugin_options) . '> ';
	}

	/**
	 * Returns HTML for text input
	 *
	 * @param $plugin_options array Plugin options array
	 * @return string HTML for text input
	 */
	public function text_box_html($plugin_options)
	{
		$attribute_array = array(
			'type' => 'text',
			'id' => $this->custom_address,
			'name' => $this->post_name . '[' . $this->custom_address . ']',
			'placeholder' => 'email@example.com',
			'value' => $plugin_options[$this->custom_address],
			'onfocus' => 'document.getElementById(\'custom-radio\').checked = true',
		);
		return '<input ' . $this->get_attributes_html($attribute_array) . '>';
	}

	/**
	 * This takes a key value array of HTML attributes and returns them as an HTML string
	 *
	 * @todo Can I do this with implode and/or map?
	 * @param $attribute_array array Key is attribute name, value is attribute value
	 * @return string HTML attributes
	 */
	public function get_attributes_html($attribute_array)
	{
		$html = '';
		foreach ($attribute_array as $key => $value) {
			$html .= $key . '="' . $value . '" ';
		}
		return $html;
	}

	/**
	 * This checks to see if a radio button is checked and outputs an HTML string
	 *
	 * @param $option_name string Name of radio button
	 * @param $plugin_options array Plugin options array
	 * @return string HTML, "checked" or empty
	 */
	public function is_checked($option_name, $plugin_options)
	{
		if ($option_name === $plugin_options[$this->selection_name]) {
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