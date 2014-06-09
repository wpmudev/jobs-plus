<?php

if( !class_exists('Jobs_Plus_Pattern') ):
class Jobs_Plus_Pattern{

	function __construct(){

		global $Jobs_Plus_Core;
		
		$core = &$Jobs_Plus_Core;  //reference for convienience
		
	/**
	* Create the default pattern pages.
	* @return void
	*/
		/* Create neccessary pages */
		$current_user = wp_get_current_user();

		//Default button set
		$warning = __("<!-- You may edit this page, the title and the slug, but it requires a minimum of the correct page shortcode to function. You can recreate the original default page by deleting this one by moving it to trash and calling the page in the front end.-->\n", $core->text_domain);
		$buttons = '<p style="text-align: center;">[jbp-expert-post-btn][jbp-job-post-btn][jbp-expert-browse-btn][jbp-job-browse-btn][jbp-expert-profile-btn][jbp-job-list-btn]</p>';

		/**
		* JOB PATTERNS
		*/

		/**
		* jbp_job Archive
		*/
		$page = $core->get_page_by_meta(JBP_JOB_PATTERN_KEY, JBP_JOB_ARCHIVE_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Archive Pattern', JBP_TEXT_DOMAIN), $core->job_labels->singular_name),
			'post_name'      => sprintf( __('%s-archive-pattern', JBP_TEXT_DOMAIN), $core->job_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $warning . $buttons . '[jbp-job-archive-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_JOB_PATTERN_KEY, JBP_JOB_ARCHIVE_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_job_archive_page_id = $page_id; //Remember the number

		/**
		* jbp_job Taxonomy
		*/
		$page = $core->get_page_by_meta(JBP_JOB_PATTERN_KEY, JBP_JOB_TAXONOMY_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Taxonomy Pattern', JBP_TEXT_DOMAIN), $core->job_labels->singular_name),
			'post_name'      => sprintf( __('%s-taxonomy-pattern', JBP_TEXT_DOMAIN), $core->job_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $warning . $buttons . '[jbp-job-taxonomy-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_JOB_PATTERN_KEY, JBP_JOB_TAXONOMY_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_job_taxonomy_page_id = $page_id; //Remember the number

		/**
		* jbp_job Contact
		*/
		$page = $core->get_page_by_meta(JBP_JOB_PATTERN_KEY, JBP_JOB_CONTACT_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Contact Pattern', JBP_TEXT_DOMAIN), $core->job_labels->singular_name),
			'post_name'      => sprintf( __('%s-contact-pattern', JBP_TEXT_DOMAIN), $core->job_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $warning . $buttons . '[jbp-job-contact-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_JOB_PATTERN_KEY, JBP_JOB_CONTACT_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_job_contact_page_id = $page_id; //Remember the number

		/**
		* jbp_job Search
		*/
		$page = $core->get_page_by_meta(JBP_JOB_PATTERN_KEY, JBP_JOB_SEARCH_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Search Pattern', JBP_TEXT_DOMAIN), $core->job_labels->singular_name),
			'post_name'      => sprintf( __('%s-search-pattern', JBP_TEXT_DOMAIN), $core->job_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $warning . $buttons . '[jbp-job-search-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_JOB_PATTERN_KEY, JBP_JOB_SEARCH_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_job_search_page_id = $page_id; //Remember the number

		/**
		* jbp_job Single
		*/
		$page = $core->get_page_by_meta(JBP_JOB_PATTERN_KEY, JBP_JOB_SINGLE_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Single Pattern', JBP_TEXT_DOMAIN), $core->job_labels->singular_name),
			'post_name'      => sprintf( __('%s-single-pattern', JBP_TEXT_DOMAIN), $core->job_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $warning . $buttons . '[jbp-job-single-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_JOB_PATTERN_KEY, JBP_JOB_SINGLE_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_job_single_page_id = $page_id; //Remember the number

		/**
		* jbp_pro Update
		*/
		$page = $core->get_page_by_meta(JBP_JOB_PATTERN_KEY, JBP_JOB_UPDATE_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('Add %s', JBP_TEXT_DOMAIN), $core->job_labels->singular_name),
			'post_name'      => sprintf( __('add-%s', JBP_TEXT_DOMAIN), $core->job_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $warning . $buttons . '[jbp-job-update-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_JOB_PATTERN_KEY, JBP_JOB_UPDATE_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_job_update_page_id = $page_id; //Remember the number


		/**
		* PRO PATTERNS
		*/

		/**
		* jbp_pro Archive
		*/
		$page = $core->get_page_by_meta(JBP_PRO_PATTERN_KEY, JBP_PRO_ARCHIVE_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Archive Pattern', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name),
			'post_name'      => sprintf( __('%s-archive-pattern', JBP_TEXT_DOMAIN), $core->pro_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $warning . $buttons . '[jbp-expert-archive-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_PRO_PATTERN_KEY, JBP_PRO_ARCHIVE_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_pro_archive_page_id = $page_id; //Remember the number

		/**
		* jbp_pro Taxonomy
		*/
		$page = $core->get_page_by_meta(JBP_PRO_PATTERN_KEY, JBP_PRO_TAXONOMY_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Taxonomy Pattern', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name),
			'post_name'      => sprintf( __('%s-taxonomy-pattern', JBP_TEXT_DOMAIN), $core->pro_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $warning . $buttons . '[jbp-expert-taxonomy-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_PRO_PATTERN_KEY, JBP_PRO_TAXONOMY_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_pro_taxonomy_page_id = $page_id; //Remember the number

		/**
		* jbp_pro Contact
		*/
		$page = $core->get_page_by_meta(JBP_PRO_PATTERN_KEY, JBP_PRO_CONTACT_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Contact Pattern', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name),
			'post_name'      => sprintf( __('%s-contact-pattern', JBP_TEXT_DOMAIN), $core->pro_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $warning . $buttons . '[jbp-expert-contact-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_PRO_PATTERN_KEY, JBP_PRO_CONTACT_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_pro_contact_page_id = $page_id; //Remember the number

		/**
		* jbp_pro Search
		*/
		$page = $core->get_page_by_meta(JBP_PRO_PATTERN_KEY, JBP_PRO_SEARCH_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Search Pattern', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name),
			'post_name'      => sprintf( __('%s-search-pattern', JBP_TEXT_DOMAIN), $core->pro_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $warning . $buttons . '[jbp-expert-search-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_PRO_PATTERN_KEY, JBP_PRO_SEARCH_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_pro_search_page_id = $page_id; //Remember the number

		/**
		* jbp_pro Single
		*/
		$page = $core->get_page_by_meta(JBP_PRO_PATTERN_KEY, JBP_PRO_SINGLE_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('%s Single Pattern', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name),
			'post_name'      => sprintf( __('%s-single-pattern', JBP_TEXT_DOMAIN), $core->pro_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $warning . $buttons . '[jbp-expert-single-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_PRO_PATTERN_KEY, JBP_PRO_SINGLE_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_pro_single_page_id = $page_id; //Remember the number

		/**
		* jbp_pro Update
		*/
		$page = $core->get_page_by_meta(JBP_PRO_PATTERN_KEY, JBP_PRO_UPDATE_FLAG );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf( __('Add %s', JBP_TEXT_DOMAIN), $core->pro_labels->singular_name),
			'post_name'      => sprintf( __('add-%s', JBP_TEXT_DOMAIN), $core->pro_slug ),
			'post_status'    => 'pattern',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $warning . $buttons . '[jbp-expert-update-page]',
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, JBP_PRO_PATTERN_KEY, JBP_PRO_UPDATE_FLAG);
		} else {
			//Make sure it stays pattern
			if( !in_array($page->post_status, array('pattern', 'trash') ) ) 
			wp_update_post( array('ID' => $page_id, 'post_status' => 'pattern') );
		}
		$core->_pro_update_page_id = $page_id; //Remember the number
	
	}	
}

new Jobs_Plus_Pattern;

endif;
