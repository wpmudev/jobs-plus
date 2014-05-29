<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs+
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
?>
<div class="pro-social">
	<?php
	$socials = do_shortcode('[ct id="_ct_jbp_pro_Social"]');
	$socials = empty($socials) ? new stdClass : (object)json_decode($socials);
	//Add blanks on the end if not enough empties
	$max_socials = 4;
	//	while( count((array)$socials) < $max_socials)
	//	{
	//		$count = count((array)$socials);
	//		$socials->$count = null; //Must keep key as an Associative
	//	}

	$count = 0;
	
	?>
	<ul class="group"><?php foreach ( $socials as $key => $social) : ?><li>
			<div
				class="editable"
				data-type="social"
				data-name="<?php echo "_ct_jbp_pro_Social[$key]"; ?>"
				data-emptytext="<?php _e('Enter a Social link', JBP_TEXT_DOMAIN); ?>"
				data-value="<?php esc_attr_e( json_encode($social) ); ?>"
				data-original-title="<?php esc_attr_e(__('Select a Social Icon and enter your url', JBP_TEXT_DOMAIN) ); ?>">
			</div>
		</li><?php endforeach; ?></ul>
	<a href="#" id="add-social-link" class="pro-content-command pro-add show-on-edit"><?php esc_html_e('+Add an Icon', JBP_TEXT_DOMAIN ); ?></a>
</div>

<script id="add-social" type="text/template">
	<li>
	<div
	class="editable social"
	data-type="social"
	data-name="_ct_jbp_pro_Social[]"
	data-emptytext="<?php _e('Enter a Social link', JBP_TEXT_DOMAIN); ?>"
	data-value=""
	data-original-title="<?php esc_attr_e(__('Select a Social Icon and enter your url', JBP_TEXT_DOMAIN) ); ?>">
</div>
</li>
</script>

