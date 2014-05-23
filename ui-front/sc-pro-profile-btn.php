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

<button type="button" class="jbp-button pro-profile-btn <?php echo $class; ?>" onclick="window.location.assign('<? echo $url; ?>')">
<img src="<?php echo $this->plugin_url . 'img/menu-icon-profile.png'; ?>" alt="<?php esc_attr_e( $content ); ?>" title="<?php esc_attr_e( $content ); ?>" /><br />
	<?php esc_html_e( $content ); ?>
</button>