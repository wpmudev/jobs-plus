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
 * PostType module.
 *
 * This module use for register custom content
 *
 * @category JobsExperts
 * @package  Module
 *
 * @since    1.0.0
 */
class JobsExperts_Core_CustomContent extends JobsExperts_Framework_Module {
	const NAME = __CLASS__;


	/**
	 * The jobs custom post type object
	 *
	 * @since 1.0.0
	 *
	 */
	public $jobs;

	/**
	 * The experts custom post type object
	 *
	 * @since 1.0.0
	 *
	 */
	public $experts;


	public function __construct() {
		$this->_add_action( 'init', 'load_custom_post_types', 1 );
		$this->_add_action( 'init', 'load_custom_taxonomies', 1 );
		$this->_add_action( 'init', 'custom_post_status', 1 );

		//load the post type object data
		$this->jobs    = get_post_type_object( 'jbp_job' );
		$this->experts = get_post_type_object( 'jbp_pro' );
	}

	function custom_post_status() {
		if ( is_admin() ) {
			register_post_status( 'virtual', array(
				'label'                     => __( 'Virtual', JBP_TEXT_DOMAIN ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>' ),
			) );
		} else {
			//we allowed this status available on frontend to make it compatibility with other themes
			register_post_status( 'virtual', array(
				'label'                     => __( 'Virtual', JBP_TEXT_DOMAIN ),
				'public'                    => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>' ),
			) );
		}
	}

	/**
	 * Creates the initial custom post types if they don't already exist.
	 *
	 */
	function load_custom_post_types() {
		$plugin = JobsExperts_Plugin::instance();
		/**
		 * Create jbp_job post type
		 */
		if ( ! post_type_exists( 'jbp_job' ) ) {
			$jbp_job = array(
				'labels'           => array(
					'name'                => 'Jobs',
					'singular_name'       => 'Job',
					'add_new'             => 'Add New',
					'add_new_item'        => 'Add New Job',
					'edit_item'           => 'Edit Job',
					'new_item'            => 'New Job',
					'view_item'           => 'View Job',
					'search_items'        => 'Search Jobs',
					'not_found'           => 'No jobs found',
					'not_found_in_trash'  => 'No jobs found in Trash',
					'custom_fields_block' => 'Jobs Fields',
				),
				'supports'         => array(
					'title'         => 'title',
					'editor'        => 'editor',
					'author'        => 'author',
					'thumbnail'     => 'thumbnail',
					'excerpt'       => false,
					'custom_fields' => 'custom-fields',
					'revisions'     => 'revisions',
					'page_attributes' => 'page-attributes',
				),
				'supports_reg_tax' => array(
					'category' => '',
					'post_tag' => '',
				),
				'capability_type'  => 'post',
				'map_meta_cap'     => true,
				'description'      => 'Job offerings',
				'menu_position'    => '',
				'public'           => true,
				'hierarchical'     => true,
				'has_archive'      => 'jobs',
				'rewrite'          => array(
					'slug'       => 'job',
					'with_front' => false,
					'feeds'      => true,
					'pages'      => true,
					'ep_mask'    => 4096,
				),
				'query_var'        => true,
				'can_export'       => true,
				'cf_columns'       => NULL,
				'menu_icon'        => $plugin->_module_url . 'assets/image/backend/icons/16px/16px_Jobs_Bright.svg',
			);

			register_post_type( 'jbp_job', $jbp_job );

		} //jbp_job post type complete

		/**
		 * Create jbp_pro post type
		 */
		if ( ! post_type_exists( 'jbp_pro' ) ) {

			$jbp_pro = array(
				'labels'           =>
					array(
						'name'                => 'Experts',
						'singular_name'       => 'Expert',
						'add_new'             => 'Add New',
						'add_new_item'        => 'Add New Expert',
						'edit_item'           => 'Edit Expert',
						'new_item'            => 'New Expert',
						'view_item'           => 'View Expert',
						'search_items'        => 'Search Expert',
						'not_found'           => 'No experts found',
						'not_found_in_trash'  => 'No experts found in Trash',
						'custom_fields_block' => 'Expert fields',
					),
				'supports'         =>
					array(
						'title'     => 'title',
						'editor'    => 'editor',
						'author'    => 'author',
						'thumbnail' => 'thumbnail',
						'excerpt'   => false,
						'revisions' => 'revisions',
                        'post-formats'=>'post-formats'
                        //'page_attributes' => 'page-attributes',
					),
				'supports_reg_tax' =>
					array(
						'category' => '',
						'post_tag' => '',
					),
				'capability_type'  => 'post',
				'map_meta_cap'     => true,
				'description'      => 'Expert and extended profile',
				'menu_position'    => '',
				'public'           => true,
				'hierarchical'     => true,
				'has_archive'      => 'experts',
				'rewrite'          =>
					array(
						'slug'       => 'expert',
						'with_front' => false,
						'feeds'      => true,
						'pages'      => true,
						'ep_mask'    => 4096,
					),
				'query_var'        => true,
				'can_export'       => true,
				'cf_columns'       => NULL,
				'menu_icon'        => $plugin->_module_url . 'assets/image/backend/icons/16px/16px_Expert_Bright.svg',
			);
			register_post_type( 'jbp_pro', $jbp_pro );
		} //jbp_pro post type complete

	}

	/**
	 * Create the default taxonomies
	 *
	 */
	function load_custom_taxonomies() {

		if ( ! taxonomy_exists( 'jbp_category' ) ) {
			$jbp_category = array(
				'object_type' => array(
					0 => 'jbp_job',
				),
				'hide_type'   => array(
					0 => 'jbp_job',
				),
				'args'        => array(
					'labels'            => array(
						'name'                  => 'Job Categories',
						'singular_name'         => 'Job Category',
						'add_new_item'          => 'Add New Job Categories',
						'new_item_name'         => 'New Job Category',
						'edit_item'             => 'Edit Job Category',
						'update_item'           => 'Update Job Category',
						'popular_items'         => 'Search Job Categories',
						'all_items'             => 'All Job Categories',
						'parent_item'           => 'Job Categories',
						'parent_item_colon'     => 'Job Categories: ',
						'add_or_remove_items'   => 'Add or Remove Job Categories',
						'choose_from_most_used' => 'All Job Categories',
					),
					'public'            => true,
					'show_admin_column' => NULL,
					'hierarchical'      => true,
					'rewrite'           => array(
						'slug'         => 'jobs-category',
						'with_front'   => true,
						'hierarchical' => false,
						'ep_mask'      => 0,
					),
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'manage_categories',
						'delete_terms' => 'manage_categories',
						//'assign_terms' => 'edit_jobs',
					),
				),

			);

			register_taxonomy( 'jbp_category', array( 'jbp_job' ), $jbp_category['args'] );
		}

		if ( ! taxonomy_exists( 'jbp_skills_tag' ) ) {
			$jbp_tag = array(
				'object_type' => array(
					0 => 'jbp_job',
				),
				'hide_type'   => array(
					0 => 'jbp_job',
				),
				'args'        => array(
					'labels'            => array(
						'name'                  => 'Job Skills Tags',
						'singular_name'         => 'Job Skills Tag',
						'add_new_item'          => 'Add New Job Skills Tag',
						'new_item_name'         => 'New Job Skills Tag',
						'edit_item'             => 'Edit Job Skills Tag',
						'update_item'           => 'Update Job Skills Tag',
						'search_items'          => 'Search Job Skills Tags',
						'popular_items'         => 'Popular Job Skills Tags',
						'all_items'             => 'All Job Skills Tags',
						'parent_item_colon'     => 'Jobs tags:',
						'add_or_remove_items'   => 'Add or Remove Job Skills Tags',
						'choose_from_most_used' => 'All Job Skills Tags',
					),
					'public'            => true,
					'hierarchical'      => false,
					'rewrite'           =>
						array(
							'slug'         => 'job-skills',
							'with_front'   => true,
							'hierarchical' => false,
							'ep_mask'      => 0,
						),
					'query_var'         => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'manage_categories',
						'delete_terms' => 'manage_categories',
						//'assign_terms' => 'edit_jobs',
					),
				),
			);

			register_taxonomy( 'jbp_skills_tag', array( 'jbp_job' ), $jbp_tag['args'] );
		}

	}
}