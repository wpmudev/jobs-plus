<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

$phrase = (empty($_GET['s']) ) ? '' : $_GET['s'];

?>

<section id="pros-search" class="<?php echo $class; ?>">
	<form class="search-form" method="GET" action="<?php echo esc_attr(get_post_type_archive_link('jbp_pro') ); ?>" >
		<div class="pro-search-wrap">
				<ul class="pro-search group">
					<li>
						<span>
							<input type="text" class="pro-search-input" name="s" value="<?php echo esc_attr($phrase); ?>" autocomplete="off" placeholder="<?php echo esc_attr( $text ); ?>" />
						</span>
						<button type="submit" class="pro-submit-search <?php echo $class; ?>" value="" >
							<div class="div-img">&nbsp;</div>
						</button>
					</li>
				</ul>
		</div>
	</form>
</section>
