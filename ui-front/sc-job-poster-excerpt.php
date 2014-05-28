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
	case 0: $color = 'job-yellow'; break;
	case 1: $color = 'job-mint'; break;
	case 2: $color = 'job-rose'; break;
	case 3: $color = 'job-blue'; break;
	case 4: $color = 'job-amber'; break;
	case 5: $color = 'job-grey'; break;
}

$size = 'medium';
$terms = wp_get_post_terms( $post->ID, 'jbp_category', array('fields' => 'ids') );

?>

<div class="job-custom group" data-permalink="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" >
	<div class="job-image">
		<?php echo do_shortcode(	sprintf('[ti term="%s" width="120" height="120" class="job-image" ]', $terms[0]) ); ?>
	</div>

	<div class="<?php echo $color; ?> job-details group" >
		<h2><a href="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" class="job-show" title="<?php the_title(); ?>" ><?php the_title(); ?></a></h2>
		<span class="job-read-more"><a href="<?php the_permalink(); ?>" ><?php esc_html_e('Read more...', JBP_TEXT_DOMAIN ); ?></a></span>
		<span class="job-cat"><?php the_terms(get_the_id(), 'jbp_category', '', ', ', ''); ?>&nbsp;</span>
		<span class="job-budget"><?php _e('Budget: $', JBP_TEXT_DOMAIN); ?><?php echo do_shortcode('[ct id="_ct_jbp_job_Budget"]'); ?></span>
	</div>
	
</div>
