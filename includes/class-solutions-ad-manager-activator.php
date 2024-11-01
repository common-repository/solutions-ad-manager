<?php

/**
 * Fired during plugin activation
 *
 * @link       https://solutionsbysteve.com
 * @since      0.1.0
 *
 * @package    Solutions_Ad_Manager
 * @subpackage Solutions_Ad_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Solutions_Ad_Manager
 * @subpackage Solutions_Ad_Manager/includes
 * @author     Steven Maloney <steve@solutionsbysteve.com>
 */
class Solutions_Ad_Manager_Activator {

	public static function activate() {

		if ( ! wp_next_scheduled( 'solutions_ad_manager_update' ) ) {
			wp_schedule_event( time(), 'hourly', 'solutions_ad_manager_update');
		}

	}

}
