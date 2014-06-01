<?php
add_action('widgets_init', create_function('', 'register_widget( "WP_Widget_Recent_Job_Posts" );'));

/**
* Recent Job_Posts widget class
*
* @since 1.0
*/
class WP_Widget_Recent_Job_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_job_entries', 'description' => __("The most recent job posts on your site"));
		parent::__construct('recent-job-posts', __('Recent Job Posts'), $widget_ops);
		$this->alt_option_name = 'widget_recent_job_entries';

		add_action('save_post', array($this, 'flush_widget_cache'));
		add_action('deleted_post', array($this, 'flush_widget_cache'));
		add_action('switch_theme', array($this, 'flush_widget_cache'));
	}

	function widget($args, $instance) {
		//wp_enqueue_style('jobs-plus');
		$cache = wp_cache_get('widget_recent_job_posts', 'widget');

		if (!is_array($cache))
		$cache = array();

		if (!isset($args['widget_id']))
		$args['widget_id'] = $this->id;

		if (isset($cache[$args['widget_id']])) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Job Posts') : $instance['title'], $instance, $this->id_base);

		if (empty($instance['number']) || !$number = absint($instance['number']))
		$number = 10;
		$show_cat = isset($instance['show_cat']) ? $instance['show_cat'] : false;
		//		$r = new WP_Query( apply_filters( 'widget_job_posts_args', array(

		$posts = get_posts(array(
		'post_type' => 'jbp_job',
		'posts_per_page' => $number,
		//'no_found_rows' => true,
		'post_status' => 'publish',
		//'ignore_sticky_posts' => true,
		'orderby' => 'date',
		'order' => 'DESC'
		));

		if (count($posts) > 0) :
		?>
		<?php echo $before_widget; ?>
		<?php if ($title) echo $before_title . $title . $after_title; ?>
		<ul class="job-widgetbar">
			<?php foreach ($posts as $key => $post) : setup_postdata($post) ?>
			<li>
				<?php
				switch ($key % 6) {
					case 0: $color = 'jbp-grey';
					break;
					case 1: $color = 'jbp-yellow';
					break;
					case 2: $color = 'jbp-mint';
					break;
					case 3: $color = 'jbp-rose';
					break;
					case 4: $color = 'jbp-blue';
					break;
					case 5: $color = 'jbp-amber';
					break;
				}
				?>
				<?php
				/* JOB CATEGORIES */

				$job_categories = get_the_terms($post->ID, 'jbp_category');
				if ($job_categories && !is_wp_error($job_categories)) {

					$job_categories_slugs = array();

					foreach ($job_categories as $job_category) {
						$term_id = $job_category->term_id;
						$current_job_category = trim($job_category->name);
					}
				}
				$icon = get_term_image_url($term_id );
				?>
				<div class="job-widget-bullet <?php echo $color; ?>">
					<?php 
					if($icon) {
						printf('<img src="%s" />', $icon); 
					}
					?>
				</div>

				<a href="<?php echo get_permalink($post->ID) ?>" title="<?php echo esc_attr($post->post_title ? $post->post_title : $post->ID ); ?>"><?php echo $post->post_title ? $post->post_title : $post->ID; ?></a>

				<?php if ($show_cat) : ?>
				<?php
				$terms = get_the_terms($post->ID, 'jbp_category');

				if ($terms && !is_wp_error($terms)) {

					$job_categories = array();

					foreach ($terms as $term) {
						$job_categories[] = $term->name;
					}

					$job_categories_print = join(", ", $job_categories);
				}
				?>
				<span class="job_category"><?php echo $job_categories_print; ?></span>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<?php echo $after_widget; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_job_posts', $cache, 'widget');
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_cat'] = (bool) $new_instance['show_cat'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get('alloptions', 'options');
		if (isset($alloptions['widget_recent_job_entries']))
		delete_option('widget_recent_job_entries');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_job_posts', 'widget');
	}

	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
		$show_cat = isset($instance['show_cat']) ? (bool) $instance['show_cat'] : false;
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of posts to show:'); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked($show_cat); ?> id="<?php echo $this->get_field_id('show_cat'); ?>" name="<?php echo $this->get_field_name('show_cat'); ?>" />
			<label for="<?php echo $this->get_field_id('show_cat'); ?>"><?php esc_html_e('Display job categories?'); ?></label>
		</p>

		<?php
	}

}
