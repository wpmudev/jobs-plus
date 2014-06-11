<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

/**
* Do not use raw shortcodes( [xxx] ) inside the loop. Use echo do_shortcode('[xxx]');
*/
global $post;

$posts = get_posts(
array(
'post_type' => 'jbp_pro',
'post_status' => 'publish',
'posts_per_page' => 9,
'orderby' => 'rand',
//'order' => 'DESC',
//'suppress_filters' => false,
)
);
/*
switch($jbp_query->current_post % 6){
case 0: $color = 'jbp-yellow'; break;
case 1: $color = 'jbp-mint'; break;
case 2: $color = 'jbp-rose'; break;
case 3: $color = 'jbp-blue'; break;
case 4: $color = 'jbp-amber'; break;
case 5: $color = 'jbp-grey'; break;
}
*/

wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('element-query');

?>

<div class="pro-poster"  data-eq-pts=" break: 420">
	<h2 class="h2_title"><?php echo esc_html( $title ); ?></h2>
	<hr />
	<div class="poster">
		<ul class="pro-gallery group">
			<?php
			$currently_displayed = 0;

			foreach ($posts as $ndx => $value):
			//if ($currently_displayed < $max_displayed) {
			$post = $value;
			setup_postdata($post);
			$email = do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]');
			//if (jb_is_valid_gravatar($email)) {
			?>
			<li>
				<div class="view view-first">
					<?php echo get_avatar_or_gravatar($post->post_author, $email, 120); ?>
				<div class="mask">
					<a href="<?php echo get_permalink(get_the_ID()); ?>" title="<?php the_title(); ?>" alt="<?php the_title(); ?>" >
						<h2><?php the_title(); ?></h2>
						<p>[jbp-rating post="<?php the_ID(); ?>"]</p>
					</a>
				</div>
				</div>
			</li>
			<?php
			endforeach;
			?>
		</ul>
	</div>
	<div class="pros-link">
		<span><a class="browse-pros-link" href="<?php echo get_post_type_archive_link('jbp_pro'); ?>"><?php echo esc_html( $link ); ?></a></span>
	</div>
	<div>
		[jbp-expert-post-btn text="<?php echo esc_html( $legend ); ?>" class="none"]
	</div>
	<?php wp_reset_postdata(); ?>
</div>
