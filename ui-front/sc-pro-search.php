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
				<ul class="pro-search group">
					<li>
						<span>
							<input type="text" class="pro-search-input" name="s" value="<?php echo $phrase; ?>" autocomplete="off" placeholder="<?php echo $text; ?>" />
						</span>
						<button type="submit" class="pro-submit-search" value="" >
							<img src="<?php echo $this->plugin_url . 'img/search.png'; ?>" alt="alt" title="title" />
						</button>
					</li>
				</ul>
		</div>
	</form>
</section>

