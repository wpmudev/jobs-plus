<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $CustomPress_Core, $wp_query, $post;

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
$project_budget = empty( $project_min) ? '' : $this->get_setting('general->currency', __('$', JBP_TEXT_DOMAIN) ) . $project_min;
$project_budget .= !empty( $project_min ) && !empty( $project_max ) ? ' - ' : '';
$project_budget .= empty( $project_max) ? '' : $this->get_setting('general->currency', __('$', JBP_TEXT_DOMAIN) ) . $project_max;
$project_budget = ($project_budget == '' ? 'N/A' : $project_budget);


?>
<div class="job-single-wrapper">
	<?php if(dynamic_sidebar('job-widget') ) : else: endif; ?>
	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>


	<div class="job-meta group">
		
		<ul>
			<li><span class="meta-label"><?php _e('Job Budget', JBP_TEXT_DOMAIN);?><br /></span><span class="meta-red"><?php echo $project_budget;?></span></li>
			<li><span class="meta-label"><?php _e('This job open for', JBP_TEXT_DOMAIN);?><br /></span><span class="meta-green"><?php echo days_hours( get_post_meta(get_the_ID(), JBP_JOB_EXPIRES_KEY, true) );?></span></li>
			<li class="border"><span class="meta-label"><?php _e('Must be complete by', JBP_TEXT_DOMAIN);?><br /></span><span class="meta-red"><?php echo do_shortcode('[ct id="_ct_jbp_job_Due" ]'); ?></span></li>
			<li>
				<?php 
			if( get_post_meta( get_the_ID(), JBP_JOB_EXPIRES_KEY, true) > time() ):
				echo do_shortcode('[jbp-job-contact-btn text="Contact" class="job-contact"]');
			endif; 
			?>
			</li>
		</ul>
<?php //the_author_posts_link(); ?>
		<div style="clear: both"></div>
	</div>

	<div id="post-full-<?php the_ID(); ?>" <?php post_class(); ?> >

		<div class="job-item-full">
			<div class="job-top">
				<span class="job-cat"><?php the_terms(get_the_id(), 'jbp_category', __('Categories: ', JBP_TEXT_DOMAIN), ', ', ''); ?>&nbsp;</span>
				<span class="job-date"><?php _e('Posted: ', JBP_TEXT_DOMAIN ); the_date(); ?></span>
			</div>
			<?php the_content(); ?>

			<div class="job-skills">
				<?php echo get_the_term_list(get_the_ID(), 'jbp_skills_tag', __('<p>You will need to have these skills:', JBP_TEXT_DOMAIN) . '</p><ul><li>', '</li><li>', '</li></ul>')?>
			</div>

			<div class="job-portfolio group">
				<span><?php _e('Examples:', JBP_TEXT_DOMAIN); ?></span>
				<?php
				$portfolios = do_shortcode('[ct id="_ct_jbp_job_Portfolio"]');
				$portfolios = empty($portfolios) ? new stdClass : (object)json_decode($portfolios);
				?>
				<div class="job-images">
					<ul>
						<?php
						foreach ( $portfolios as $key => $portfolio) :
						?>
						<li class="portfolio">
							<?php
							global $attachment_id;
							//Set global so custom upload directory is found
							$attachment_id = $portfolio->attachment_id;
							
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
			<span class="job-edit"><button class="jobs_button job-edit" onclick="window.location='<?php echo trailingslashit( get_permalink() ) . 'edit/'; ?>';">Edit</button></span>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {
		magnificPopupAttach(true);
	});
</script>
