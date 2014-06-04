<?php

/**
 * Search Experts widget class
 *
 * @since 1.0
 */
class WP_Widget_Add_Job extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_add_job', 'description' => __( "Post new job widget" ) );
		parent::__construct( 'add-job', __( 'Post new job' ), $widget_ops );
		$this->alt_option_name = 'widget_add_job';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		//wp_enqueue_style('experts-plus');
		$cache = wp_cache_get( 'widget_add_job', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];

			return;
		}

		global $Jobs_Plus_Core;

		$view = apply_filters( 'widget_search_expert_can_view', empty( $instance['view'] ) ? 'both' : $instance['view'], $instance, $this->id_base );
		if ( ! $Jobs_Plus_Core->can_view( $view ) ) {
			return '';
		}

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Post new job' ) : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		?>
		<section class="jobsearch-widgetbar postjob">
			<form method="GET" action="<?php echo site_url('/job/add'); ?>">
				<div class="jbp_search_box_container">
					<input type="text" name="job_title" value="" autocomplete="off" placeholder="<?php _e('Job title') ?>" />
					<button type="submit" value="">
						<?php _e('Submit') ?>
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
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php esc_html_e( 'Who can view:' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'view' ) ?>">
				<option <?php echo selected('both',$view) ?> value="both"><?php _e( 'Both', 'domain' ) ?></option>
				<option <?php echo selected('loggedin',$view) ?> value="loggedin"><?php _e( 'Signed in', 'domain' ) ?></option>
				<option <?php echo selected('loggedout',$view) ?> value="loggedout"><?php _e( 'Not sign in', 'domain' ) ?></option>
			</select>
		</p>
	<?php
	}

}