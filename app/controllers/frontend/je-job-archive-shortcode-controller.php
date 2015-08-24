<?php

/**
 * @author:Hoang Ngo
 */
class JE_Job_Archive_Shortcode_Controller extends IG_Request {
	public function __construct() {
		add_shortcode( 'jbp-job-archive-page', array( &$this, 'main' ) );
	}

	function main( $atts ) {
		je()->load_script( 'jobs' );
		$a = shortcode_atts( array(
			'post_per_page' => je()->settings()->job_per_page
		), $atts );

		//get jobs
		$post_per_page = $a['post_per_page'];

		$paged = get_query_var( 'je-paged' );

		$args      = array(
			'post_status'    => 'publish',
			'posts_per_page' => $post_per_page,
			'paged'          => $paged
		);
		$tax_query = array();
		//check does we on category page
		if ( is_tax( 'jbp_category' ) ) {
			$current_cat = get_term_by( 'slug', get_query_var( 'jbp_category' ), 'jbp_category' );
			$cat         = $current_cat->term_id;
			$tax_query[] = array(
				'taxonomy' => 'jbp_category',
				'field'    => 'term_id',
				'terms'    => $cat
			);
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
		if ( isset( $_GET['query'] ) ) {
			$search = $args['s'] = $_GET['query'];
		}
		$args['tax_query'] = $tax_query;
		$args              = apply_filters( 'jbp_job_search_params', $args );
		$instance          = je();

		$models      = JE_Job_Model::model()->all_with_condition( $args, $instance );
		$query       = je()->global['wp_query'];
		$total_pages = $query->max_num_pages;

		$css_class = array(
			'lg' => 'col-md-12 col-xs-12 col-sm-12',
			'md' => 'col-md-6 col-xs-12 col-sm-12',
			'xs' => 'col-md-3 col-xs-12 col-sm-12',
			'sm' => 'col-md-4 col-xs-12 col-sm-12'
		);
		$css_class = apply_filters( 'je_job_css_class', $css_class );

		$colors = array( 'jbp-yellow', 'jbp-mint', 'jbp-rose', 'jbp-blue', 'jbp-amber', 'jbp-grey' );
		if ( empty( $models ) ) {
			$chunks = array();
		} else {
			//we got the models, not chunk it
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
				$rule = array_filter( explode( ',', $rule ) );

				$chunk = array();
				foreach ( $rule as $val ) {
					$val  = trim( $val );
					$post = array_shift( $models );

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
			if ( count( $models ) ) {
				foreach ( array_chunk( $models, apply_filters( 'je_job_archive_default_grid', 3 ) ) as $row ) {
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
		}

		return $this->render( 'job-archive/main', array(
			'chunks'      => $chunks,
			'search'      => $search,
			'colors'      => $colors,
			'total_pages' => $total_pages
		), false );
	}
}