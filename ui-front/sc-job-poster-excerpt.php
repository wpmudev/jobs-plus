<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $jbp_query;
$author = $post->post_author;


switch($jbp_query->current_post % 6){
	case 0: $color = 'job-grey'; break;
	case 1: $color = 'job-yellow'; break;
	case 2: $color = 'job-mint'; break;
	case 3: $color = 'job-rose'; break;
	case 4: $color = 'job-blue'; break;
	case 5: $color = 'job-amber'; break;
}

$size = 'medium';

?>

<div class="job-excerpt <?php echo $size; ?> <?php echo $color; ?> group" data-permalink="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" >
	<div class="job-item">
		<div class="job-item-content">
			<h4>
				<a href="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" class="job-show" title="<?php the_title(); ?>" ><?php the_title(); ?></a>
			</h4>
			<span><a href="<?php the_permalink(); ?>" ><?php esc_html_e('Read more...', JBP_TEXT_DOMAIN ); ?></a></span>
		</div>
	</div>
	<div class="job-terms <?php echo $class; ?>">
		<div class="job-item-content">
			<span class="job-cat"><?php the_terms(get_the_id(), 'jbp_category', __('Categories: ', JBP_TEXT_DOMAIN), ', ', ''); ?>&nbsp;</span>
			<span class="job-budget"><?php _e('Budget: $', JBP_TEXT_DOMAIN); ?><?php echo do_shortcode('[ct id="_ct_jbp_job_Budget"]'); ?></span>
		</div>
	</div>

</div>
