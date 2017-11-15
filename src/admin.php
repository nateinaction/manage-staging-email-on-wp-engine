<?php

namespace NateGay\ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Admin extends Settings
{
    /**
     * Adds Manage Staging Emails menu item to dashboard
     *
     * @return null
     */
    public function adminMenuItem()
    {
        \add_menu_page(
            'Manage Staging Emails',
            'Manage Staging Emails',
            'administrator',
            'manage-staging-emails-wpe',
            array($this, 'renderAdminPage'),
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
    public function renderAdminPage()
    {
        $post = $this->checkForPostOnAdmin();
        if ($post) {
            $setPluginOptions = $this->setPluginOptions($post);
            $postSuccessHtml = $this->postSuccessHtml($setPluginOptions);
        }

        $form = $this->adminPageHtml();
        
        echo $form . $postSuccessHtml;
    }

    /**
     * Display success/failure message on admin page if a valid POST request was received
     *
     * @return string HTML output of success message
     */
    public function postSuccessHtml($setPluginOptions)
    {
        $attribute_array['style'] = 'color:green;font-weight:800;';
        if (!$setPluginOptions['status']) {
            $attribute_array['style'] = 'color:red;font-weight:800;';
        }

        $html = '<p ' . $this->getAttributesHtml($attribute_array) . '>';
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
        $html .= 'WordPress admin email: ' . $this->getAdminEmail() . '<br/>';

        $html .= $this->radioOptionHtml('custom');
        $html .= 'Custom email: ';
        $html .= $this->textBoxHtml() . '<br/>';

        $html .= $this->radioOptionHtml('log');
        $html .= 'Send emails to PHP error log<br/>';

        $html .= $this->radioOptionHtml('none');
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
            'name' => $this->post_name . '[' . $this->selection_name . ']',
            'value' => $option_name,
            'onclick' => '',
        );

        if ($option_name === 'custom') {
            $attribute_array['onclick'] = 'document.getElementById(\'' . $this->custom_address . '\').focus()';
        }

        return '<input ' . $this->getAttributesHtml($attribute_array) . ' ' . $this->isChecked($option_name) . '> ';
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
        return '<input ' . $this->getAttributesHtml($attribute_array) . '>';
    }

    /**
     * This takes a key value array of HTML attributes and returns them as an HTML string
     *
     * @param $attribute_array array Key is attribute name, value is attribute value
     * @return string HTML attributes
     */
    public function getAttributesHtml($attribute_array)
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
    public function checkForPostOnAdmin()
    {
        if (isset($_POST[$this->post_name])) {
            return $_POST[$this->post_name];
        }
    }
}
