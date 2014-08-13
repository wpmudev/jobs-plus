<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post;
$author = $post->post_author;

switch( rand(0,5) ){
	case 0: $color = 'jbp-yellow'; break;
	case 1: $color = 'jbp-mint'; break;
	case 2: $color = 'jbp-rose'; break;
	case 3: $color = 'jbp-blue'; break;
	case 4: $color = 'jbp-amber'; break;
	case 5: $color = 'jbp-grey'; break;
}

$size = 'medium';
$terms = wp_get_post_terms( $post->ID, 'jbp_category', array('fields' => 'ids') );

$term_image = '';
if( !empty($terms) ){
	$term_image = do_shortcode(	sprintf('[ti term="%s" width="120" height="120" class="job-image" ]', $terms[0]) );
}

?>

<div class="job-custom group" data-permalink="<?php echo esc_attr(get_permalink(get_the_ID())); ?>" >
	<div class="job-image">
		<?php echo $term_image; ?>
	</div>

	<div class="<?php echo $color; ?> job-details group" >
		<h2><a href="<?php echo esc_attr(get_permalink(get_the_ID())); ?>" class="job-show" title="<?php the_title(); ?>" ><?php the_title(); ?></a></h2>
		<span class="job-read-more"><a href="<?php the_permalink(); ?>" ><?php esc_html_e('Read more...', JBP_TEXT_DOMAIN ); ?></a></span>
		<span class="job-cat"><?php the_terms(get_the_id(), 'jbp_category', '', ', ', ''); ?>&nbsp;</span>
		<span class="job-budget"><?php esc_html_e('Budget: ', JBP_TEXT_DOMAIN); ?><span class="currency_symbol"></span><?php echo do_shortcode('[ct id="_ct_jbp_job_Budget"]' ); ?></span>
	</div>

</div>
