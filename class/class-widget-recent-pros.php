<?php

/**
* Recent Experts widget class
*
* @since 1.0
*/
class WP_Widget_Recent_Pros extends WP_Widget {

	function __construct() {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		$widget_ops = array( 'classname' => 'widget_recent_expert_entries', 'description' => sprintf(__( "The most recent %s on your site", JBP_TEXT_DOMAIN ), $core->pro_labels->name) );
		parent::__construct( 'recent-experts', sprintf(__( 'Jobs + Recent %s', JBP_TEXT_DOMAIN ), $core->pro_labels->name), $widget_ops );
		$this->alt_option_name = 'widget_recent_experts';

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		global $Jobs_Plus_Core;
		$core = $Jobs_Plus_Core;

		wp_enqueue_script('jobs-plus-shortcode');

		$cache = wp_cache_get( 'widget_recent_experts', 'widget' );

		if ( ! is_array( $cache ) ) $cache = array();

		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = $this->id;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();

		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? sprintf(__( 'Our Experts', JBP_TEXT_DOMAIN ), $core->pro_labels->name) : $instance['title'], $instance, $this->id_base );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) $number = 10;

		$post_args = array(
		'post_type'      => 'jbp_pro',
		'posts_per_page' => $number,
		//'no_found_rows' => true,
		'post_status'    => 'publish',
		//'ignore_sticky_posts' => true,
		'order'          => 'DESC'
		);

		$order_by = isset( $instance['order_by'] ) ? $instance['order_by'] : '';

		switch ( $order_by ) {
			case 'latest':
			$post_args['orderby'] = 'date';
			break;
			case 'randomize':
			$post_args['orderby'] = 'rand';
			break;
		}

		$posts = get_posts( $post_args );

		if ( count( $posts ) > 0 ) :
		?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) {
			echo $before_title . $title . $after_title;
		} ?>
		<ul class="expert-widgetbar">
			<?php foreach ( $posts as $key => $p ): ?>
			<?php
			global $post;
			$post = $p;
			setup_postdata( $post );
			//calculate line width
			$line_count   = isset( $instance['line_count'] ) ? $instance['line_count'] : 3;
			$li_width     = floor( 100 / $line_count ) - 1.5;
			$margin_right = ( ( $key + 1 ) % $line_count != 0 ) ? 'margin-right:1%;' : '';
			?>
			<li style="width: <?php echo $li_width ?>%;<?php echo $margin_right ?>">
				<a title="<?php esc_html( the_title() ) ?>" href="<?php the_permalink() ?>"><?php echo get_avatar_or_gravatar( $post->post_author, do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]'), 256 );?></a>

				<div class="jbp-expert-wg-mask">
					<a title="<?php echo esc_attr( the_title() ) ?>" href="<?php the_permalink() ?>">
					<h3><?php the_title() ?></h3></a>

					</div>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( isset( $instance['show_browse_link'] ) && $instance['show_browse_link'] == 1 ): ?>
			<a href="<?php echo esc_attr(site_url( '/experts' ) );?>"><?php esc_html_e( 'Browse experts...', JBP_TEXT_DOMAIN ) ?></a>
			<?php endif; ?>

			<?php echo $after_widget; ?>
			<?php
			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();

			endif;

			$cache[$args['widget_id']] = ob_get_flush();
			wp_cache_set( 'widget_recent_experts', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance               = $old_instance;
			$instance['title']      = strip_tags( $new_instance['title'] );
			$instance['number']     = (int) $new_instance['number'];
			$instance['order_by']   = $new_instance['order_by'];
			$instance['line_count'] = $new_instance['line_count'];
			$this->flush_widget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_recent_experts'] ) ) {
				delete_option( 'widget_recent_experts' );
			}

			return $instance;
		}

		function flush_widget_cache() {
			wp_cache_delete( 'widget_recent_experts', 'widget' );
		}

		function form( $instance ) {
			$title      = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$number     = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			$order_by   = isset( $instance['order_by'] ) ? $instance['order_by'] : 'latest';
			$line_count = isset( $instance['line_count'] ) ? $instance['line_count'] : 3;
			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', JBP_TEXT_DOMAIN ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', JBP_TEXT_DOMAIN ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'line_count' ) ); ?>"><?php esc_html_e( 'Number of posts per line:', JBP_TEXT_DOMAIN ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'line_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'line_count' ) ); ?>" type="text" value="<?php echo esc_attr( $line_count ); ?>" size="3" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'order_by' ) ); ?>"><?php esc_html_e( 'Order by', JBP_TEXT_DOMAIN ); ?>:</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'order_by' ) ); ?>" name="<?php echo $this->get_field_name( 'order_by'); ?>">
					<option <?php selected( 'randomize', $order_by ) ?> value="randomize"><?php esc_html_e( 'Randomize', JBP_TEXT_DOMAIN );?></option>
					<option <?php selected( 'latest', $order_by ) ?> value="latest"><?php esc_html_e( 'Latest', JBP_TEXT_DOMAIN );?></option>
				</select>
			</p>
			<?php
		}

	}
