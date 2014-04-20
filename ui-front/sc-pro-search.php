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
	<form class="search-form" method="GET" action="<?php echo get_post_type_archive_link('jbp_pro'); ?>" >
		<div class="pro-search-wrap">
			<div class="pro-meta group">
				<ul class="group">
					<li class="filler">
						<span>
							<input type="text" class="" id="searchbox-pros" name="s" value="<?php echo $phrase; ?>" autocomplete="off" placeholder="<?php echo $text; ?>" />
						</span>
					<input type="submit" class="submit-pros-search" />
					</li>
				</ul>
			</div>
		</div>
	</form>
</section>

