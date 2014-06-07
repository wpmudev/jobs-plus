<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

wp_enqueue_style('magnific-popup');
wp_enqueue_style('jobs-plus-custom');


?>
<div class="wrap">
	<?php $this->render_tabs('job'); ?>
	<h2><?php esc_html_e('Getting Started with Jobs+', JBP_TEXT_DOMAIN);?></h2>


</div>
