<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://solutionsbysteve.com
 * @since             0.1.0
 * @package           Solutions_Ad_Manager
 * @basename   		  solutions-ad-manager/solutions-ad-manager.php
 *
 * @wordpress-plugin
 * Plugin Name:       Solutions Ad Manager
 * Plugin URI:        https://solutionsbysteve.com/products/solutions-ad-manager/
 * Description:       A simple ad manager that allows you to display pictures and YouTube videos in to a widget.
 * Version:           1.0.0
 * Author:            Solutions by Steve
 * Author URI:        https://solutionsbysteve.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       solutions-ad-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-solutions-ad-manager-activator.php
 */
function activate_solutions_ad_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-solutions-ad-manager-activator.php';
	Solutions_Ad_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-solutions-ad-manager-deactivator.php
 */
function deactivate_solutions_ad_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-solutions-ad-manager-deactivator.php';
	Solutions_Ad_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_solutions_ad_manager' );
register_deactivation_hook( __FILE__, 'deactivate_solutions_ad_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-solutions-ad-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_solutions_ad_manager() {

	$plugin = new Solutions_Ad_Manager();
	$plugin->run();

}
run_solutions_ad_manager();
