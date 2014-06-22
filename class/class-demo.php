<?php

if( !class_exists('Jobs_Plus_Demo') ):

class Jobs_Plus_Demo{

	public $cats = array();
	public $tags = array();

	private $blogmu = '{"%s":{"attachment_id":%s,"url":"http://premium.wpmudev.org/project/buddypress-blogsmu-theme/","src":"%s","caption":"A stunning teams, groups and networking oriented theme. Control the site appearance and allow members to customize their profile pages too u2013 works great with BuddyPress, WordPress and Multisite!","remove":"","file":"blogmu.png"}}';

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
		$page_id = $core->get_post_id_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'1' );
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

			$this->insert_portfolio_image( 
			$page_id, 
			'blogmu.png', 
			"http://premium.wpmudev.org/project/buddypress-blogsmu-theme/", 
			"A stunning teams, groups and networking oriented theme. Control the site appearance and allow members to customize their profile pages too - works great with BuddyPress, WordPress and Multisite!" 
			);
		}

		$page_id = $core->get_post_id_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'2' );
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

			$this->insert_portfolio_image( 
			$page_id, 
			'scholar.png', 
			"http://premium.wpmudev.org/project/buddypress-scholar-theme/", 
			"Scholar is a theme designed to work for heavy content sites, educational institutions and more ‘formal’ communities." 
			);

		}

		$page_id = $core->get_post_id_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_JOB_FLAG.'3' );
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

			$this->insert_portfolio_image( 
			$page_id, 
			'bpsocial.png', 
			"http://premium.wpmudev.org/project/buddypress-social-theme/", 
			"A truly beautiful theme that takes inspiration from Facebook and delivers you more than you ever thought was possible from a theme – BuddyPress, WordPress or Multisite ready to go!" 
			);
		}
	}


	function load_pros(){
		global $Jobs_Plus_Core;

		$core = &$Jobs_Plus_Core;

		/**
		* DEMO PROS
		*/
		$page_id = $core->get_post_id_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'1' );
		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => __('Expert  Webmaster', JBP_TEXT_DOMAIN),
			'post_name'      => __('expert-webmaster', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_pro',
			'post_content'   =>

			'As a professional webmaster and blogger I am able to help you to take advantage of all the possibilities and functions that come with Wordpress. No matter if questions regarding using the software or extending it with new themes and functions - I will help you!'
			.'I´ve studied "business informatics" and have been working in many different companies as a system administrator and webmaster. Projects from websites for companies located in the shipping business to websites for nightlife photographers. In the office I took care of the network infrastructure and if any problem appeared from printer to synchronization problems with outlook I solved those problems. Years of experience are waiting for your questions to be answered quickly.'
			,

			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'1');

			update_post_meta($page_id, '_ct_jbp_pro_First_Last', json_encode( array('first' => 'Phil', 'last' => 'Anders') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Company_URL', json_encode( array('link' => 'WPMU DEV', 'url' => 'http://premium.wpmudev.org') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Location', 'Australia' );
			update_post_meta($page_id, '_ct_jbp_pro_Contact_Email', 'demo.incsub+expert-1@gmail.com' );
			update_post_meta($page_id, '_ct_jbp_pro_Tagline', 'WordPress Theme Expert' );
			update_post_meta($page_id, '_ct_jbp_pro_Social', '{"fb":{"social":"Facebook","url":"http://www.facebook.com/wpmudev","social_id":"fb","remove":""},"tw":{"social":"Twitter: @username","url":"http://twitter.com/wpmudev","social_id":"tw","remove":""}}' );
			update_post_meta($page_id, '_ct_jbp_pro_Skills', '{"539a1402eadb2":{"skill_id":"539a1402eadb2","skill":"WordPress","remove":"","percent":"97"},"539a1413cb368":{"skill_id":"539a1413cb368","skill":"PHP","remove":"","percent":"98"},"539a1425182f2":{"skill_id":"539a1425182f2","skill":"MySQL","remove":"","percent":"94"}}' );

			$this->insert_portfolio_image( 
			$page_id, 
			'edublog.png', 
			"http://premium.wpmudev.org/project/the-edublogs-homepage-theme/", 
			"The Edublogs.org homepage theme, ready for you to disassemble and use in as many personal, business or other projects as you like!" 
			);
		}

		$page_id = $core->get_post_id_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'2' );
		if ( empty($page_id) ) {
			/// Construct args for the new post
			$args = array(
			'post_title'     => __('WordPress Website Support Coordinator', JBP_TEXT_DOMAIN),
			'post_name'      => __('wordpress-website-support-coordinator', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_pro',
			'post_content'   =>

			'I have expertise in web development and mostly create websites using Wordpress CMS or Code Igniter framework. Further, I have expertise in AJAX, Jquery, PHP, Mysql, Javascript, Vb Script, HTML, DHTML, XHTML, XML and CSS Stylsheets. I have developed many websites ranging from small static portfolio pages to complex ecommerce websites. I can setup, install and convert any template to Wordpress (PSD-to-HTML) at an affordable cost.'
			.'Experience & Qualifications 3+ Year experience in WordPress'
			,

			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'2');

			update_post_meta($page_id, '_ct_jbp_pro_First_Last', json_encode( array('first' => 'Tom', 'last' => 'Eagles') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Company_URL', json_encode( array('link' => 'WPMU DEV', 'url' => 'http://premium.wpmudev.org') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Location', 'United States' );
			update_post_meta($page_id, '_ct_jbp_pro_Contact_Email', 'demo.incsub+expert-2@gmail.com' );
			update_post_meta($page_id, '_ct_jbp_pro_Tagline', 'Wordpress Expert with more than 6 years experience in web development using PHP/jQuery/CSS/MySQL.' );
			update_post_meta($page_id, '_ct_jbp_pro_Social', '{"fb":{"social":"Facebook","url":"http://www.facebook.com/wpmudev","social_id":"fb","remove":""},"tw":{"social":"Twitter: @username","url":"http://twitter.com/wpmudev","social_id":"tw","remove":""}}' );
			update_post_meta($page_id, '_ct_jbp_pro_Skills', '{"539a1402eadb2":{"skill_id":"539a1402eadb2","skill":"WordPress","remove":"","percent":"97"},"539a1413cb368":{"skill_id":"539a1413cb368","skill":"PHP","remove":"","percent":"98"},"539a1425182f2":{"skill_id":"539a1425182f2","skill":"MySQL","remove":"","percent":"94"}}' );

			$this->insert_portfolio_image( 
			$page_id, 
			'network.png', 
			"http://premium.wpmudev.org/project/network-theme/", 
			"A powerful front page theme perfect for displaying network-wide content on WordPress or BuddyPress" 
			);
		}

		$page_id = $core->get_post_id_by_meta(JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'3' );
		if ( empty($page_id) ) {
			// Construct args for the new post
			$args = array(
			'post_title'     => __('Blog, Commerce, Membership custom site', JBP_TEXT_DOMAIN),
			'post_name'      => __('blog-commerce-membership-custom-site', JBP_TEXT_DOMAIN),
			'post_status'    => 'publish',
			'post_type'      => 'jbp_pro',
			'post_content'   =>

			'I am an expert in (Wordpress) theme integration of customers choice or can provide our own responsive and customized design. Not only design, i do have the ability to provide wordpress plugin according to the customer needs and features. Specialized in Responsive theme and plugins integration with complete admin panel.'
			.'Working as a Software Engineer, 5+ years experience in PHP, 1 year in java development (desktop application and JSP). 2 years experience in open-source like WordPress, Joomla and other expertise.'
			,

			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_DEMO_PATTERN_KEY, JBP_DEMO_PRO_FLAG.'3');

			update_post_meta($page_id, '_ct_jbp_pro_First_Last', json_encode( array('first' => 'John', 'last' => 'Rivera') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Company_URL', json_encode( array('link' => 'WPMU DEV', 'url' => 'http://premium.wpmudev.org') ) );
			update_post_meta($page_id, '_ct_jbp_pro_Location', 'United Kingdom' );
			update_post_meta($page_id, '_ct_jbp_pro_Contact_Email', 'demo.incsub+expert-3@gmail.com' );
			update_post_meta($page_id, '_ct_jbp_pro_Tagline', 'Offering fast help in PHP, MySQL, JQuery, AJAX, XML, SOAP, CodeIgnitor, CakePHP, Joomla, WordPress, Facebook API, Google API' );
			update_post_meta($page_id, '_ct_jbp_pro_Social', '{"fb":{"social":"Facebook","url":"http://www.facebook.com/wpmudev","social_id":"fb","remove":""},"tw":{"social":"Twitter: @username","url":"http://twitter.com/wpmudev","social_id":"tw","remove":""}}' );
			update_post_meta($page_id, '_ct_jbp_pro_Skills', '{"539a1402eadb2":{"skill_id":"539a1402eadb2","skill":"WordPress","remove":"","percent":"92"},"539a1413cb368":{"skill_id":"539a1413cb368","skill":"PHP","remove":"","percent":"98"},"539a1425182f2":{"skill_id":"539a1425182f2","skill":"Google API","remove":"","percent":"94"}}' );

			$this->insert_portfolio_image( 
			$page_id, 
			'studio.png', 
			"http://premium.wpmudev.org/project/studio-theme/", 
			"A balance of beautiful, pixel-perfect design with flexible, easy-to-navigate customization." 
			);
		}

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

	function insert_portfolio_image( $parent, $filename, $link, $caption ){

		$upload = wp_upload_dir();
		$url = $upload['baseurl'] . "/demo/$filename";
		$filename = $upload['basedir'] . "/demo/$filename";
		if(! file_exists($filename) ) return;

		$mime = wp_check_filetype( basename( $filename ), null );

		$id = wp_insert_attachment( array(
		'guid' => $url,
		'post_mime_type' => $mime['type'],
		'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
		'post_content' => $caption,
		'post_parent' => $parent,
		'post_status' => 'inherit',
		));

		$attach_data = wp_generate_attachment_metadata( $id, $filename );
		wp_update_attachment_metadata( $id, $attach_data );

		update_attached_file( $id, 'demo/'. basename($filename) );

		$folio = new stdClass;
		$idstr = strval($id);
		$folio->$idstr = new stdClass;
		$folio->$idstr->attachment_id = $id;
		$folio->$idstr->url = $link;
		$folio->$idstr->src = $url;
		$folio->$idstr->caption = $caption;
		$folio->$idstr->remove = "";
		$folio->$idstr->file = basename($filename);
		
		$post_type = get_post_type($parent);

		update_post_meta($parent, "_ct_{$post_type}_Portfolio", json_encode( $folio ));
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