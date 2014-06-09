<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

/**
* Custom post types
* jbp_job
* jbp_pro
*/

/**
* Custom taxonomies
* jbp_category
* jbp_tag
* jbp_skills_tag
*/


if ( !class_exists('Jobs_Plus_Core_Data') ):

class Jobs_Plus_Core_Data{

	public $plugin_dir    = JBP_PLUGIN_DIR;
	public $plugin_url    = JBP_PLUGIN_URL;
	public $text_domain   = JBP_TEXT_DOMAIN;
	public $settings_name = JBP_SETTINGS_NAME;
	public $allow_network = false;


	function __construct(){
		add_action('init', array(&$this, 'load_custom_post_types'), 0); //zero priority because need data before setting it.
		add_action('init', array(&$this, 'load_custom_taxonomies'), 0);
		add_action('init', array(&$this, 'load_custom_fields'), 0);
		add_action('init', array(&$this, 'load_default_settings'), 0);
	}

	/**
	* Creates the initial custom post types if they don't already exist.
	*
	*/
	function load_custom_post_types(){

		/**
		* Create jbp_job post type
		*/
		if (! post_type_exists('jbp_job') ) {

			$jbp_job = array (
			'labels' => array (
			'name' => 'Jobs',
			'singular_name' => 'Job',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Job',
			'edit_item' => 'Edit Job',
			'new_item' => 'New Job',
			'view_item' => 'View Job',
			'search_items' => 'Search Jobs',
			'not_found' => 'No jobs found',
			'not_found_in_trash' => 'No jobs found in Trash',
			'custom_fields_block' => 'Jobs Fields',
			),
			'supports' => array (
			'title' => 'title',
			'editor' => 'editor',
			'author' => 'author',
			'thumbnail' => 'thumbnail',
			'excerpt' => 'excerpt',
			'custom_fields' => 'custom-fields',
			'revisions' => 'revisions',
			'page_attributes' => 'page-attributes',
			),
			'supports_reg_tax' => array (
			'category' => '',
			'post_tag' => '',
			),
			'capability_type' => 'job',
			'map_meta_cap' => true,
			'description' => 'Job offerings',
			'menu_position' => '',
			'public' => true,
			'hierarchical' => true,
			'has_archive' => 'jobs',
			'rewrite' => array (
			'slug' => 'job',
			'with_front' => true,
			'feeds' => true,
			'pages' => true,
			'ep_mask' => 4096,
			),
			'query_var' => true,
			'can_export' => true,
			'cf_columns' => NULL,
			'menu_icon' => $this->plugin_url . 'icons/16px/16px_Jobs_Bright.svg',
			);

			//Update custom post types
			if( $this->allow_network && is_network_admin()){
				$ct_custom_post_types = get_site_option( 'ct_custom_post_types' );
				$ct_custom_post_types['jbp_job'] = $jbp_job;
				update_site_option( 'ct_custom_post_types', $ct_custom_post_types );
			} else {
				$ct_custom_post_types = get_option( 'ct_custom_post_types' );
				$ct_custom_post_types['jbp_job'] = $jbp_job;
				update_option( 'ct_custom_post_types', $ct_custom_post_types );
			}

			// Update post types and delete tmp options
			flush_network_rewrite_rules();
		} //jbp_job post type complete

		/**
		* Create jbp_pro post type
		*/
		if (! post_type_exists('jbp_pro') ) {

			$jbp_pro = array (
			'labels' =>
			array (
			'name' => 'Experts',
			'singular_name' => 'Expert',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Expert',
			'edit_item' => 'Edit Expert',
			'new_item' => 'New Expert',
			'view_item' => 'View Expert',
			'search_items' => 'Search Expert',
			'not_found' => 'No experts found',
			'not_found_in_trash' => 'No experts found in Trash',
			'custom_fields_block' => 'Expert fields',
			),
			'supports' =>
			array (
			'title' => 'title',
			'editor' => 'editor',
			'author' => 'author',
			'thumbnail' => 'thumbnail',
			'excerpt' => 'excerpt',
			'revisions' => 'revisions',
			),
			'supports_reg_tax' =>
			array (
			'category' => '',
			'post_tag' => '',
			),
			'capability_type' => 'expert',
			'map_meta_cap' => true,
			'description' => 'Expert and extended profile',
			'menu_position' => '',
			'public' => true,
			'hierarchical' => true,
			'has_archive' => 'experts',
			'rewrite' =>
			array (
			'slug' => 'expert',
			'with_front' => true,
			'feeds' => true,
			'pages' => true,
			'ep_mask' => 4096,
			),
			'query_var' => true,
			'can_export' => true,
			'cf_columns' => NULL,
			'menu_icon' => $this->plugin_url . 'icons/16px/16px_Expert_Bright.svg',
			);

			//Update custom post types
			if( $this->allow_network && is_network_admin()){
				$ct_custom_post_types = get_site_option( 'ct_custom_post_types' );
				$ct_custom_post_types['jbp_pro'] = $jbp_pro;
				update_site_option( 'ct_custom_post_types', $ct_custom_post_types );
			} else {
				$ct_custom_post_types = get_option( 'ct_custom_post_types' );
				$ct_custom_post_types['jbp_pro'] = $jbp_pro;
				update_option( 'ct_custom_post_types', $ct_custom_post_types );
			}

			// Update post types and delete tmp options
			flush_network_rewrite_rules();
		} //jbp_pro post type complete

		//Custompress specfic
		if(is_multisite()){
			update_site_option( 'allow_per_site_content_types', true );
			update_site_option( 'display_network_content_types', true );
		}

	}

	/**
	* Create the default taxonomies
	*
	*/
	function load_custom_taxonomies(){

		if(! taxonomy_exists('jbp_category') ) {
			$jbp_category =
			array (
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'args' =>
			array (
			'labels' =>
			array (
			'name' => 'Job Categories',
			'singular_name' => 'Job Category',
			'add_new_item' => 'Add New Job Categories',
			'new_item_name' => 'New Job Category',
			'edit_item' => 'Edit Job Category',
			'update_item' => 'Update Job Category',
			'popular_items' => 'Search Job Categories',
			'all_items' => 'All Job Categories',
			'parent_item' => 'Job Categories',
			'parent_item_colon' => 'Job Categories: ',
			'add_or_remove_items' => 'Add or Remove Job Categories',
			'choose_from_most_used' => 'All Job Categories',
			),
			'public' => true,
			'show_admin_column' => NULL,
			'hierarchical' => true,
			'rewrite' =>
			array (
			'slug' => 'jobs-category',
			'with_front' => true,
			'hierarchical' => false,
			'ep_mask' => 0,
			),
			'query_var' => true,
			'capabilities' =>
			array (
			'manage_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_jobs',
			),
			),

			);
			if( $this->allow_network && is_network_admin()){
				$ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['jbp_category'] = $jbp_category;
				update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			} else {
				$ct_custom_taxonomies = get_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['jbp_category'] = $jbp_category;
				update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			}
		}

		/*
		if(! taxonomy_exists('jbp_tag') ) {
		$jbp_tag =
		array (
		'object_type' =>
		array (
		0 => 'jbp_job',
		),
		'hide_type' =>
		array (
		0 => 'jbp_job',
		),
		'args' =>
		array (
		'labels' =>
		array (
		'name' => 'Job Tags',
		'singular_name' => 'Job Tag',
		'add_new_item' => 'Add New Job Tag',
		'new_item_name' => 'New Job Tag',
		'edit_item' => 'Edit Job Tag',
		'update_item' => 'Update Job Tag',
		'search_items' => 'Search Job Tags',
		'popular_items' => 'Popular Job Tags',
		'all_items' => 'All Job Tags',
		'parent_item_colon' => 'Jobs tags:',
		'add_or_remove_items' => 'Add or Remove Job Tags',
		'choose_from_most_used' => 'All Job Tags',
		),
		'public' => true,
		'show_admin_column' => NULL,
		'hierarchical' => false,
		'rewrite' =>
		array (
		'slug' => 'job-tag',
		'with_front' => true,
		'hierarchical' => false,
		'ep_mask' => 0,
		),
		'query_var' => true,
		'capabilities' =>
		array (
		'manage_terms' => 'manage_categories',
		'edit_terms' => 'manage_categories',
		'delete_terms' => 'manage_categories',
		'assign_terms' => 'edit_jobs',
		),
		),
		);

		if(is_network_admin()){
		$ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
		$ct_custom_taxonomies['jbp_tag'] = $jbp_tag;
		update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
		} else {
		$ct_custom_taxonomies = get_option('ct_custom_taxonomies');
		$ct_custom_taxonomies['jbp_tag'] = $jbp_tag;
		update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
		}
		}
		*/


		if(! taxonomy_exists('jbp_skills_tag') ) {
			$jbp_tag =
			array (
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'args' =>
			array (
			'labels' =>
			array (
			'name' => 'Job Skills Tags',
			'singular_name' => 'Job Skills Tag',
			'add_new_item' => 'Add New Job Skills Tag',
			'new_item_name' => 'New Job Skills Tag',
			'edit_item' => 'Edit Job Skills Tag',
			'update_item' => 'Update Job Skills Tag',
			'search_items' => 'Search Job Skills Tags',
			'popular_items' => 'Popular Job Skills Tags',
			'all_items' => 'All Job Skills Tags',
			'parent_item_colon' => 'Jobs tags:',
			'add_or_remove_items' => 'Add or Remove Job Skills Tags',
			'choose_from_most_used' => 'All Job Skills Tags',
			),
			'public' => true,
			'show_admin_column' => NULL,
			'hierarchical' => false,
			'rewrite' =>
			array (
			'slug' => 'job-skills',
			'with_front' => true,
			'hierarchical' => false,
			'ep_mask' => 0,
			),
			'query_var' => true,
			'capabilities' =>
			array (
			'manage_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories',
			'assign_terms' => 'edit_jobs',
			),
			),
			);

			if( $this->allow_network && is_network_admin()){
				$ct_custom_taxonomies = get_site_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['jbp_skills_tag'] = $jbp_tag;
				update_site_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			} else {
				$ct_custom_taxonomies = get_option('ct_custom_taxonomies');
				$ct_custom_taxonomies['jbp_skills_tag'] = $jbp_tag;
				update_option( 'ct_custom_taxonomies', $ct_custom_taxonomies );
			}
		}

	}

	/**
	* Create the default custom fields to be used
	* jbp_job -> Job_Budget_text, Job_Due_datepicker
	*
	*/
	function load_custom_fields(){

		/* Check whether custom fields data is loaded */
		$ct_custom_fields = ( get_option( 'ct_custom_fields' ) );
		$ct_network_custom_fields = ( get_site_option( 'ct_custom_fields' ) );

		$ct_custom_fields = ( get_option( 'ct_custom_fields' ) );
		$ct_network_custom_fields = ( get_site_option( 'ct_custom_fields' ) );


		if ( empty($ct_custom_fields['jbp_pro_First_Last'])
		&& empty($ct_network_custom_fields['jbp_pro_First_Last'])){

			$jbp_pro_First_Last =
			array (
			'field_title' => 'First and Last Name',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => 'mm/dd/yy',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'optional',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 0,
			'field_id' => 'jbp_pro_First_Last',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_First_Last'] = $jbp_pro_First_Last;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_First_Last'] = $jbp_pro_First_Last;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_pro_Tagline'])
		&& empty($ct_network_custom_fields['jbp_pro_Tagline'])){

			$jbp_pro_Tagline =
			array (
			'field_title' => 'Tag Line',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => '',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Tagline ',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' => NULL,
			'field_required' => 0,
			'field_id' => 'jbp_pro_Tagline',
			'field_order' => 14,
			);
			
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Tagline'] = $jbp_pro_Tagline;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Tagline'] = $jbp_pro_Tagline;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_pro_Company_URL'])
		&& empty($ct_network_custom_fields['jbp_pro_Company_URL'])){

			$jbp_pro_Company_URL =
			array (
			'field_title' => 'Company URL',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => 'mm/dd/yy',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'optional',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 0,
			'field_id' => 'jbp_pro_Company_URL',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Company_URL'] = $jbp_pro_Company_URL;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Company_URL'] = $jbp_pro_Company_URL;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_pro_Location'])
		&& empty($ct_network_custom_fields['jbp_pro_Location'])){

			$jbp_pro_Location =
			array (
			'field_title' => 'Location',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => 'mm/dd/yy',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'optional',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 0,
			'field_id' => 'jbp_pro_Location',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Location'] = $jbp_pro_Location;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Location'] = $jbp_pro_Location;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_pro_Contact_Email'])
		&& empty($ct_network_custom_fields['jbp_pro_Contact_Email'])){
			$jbp_pro_Contact_Email =
			array (
			'field_title' => 'Contact Email',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => 'mm/dd/yy',
			'field_regex' => '^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$',
			'field_regex_options' => 'i',
			'field_regex_message' => 'Please enter a valid contact email address',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Contact email address for the pro',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 1,
			'field_id' => 'jbp_pro_Contact_Email',
			'field_order' => 0,
			);

			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Contact_Email'] = $jbp_pro_Contact_Email;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Contact_Email'] = $jbp_pro_Contact_Email;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}


		if ( empty($ct_custom_fields['jbp_pro_Social'])
		&& empty($ct_network_custom_fields['jbp_pro_Social'])){

			$jbp_pro_Social =
			array (
			'field_title' => 'Social',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => 'mm/dd/yy',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Social link icons',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 0,
			'field_id' => 'jbp_pro_Social',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Social'] = $jbp_pro_Social;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Social'] = $jbp_pro_Social;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}


		//		if ( empty($ct_custom_fields['jbp_pro_Facebook_URL'])
		//		&& empty($ct_network_custom_fields['jbp_pro_Facebook_URL'])){
		//
		//			$jbp_pro_Facebook_URL =
		//			array (
		//			'field_title' => 'Facebook URL',
		//			'field_wp_allow' => 0,
		//			'field_type' => 'text',
		//			'field_sort_order' => 'default',
		//			'field_regex' => '',
		//			'field_regex_options' => '',
		//			'field_regex_message' => '',
		//			'field_message' => '',
		//			'field_default_option' => NULL,
		//			'field_description' => 'optional',
		//			'object_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'hide_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'field_required' => 0,
		//			'field_id' => 'jbp_pro_Facebook_URL',
		//			'field_order' => 0,
		//			);
		//			if( $this->allow_network && is_network_admin()){
		//				$ct_network_custom_fields['jbp_pro_Facebook_URL'] = $jbp_pro_Facebook_URL;
		//				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
		//			} else {
		//				$ct_custom_fields['jbp_pro_Facebook_URL'] = $jbp_pro_Facebook_URL;
		//				update_option( 'ct_custom_fields', $ct_custom_fields );
		//			}
		//
		//		}
		//
		//		if ( empty($ct_custom_fields['jbp_pro_LinkedIn_URL'])
		//		&& empty($ct_network_custom_fields['jbp_pro_LinkedIn_URL'])){
		//
		//			$jbp_pro_LinkedIn_URL =
		//			array (
		//			'field_title' => 'LinkedIn URL',
		//			'field_wp_allow' => 0,
		//			'field_type' => 'text',
		//			'field_sort_order' => 'default',
		//			'field_regex' => '',
		//			'field_regex_options' => '',
		//			'field_regex_message' => '',
		//			'field_message' => '',
		//			'field_default_option' => NULL,
		//			'field_description' => 'optional',
		//			'object_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'hide_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'field_required' => 0,
		//			'field_id' => 'jbp_pro_LinkedIn_URL',
		//			'field_order' => 0,
		//			);
		//			if( $this->allow_network && is_network_admin()){
		//				$ct_network_custom_fields['jbp_pro_LinkedIn_URL'] = $jbp_pro_LinkedIn_URL;
		//				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
		//			} else {
		//				$ct_custom_fields['jbp_pro_LinkedIn_URL'] = $jbp_pro_LinkedIn_URL;
		//				update_option( 'ct_custom_fields', $ct_custom_fields );
		//			}
		//
		//		}
		//
		//		if ( empty($ct_custom_fields['jbp_pro_Twitter_URL'])
		//		&& empty($ct_network_custom_fields['jbp_pro_Twitter_URL'])){
		//
		//			$jbp_pro_Twitter_URL =
		//			array (
		//			'field_title' => 'Twitter URL',
		//			'field_wp_allow' => 0,
		//			'field_type' => 'text',
		//			'field_sort_order' => 'default',
		//			'field_regex' => '',
		//			'field_regex_options' => '',
		//			'field_regex_message' => '',
		//			'field_message' => '',
		//			'field_default_option' => NULL,
		//			'field_description' => 'optional',
		//			'object_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'hide_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'field_required' => 0,
		//			'field_id' => 'jbp_pro_Twitter_URL',
		//			'field_order' => 0,
		//			);
		//			if( $this->allow_network && is_network_admin()){
		//				$ct_network_custom_fields['jbp_pro_Twitter_URL'] = $jbp_pro_Twitter_URL;
		//				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
		//			} else {
		//				$ct_custom_fields['jbp_pro_Twitter_URL'] = $jbp_pro_Twitter_URL;
		//				update_option( 'ct_custom_fields', $ct_custom_fields );
		//			}
		//
		//		}
		//
		//		if ( empty($ct_custom_fields['jbp_pro_Skype_URL'])
		//		&& empty($ct_network_custom_fields['jbp_pro_Skype_URL'])){
		//
		//			$jbp_pro_Skype_URL =
		//			array (
		//			'field_title' => 'Skype URL',
		//			'field_wp_allow' => 0,
		//			'field_type' => 'text',
		//			'field_sort_order' => 'default',
		//			'field_regex' => '',
		//			'field_regex_options' => '',
		//			'field_regex_message' => '',
		//			'field_message' => '',
		//			'field_default_option' => NULL,
		//			'field_description' => 'optional',
		//			'object_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'hide_type' =>
		//			array (
		//			0 => 'jbp_pro',
		//			),
		//			'field_required' => 0,
		//			'field_id' => 'jbp_pro_Skype_URL',
		//			'field_order' => 0,
		//			);
		//			if( $this->allow_network && is_network_admin()){
		//				$ct_network_custom_fields['jbp_pro_Skype_URL'] = $jbp_pro_Skype_URL;
		//				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
		//			} else {
		//				$ct_custom_fields['jbp_pro_Skype_URL'] = $jbp_pro_Skype_URL;
		//				update_option( 'ct_custom_fields', $ct_custom_fields );
		//			}
		//		}

		if ( empty($ct_custom_fields['jbp_pro_Portfolio'])
		&& empty($ct_network_custom_fields['jbp_pro_Portfolio'])){

			$jbp_pro_Portfolio =
			array (
			'field_title' => 'Portfolio',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Image link to the pros portfolio',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 0,
			'field_id' => 'jbp_pro_Portfolio',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Portfolio'] = $jbp_pro_Portfolio;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Portfolio'] = $jbp_pro_Portfolio;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_pro_Skills'])
		&& empty($ct_network_custom_fields['jbp_pro_Skills'])){

			$jbp_pro_Skills =
			array (
			'field_title' => 'Skills',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_date_format' => 'mm/dd/yy',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Skills this pro has.',
			'object_type' =>
			array (
			0 => 'jbp_pro',
			),
			'hide_type' =>
			array (
			0 => 'jbp_pro',
			),
			'field_required' => 0,
			'field_id' => 'jbp_pro_Skills',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_pro_Skills'] = $jbp_pro_Skills;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_pro_Skills'] = $jbp_pro_Skills;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_job_Contact_Email'])
		&& empty($ct_network_custom_fields['jbp_job_Contact_Email'])){
			$jbp_job_Contact_Email =
			array (
			'field_title' => 'Job Contact Email',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_regex' => '^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$',
			'field_regex_options' => 'i',
			'field_regex_message' => 'Please enter a valid contact email address',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Contact email address for the job offer',
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'field_required' => 1,
			'field_id' => 'jbp_job_Contact_Email',
			'field_order' => 0,
			);

			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_job_Contact_Email'] = $jbp_job_Contact_Email;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_job_Contact_Email'] = $jbp_job_Contact_Email;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_job_Budget'])
		&& empty($ct_network_custom_fields['jbp_job_Budget'])){

			$jbp_job_Budget =
			array (
			'field_title' => 'Job Budget',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_regex' => '^(\\d*\\.\\d{1,2}|\\d+)$',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Please enter the maximum budget',
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'field_required' => 1,
			'field_id' => 'jbp_job_Budget',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_job_Budget'] = $jbp_job_Budget;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_job_Budget'] = $jbp_job_Budget;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}

		}

		if ( empty($ct_custom_fields['jbp_job_Min_Budget'])
		&& empty($ct_network_custom_fields['jbp_job_Min_Budget'])){

			$jbp_job_Min_Budget =
			array (
			'field_title' => 'Job Minimum Budget',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_regex' => '^(\\d*\\.\\d{1,2}|\\d+)$',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Please enter the range of your budget',
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'field_required' => 1,
			'field_id' => 'jbp_job_Min_Budget',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_job_Min_Budget'] = $jbp_job_Min_Budget;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_job_Min_Budget'] = $jbp_job_Min_Budget;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}

		}

		if ( empty($ct_custom_fields['jbp_job_Due'])
		&& empty($ct_network_custom_fields['jbp_job_Due'])){

			$jbp_job_Due =
			array (
			'field_title' => 'Job Due Date',
			'field_wp_allow' => 0,
			'field_type' => 'datepicker',
			'field_sort_order' => 'default',
			'field_options' =>
			array (
			1 => '',
			),
			'field_date_format' => 'M d, yy',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'When must this job be completed by? Or NA for not applicable.',
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'field_required' => 1,
			'field_id' => 'jbp_job_Due',
			'field_order' => 0,
			);

			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_job_Due'] = $jbp_job_Due;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_job_Due'] = $jbp_job_Due;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_job_Open_for'])
		&& empty($ct_network_custom_fields['jbp_job_Open_for'])){
			$jbp_job_Open_for =
			array (
			'field_title' => 'Job Open for',
			'field_wp_allow' => 0,
			'field_type' => 'selectbox',
			'field_sort_order' => 'default',
			'field_options' =>
			array (
			1 => '',
			2 => '3 Days',
			3 => '7 Days',
			4 => '14 Days',
			5 => '21 Days',
			),
			'field_date_format' => 'mm/dd/yy',
			'field_message' => '',
			'field_default_option' => '1',
			'field_description' => 'How long is this job open for from Today?',
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'field_required' => 1,
			'field_id' => 'jbp_job_Open_for',
			'field_order' => 0,
			);

			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_job_Open_for'] = $jbp_job_Open_for;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_job_Open_for'] = $jbp_job_Open_for;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}

		if ( empty($ct_custom_fields['jbp_job_Portfolio'])
		&& empty($ct_network_custom_fields['jbp_job_Portfolio'])){

			$jbp_job_Portfolio =
			array (
			'field_title' => 'Job Portfolio',
			'field_wp_allow' => 0,
			'field_type' => 'text',
			'field_sort_order' => 'default',
			'field_regex' => '',
			'field_regex_options' => '',
			'field_regex_message' => '',
			'field_message' => '',
			'field_default_option' => NULL,
			'field_description' => 'Image link to the pros portfolio',
			'object_type' =>
			array (
			0 => 'jbp_job',
			),
			'hide_type' =>
			array (
			0 => 'jbp_job',
			),
			'field_required' => 0,
			'field_id' => 'jbp_job_Portfolio',
			'field_order' => 0,
			);
			if( $this->allow_network && is_network_admin()){
				$ct_network_custom_fields['jbp_job_Portfolio'] = $jbp_job_Portfolio;
				update_site_option( 'ct_custom_fields', $ct_network_custom_fields );
			} else {
				$ct_custom_fields['jbp_job_Portfolio'] = $jbp_job_Portfolio;
				update_option( 'ct_custom_fields', $ct_custom_fields );
			}
		}
	}

	function load_default_settings(){
		global $Jobs_Plus_Core;

		$sname = $Jobs_Plus_Core->settings_name;

		$settings = get_option( $sname );
		

		if( $settings === false) $settings = array('pro' => array(), 'job' => array() );

		if( !isset($settings['pro']['email_subject']) ) $settings['pro']['email_subject'] = $GLOBALS['default_pro_subject'];
		if( !isset($settings['pro']['email_content']) ) $settings['pro']['email_content'] = $GLOBALS['default_pro_content'];

		if( !isset($settings['job']['email_subject']) ) $settings['job']['email_subject'] = $GLOBALS['default_pro_subject'];
		if( !isset($settings['job']['email_content']) ) $settings['job']['email_content'] = $GLOBALS['default_job_content'];

		if( !isset($settings['pro']['moderation']['publish']) ) $settings['pro']['moderation']['publish'] = 1;
		if( !isset($settings['job']['moderation']['publish']) ) $settings['pro']['moderation']['publish'] = 1;

		if( !isset($settings['pro']['moderation']['publish']) ) $settings['pro']['moderation']['draft'] = 1;
		if( !isset($settings['job']['moderation']['publish']) ) $settings['pro']['moderation']['draft'] = 1;

		update_option($sname, $settings);
	}

}

new Jobs_Plus_Core_Data;

endif;

//GLOBALS for settings

global $default_pro_subject, $default_pro_content, $default_job_subject, $default_job_content;

$default_pro_subject = __( 'SITE_NAME Contact Request: [ POST_TITLE ]', $this->text_domain);
$default_pro_content = __(
'Hi TO_NAME, you have received a message from

Name: FROM_NAME
Email: FROM_EMAIL
Message:

FROM_MESSAGE


Expert link: POST_LINK
', $this->text_domain);


$default_job_subject = __( 'SITE_NAME Contact Request: [ POST_TITLE ]', $this->text_domain);
$default_job_content = __(
'Hi TO_NAME, you have received a message from

Name: FROM_NAME
Email: FROM_EMAIL
Message:

FROM_MESSAGE


Job link: POST_LINK
', $this->text_domain);


