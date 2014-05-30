<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

$phrase = (empty($_GET['s']) ) ? '' : $_GET['s'];

$sort_latest = (empty($_GET['prj-sort']) || $_GET['prj-sort'] == 'latest' ? 'active-sort' : 'inactive-sort');
$sort_ending = (!empty($_GET['prj-sort']) && $_GET['prj-sort'] == 'ending' ? 'active-sort' : 'inactive-sort');
?>

<section class="jobs-search-form <?php echo $class; ?> group">
	<form class="search-form group" method="GET" action="<?php echo get_post_type_archive_link('jbp_job'); ?>" >
		<div class="job-search-wrap group"  data-eq-pts=" break: 560" >
			<ul class="job-search group">
				<li>
					<span class="divider">Sort by</span>
				</li>
				<li>
					<span class="sort-by-latest <?php echo $sort_latest; ?> divider">
						<a href="<?php echo add_query_arg( array('prj_sort' => 'latest', ), get_post_type_archive_link('jbp_job') );?>" ><?php esc_html_e('Latest', JPB_TEXT_DOMAIN ); ?></a>
					</span>
				</li>
				<li class="right-border">
					<span class="sort-by-end <?php echo $sort_ending; ?>">
						<a href="<?php echo add_query_arg( array('prj_sort' => 'ending', ), get_post_type_archive_link('jbp_job') );?>" ><?php esc_html_e('About to End', JPB_TEXT_DOMAIN ); ?></a>
					</span>
				</li>
				<li>
						<input type="text" class="job-search-input" name="s" value="<?php echo esc_attr($phrase); ?>" autocomplete="off" placeholder="<?php echo $text; ?>" />
					<button type="submit" class="job-submit-search" value="">
						<img src="<?php echo $this->plugin_url . 'img/search.png'; ?>" alt="" title="title" />
						</button>
				</li>
			</ul>
		</div>
	</form>
</section>

