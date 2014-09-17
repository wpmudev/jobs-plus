<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 *
 * @category JobsExperts
 * @package  Shorcode
 *
 * @since    1.0.0
 */
class JobsExperts_Core_Shortcode_JobList extends JobsExperts_Shortcode {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_shortcode( 'jbp-job-archive-page', 'shortcode' );
	}

	function load_scripts() {
		wp_enqueue_style( 'jobs-list-shortcode' );
	}

	public function shortcode( $atts ) {
		$this->load_scripts();
		//get plugin instance
		$plugin      = JobsExperts_Plugin::instance();
		$page_module = $plugin->page_module();

		$a = shortcode_atts( array(
			'post_per_page' => $plugin->settings()->job_per_page
		), $atts );

		//get jobs
		$post_per_page = $a['post_per_page'];

		$paged = get_query_var( 'paged' );

		$args = array(
			'post_status'    => 'publish',
			'posts_per_page' => $post_per_page,
			'paged'          => $paged
		);


		$tax_query = array();
		//check does we on category page
		if ( is_tax( 'jbp_category' ) ) {
			$current_cat = get_term_by( 'slug', get_query_var( 'jbp_category' ), 'jbp_category' );
			$cat         = $current_cat->term_id;
		}
		if ( is_tax( 'jbp_skills_tag' ) ) {
			$current_skill = get_term_by( 'slug', get_query_var( 'jbp_skills_tag' ), 'jbp_skills_tag' );
			$tax_query[]   = array(
				'taxonomy' => 'jbp_skills_tag',
				'field'    => 'term_id',
				'terms'    => $current_skill->term_id
			);
		}

		$search = '';
		if ( isset( $_GET['s'] ) ) {
			$search = $args['s'] = $_GET['s'];

		}

		$args = apply_filters( 'jbp_job_search_params', $args );
		$data = JobsExperts_Core_Models_Job::instance()->get_all( $args );

		$jobs = $data['data'];

		$total_pages = $data['total_pages'];
		//prepare styles
		$css_class = array(
			'lg' => 'col-md-12 col-xs-12 col-sm-12',
			'md' => 'col-md-6 col-xs-12 col-sm-12',
			'xs' => 'col-md-3 col-xs-12 col-sm-12',
			'sm' => 'col-md-4 col-xs-12 col-sm-12'
		);

		$colors = array( 'jbp-yellow', 'jbp-mint', 'jbp-rose', 'jbp-blue', 'jbp-amber', 'jbp-grey' );
		ob_start();
		?>
		<div class="hn-container">
			<div class="job-search">
				<form method="get"
				      action="<?php echo is_singular() ? get_permalink( get_the_ID() ) : get_post_type_archive_link( 'jbp_job' ) ?>">
					<div class="search input-group input-group-lg has-feedback" role="search" id="mySearch">
						<input style="border-radius: 0" name="s" value="<?php echo $search ?>" type="search"
						       class="form-control job-query"
						       placeholder="<?php echo __( sprintf( 'Search For %s', $plugin->get_job_type()->labels->name ), JBP_TEXT_DOMAIN ) ?>"/>
<span class="input-group-btn">
    <button style="border-radius: 0" class="btn btn-default" type="submit">
	    <span class="glyphicon glyphicon-search"></span>
	    <span class="sr-only">Search</span>
    </button>
  </span>
					</div>
					<?php do_action( 'jbp_job_listing_after_search_form' ) ?>
				</form>
			</div>


			<?php if ( empty( $jobs ) ): ?>
				<h2><?php printf( __( 'No %s Found', JBP_TEXT_DOMAIN ), $plugin->get_job_type()->labels->name ); ?></h2>
			<?php else: ?>
				<div class="jbp-job-list">
					<?php
					//prepare for layout, we will create the jobs data at chunk
					//the idea is, we will set fix of the grid on layout, seperate the array into chunk, each chunk is a row
					//so it will supported by css and responsive
					$grid_rules = array(
						0 => 'lg',
						1 => 'md,md',
						2 => 'lg',
						3 => 'md,md'
					);
					$grid_rules = apply_filters( 'jbp_jobs_list_layout', $grid_rules );
					$chunks     = array();
					foreach ( $grid_rules as $rule ) {
						$rule  = explode( ',', $rule );
						$chunk = array();
						foreach ( $rule as $val ) {
							$val  = trim( $val );
							$post = array_shift( $jobs );
							if ( is_object( $post ) ) {
								$chunk[] = array(
									'class'       => $css_class[ $val ],
									'item'        => $post,
									'text_length' => count( $rule )
								);
							} else {
								break;
							}
						}
						$chunks[] = $chunk;
					}
					//if still have items, use default chunk
					if ( count( $jobs ) ) {
						foreach ( array_chunk( $jobs, 3 ) as $row ) {
							//ok now, we have large chunk each is 3 items
							$chunk = array();
							foreach ( $row as $r ) {
								$chunk[] = array(
									'class'       => $css_class['sm'],
									'item'        => $r,
									'text_length' => 3
								);
							}
							$chunks[] = $chunk;
						}
					}
					$template = new JobsExperts_Core_Views_JobList( array(
						'chunks'      => $chunks,
						'colors'      => $colors,
						'total_pages' => $total_pages
					) );
					$template->render();
					?>
				</div>
			<?php endif; ?>
			<div style="clear: both"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}

new JobsExperts_Core_Shortcode_JobList;