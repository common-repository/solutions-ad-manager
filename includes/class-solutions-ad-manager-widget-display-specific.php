<?php 

/**
 * Adds Solutions_Ad_Manager_Specific_Widget widget.
 */
class Solutions_Ad_Manager_Specific_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$this->solutions_ad_manager = 'solutions-ad-manager';
		$this->version = '1.0.0';
		parent::__construct(
			'sam_specific_widget', // Base ID
			__( 'Ad (specific)', 'solutions-ad-manager' ), // Name
			array( 'description' => __( 'Display a specific Ad', 'solutions-ad-manager' ), ) // Args
		);
	}
	
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_solutions_ad_manager() {
		return $this->solutions_ad_manager;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		$query_args = array( 
			'post_type' => array('solutions-ad-manager'),
			'p' => $instance['specificAd'] ,
		);
		
		$specificAd = new WP_Query( $query_args );
		
	
		echo $args['before_widget'];
		echo '<div class="solutions-ad-manager-widget">';
		
		if ( $specificAd->have_posts() ) {
			$specificAd->the_post();
			
			$siteURL = home_url();
			$meta = array();
			$meta['URL'] = get_post_meta( $specificAd->post->ID, 'solutions_ad_url', true );
			$meta['title'] = get_the_title();
			$meta['media'] = esc_html(get_post_meta( $specificAd->post->ID, 'solutions_ad_oembed', true ));
			
			if ( $instance['showTitle'] == 'on' && !empty($meta['title']) ) {
				echo $args['before_title'];
				if(!empty($meta['URL'])){echo '<a href="' . $meta['URL'] . '">';}
				echo $meta['title'];
				if(!empty($meta['URL'])){echo '</a>';}
				echo $args['after_title'];
			}
			if ( !empty($meta['media']) ){
				echo '<div class="media">';
				$plugin_public = new Solutions_Ad_Manager_Public( $this->get_solutions_ad_manager(), $this->get_version() );
				echo $plugin_public->sam_oembed($meta['media']);
				echo '</div>';
			}else{
				echo '<div class="image">';
				if(!empty($meta['URL'])){echo '<a href="' . esc_url($siteURL) . '?sam-redirect-to=' . esc_url( $meta['URL'] ) . '&sam-post-id=' . $specificAd->post->ID . '" rel="nofollow">';}
				echo get_the_post_thumbnail( $specificAd->post->ID,  'full' );
				if(!empty($meta['URL'])){echo '</a>';}
				echo '</div>';
			}
			
		} else {
			echo __( 'No Ad Found', 'solutions-ad-manager' );
		}
		
		echo '</div>';
		echo $args['after_widget'];
		wp_reset_postdata();
		
		
		
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		//Default Widget Settings
		$defaults = array(
			'showTitle' => 'on'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		$query_args = array( 
			'post_type' => array('solutions-ad-manager'),
			'posts_per_page' => -1 ,
		);
		$ads = new WP_Query( $query_args );
		
		if ( $ads->have_posts() ) {
			//Display Form
			echo '<p>';
			echo '<select id="' . $this->get_field_id('specificAd') . '" name="' . $this->get_field_name('specificAd') . '" class="widefat" style="width:100%;">';
			while ( $ads->have_posts() ){
				$ads->the_post();
				echo '<option ' . selected( $instance['specificAd'], $ads->post->ID ) . ' value="' . $ads->post->ID . '">' . get_the_title() . '</option>';
			}
			echo '</select>';
			echo '</p>';
			
			echo '<p>';
			echo '<input class="checkbox" type="checkbox"' . checked($instance['showTitle'], 'on', false) . ' id="' . $this->get_field_id('showTitle') . '" name="' . $this->get_field_name('showTitle') . '" />';
			echo '<label for="'.$this->get_field_id('showTitle').'">' . __( 'Show Title', 'solutions-ad-manager' ) .'</label>';
			echo '</p>';
		}
		wp_reset_postdata();
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		// Fields
		$instance['specificAd'] = $new_instance['specificAd'];
		$instance['showTitle'] = $new_instance['showTitle'];
		
		return $instance;
	}



} // class Solutions_Ad_Manager_Random_From_Group_Widget


?>