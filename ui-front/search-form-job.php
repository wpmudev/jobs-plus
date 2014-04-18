<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

$search_prompt = __('Search jobs for', JBP_TEXT_DOMAIN);
$phrase = (empty($_GET['s']) ) ? '' : $_GET['s'];

$sort_latest = (empty($_GET['prj-sort']) || $_GET['prj-sort'] == 'latest' ? 'active-sort' : 'inactive-sort');
$sort_ending = (!empty($_GET['prj-sort']) && $_GET['prj-sort'] == 'ending' ? 'active-sort' : 'inactive-sort');

?>

<section id="jobs-search" class="big-search group">
	<form class="search-form" method="GET" action="<?php echo get_post_type_archive_link('jbp_job'); ?>" >
		<div class="job-search-wrap">
			<div class="job-meta group">
				<ul class="group">
					<li>
						<span>Sort by</span>
					</li>
					<li>
						<span class="sort-by-latest <?php echo $sort_latest; ?>">
							<a href="<?php echo add_query_arg( array('prj_sort' => 'latest', ), get_post_type_archive_link('jbp_job') );?>" ><?php _e('Latest', JPB_TEXT_DOMAIN ); ?></a>
						</span>
					</li>
					<li>
						<span class="sort-by-end <?php echo $sort_ending; ?>">
							<a href="<?php echo add_query_arg( array('prj_sort' => 'ending', ), get_post_type_archive_link('jbp_job') );?>" ><?php _e('About to End', JPB_TEXT_DOMAIN ); ?></a>
						</span>
					</li>
					<li class="filler">
						<span>
							<input type="text" class="" id="searchbox-jobs" name="s" autocomplete="off" placeholder="Search for jobs" />
						</span>
					<input type="submit" class="submit-jobs-search" value=" " />
					</li>
				</ul>
			</div>
		</div>
	</form>

</section>
<!--
<div class="job-search">
<form method="GET" action="<?php echo get_post_type_archive_link('jbp_job'); ?>" >
<input type="text" class="textf" name="s" value="<?php esc_attr_e($phrase); ?>" placeholder="<?php esc_attr_e( $search_prompt );?>"/>
<button type="submit" value="Search" class="sbutton"><?php _e('Search', JBP_TEXT_DOMAIN); ?></button>
</form>
</div>
-->