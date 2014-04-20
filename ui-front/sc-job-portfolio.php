<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

?>
<div class="job-portfolio group">
	<?php
	$portfolios = do_shortcode('[ct id="_ct_jbp_job_Portfolio" ]');
	$portfolios = empty($portfolios) ? new stdClass : (object)json_decode($portfolios);
	//var_dump($portfolios);
	//Add blanks on the end if not enough empties
	global $Jobs_Plus_Core;
	$max_gallery = $Jobs_Plus_Core->get_setting('job->max_gallery');
	/*
	while( count((array)$portfolios) < $max_gallery)
	{
	$count = count((array)$portfolios);
	$portfolios->$count = null; //Must keep key as an Associative
	}
	*/
	$count = 0;
	?>
	<div class="job-images">
		<ul class="job-content-editable">
			<?php
			foreach ( $portfolios as $key => $portfolio) :
			//var_dump(json_encode($portfolio));
			?>
			<li>
				<div class="editable portfolio"
					data-type="portfolio"
					data-name="<?php esc_attr_e("_ct_jbp_job_Portfolio[$key]"); ?>"
					data-value="<?php esc_attr_e( json_encode($portfolio) ); ?>"
					data-emptytext="<?php esc_attr_e('No Image Selected', JBP_TEXT_DOMAIN); ?>"
					data-original-title="<?php esc_attr_e(__('Select an Image', JBP_TEXT_DOMAIN) ); ?>"
					data-button-label="<?php _e('Choose an image', JBP_TEXT_DOMAIN); ?>"
					>
				</div>
			</li>
			<?php
			$count++;
			endforeach;
			?>
		</ul>
		<!-- Don't move link. Must be sibling of <ul> -->
		<a href="#" id="add-job-portfolio-link" class="job-content-command job-add show-on-edit">+Add</a>
		</div>
	</div>

	<script id="add-job-portfolio" type="text/template">
		<li class="new-portfolio">
		<div
		class="editable portfolio"
		data-type="portfolio"
		data-mode="popup"
		data-name="<?php esc_attr_e('_ct_jbp_job_Portfolio[]'); ?>"
		data-value=""
		data-original-title="<?php esc_attr_e(__('Select an Image', JBP_TEXT_DOMAIN) ); ?>"
		data-button-label="Choose an image"
		>
		</div>
		</li>
	</script>
