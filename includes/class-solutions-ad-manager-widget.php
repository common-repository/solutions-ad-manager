<?php 

/**
 * Adds Solutions_Ad_Manager_Random_From_Group_Widget widget.
 */
class Solutions_Ad_Manager_Random_From_Group_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$this->solutions_ad_manager = 'solutions-ad-manager';
		$this->version = '1.0.0';
		parent::__construct(
			'sam_random_from_group_widget', // Base ID
			__( 'Ad (random from group)', 'solutions-ad-manager' ), // Name
			array( 'description' => __( 'Grab a random ad from selected group', 'solutions-ad-manager' ), ) // Args
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
		
		global $sam_ad_array;
		$term = get_term( $instance['groupTax'], 'solutions-ad-group' );
		$group = $term->slug;
		$query_args = array( 
			'post_type' => array('solutions-ad-manager'),
			'tax_query' => array(
				array(
					'taxonomy' => $term->taxonomy,
					'field'    => 'slug',
					'terms'    => $group,
				),
			),
			'orderby' => 'rand',
			'nopaging' => 'true', //shows all adds in stead of 10 per query
			'meta_query' => array(
				array(
					'key'     => 'solutions_ad_end_date',
					'value'   => time(),
					'compare' => '>',
				),
			),
			
			
		);
		
		if( empty($sam_ad_array[$group]) || !$sam_ad_array[$group]->have_posts() ){
			// Create new query
			$sam_ad_array[$group] = new WP_Query( $query_args );
		}
		
		$options = get_option( 'solutions-ad-manager-options' );
		if(!isset( $options['solutions-ad-manager-stretch-image'] )){ 
			$stretchImage = false;
		}else{
			$stretchImage = $options['solutions-ad-manager-stretch-image'];
		}
		
		if ( $sam_ad_array[$group]->have_posts() ) {
			$sam_ad_array[$group]->the_post();
			
			echo $args['before_widget'];
			echo '<div class="solutions-ad-manager-widget" id="samid-'.$sam_ad_array[$group]->post->ID.'">';
			
			$siteURL = home_url();
			$meta = array();
			$meta['URL'] = get_post_meta( $sam_ad_array[$group]->post->ID, 'solutions_ad_url', true );
			$meta['title'] = get_the_title();
			$meta['media'] = esc_html(get_post_meta( $sam_ad_array[$group]->post->ID, 'solutions_ad_oembed', true ));
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
				if(!empty($meta['URL'])){echo '<a href="' . esc_url($siteURL) . '?sam-redirect-to=' . esc_url( $meta['URL'] ) . '&sam-post-id=' . $sam_ad_array[$group]->post->ID . '" rel="nofollow">';}
				if($stretchImage){
					echo get_the_post_thumbnail( $sam_ad_array[$group]->post->ID,  'full', array('class' => 'stretch') );
				}else{
					echo get_the_post_thumbnail( $sam_ad_array[$group]->post->ID,  'full' );
				}
				if(!empty($meta['URL'])){echo '</a>';}
				echo '</div>';
			}
			
			echo '</div>';
			echo $args['after_widget'];
		} else {
			echo $args['before_widget'];
			echo '<div class="solutions-ad-manager-widget">';
			echo __( 'No Ad Found', 'solutions-ad-manager' );
			echo '</div>';
			echo $args['after_widget'];
		}
		
		
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

		//Display Form
		echo '<p>';
		echo '<select id="' . $this->get_field_id('groupTax') . '" name="' . $this->get_field_name('groupTax') . '" class="widefat" style="width:100%;">';
		foreach(get_terms('solutions-ad-group','parent=0&hide_empty=0') as $term) {
			echo '<option ' . selected( $instance['groupTax'], $term->term_id ) . ' value="' . $term->term_id . '">' . $term->name . '</option>';
		}
		echo '</select>';
		echo '</p>';
		
		echo '<p>';
		echo '<input class="checkbox" type="checkbox"' . checked($instance['showTitle'], 'on', false) . ' id="' . $this->get_field_id('showTitle') . '" name="' . $this->get_field_name('showTitle') . '" />';
		echo '<label for="'.$this->get_field_id('showTitle').'">' . __( 'Show Title', 'solutions-ad-manager' ) .'</label>';
		echo '</p>';
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
		$instance['groupTax'] = $new_instance['groupTax'];
		$instance['showTitle'] = $new_instance['showTitle'];
		
		return $instance;
	}


} // class Solutions_Ad_Manager_Random_From_Group_Widget


?>