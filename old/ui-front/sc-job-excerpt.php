<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $wp_query;
$author = $post->post_author;

switch($wp_query->current_post){
	case 0: $size = 'medium'; break;
	case 2:
	case 6: $size = 'small'; break;
	default: $size = 'small'; break;
}

switch($wp_query->current_post % 6){
	case 0: $color = 'jbp-yellow'; break;
	case 1: $color = 'jbp-mint'; break;
	case 2: $color = 'jbp-rose'; break;
	case 3: $color = 'jbp-blue'; break;
	case 4: $color = 'jbp-amber'; break;
	case 5: $color = 'jbp-grey'; break;
}

wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('jquery-format-currency-i18n');

?>

<div class="job-excerpt <?php echo $size; ?> <?php echo $color; ?> group" data-permalink="<?php echo esc_attr(get_permalink(get_the_ID())); ?>" >

	<div class="job-item">
		<div class="job-item-content">
			<h4>
				<a href="<?php echo esc_attr(get_permalink(get_the_ID())); ?>" class="job-show" title="<?php the_title(); ?>" ><?php the_title(); ?></a>
				</h4>
			<div class="ellipsis">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<div class="job-terms <?php echo $class; ?>">
		<div class="job-item-content">
		<span class="job-cat"><?php the_terms(get_the_id(), 'jbp_category', __('Categories: ', JBP_TEXT_DOMAIN), ', ', ''); ?>&nbsp;</span>
		<?php if($size != 'small'): ?>
		<span class="job-skill"><?php the_terms(get_the_id(), 'jbp_skills_tag', __('Skills: ', JBP_TEXT_DOMAIN), ', ', ''); ?>&nbsp;</span>
		<?php endif; ?>
	</div>
	</div>
	<div class="job-footer <?php echo $class; ?>">
		<div class="job-stats">
			<?php if(get_post_status() != 'publish') echo ucfirst($post->post_status); ?>
			<span class="job-due"><?php esc_html_e('Due: ', JBP_TEXT_DOMAIN); ?><?php echo do_shortcode('[ct id="_ct_jbp_job_Due"]'); ?></span>
			<span class="job-budget"><?php esc_html_e('Budget: ', JBP_TEXT_DOMAIN); ?><span class="currency_symbol">$</span><?php echo do_shortcode('[ct id="_ct_jbp_job_Budget"]'); ?></span>
		</div>
	</div>

</div>
