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

<section id="jobs-search" class="<?php echo $class; ?> group">
	<form class="search-form group" method="GET" action="<?php echo get_post_type_archive_link('jbp_job'); ?>" >
		<div class="job-search-wrap group">
			<ul class="job-search group">
				<li>
					<ul class="job-meta">
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
					</ul>
				</li>
				<li>
					<ul class="job-meta last">
						<li class="filler">
							<span>
								<input type="text" class="" id="searchbox-jobs" name="s" value="<?php echo $phrase; ?>" autocomplete="off" placeholder="<?php echo $text; ?>" />
							</span>
							<input type="submit" class="submit-jobs-search" value="" />
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</form>
</section>

