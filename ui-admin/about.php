<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

?>
<div class="wrap">
	<?php screen_icon('jobs-plus'); ?>
	<h2><?php echo esc_html__('Getting Started with Jobs+', JBP_TEXT_DOMAIN);?></h2>
	<?php $this->render_tabs('job'); ?>
</div>


