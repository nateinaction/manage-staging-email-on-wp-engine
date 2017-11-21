<?php

namespace ManageStagingEmailWPE;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The Settings class handles getting and storing plugin settings.
 *
 */
class Settings
{
    // some shared static vars
    public $plugin_title = 'Manage Staging Email on WP Engine';
    public $option_name = 'manage_staging_email_wpe';
    public $post_name = 'manage_staging_email_wpe_settings';
    public $selection_name = 'email_preference';
    public $custom_address = 'custom_address';

    /**
     * Sanitize options before setting in DB
     *
     * @return array Status will be a bool, true for success
     */
    public function setPluginOptions($options_array)
    {
        $current_options = $this->getPluginOptions();
        $selection = $options_array[$this->selection_name];
        $email_address = $options_array[$this->custom_address];
            
        if ('custom' === $selection) {
            if (!$this->checkForValidEmail($email_address)) {
                return array(
                    'status' => false,
                    'message' => 'Please enter a valid email.',
                );
            }
        } else {
            $options_array[$this->custom_address] = $current_options[$this->custom_address];
        }
        
        $this->setOptionsInDb($options_array);
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
    public function checkForValidEmail($email_address)
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
    public function setOptionsInDb($options_array)
    {
        \update_option($this->option_name, $options_array);
    }

    /**
     * Provides a valid options array even if options have not yet been set
     *
     * @return array Array with settings
     */
    public function getPluginOptions()
    {
        $options = $this->getOptionsFromDb();
        if (!$options) {
            $options = array();
            $options[$this->selection_name] = 'admin';
            $options[$this->custom_address] = '';
        }
        return $options;
    }

    /**
     * An easy way to access selection option in DB
     *
     * @return string Current selection in DB options
     */
    public function getSelection()
    {
        $options_array = $this->getPluginOptions();
        return $options_array[$this->selection_name];
    }

    /**
     * An easy way to access custom_address option in DB
     *
     * @return string Current custom_address in DB options
     */
    public function getCustomAddress()
    {
        $options_array = $this->getPluginOptions();
        return $options_array[$this->custom_address];
    }

    /**
     * Gets plugin options from WordPress' database
     *
     * @uses get_site_option() Grabs plugin settings from db
     * @return array|bool Will be false if no settings exist yet, else array with settings
     */
    public function getOptionsFromDb()
    {
        return \get_site_option($this->option_name);
    }

    /**
     * Gets admin email from WordPress' database
     *
     * @uses get_site_option() Grabs preferred email address from db
     * @return string Value of admin email
     */
    public function getAdminEmail()
    {
        return \get_site_option('admin_email');
    }

    /**
     * Gets prefferred emailaddress based on plugin settings
     *
     * @return string Value of preferred email address
     */
    public function getPreferredAddress($selection)
    {
        if ('custom' === $selection) {
            return $this->getCustomAddress();
        }
        return $this->getAdminEmail();
    }
}
