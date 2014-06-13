<?php

/**
 * Search Jobs widget class
 *
 * @since 1.0
 */
class WP_Widget_Search_Jobs extends WP_Widget {

	function __construct() {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$widget_ops = array( 'classname' => 'widget_search_jobs', 'description' => sprintf( __( "Search %s on your site", JBP_TEXT_DOMAIN  ), $core->job_labels->name ) );
		parent::__construct( 'search-jobs', sprintf( __( 'Jobs + Search %s', JBP_TEXT_DOMAIN ), $core->job_labels->name) , $widget_ops );
		$this->alt_option_name = 'widget_search_jobs';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$cache = wp_cache_get( 'widget_search_jobs', 'widget' );

		if ( ! is_array( $cache ) ) $cache = array();
		
		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = $this->id;
		
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		$view = apply_filters( 'widget_search_job_can_view', empty( $instance['view'] ) ? 'both' : $instance['view'], $instance, $this->id_base );
		if ( ! $core->can_view( $view ) ) {
			return '';
		}
		ob_start();
		
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? sprintf( __( 'Search %s', JBP_TEXT_DOMAIN ), $core->job_labels->name  ) : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		$phrase = ( empty( $_GET['s'] ) ) ? '' : $_GET['s'];


		$sort_latest = ( empty( $_GET['prj_sort'] ) || $_GET['prj_sort'] == 'latest' ? 'active-sort' : 'inactive-sort' );
		$sort_ending = ( ! empty( $_GET['prj_sort'] ) && $_GET['prj_sort'] == 'ending' ? 'active-sort' : 'inactive-sort' );

		$job_min_price = intval( empty( $_GET['job_min_price'] ) ? 0 : $_GET['job_min_price'] );
		$job_max_price = intval( empty( $_GET['job_max_price'] ) ? $core->get_max_budget() : $_GET['job_max_price'] );
		?>
		<section class="jobsearch-widgetbar">
			<form method="GET" action="<?php echo get_post_type_archive_link( 'jbp_job' ); ?>">
				<ul>
					<li>
						<span>Sort by</span>
					</li>
					<li>
					<span class="sort-by-latest <?php echo $sort_latest; ?>">
						<a href="<?php echo add_query_arg( array( 'prj_sort' => 'latest', ), get_post_type_archive_link( 'jbp_job' ) ); ?>"><?php esc_html_e( 'Latest', JBP_TEXT_DOMAIN ); ?></a>
					</span>
					</li>
					<li>
					<span class="sort-by-end <?php echo $sort_ending; ?>">
						<a href="<?php echo add_query_arg( array( 'prj_sort' => 'ending', ), get_post_type_archive_link( 'jbp_job' ) ); ?>"><?php esc_html_e( 'About to End', JBP_TEXT_DOMAIN ); ?></a>
					</span>
					</li>
				</ul>
				<div style="clear: both"></div>
				<div class="jbp-search-box-container">
					<input type="hidden" class="job_min_price" name="job_min_price" value="<?php echo $job_min_price; ?>" />
					<input type="hidden" class="job_max_price" name="job_max_price" value="<?php echo $job_max_price; ?>" />
					<input type="text" name="s" value="<?php echo esc_attr( $phrase ); ?>" autocomplete="off" placeholder="<?php echo esc_attr( sprintf(__('Search %s', JBP_TEXT_DOMAIN), $core->job_labels->name ) ); ?>" />
					<button type="submit" class="job-submit-search" "value="">
						<div class="div-img">&nbsp;</div>
					</button>
				</div>
			</form>
		</section>
		<?php
		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_recent_experts', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['view']  = $new_instance['view'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_search_jobs'] ) ) {
			delete_option( 'widget_search_jobs' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_search_jobs', 'widget' );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$view  = isset( $instance['view'] ) ? esc_attr( $instance['view'] ) : '';
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', JBP_TEXT_DOMAIN ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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