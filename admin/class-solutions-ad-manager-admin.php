<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://solutionsbysteve.com
 * @since      0.1.0
 *
 * @package    Solutions_Ad_Manager
 * @subpackage Solutions_Ad_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Solutions_Ad_Manager
 * @subpackage Solutions_Ad_Manager/admin
 * @author     Steven Maloney <steve@solutionsbysteve.com>
 */
class Solutions_Ad_Manager_Admin {

	private $solutions_ad_manager;
	private $version;

	public function __construct( $solutions_ad_manager, $version ) {
		$this->solutions_ad_manager = $solutions_ad_manager;
		$this->version = $version;
	}

	// Register the stylesheets for the admin area.
	public function solutions_ad_manager_admin_enqueue_styles() {
		wp_enqueue_style( $this->solutions_ad_manager, plugin_dir_url( __FILE__ ) . 'css/solutions-ad-manager-admin.css', array(), $this->version, 'all' );
	}

	// Register the JavaScript for the admin area.
	public function solutions_ad_manager_admin_enqueue_scripts() {
		wp_enqueue_script( $this->solutions_ad_manager, plugin_dir_url( __FILE__ ) . 'js/solutions-ad-manager-admin.js', array( 'jquery' ), $this->version, false );
	}

	// Setup CMB2 Metaboxes.
	public function solutions_ad_manager_setup_CMB2() {
		// AD Meta Metabox
		$solutions_ad_meta = new_cmb2_box( array(
			'id'            => 'solutions_ad_meta',
			'title'         => esc_html__( 'Ad Meta', 'solutions-ad-manager' ),
			'object_types'  => array( 'solutions-ad-manager' ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
		) );
		$solutions_ad_meta->add_field( array(
			'name'       => esc_html__( 'End Date', 'solutions-ad-manager' ),
			'id'         => 'solutions_ad_end_date',
			'type'       => 'text_datetime_timestamp',
			'default'    => date( "U", strtotime('+1 year')),
		) );
		$solutions_ad_meta->add_field( array(
			'name'       => esc_html__( 'Website URL', 'solutions-ad-manager' ),
			'id'         => 'solutions_ad_url',
			'type'       => 'text_url',
		) );
		$solutions_ad_meta->add_field( array(
			'name'       => esc_html__( 'Clicks', 'solutions-ad-manager' ),
			'id'         => 'solutions_ad_clicks',
			'type'       => 'text_small',
			'default'    => 0,
			'attributes'  => array(
				'disabled'    => 'disabled',
			),
		) );


		// AD Media Metabox
		$solutions_ad_media = new_cmb2_box( array(
			'id'            => 'solutions_ad_media',
			'title'         => esc_html__( 'Ad Media', 'solutions-ad-manager' ),
			'object_types'  => array( 'solutions-ad-manager' ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
		) );
		$solutions_ad_media->add_field( array(
			'name' => esc_html__( 'Media', 'solutions-ad-manager' ),
			'desc' => sprintf(
				/* translators: %s: link to codex.wordpress.org/Embeds */
				esc_html__( 'Enter a youtube, twitter, or instagram URL. Supports services listed at %s.', 'solutions-ad-manager' ),
				'<a href="https://wordpress.org/support/article/embeds/">codex.wordpress.org/Embeds</a>'
			),
			'id'   => 'solutions_ad_oembed',
			'type' => 'oembed',
		) );
	}


	// Setup Required Plugins.
	public function solutions_ad_manager_register_required_plugins() {
		$plugins = array(
			array(
				'name'      => 'CMB2',
				'slug'      => 'cmb2',
				'required'  => true,
			),
		);
		$config = array(
			'id'           => 'solutions-ad-manager',	// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',						// Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',	// Menu slug.
			'parent_slug'  => 'plugins.php',			// Parent menu slug.
			'capability'   => 'manage_options',			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,						// Show admin notices or not.
			'dismissable'  => false,					// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => 'You need to install CMB2 for this plugin to work correctly!',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.

		);
		tgmpa( $plugins, $config );
	}
	
	// Registers the post type.
	public function solutions_ad_manager_register_ad_post_type() {
		$labels = array(
			'name'                => _x( 'Ads', 'Post Type General Name', 'solutions-ad-manager' ),
			'singular_name'       => _x( 'Ad', 'Post Type Singular Name', 'solutions-ad-manager' ),
			'menu_name'           => __( 'Ad Manager', 'solutions-ad-manager' ),
			'name_admin_bar'      => __( 'Ad', 'solutions-ad-manager' ),
			'parent_item_colon'   => __( 'Parent Ad:', 'solutions-ad-manager' ),
			'all_items'           => __( 'Ads', 'solutions-ad-manager' ),
			'add_new_item'        => __( 'Add New Ad', 'solutions-ad-manager' ),
			'add_new'             => __( 'Add New', 'solutions-ad-manager' ),
			'new_item'            => __( 'New Ad', 'solutions-ad-manager' ),
			'edit_item'           => __( 'Edit Ad', 'solutions-ad-manager' ),
			'update_item'         => __( 'Update Ad', 'solutions-ad-manager' ),
			'view_item'           => __( 'View Ad', 'solutions-ad-manager' ),
			'search_items'        => __( 'Search Ad', 'solutions-ad-manager' ),
			'not_found'           => __( 'Not found', 'solutions-ad-manager' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'solutions-ad-manager' ),
		);
		$args = array(
			'label'               => __( 'ad', 'solutions-ad-manager' ),
			'description'         => __( 'Ad', 'solutions-ad-manager' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail', 'revisions' ),
			'taxonomies'          => array( 'solutions-ad-group' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 101,
			'menu_icon'           => 'dashicons-groups',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'solutions-ad-manager', $args );
	}

	// Registers the post type.
	public function solutions_ad_manager_register_ad_group_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Group', 'Taxonomy General Name', 'solutions-ad-manager' ),
			'singular_name'              => _x( 'Group', 'Taxonomy Singular Name', 'solutions-ad-manager' ),
			'menu_name'                  => __( 'Groups', 'solutions-ad-manager' ),
			'all_items'                  => __( 'All Groups', 'solutions-ad-manager' ),
			'parent_item'                => __( 'Parent Group', 'solutions-ad-manager' ),
			'parent_item_colon'          => __( 'Parent Group:', 'solutions-ad-manager' ),
			'new_item_name'              => __( 'New Group Name', 'solutions-ad-manager' ),
			'add_new_item'               => __( 'Add New Group', 'solutions-ad-manager' ),
			'edit_item'                  => __( 'Edit Group', 'solutions-ad-manager' ),
			'update_item'                => __( 'Update Group', 'solutions-ad-manager' ),
			'view_item'                  => __( 'View Group', 'solutions-ad-manager' ),
			'separate_items_with_commas' => __( 'Separate menus with commas', 'solutions-ad-manager' ),
			'add_or_remove_items'        => __( 'Add or remove menus', 'solutions-ad-manager' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'solutions-ad-manager' ),
			'popular_items'              => __( 'Popular Groups', 'solutions-ad-manager' ),
			'search_items'               => __( 'Search Groups', 'solutions-ad-manager' ),
			'not_found'                  => __( 'Not Found', 'solutions-ad-manager' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => false,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
		);
		register_taxonomy( 'solutions-ad-group', array( 'solutions-ad-manager' ), $args );
	}
	
	// Custom Post Type Columns.
	public function solutions_ad_manager_custom_columns( $columns ) {
		unset($columns['date']);
		unset($columns['thumbnail']);
		$columns['url'] = __( 'URL', 'solutions-ad-manager' );
		$columns['status'] = __( 'Status', 'solutions-ad-manager' );
		$columns['media'] = __( 'Media', 'solutions-ad-manager' );
		$columns['clicks'] = __( 'Clicks', 'solutions-ad-manager' );
		return $columns;
	}
	
	// Display the column content
	public function solutions_ad_manager_custom_columns_display( $column, $post_id ) {
		switch ( $column ) {
			case 'url' :
				$meta = get_post_meta($post_id, 'solutions_ad_url', true);
				if ( !$meta ){
					$meta = '—';
				}
				echo $meta;
				break;
			case 'status' :
				$meta = get_post_status($post_id);
				if( $meta == 'publish' ){
					$meta = 'Active';
				}elseif( $meta == 'pending' ){
					$meta = 'Pending Review';
				}elseif( $meta == 'draft' ){
					$meta = 'Inactive';
				}else{
					$meta = '—';
				}
				echo $meta;
				break;
			case 'media' :
				$media = esc_html(get_post_meta( $post_id, 'solutions_ad_oembed', true ));
				$image = get_the_post_thumbnail( $post_id,  array(50,50) );
				if ( !empty($media) ){
					//$meta = wp_oembed_get($media, array('width'=>50,'height'=>50));
					$meta = 'Media';
				}elseif( !empty($image) ){
					$meta = $image;
				}else{
					$meta = '—';
				}
			
				echo $meta;
				break;
			case 'clicks' :
				$meta = get_post_meta($post_id, 'solutions_ad_clicks', true);
				if ( !$meta ){
					$meta = '—';
				}
				echo $meta;
				break;
		}
	}
	// Register the column as sortable
	public function solutions_ad_manager_custom_columns_sortable( $columns ) {
		$columns['clicks'] = 'clicks';
		return $columns;
	}
	public function solutions_ad_manager_custom_columns_sortable_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) && 'clicks' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => 'solutions_ad_clicks',
				'orderby' => 'meta_value_num'
			) );
		}
		return $vars;
	}
	
	// Taxonomy Filters.
	public function solutions_ad_manager_taxonomy_filters() {
		global $typenow;
		// an array of all the taxonomyies you want to display. Use the taxonomy name or slug
		$taxonomies = array('solutions-ad-group');
		// must set this to the post type you want the filter(s) displayed on
		if( $typenow == 'solutions-ad-manager' ){
			foreach ($taxonomies as $tax_slug) {
				$tax_obj = get_taxonomy($tax_slug);
				$tax_name = $tax_obj->labels->all_items;
				$terms = get_terms($tax_slug);
				if(count($terms) > 0) {
					echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
					echo "<option value=''>$tax_name</option>";
					foreach ($terms as $term) { 
						echo '<option ';
						echo 'value="' . $term->slug . '"';
						if(isset($_GET[$tax_slug]) && $_GET[$tax_slug] == $term->slug ){
							echo ' selected="selected"';
						}
						echo '>' . $term->name .' (' . $term->count .')</option>'; 
					}
				echo "</select>";
				}
			}
		}
	}
	
	// Settings Page
	function solutions_ad_manager_add_admin_menu(  ) { 
		//remove add new from menu
		global $submenu;
		unset($submenu['edit.php?post_type=solutions-ad-manager'][10]);
		//add how to use to menu
		add_submenu_page( 
			'edit.php?post_type=solutions-ad-manager', 
			__( 'Solutions Ad Manager', 'solutions-ad-manager' ), 
			__( 'How To Use', 'solutions-ad-manager' ), 
			'manage_options', 
			$this->solutions_ad_manager.'-howto',
			array( $this, 'solutions_ad_manager_howto_page' ) 
		);
		//add options page
		add_submenu_page( 
			'edit.php?post_type=solutions-ad-manager', 
			__( 'Solutions Ad Manager', 'solutions-ad-manager' ), 
			__( 'Options', 'solutions-ad-manager' ), 
			'manage_options', 
			$this->solutions_ad_manager.'-options',
			array( $this, 'solutions_ad_manager_options_page' ) 
		);
	}

	// Contains markup for the admin "How To Use" page
	function solutions_ad_manager_howto_page(  ) { 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/solutions-ad-manager-admin-display-howto.php';
	}

	// Contains markup for the admin "How To Use" page
	function solutions_ad_manager_options_page(  ) { 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/solutions-ad-manager-admin-display.php';
	}

	// Add settings link on plugin page
	function solutions_ad_manager_plugin_settings_link($links) { 
	  $settings_link = '<a href="'.get_admin_url(NULL, 'edit.php?post_type=solutions-ad-manager&page=solutions-ad-manager-howto').'"> ' . __( 'How To Use', 'solutions-ad-manager' ) . '</a>'; 
	  array_unshift($links, $settings_link); 
	  $settings_link = '<a href="'.get_admin_url(NULL, 'edit.php?post_type=solutions-ad-manager&page=solutions-ad-manager-options').'"> ' . __( 'Options', 'solutions-ad-manager' ) . '</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}

	// Settings for Media Options.
	function solutions_ad_manager_settings_init(  ) { 
		register_setting( 'solutions-ad-manager-options', 'solutions-ad-manager-options' );
		add_settings_section(
			$this->solutions_ad_manager.'-image-section', 
			__( 'Image Options', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_image_section_render' ), 
			'solutions-ad-manager-options'
		);
		add_settings_field( 
			$this->solutions_ad_manager.'-stretch-image', 
			__( 'Stretch Image to fit', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_image_stretch_render' ), 
			'solutions-ad-manager-options', 
			$this->solutions_ad_manager.'-image-section' 
		);
		add_settings_section(
			$this->solutions_ad_manager.'-youtube-section', 
			__( 'Youtube Options', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_youtube_section_render' ), 
			'solutions-ad-manager-options'
		);
		add_settings_field( 
			$this->solutions_ad_manager.'-youtube-showtitle', 
			__( 'Show Title', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_youtube_showtitle_render' ), 
			'solutions-ad-manager-options', 
			$this->solutions_ad_manager.'-youtube-section' 
		);
		add_settings_field( 
			$this->solutions_ad_manager.'-youtube-showcontrols', 
			__( 'Show Controls', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_youtube_showcontrols_render' ), 
			'solutions-ad-manager-options', 
			$this->solutions_ad_manager.'-youtube-section' 
		);
		add_settings_field( 
			$this->solutions_ad_manager.'-youtube-autoplay', 
			__( 'Autoplay', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_youtube_autoplay_render' ), 
			'solutions-ad-manager-options', 
			$this->solutions_ad_manager.'-youtube-section' 
		);
		add_settings_field( 
			$this->solutions_ad_manager.'-youtube-showrelated', 
			__( 'Show Related', 'solutions-ad-manager' ), 
			array( $this, 'solutions_ad_manager_youtube_showrelated_render' ), 
			'solutions-ad-manager-options', 
			$this->solutions_ad_manager.'-youtube-section' 
		);
	}
	// Call Back Functions for settings above
	function solutions_ad_manager_image_section_render(  ) { 
		echo __( 'The section below only provides settings for images.', 'solutions-ad-manager' );
	}
	function solutions_ad_manager_image_stretch_render(  ) { 
		$options = get_option( 'solutions-ad-manager-options' );
		if(!isset( $options[$this->solutions_ad_manager.'-stretch-image'] )){ $options[$this->solutions_ad_manager.'-stretch-image'] = 0;}
		?>
		<input type="checkbox" name="<?php echo 'solutions-ad-manager-options' ?>[<?php echo $this->solutions_ad_manager.'-stretch-image' ?>]" value="1"<?php checked( 1, $options[$this->solutions_ad_manager.'-stretch-image'], true )?>>
		<?php
	}
	function solutions_ad_manager_youtube_section_render(  ) { 
		echo __( 'The section below only provides settings for playback of youtube videos.', 'solutions-ad-manager' );
	}
	function solutions_ad_manager_youtube_showtitle_render(  ) { 
		$options = get_option( 'solutions-ad-manager-options' );
		if(!isset( $options[$this->solutions_ad_manager.'-youtube-showtitle'] )){ $options[$this->solutions_ad_manager.'-youtube-showtitle'] = 0;}
		?>
		<input type="checkbox" name="<?php echo 'solutions-ad-manager-options' ?>[<?php echo $this->solutions_ad_manager.'-youtube-showtitle' ?>]" value="1"<?php checked( 1, $options[$this->solutions_ad_manager.'-youtube-showtitle'], true )?>>
		<?php
	}
	function solutions_ad_manager_youtube_showcontrols_render(  ) { 
		$options = get_option( 'solutions-ad-manager-options' );
		if(!isset( $options[$this->solutions_ad_manager.'-youtube-showcontrols'] )){ $options[$this->solutions_ad_manager.'-youtube-showcontrols'] = 0;}
		?>
		<input type="checkbox" name="<?php echo 'solutions-ad-manager-options' ?>[<?php echo $this->solutions_ad_manager.'-youtube-showcontrols' ?>]" value="1"<?php checked( 1, $options[$this->solutions_ad_manager.'-youtube-showcontrols'], true )?>>
		<?php
	}
	function solutions_ad_manager_youtube_autoplay_render(  ) { 
		$options = get_option( 'solutions-ad-manager-options' );
		if(!isset( $options[$this->solutions_ad_manager.'-youtube-autoplay'] )){ $options[$this->solutions_ad_manager.'-youtube-autoplay'] = 0;}
		?>
		<input type="checkbox" name="<?php echo 'solutions-ad-manager-options' ?>[<?php echo $this->solutions_ad_manager.'-youtube-autoplay' ?>]" value="1"<?php checked( 1, $options[$this->solutions_ad_manager.'-youtube-autoplay'], true )?>>
		<?php
	}
	function solutions_ad_manager_youtube_showrelated_render(  ) { 
		$options = get_option( 'solutions-ad-manager-options' );
		if(!isset( $options[$this->solutions_ad_manager.'-youtube-showrelated'] )){ $options[$this->solutions_ad_manager.'-youtube-showrelated'] = 0;}
		?>
		<input type="checkbox" name="<?php echo 'solutions-ad-manager-options' ?>[<?php echo $this->solutions_ad_manager.'-youtube-showrelated' ?>]" value="1"<?php checked( 1, $options[$this->solutions_ad_manager.'-youtube-showrelated'], true )?>>
		<?php
	}

}
