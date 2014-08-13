<?php

/**
 * Search Experts widget class
 *
 * @since 1.0
 */
class WP_Widget_Add_Job extends WP_Widget {

	function __construct() {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$widget_ops = array( 
		'classname' => 'widget_add_job', 
		'description' => sprintf(__( "Post a new %s", JBP_TEXT_DOMAIN), $core->job_labels->singular_name )
		);
		parent::__construct( 'add-job', sprintf(__( 'Jobs + Post New %s', JBP_TEXT_DOMAIN), $core->job_labels->singular_name), $widget_ops );
		$this->alt_option_name = 'widget_add_job';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$cache = wp_cache_get( 'widget_add_job', 'widget' );

		if ( ! is_array( $cache ) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = $this->id;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		$view = apply_filters( 'widget_search_job_can_view', empty( $instance['view'] ) ? 'both' : $instance['view'], $instance, $this->id_base );
		if ( ! $core->can_view( $view ) ) return '';

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? sprintf(__( 'Post new %s', JBP_TEXT_DOMAIN ), $core->job_labels->singular_name ) : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;	

		?>
		<section class="jobsearch-widgetbar postjob">
			<form method="GET" action="<?php echo get_permalink($core->job_update_page_id); ?>">
				<div class="jbp-search-box-container">
					<input type="text" name="job_title" value="" autocomplete="off" placeholder="<?php echo esc_attr( sprintf(__('%s title', JBP_TEXT_DOMAIN), $core->job_labels->singular_name ) ); ?>" />
					<button type="submit" class="job-post-btn dark no-pad" value="">
						<div class="div-img">&nbsp;</div>
					</button>
				</div>
			</form>
		</section>
		<?php
		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_add_job', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['view']  = $new_instance['view'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_add_job'] ) ) {
			delete_option( 'widget_add_job' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_add_job', 'widget' );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$view  = isset( $instance['view'] ) ? esc_attr( $instance['view'] ) : '';
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title: ', JBP_TEXT_DOMAIN ); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'view' ) ); ?>"><?php esc_html_e( 'Who can view:', JBP_TEXT_DOMAIN ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'view' ) ); ?>">
				<option <?php echo selected('both',$view) ?> value="both"><?php esc_html_e( 'Both', JBP_TEXT_DOMAIN ) ?></option>
				<option <?php echo selected('loggedin',$view) ?> value="loggedin"><?php esc_html_e( 'Signed in', JBP_TEXT_DOMAIN ) ?></option>
				<option <?php echo selected('loggedout',$view) ?> value="loggedout"><?php esc_html_e( 'Not sign in', JBP_TEXT_DOMAIN ) ?></option>
			</select>
		</p>
	<?php
	}

}