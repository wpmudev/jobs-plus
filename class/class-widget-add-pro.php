<?php
/**
* Add Pros widget class
* @package Jobs+
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
class WP_Widget_Add_Pro extends WP_Widget {
	
	function __construct() {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$widget_ops = array( 
		'classname' => 'widget_add_pro', 
		'description' => sprintf(__( "Let a user become %s", JBP_TEXT_DOMAIN), $core->pro_labels->singular_name ) 
		);
		parent::__construct( 'add-pro', sprintf(__( 'Jobs + Become %s', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name ), $widget_ops );
		$this->alt_option_name = 'widget_add_pro';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$cache = wp_cache_get( 'widget_add_pro', 'widget' );

		if ( ! is_array( $cache ) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = $this->id;
		
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		$limit = intval($core->get_setting('pro->max_records', 1) );

		if( !current_user_can( 'administrator')
		&& $core->count_user_posts_by_type(get_current_user_id(), 'jbp_pro') >= $limit){
			return '';
		}

		$view = apply_filters( 'widget_search_pro_can_view', empty( $instance['view'] ) ? 'both' : $instance['view'], $instance, $this->id_base );
		if ( ! $core->can_view( $view ) ) {
			return '';
		}

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Become Pro', JBP_TEXT_DOMAIN ) : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		?>
		<section class="jobsearch-widgetbar addpro">
			<form method="GET" action="<?php echo get_permalink( $core->pro_update_page_id ); ?>">
				<div class="jbp-search-box-container">
					<input type="text" name="pro_title" value="" autocomplete="off" placeholder="<?php esc_attr_e('Your title', JBP_TEXT_DOMAIN); ?>" />
					<button type="submit" class="pro-post-btn dark no-pad" value="">
					<div class="div-img">&nbsp;</div>
					</button>
				</div>
			</form>
		</section>
		<?php
		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();

		wp_cache_set( 'widget_add_pro', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['view']  = $new_instance['view'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		
		if ( isset( $alloptions['widget_add_pro'] ) ) delete_option( 'widget_add_pro' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_add_pro', 'widget' );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$view  = isset( $instance['view'] ) ? esc_attr( $instance['view'] ) : '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', JBP_TEXT_DOMAIN ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php esc_html_e( 'Who can view:', JBP_TEXT_DOMAIN ); ?></label>
			<select name="<?php echo $this->get_field_name( 'view' ) ?>">
				<option <?php echo selected('both',$view) ?> value="both"><?php _e( 'Both', JBP_TEXT_DOMAIN ) ?></option>
				<option <?php echo selected('loggedin',$view) ?> value="loggedin"><?php _e( 'Signed in', JBP_TEXT_DOMAIN ) ?></option>
				<option <?php echo selected('loggedout',$view) ?> value="loggedout"><?php _e( 'Not signed in', JBP_TEXT_DOMAIN ) ?></option>
			</select>
		</p>
		<?php
	}

}