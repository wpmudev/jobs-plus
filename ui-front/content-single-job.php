<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $CustomPress_Core, $wp_query, $post, $post_ID;

function days_hours( $expires ){
	$date = intval($expires);
	$secs = $date - time();
	if($secs > 0){
		$days = floor($secs / (60*60*24));
		$hours = round(( $secs - $days*60*60*24)/(60*60));
		return sprintf(__('%d Days %dhrs',JBP_TEXT_DOMAIN), $days, $hours );
	} else {
		return __('Expired', JBP_TEXT_DOMAIN);
	}
}

$project_min = sanitize_text_field(trim( do_shortcode('[ct id="_ct_jbp_job_Min_Budget" ]') ) );
$project_max = sanitize_text_field(trim( do_shortcode('[ct id="_ct_jbp_job_Budget" ]') ) );
$project_min = empty( $project_min) ? '' : $project_min;
$project_dash = !empty( $project_min ) && !empty( $project_max ) ? ' - ' : '';
$project_max = empty( $project_max) ? '' : $project_max;
$project_max = ($project_min . $project_max == '') ? 'N/A' : $project_max;

//$post_ID = $post->ID;
wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('jquery-format-currency-i18n');

?>
<div class="job-single-wrapper">

	<div class="group">
		<?php if(dynamic_sidebar('job-widget') ) : else: endif; ?>
	</div>
	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>

	<div class="job-meta group">
		<div class="job-date"><p class="subheader1"><?php  echo esc_html( sprintf(__('Posted by: %s on %s', JBP_TEXT_DOMAIN ), get_the_author(), get_the_date() ) ); ?></p></div>
		<ul class="group">
			<li><span class="meta-label"><?php esc_html_e('Job Budget', JBP_TEXT_DOMAIN);?><br /></span><span id="project-min" class="meta-red "></span><span id="project-dash" class="meta-red "></span><span id="project-max" class="meta-red "></span></li>
			<li><span class="meta-label"><?php esc_html_e('This job open for', JBP_TEXT_DOMAIN);?><br /></span><span class="meta-green"><?php echo days_hours( get_post_meta(get_the_ID(), JBP_JOB_EXPIRES_KEY, true) );?></span></li>
			<li class="border"><span class="meta-label"><?php esc_html_e('Must be complete by', JBP_TEXT_DOMAIN);?><br /></span><span class="meta-red"><?php echo do_shortcode('[ct id="_ct_jbp_job_Due" ]'); ?></span></li>
			<li>
				<?php
				if( get_post_meta( get_the_ID(), JBP_JOB_EXPIRES_KEY, true) > time() ):
				echo do_shortcode('[jbp-job-contact-btn text="Contact" class="job-contact"]');
				endif;
				?>
			</li>
		</ul>
	</div>

	<div id="post-full-<?php the_ID(); ?>" <?php post_class(); ?> >

		<div class="job-item-full">
			<div class="job-top">
				<?php the_terms(get_the_id(), 'jbp_category', __('<h2 class="job-cat">Categories:</h2> ', JBP_TEXT_DOMAIN), ', ', ''); ?>
			</div>
			<?php the_content(); ?>

			<div class="job-skills">
				<?php echo get_the_term_list(get_the_ID(), 'jbp_skills_tag', __('<h3>You will need to have these skills:', JBP_TEXT_DOMAIN) . '</h3><ul><li>', '</li><li>', '</li></ul>')?>
			</div>

			<div class="job-portfolio group">
				<h3><?php esc_html_e('Examples:', JBP_TEXT_DOMAIN); ?></h3>
				<?php
				$portfolios = do_shortcode('[ct id="_ct_jbp_job_Portfolio"]');
				$portfolios = empty($portfolios) ? new stdClass : (object)json_decode($portfolios);
				?>
				<div class="job-images group">
					<ul class="group">
						<?php
						foreach ( $portfolios as $key => $portfolio) :
						?>
						<li class="portfolio">
							<?php
							global $attachment_id;

							$thumb_img = wp_get_attachment_image_src($portfolio->attachment_id, 'job-thumbnail');
							$full_img = wp_get_attachment_image_src($portfolio->attachment_id, 'full');
							printf('<a href="%s" title="%s" ><img src="%s" style="width:%dpx;height=%dpx;" /></a>', $full_img[0], $portfolio->caption, $thumb_img[0], 160, 120);
							?>
						</li>
						<?php
						endforeach;
						?>
					</ul>
				</div>
			</div>

			<?php if( current_user_can( EDIT_JOB, $post->ID ) ): ?>
			<span class="job-edit"><button class="jbp-button job-edit" onclick="window.location='<?php echo trailingslashit( get_permalink() ) . 'edit/'; ?>';">Edit</button></span>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {
		magnificPopupAttach(true);
		
		$('#project-min').text('<?php echo $project_min; ?>').formatCurrency({ region: '<?php echo $this->js_locale; ?>', roundToDecimalPlace: 0});
		$('#project-max').text('<?php echo $project_max; ?>').formatCurrency({ region: '<?php echo $this->js_locale; ?>', roundToDecimalPlace: 0});
		$('#project-dash').text('<?php echo $project_dash; ?>');
	
	});
</script>
