<?php

/**
 * @author:Hoang Ngo
 */
class JE_Expert_Archive_Shortcode_Controller extends IG_Request {
	public function __construct() {
		add_shortcode( 'jbp-expert-archive-page', array( &$this, 'main' ) );
	}

	function main( $atts ) {
		je()->load_script( 'experts' );
		$a = shortcode_atts( array(
			'post_per_page' => je()->settings()->expert_per_page
		), $atts );

		//get jobs
		$post_per_page = $a['post_per_page'];
		$paged         = get_query_var( 'je-paged' );
		$args          = array(
			'post_status'    => 'publish',
			'posts_per_page' => $post_per_page,
			'paged'          => $paged
		);

		$search = '';
		if ( isset( $_GET['query'] ) ) {
			$search = $args['s'] = $_GET['query'];
		}

		$args = apply_filters( 'jbp_expert_search_params', $args );

		$instance    = je();
		$models      = JE_Expert_Model::model()->all_with_condition( $args, $instance );
		$total_pages = je()->global['wp_query']->max_num_pages;
		$css_class   = array(
			'lg' => 'col-md-12 col-sx-12 col-sm-12',
			'md' => 'col-md-6 col-sx-6 col-sm-6',
			'xs' => 'col-md-3 col-sx-12 col-sm-12',
			'sm' => 'col-md-4 col-sx-12 col-sm-4'
		);
		$css_class   = apply_filters( 'je_expert_css_classes', $css_class );
		$grid_rules  = array(
			0 => 'sm,sm,sm',
			1 => 'sm,sm,sm',
		);
		$grid_rules  = apply_filters( 'jbp_expert_list_layout', $grid_rules );
		$chunks      = array();
		foreach ( $grid_rules as $rule ) {
			$rule  = explode( ',', $rule );
			$rule  = array_filter( $rule );
			$chunk = array();
			foreach ( $rule as $val ) {
				$val  = trim( $val );
				$post = array_shift( $models );
				if ( is_object( $post ) ) {
					$chunk[] = array(
						'class'       => $css_class[ $val ],
						'item'        => $post,
						'text_length' => 1
					);
				} else {
					break;
				}
			}
			$chunks[] = $chunk;
		}
		//if still have items, use default chunk
		if ( count( $models ) ) {
			foreach ( array_chunk( $models, 4 ) as $row ) {
				//ok now, we have large chunk each is 3 items
				$chunk = array();
				foreach ( $row as $r ) {
					$chunk[] = array(
						'class'       => $css_class['xs'],
						'item'        => $r,
						'text_length' => 1.6
					);
				}
				$chunks[] = $chunk;
			}
		}

		return $this->render( 'expert-archive/main', array(
			'search'      => $search,
			'chunks'      => $chunks,
			'total_pages' => $total_pages
		), false );
	}
}