<?php

namespace nategay\manage_staging_email_wpe;
 
class Main
{
	/**
	 * Initialize main function
	 *
	 * @return null
	 */
    public function init()
    {
    	if ($this->check_staging()) {
    		$admin = new Admin();
			\add_action('admin_menu', array($admin, 'admin_menu_item'));
    		\add_filter('wp_mail', array($this, 'redirect_email'), 1000, 1);
    	}
    }

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
 
    public static function run()
    {
        $main = new Main();
        //$settings = new Settings();

        $main->init();
    }
}