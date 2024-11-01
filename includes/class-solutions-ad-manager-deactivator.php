<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://solutionsbysteve.com
 * @since      0.1.0
 *
 * @package    Solutions_Ad_Manager
 * @subpackage Solutions_Ad_Manager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Solutions_Ad_Manager
 * @subpackage Solutions_Ad_Manager/includes
 * @author     Steven Maloney <steve@solutionsbysteve.com>
 */
class Solutions_Ad_Manager_Deactivator {

	public static function deactivate() {
		unregister_setting( 'solutions-ad-manager-options', 'solutions-ad-manager-options' );
		wp_clear_scheduled_hook( 'solutions_ad_manager_update' );
	}

}
