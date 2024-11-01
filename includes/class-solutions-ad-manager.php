<?php
class Solutions_Ad_Manager {

	protected $loader;
	protected $solutions_ad_manager;
	public $basename;
	protected $version;

	public function __construct() {

		$this->solutions_ad_manager = 'solutions-ad-manager';
		$this->version = '1.0.0';
		$this->basename = 'solutions-ad-manager/solutions-ad-manager.php';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_mutual_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	// Load the required dependencies for this plugin.
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-solutions-ad-manager-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-solutions-ad-manager-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-solutions-ad-manager-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-solutions-ad-manager-public.php';
        // Include Required Plugins Module
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/required-plugins/class-tgm-plugin-activation.php';
		// The class responsible for loading the required plugins module
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/required-plugins/class-tgm-plugin-activation.php';
		
		$this->loader = new Solutions_Ad_Manager_Loader();
	}

	// Define the locale for this plugin for internationalization.
	private function set_locale() {
		$plugin_i18n = new Solutions_Ad_Manager_i18n();
		$plugin_i18n->set_domain( $this->get_solutions_ad_manager() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	// Register all of the hooks used by both admin and public
	private function define_mutual_hooks() {
		// Check the plugin database
		$this->loader->add_action( 'init', $this, 'update_plugin_database' );
		// Setup Update Schedule
		$this->loader->add_action( 'init', $this, 'solutions_ad_manager_setup_schedule' );
		$this->loader->add_action( 'solutions_ad_manager_update', $this, 'solutions_ad_manager_update' );
		//Widgets
		$this->loader->add_action( 'widgets_init', $this, 'register_solutions_ad_manager_widget' );
	}


	// Register all of the hooks related to the admin area functionality
	private function define_admin_hooks() {
		$plugin_admin = new Solutions_Ad_Manager_Admin( $this->get_solutions_ad_manager(), $this->get_version() );
		//Required Plugins
		$this->loader->add_action( 'tgmpa_register', $plugin_admin, 'solutions_ad_manager_register_required_plugins' );
		//Register Post Type
		$this->loader->add_action( 'init', $plugin_admin, 'solutions_ad_manager_register_ad_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'solutions_ad_manager_register_ad_group_taxonomy' );
		//Setup CMB2 - must be in required plugins
		$this->loader->add_filter( 'cmb2_admin_init', $plugin_admin, 'solutions_ad_manager_setup_CMB2' );
		//Custom Columns
		$this->loader->add_filter( 'restrict_manage_posts', $plugin_admin, 'solutions_ad_manager_taxonomy_filters' );
		$this->loader->add_filter( 'manage_edit-solutions-ad-manager_columns', $plugin_admin, 'solutions_ad_manager_custom_columns' );
		$this->loader->add_action( 'manage_solutions-ad-manager_posts_custom_column', $plugin_admin, 'solutions_ad_manager_custom_columns_display', 10, 2 );
		$this->loader->add_filter( 'manage_edit-solutions-ad-manager_sortable_columns', $plugin_admin, 'solutions_ad_manager_custom_columns_sortable' );
		$this->loader->add_filter( 'request', $plugin_admin, 'solutions_ad_manager_custom_columns_sortable_orderby' );
		//Settings Page
		$this->loader->add_action( 'admin_init', $plugin_admin, 'solutions_ad_manager_settings_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solutions_ad_manager_add_admin_menu' );
		$this->loader->add_filter( "plugin_action_links_".$this->basename, $plugin_admin, 'solutions_ad_manager_plugin_settings_link' );
		//Enqueue scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'solutions_ad_manager_admin_enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'solutions_ad_manager_admin_enqueue_scripts' );
		//Add Support
		$this->loader->add_action( 'after_setup_theme', $this, 'custom_theme_setup' );
	}

	// Register all of the hooks related to the public-facing functionality
	private function define_public_hooks() {
		$plugin_public = new Solutions_Ad_Manager_Public( $this->get_solutions_ad_manager(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_public, 'solutions_ad_manager_redirect' );
		// Enqueue scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'solutions_ad_manager_public_enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'solutions_ad_manager_public_enqueue_scripts' );
		// Add Shortcodes
		$this->loader->add_action( 'init', $plugin_public, 'register_solutions_ad_manager_shortcode' );
	}

	// Run the loader to execute all of the hooks with WordPress.
	public function run() {
		$this->loader->run();
	}

	// The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	public function get_solutions_ad_manager() {
		return $this->solutions_ad_manager;
	}

	// The reference to the class that orchestrates the hooks with the plugin.
	public function get_loader() {
		return $this->loader;
	}

	// Retrieve the version number of the plugin.
	public function get_version() {
		return $this->version;
	}

	// Registers the widgets
	public function register_solutions_ad_manager_widget() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-solutions-ad-manager-widget.php';
		register_widget( 'Solutions_Ad_Manager_Random_From_Group_Widget' );
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-solutions-ad-manager-widget-display-specific.php';
		register_widget( 'Solutions_Ad_Manager_Specific_Widget' );
	}

	// Adds theme support for thumbnails.
	public function custom_theme_setup() {
		add_theme_support( 'post-thumbnails', array( 'solutions-ad-manager' ) );
	}

	// Create database version update to manage posts for each plugin update.
	public function update_plugin_database(){
		
		$database_version = get_option( 'solutions_ad_database_version' );
		
		//Add option for database version
		if( !$database_version ){
			add_option( 'solutions_ad_database_version', '0.0.0' );
			//update database version for next check
			$database_version = get_option( 'solutions_ad_database_version' );
		}
		
		//0.8.0 - Fix for missing default end date on existing ads
		if( $database_version < '0.8.0' ){
			$query_args = array( 
				'post_type' => array('solutions-ad-manager'),
				'post_status' => 'any',
				'nopaging' => 'true', //shows all adds in stead of 10 per query
			);
			$the_query = new WP_Query( $query_args );
			while ( $the_query->have_posts() ) : $the_query->the_post();
				$endDate = get_post_meta( $the_query->post->ID, 'solutions_ad_end_date', true );
				if( !$endDate || empty($endDate) || is_null($endDate) ){
					update_post_meta( $the_query->post->ID, 'solutions_ad_end_date', date( "U", strtotime('+1 year')) );
				}
			endwhile;
			update_option( 'solutions_ad_database_version', '0.8.0' );
			//update database version for next check
			//$database_version = get_option( 'solutions_ad_database_version' );
		}
		
	}
	
	// Setup Scheduled Updates.
	public function solutions_ad_manager_setup_schedule() {
		//makes sure cron is scheduled incase plugin was not deactivated and reactivated
		if ( ! wp_next_scheduled( 'solutions_ad_manager_update' ) ) {
			wp_schedule_event( time(), 'hourly', 'solutions_ad_manager_update');
		}
	}
	public function solutions_ad_manager_update(){
		$query_args = array( 
			'post_type' => array('solutions-ad-manager'),
			'post_status' => 'any',
			'nopaging' => 'true', //shows all adds in stead of 10 per query
		);
		$the_query = new WP_Query( $query_args );
		while ( $the_query->have_posts() ) : $the_query->the_post();
			$endDate = get_post_meta( $the_query->post->ID, 'solutions_ad_end_date', true );
			if( !$endDate || empty($endDate) || is_null($endDate) ){
				update_post_meta( $the_query->post->ID, 'solutions_ad_end_date', date( "U", strtotime('+1 year')) );
				//update endDate for next check
				$endDate = get_post_meta( $the_query->post->ID, 'solutions_ad_end_date', true );
			}
			if( $endDate < time() ){
				// Update post
				$my_post = array();
				$my_post['ID'] = $the_query->post->ID;
				$my_post['post_status'] = 'pending';
				wp_update_post( $my_post );
			}
		endwhile;
	}

	
}
