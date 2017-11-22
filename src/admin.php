<?php

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Admin
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }
    /**
     * Check if a POST exists, set settings and retrieve success/failure message, echo form and message
     *
     * @todo refactor this
     * @return null
     */
    public function renderAdminPage()
    {
        $post = $this->checkForPostOnAdmin($this->settings->post_name);
        if ($post) {
            $setPluginOptions = $this->settings->setPluginOptions($post);
            $htmlPostSuccess = $this->htmlPostSuccess($setPluginOptions);
        }

        $form = $this->adminPageHtml();
        
        echo $form . $htmlPostSuccess;
    }

    /**
     * Display success/failure message on admin page if a valid POST request was received
     *
     * @return string HTML output of success message
     */
    public function htmlPostSuccess($setPluginOptions)
    {
        $attribute_array['style'] = 'color:green;font-weight:800;';
        if (!$setPluginOptions['status']) {
            $attribute_array['style'] = 'color:red;font-weight:800;';
        }

        $html = '<p ' . $this->htmlAttributes($attribute_array) . '>';
        $html .= $setPluginOptions['message'];
        $html .= '</p>';
        return $html;
    }

    /**
     * Returns HTML form for admin page
     *
     * @return string HTML for form
     */
    public function adminPageHtml()
    {
        $html = '';
        $html .= '<h2>Manage Staging Emails</h2>';
        $html .= '<p>Where would you like your staging emails to be directed?</p>';
        $html .= '<form  method="post">';

        $html .= $this->radioOptionHtml('admin');
        $html .= 'WordPress admin email: ' . $this->settings->getAdminEmail() . '<br/>';

        $html .= $this->radioOptionHtml('custom');
        $html .= 'Custom email: ';
        $html .= $this->textBoxHtml() . '<br/>';

        $html .= $this->radioOptionHtml('log');
        $html .= 'Send emails to PHP error log<br/>';

        $html .= $this->radioOptionHtml('halt');
        $html .= 'Halt all emails<br/>';

        $html .= '<p><input type="submit" value="Save"></p>';
        $html .= '</form>';
        return $html;
    }

    /**
     * Returns HTML for radio button
     *
     * @param $option_name string Name of radio button
     * @return string HTML for radio button
     */
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

        return '<input ' . $this->htmlAttributes($attribute_array) . ' ' . $this->isChecked($option_name) . '> ';
    }

    /**
     * Returns HTML for text input
     *
     * @return string HTML for text input
     */
    public function textBoxHtml()
    {
        $attribute_array = array(
            'type' => 'text',
            'id' => $this->custom_address,
            'name' => $this->post_name . '[' . $this->custom_address . ']',
            'placeholder' => 'email@example.com',
            'value' => $this->getCustomAddress(),
            'onfocus' => 'document.getElementById(\'custom-radio\').checked = true',
        );
        return '<input ' . $this->htmlAttributes($attribute_array) . '>';
    }

    /**
     * This takes a key value array of HTML attributes and returns them as an HTML string
     *
     * @param $attribute_array array Key is attribute name, value is attribute value
     * @return string HTML attributes
     */
    public function htmlAttributes($attribute_array)
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
     * @return string HTML, "checked" or empty
     */
    public function isChecked($option_name)
    {
        if ($option_name === $this->getSelection()) {
            return 'checked';
        }
        return '';
    }

    /**
     * Check to see if a POST request was made, if so, set it in db
     *
     * @return bool True if valid POST received
     */
    public function checkForPostOnAdmin($postName)
    {
        if (isset($_POST[$post_name])) {
            return $_POST[$post_name];
        }
    }
}
