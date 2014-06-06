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

//do they need to register?
if($register_url) {
	$destination = sprintf('data-mfp-src="%s"', $register_url);
	$class .= ' mfp-ajax'; // add ajax popup link
} else {
	$destination = sprintf('onclick="window.location.assign(\'%s\');"', $url);
}
?>

<button type="button" class="jbp-button job-post-btn jbp-register-popup <?php echo $class; ?>" <?php echo $destination; ?>>
	<?php if($img): ?>
	<img src="<?php echo $this->plugin_url . 'img/menu-icon-post.png'; ?>" alt="<?php echo esc_attr( $content ); ?>" title="<?php echo esc_attr( $content ); ?>" />
	<?php endif; ?>
	<?php echo esc_html( $content ); ?>
</button>

<?php if($register_url): ?>
<script type="text/javascript">jQuery(document).ready( function($){ $('.jbp-register-popup').magnificPopup({closeBtnInside: true}); } );</script>
<?php endif; ?>