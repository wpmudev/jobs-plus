<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

wp_enqueue_style('jobs-plus-custom');
wp_enqueue_script('element-query');

?>

<div class="jbp-landing-page group" data-eq-pts="break: 680">
	<div class="poster-left">[jbp-job-poster count="5"]</div>
	<div class="poster-right">[jbp-expert-poster]</div>
</div>
