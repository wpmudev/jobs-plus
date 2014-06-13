<?php

if( !class_exists('Jobs_Plus_Demo') ):

class Jobs_Plus_Demo{

	public $cats = array();
	public $tags = array();

	function __construct(){

		$this->load_demo_images();
		$this->load_jobs();
		$this->load_pros();

	}

	function load_jobs(){
		global $Jobs_Plus_Core;

		$core = &$Jobs_Plus_Core;

		/**
		* DEMO JOBS
		*/
		$page = $core->get_page_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'1' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => __('Experienced WordPress / PHP Developer', JBP_TEXT_DOMAIN),
			'post_name'      => __('experienced-wordpress-php-developer', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_job',
			'post_content'   => 'Content text',
			'ping_status'    => 'closed',
			'comment_status' => 'closed',

			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'1');
			$result = wp_set_object_terms( $page_id, 'plugins-code', 'jbp_category' );
			$result = wp_set_object_terms( $page_id, array('PHP', 'AJAX'), 'jbp_skills_tag' );
			
			update_post_meta($page_id, '_ct_jbp_job_Budget', 100 );
			update_post_meta($page_id, '_ct_jbp_job_Contact_Email', 'arnold@incsub.com' );
			update_post_meta($page_id, '_ct_jbp_job_Due', date( 'M j, Y') );
			update_post_meta($page_id, JBP_JOB_EXPIRES_KEY, time() + (7 * 24 * 60 * 60) );

		}

		$page = $core->get_page_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'2' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => __('WordPress Website Support Coordinator', JBP_TEXT_DOMAIN),
			'post_name'      => __('wordpress-website-support-coordinator', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_job',
			'post_content'   => 'Content text',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'2');
			$result = wp_set_object_terms( $page_id, 'themes-design', 'jbp_category' );
			$result = wp_set_object_terms( $page_id, array('PHP', 'Javascript', 'CSS' ), 'jbp_skills_tag' );

			update_post_meta($page_id, '_ct_jbp_job_Budget', 1500 );
			update_post_meta($page_id, '_ct_jbp_job_Contact_Email', 'arnold@incsub.com' );
			update_post_meta($page_id, '_ct_jbp_job_Due', date( 'M j, Y') );
			update_post_meta($page_id, JBP_JOB_EXPIRES_KEY, time() + (7 * 24 * 60 * 60) );

		}

		$page = $core->get_page_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'3' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => __('Blog, Commerce, Membership custom site', JBP_TEXT_DOMAIN),
			'post_name'      => __('blog-commerce-membership-custom-site', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_job',
			'post_content'   => 'Content text',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'3');
			$result = wp_set_object_terms( $page_id, 'wordpress', 'jbp_category' );
			$result = wp_set_object_terms( $page_id, array('bbPress', 'PHP', 'Video', 'Membership', 'eCommerce'), 'jbp_skills_tag' );

			update_post_meta($page_id, '_ct_jbp_job_Budget', 600 );
			update_post_meta($page_id, '_ct_jbp_job_Contact_Email', 'arnold@incsub.com' );
			update_post_meta($page_id, '_ct_jbp_job_Due', date( 'M j, Y') );
			update_post_meta($page_id, JBP_JOB_EXPIRES_KEY, time() + (7 * 24 * 60 * 60) );
		}
	}


	function load_pros(){
		global $Jobs_Plus_Core;

		$core = &$Jobs_Plus_Core;

		/**
		* DEMO PROS
		*/
		$page = $core->get_page_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'1' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => __('Tom Eagles', JBP_TEXT_DOMAIN),
			'post_name'      => __('tom-eagles', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_pro',
			'post_content'   => "Hi There,\n\n".
													'25+ Years in the IT business, Currently a WPMU DEV lifetime Member, and ex WPMUDEV support staff, I currently run a hosting and consultancy business and specialise in WordPress and WordPress Multisites. One of my products the Pro-Sites custom checkout is one of the most popular addons available from a 3rd Party vendor for WPMUDEV products.',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'1');

			update_post_meta($page_id, '_ct_jbp_pro_First_Last', json_encode( array('first' => 'Tom', 'last' => 'Eagles') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Company_URL', json_encode( array('link' => 'wpmu-hosting.net', 'url' => 'http://WPMU Hosting.net') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Location', 'United Kingdom' );
			update_post_meta($page_id, '_ct_jbp_pro_Contact_Email', 'tom.eagles@gmail.com' );
			update_post_meta($page_id, '_ct_jbp_pro_Tagline', '25+ Years in the IT business' );
			update_post_meta($page_id, '_ct_jbp_pro_Social', '{"fb":{"social":"Facebook","url":"http://www.facebook.com/wpmuhosting","social_id":"fb","remove":""},"tw":{"social":"Twitter: @username","url":"http://twitter.com/wpmuhosting","social_id":"tw","remove":""},"sk":{"social":"Skype (SKYPE:skype id)","url":"http://premium.wpmudev.org/pro/tom-eagles/wpmu-hosting.net","social_id":"sk","remove":""},"li":{"social":"LinkedIn","url":"http://cz.linkedin.com/pub/tom-eagles/5a/866/b4/","social_id":"li","remove":""}}' );
			update_post_meta($page_id, '_ct_jbp_pro_Skills', '{"539a1402eadb2":{"skill_id":"539a1402eadb2","skill":"WordPress","remove":"","percent":"97"},"539a1413cb368":{"skill_id":"539a1413cb368","skill":"PHP","remove":"","percent":"98"},"539a1425182f2":{"skill_id":"539a1425182f2","skill":"MySQL","remove":"","percent":"94"}}' );
		}
/*
		$page = $core->get_page_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'2' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/// Construct args for the new post 
			$args = array(
			'post_title'     => __('WordPress Website Support Coordinator', JBP_TEXT_DOMAIN),
			'post_name'      => __('wordpress-website-support-coordinator', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_pro',
			'post_content'   => 'Content text',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'2');
		}

		$page = $core->get_page_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'3' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => __('Blog, Commerce, Membership custom site', JBP_TEXT_DOMAIN),
			'post_name'      => __('blog-commerce-membership-custom-site', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_pro',
			'post_content'   => 'Content text',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'3');
		}
*/

	}

	function load_demo_images(){
		global $Jobs_Plus_Core;

		$core = &$Jobs_Plus_Core;

		$upload = wp_upload_dir();

		if(!is_dir($upload['basedir'] . '/demo')):
		mkdir( $upload['basedir'] . '/demo', 0755, true);
		endif;

		$this->recurse_copy( $core->plugin_dir . 'demo', $upload['basedir'] . '/demo' );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		/**
		* Job categories
		*/
		$this->insert_term_image('Plugins & Code', 'jbp_category',  $upload['basedir'] . '/demo/plugins.png' );
		$this->insert_term_image('Themes & Design', 'jbp_category', $upload['basedir'] . '/demo/themes.png' );
		$this->insert_term_image('Buddypress', 'jbp_category',      $upload['basedir'] . '/demo/buddypress.png' );
		$this->insert_term_image('Wordpress', 'jbp_category',       $upload['basedir'] . '/demo/general.png' );

	}

	function insert_term_image( $term, $taxonomy, $filename ){
		if( !term_exists( $term, $taxonomy) ) {
			$term = wp_insert_term( $term, $taxonomy );

			$this->cats[] = $term['term_id'];

			$mime = wp_check_filetype( basename( $filename ), null );

			$id = wp_insert_attachment( array(
			'post_mime_type' => $mime['type'],
			'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content' => '',
			'post_status' => 'inherit',
			));

			$attach_data = wp_generate_attachment_metadata( $id, $filename );
			wp_update_attachment_metadata( $id, $attach_data );

			update_attached_file( $id, 'demo/'. basename($filename) );

			update_post_meta($id, '_ti_term_image', $term['term_id'] );
		}
	}

	function recurse_copy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}


}

new Jobs_Plus_Demo;
endif;