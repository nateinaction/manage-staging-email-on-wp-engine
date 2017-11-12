<?php

namespace nategay\manage_staging_email_wpe;

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
    public $option_name = 'manage_staging_email_wpe';

    /**
     * Sanitize options before setting in DB
     *
     * @return array Status will be a bool, true for success
     */
    public function setPluginOptions($options_array)
    {
        $current_options = $this->getPluginOptions();

        $admin = new Admin;
        $selection = $options_array[$admin->selection_name];
        $email_address = $options_array[$admin->custom_address];
            
        if ('custom' === $selection) {
            if (!$this->checkForValidEmail($email_address)) {
                return array(
                    'status' => false,
                    'message' => 'Please enter a valid email.',
                );
            }
        } else {
            $options_array[$admin->custom_address] = $current_options[$admin->custom_address];
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
    public function getPreferredAddress()
    {
        $options_array = $this->getPluginOptions();
        $admin = new Admin;

        if ('admin' === $options_array[$admin->selection_name]) {
            return $this->getAdminEmail();
        }
        return $options_array[$admin->custom_address];
    }
}
