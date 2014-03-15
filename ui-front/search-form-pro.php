<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

$search_prompt = __('Search pros for', JBP_TEXT_DOMAIN);
$phrase = (empty($_GET['s']) ) ? '' : $_GET['s'];

?>
<div class="pro-search">
	<form method="GET" action="<?php echo get_post_type_archive_link('jbp_pro'); ?>" >
		<input type="text" class="textf" name="s" value="<?php esc_attr_e($phrase); ?>" placeholder="<?php esc_attr_e( $search_prompt );?>"/>
		<button type="submit" class="sbutton"><?php _e('Search', JBP_TEXT_DOMAIN); ?></button>
	</form>
</div>
