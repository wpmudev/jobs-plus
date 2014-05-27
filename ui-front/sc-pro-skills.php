<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

?>
<div class="pro-skills group">
	<?php
	$skills = do_shortcode('[ct id="_ct_jbp_pro_Skills"]');
	$skills = empty($skills) ? new stdClass : (object)json_decode($skills);
	//Add blanks on the end if not enough empties
	$max_skills = 4;
	/*while( count((array)$skills) < $max_skills)
	{
	$count = count((array)$skills);
	$skills->$count = null; //Must keep key as an Associative
	}*/
	$count = 0;
	?>
	<ul class="group">
		<?php foreach ( $skills as $key => $skill) : ?>
		<li>
			<div
				class="editable skill"
				data-type="skill"
				data-mode="popup"
				data-name="<?php esc_attr_e("_ct_jbp_pro_Skills[$key]"); ?>"
				data-emptytext="<?php esc_attr_e('What is your skill?', JBP_TEXT_DOMAIN); ?>"
				data-value="<?php esc_attr_e( json_encode($skill) ); ?>"
				data-original-title="<?php esc_attr_e(__('Skills', JBP_TEXT_DOMAIN) ); ?>">
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
	<a href="#" id="add-skill-link" class="pro-content-command pro-add show-on-edit"><?php esc_html_e('+Add', JBP_TEXT_DOMAIN); ?></a>
	<br />
</div>

<script id="add-skill" type="text/template">
	<li>
	<div
	class="editable skill"
	data-type="skill"
	data-mode="popup"
	data-name="<?php esc_attr_e('_ct_jbp_pro_Skills[]'); ?>"
	data-value=""
	data-emptytext="<?php esc_attr_e('What is your skill?', JBP_TEXT_DOMAIN); ?>"
	data-original-title="<?php esc_attr_e(__('Skill', JBP_TEXT_DOMAIN) ); ?>">
	</div>
	</li>
</script>
