<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

/**
* Passed $url, $content, $class and $this is $Jobs_Plus_Core global
*
*/

wp_enqueue_style('jobs-plus');
?>

<button type="button" class="jbp-button job-list-btn jbp-nav <?php echo $class; ?>" onclick="window.location.assign('<?php echo $url; ?>')">
	<div class="div-img">&nbsp;</div>
	<?php echo esc_html( $content ); ?>
</button>