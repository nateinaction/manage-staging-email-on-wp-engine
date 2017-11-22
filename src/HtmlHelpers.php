<?php
namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class HtmlHelpers
{
	public function header($message)
	{
		return '<h2>' . $message . '</h2>';
	}

	public function paragraph($message)
	{
		return '<p>' . $message . '</p>';
	}

	public function form($contentsArray)
	{
		return '<form method="post">' . $contentsArray . '</form>';
	}

	public function input()
	{
		'<input ' . $this->htmlAttributes($attribute_array) . '>';
	}


	public function radioOptionHtml($option_name)
    {
        $attribute_array = array(
            'type' => 'radio',
            'id' => $option_name . '-radio',
            'name' => $this->settings->post_name . '[' . $this->settings->selection_name . ']',
            'value' => $option_name,
            'onclick' => '',
        );

        if ($option_name === 'custom') {
            $attribute_array['onclick'] = 'document.getElementById(\'' . $this->custom_address . '\').focus()';
        }

        return '<input ' . $this->htmlAttributes($attribute_array) . ' ' . $this->isChecked($option_name) . '>';
    }
}