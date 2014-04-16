<?php
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
	<br><?php echo $content; ?>
</button>