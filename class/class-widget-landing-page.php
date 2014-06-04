<?php

/**
 * Landing Page widget class
 *
 * @since 1.0
 */
class WP_Widget_Landing_Page extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_landing_page', 'description' => __( "Jobs+ Landing Page" ) );
		parent::__construct( 'landing-page', __( 'Landing Page' ), $widget_ops );
		$this->alt_option_name = 'widget_landing_page';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		//wp_enqueue_style('experts-plus');
		$cache = wp_cache_get( 'widget_landing_page', 'widget' );

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

		$view = apply_filters( 'widget_landing_page_can_view', empty( $instance['view'] ) ? 'both' : $instance['view'], $instance, $this->id_base );
		if ( ! $Jobs_Plus_Core->can_view( $view ) ) {
			return '';
		}

		if ( ! $Jobs_Plus_Core->can_view( $instance['view'] ) ) {
			return '';
		}

		$job_title        = isset( $instance['job_title'] ) ? esc_attr( $instance['job_title'] ) : '';
		$number       = isset( $instance['job_number'] ) ? absint( $instance['job_number'] ) : 5;
		$show_cat     = isset( $instance['job_show_cat'] ) ? (bool) $instance['job_show_cat'] : false;
		$order_by     = isset( $instance['job_order_by'] ) ? $instance['job_order_by'] : 'latest';
		$category_val = isset( $instance['category_val'] ) ? $instance['category_val'] : array();

		$expert_title    = isset( $instance['expert_title'] ) ? esc_attr( $instance['expert_title'] ) : '';
		$expert_number   = isset( $instance['expert_number'] ) ? absint( $instance['expert_number'] ) : 5;
		$expert_order_by = isset( $instance['expert_order_by'] ) ? $instance['expert_order_by'] : 'latest';
		$line_count      = isset( $instance['line_count'] ) ? $instance['line_count'] : 3;

		ob_start();
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Landing Page' ) : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( $title ) {
			//echo $before_title . $title . $after_title;
		}

		$phrase = ( empty( $_GET['s'] ) ) ? '' : $_GET['s'];
		?>
		<div class="jbp_landing_page_widget">
			<?php the_widget( 'WP_Widget_Recent_Job_Posts', array(
				'title'            => $job_title,
				'number'           => $number,
				'show_browse_link' => 1,
				'show_cat'         => $show_cat,
				'order_by'         => $order_by,
				'category_val'     => $category_val
			), array(
				'before_title' => '<h4 class="widgettitle">',
				'after_title'  => '</h4>'
			) ); ?>
			<br />
			<?php the_widget( 'WP_Widget_Recent_Experts', array(
				'number'           => $expert_number,
				'show_browse_link' => 1,
				'title'            => $expert_title,
				'order_by'         => $expert_order_by,
				'line_count'       => $line_count
			), array(
				'before_title' => '<h4 class="widgettitle">',
				'after_title'  => '</h4>'
			) ); ?>

		</div>
		<?php

		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_landing_page', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['job_title']    = strip_tags( $new_instance['job_title'] );
		$instance['view']         = $new_instance['view'];
		$instance['job_number']   = $new_instance['job_number'];
		$instance['job_show_cat'] = $new_instance['job_show_cat'];
		$instance['job_order_by'] = $new_instance['job_order_by'];
		$instance['category_val'] = $new_instance['category_val'];

		$instance['expert_title']    = $new_instance['expert_title'];
		$instance['expert_number']   = $new_instance['expert_number'];
		$instance['expert_order_by'] = $new_instance['expert_order_by'];
		$instance['line_count']      = $new_instance['line_count'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_landing_page'] ) ) {
			delete_option( 'widget_landing_page' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_landing_page', 'widget' );
	}

	function form( $instance ) {
		$title        = isset( $instance['job_title'] ) ? esc_attr( $instance['job_title'] ) : '';
		$number       = isset( $instance['job_number'] ) ? absint( $instance['job_number'] ) : 5;
		$show_cat     = isset( $instance['job_show_cat'] ) ? (bool) $instance['job_show_cat'] : false;
		$order_by     = isset( $instance['job_order_by'] ) ? $instance['job_order_by'] : 'latest';
		$category_val = isset( $instance['category_val'] ) ? $instance['category_val'] : array();


		$expert_title    = isset( $instance['expert_title'] ) ? esc_attr( $instance['expert_title'] ) : '';
		$expert_number   = isset( $instance['expert_number'] ) ? absint( $instance['expert_number'] ) : 5;
		$expert_order_by = isset( $instance['expert_order_by'] ) ? $instance['expert_order_by'] : 'latest';
		$line_count      = isset( $instance['line_count'] ) ? $instance['line_count'] : 3;

		$view = isset( $instance['view'] ) ? esc_attr( $instance['view'] ) : '';
		?>
		<fieldset>
			<legend><h3><?php _e( 'Jobs' ) ?>:</h3></legend>
			<p>
				<label for="<?php echo $this->get_field_id( 'job_title' ); ?>"><?php esc_html_e( 'Job Widget Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'job_title' ); ?>" name="<?php echo $this->get_field_name( 'job_title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'job_number' ); ?>"><?php esc_html_e( 'Number of job posts to show:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'job_number' ); ?>" name="<?php echo $this->get_field_name( 'job_number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_cat ); ?> id="<?php echo $this->get_field_id( 'job_show_cat' ); ?>" name="<?php echo $this->get_field_name( 'job_show_cat' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'job_show_cat' ); ?>"><?php esc_html_e( 'Display job categories?' ); ?></label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'job_order_by' ); ?>"><?php esc_html_e( 'Order by' ); ?>:</label>
				<select id="<?php echo $this->get_field_id( 'job_order_by' ) ?>" name="<?php echo $this->get_field_name( 'job_order_by' ); ?>">
					<option <?php selected( 'randomize', $order_by ) ?> value="randomize">Randomize</option>
					<option <?php selected( 'latest', $order_by ) ?> value="latest">Latest</option>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Categories' ) ?>:</label>
				<?php
				$job_cats = get_terms( 'jbp_category', array(
					'hide_empty' => false
				) );

				?>

				<select style="display: block;width: 100%" multiple="multiple" name="<?php echo $this->get_field_name( 'category_val' ) ?>[]" id="<?php echo $this->get_field_id( 'category_val' ) ?>">
					<?php foreach ( $job_cats as $cat ): ?>
						<option <?php echo @in_array( $cat->term_id, $category_val ) ? 'selected="selected"' : null ?> value="<?php echo $cat->term_id ?>"><?php echo esc_html( $cat->name ) ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</fieldset>
		<fieldset>
			<legend><h3><?php _e( 'Experts' ) ?>:</h3></legend>
			<p>
				<label for="<?php echo $this->get_field_id( 'expert_title' ); ?>"><?php esc_html_e( 'Expert Widget Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'expert_title' ); ?>" name="<?php echo $this->get_field_name( 'expert_title' ); ?>" type="text" value="<?php echo $expert_title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'expert_number' ); ?>"><?php esc_html_e( 'Number of experts to show:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'expert_number' ); ?>" name="<?php echo $this->get_field_name( 'expert_number' ); ?>" type="text" value="<?php echo $expert_number; ?>" size="3" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'line_count' ); ?>"><?php esc_html_e( 'Number of expert per line:' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'line_count' ); ?>" name="<?php echo $this->get_field_name( 'line_count' ); ?>" type="text" value="<?php echo $line_count; ?>" size="3" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'expert_order_by' ); ?>"><?php esc_html_e( 'Order by' ); ?>:</label>
				<select id="<?php echo $this->get_field_id( 'expert_order_by' ) ?>" name="<?php echo $this->get_field_name( 'expert_order_by' ); ?>">
					<option <?php selected( 'randomize', $expert_order_by ) ?> value="randomize">Randomize</option>
					<option <?php selected( 'latest', $expert_order_by ) ?> value="latest">Latest</option>
				</select>
			</p>
		</fieldset>
		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php esc_html_e( 'Who can view:' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'view' ) ?>">
				<option <?php echo selected( 'both', $view ) ?> value="both"><?php _e( 'Both', 'domain' ) ?></option>
				<option <?php echo selected( 'loggedin', $view ) ?> value="loggedin"><?php _e( 'Signed in', 'domain' ) ?></option>
				<option <?php echo selected( 'loggedout', $view ) ?> value="loggedout"><?php _e( 'Not sign in', 'domain' ) ?></option>
			</select>
		</p>
	<?php
	}

}