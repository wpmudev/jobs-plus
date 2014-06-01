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

<button type="button" class="jbp-button job-browse-btn <?php echo $class; ?>" onclick="window.location.assign('<?php echo $url; ?>')">
	<?php if($img): ?>
	<img src="<?php echo $this->plugin_url . 'img/menu-icon-find.png'; ?>" alt="<?php echo esc_attr( $content ); ?>" title="<?php echo esc_attr( $content ); ?>" />
	<?php endif; ?>
	<?php echo esc_html($content); ?>
</button>