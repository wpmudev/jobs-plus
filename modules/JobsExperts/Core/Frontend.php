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
 * Front end module.
 * This module will hold all the page url information.
 *
 * @category JobsExperts
 * @package  Module
 *
 * @since    1.0.0
 */
class JobsExperts_Core_Frontend extends JobsExperts_Framework_Module {

	const NAME = __CLASS__;

	public $plugin_pages;

	public $raw_query;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 *
	 * @param JobsExperts_Plugin $plugin The plugin instance.
	 */
	public function __construct( JobsExperts_Plugin $plugin ) {
		parent::__construct( $plugin );
		//rewrite stuff
		$this->_add_action( 'init', 'on_init', 11 );

		$this->_add_action( 'init', 'add_rewrite_rule', 11 );
		//$this->_add_filter( 'query_vars', 'add_query_vars_filter' );
		//template stuff
		$this->_add_filter( 'template_include', 'process_virtual_page_content' );
		$this->_add_filter( 'the_content', 'job_single_content' );
		$this->_add_filter( 'the_content', 'pro_single_content' );
		//$this->_add_filter( 'wp_title', 'custom_wp_title', 10, 2 );
		$this->_add_filter( 'the_title', 'custom_title' );
		$this->_add_action( 'wp_enqueue_scripts', 'front_scripts' );

		$this->_add_filter( 'get_edit_post_link', 'hide_edit_post_link' );
	}

	/**
	 * @param $link
	 *
	 * @return null
	 */
	function hide_edit_post_link( $link ) {
		if ( get_post_type() == 'jbp_job' || get_post_type() == 'jbp_pro' ) {
			return null;
		}

		return $link;
	}

	/**
	 * Register script and style for frontend
	 */
	function front_scripts() {
		$plugin = JobsExperts_Plugin::instance();
		//jQuery
		wp_enqueue_script( 'jquery' );
		//core css
		wp_register_style( 'jobs-plus', $plugin->_module_url . 'assets/css/jobs-plus.css', array(), JBP_VERSION );
		wp_register_style( 'jobs-plus-shortcode', $plugin->_module_url . 'assets/css/job-plus-shortcode.css' );
		wp_register_style( 'job-plus-widgets', $plugin->_module_url . 'assets/css/job-plus-widgets.css' );
		//bootstrap js
		wp_register_script( 'jbp_bootstrap', $plugin->_module_url . 'assets/bootstrap/js/bootstrap.js' );
	}

	/**
	 * Main core function in this class,handle the request
	 */
	function on_init() {
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['_wpnonce'] ) ) {
			$plugin      = JobsExperts_Plugin::instance();
			$page_module = $plugin->page_module();
			if ( wp_verify_nonce( $_POST['_wpnonce'], 'jbp_add_job' ) ) {
				$data           = $_POST['JobsExperts_Core_Models_Job'];
				$data['status'] = $_POST['status'];
				$result         = JobsExperts_Core_Controllers_Job::save( $data );
				if ( $result === true ) {
					wp_redirect( add_query_arg( array( 'post_status' => 1 ), get_permalink( $page_module->page( $page_module::MY_JOB ) ) ) );
				} else {
					JobsExperts_Plugin::instance()->global['jbp_job'] = $result;
				}
			} elseif ( wp_verify_nonce( $_POST['_wpnonce'], 'jbp_submit_image' ) ) {
				// These files need to be included as dependencies when on the front end.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				set_time_limit( 0 );
				$id = $_POST['id'];
				if ( ! empty( $id ) ) {
					$att = get_post( $id );
					//have file upload
					if ( isset( $_FILES['p_images'] ) && ! empty( $_FILES['p_images'] ) ) {
						$media_id = media_handle_upload( 'p_images', $att->post_parent );
					} else {
						$media_id = $id;
					}
				} else {
					//have file upload
					if ( isset( $_FILES['p_images'] ) && ! empty( $_FILES['p_images'] ) ) {
						$media_id = media_handle_upload( 'p_images', 0 );
					} else {
						//dont have file,manual create attachment
						$media_id = wp_insert_post( array(
							'post_type'   => 'attachment',
							'post_status' => 'publish'
						) );
					}
				}
				////
				if ( is_wp_error( $media_id ) ) {
					// There was an error uploading the image.
					echo '<div id="result">' . json_encode( array(
							'status' => 'fail',
							'msg'    => $media_id->get_error_message()
						) ) . '</div>';
					die;
				} else {
					$src = wp_get_attachment_image_src( $media_id, 'medium' );
					//update meta
					update_post_meta( $media_id, 'portfolio_link', jbp_filter_text( $_POST['portfolio_link'] ) );
					update_post_meta( $media_id, 'portfolio_des', jbp_filter_text( $_POST['portfolio_des'] ) );
					echo '<div id="result">' . json_encode( array(
							'status'    => 'success',
							'id'        => $media_id,
							'type'      => get_post_mime_type( $media_id ),
							'image_url' => $src[0],
							'name'      => jbp_shorten_text( pathinfo( get_attached_file( $media_id ), PATHINFO_BASENAME ), 50 ),
							'size'      => jbp_format_bytes( filesize( get_attached_file( $media_id ) ) )
						) ) . '</div>';
					die;
				}
				die;
			} elseif ( wp_verify_nonce( $_POST['_wpnonce'], 'jbp_add_pro' ) ) {
				$result = JobsExperts_Core_Controllers_Pro::save( $_POST['JobsExperts_Core_Models_Pro'] );
				if ( $result === true ) {
					wp_redirect( add_query_arg( array(
						'post_status' => 1
					), get_permalink( $page_module->page( $page_module::MY_EXPERT ) ) ) );
				} else {
					JobsExperts_Plugin::instance()->global['jbp_pro'] = $result;
				}
			} elseif ( wp_verify_nonce( $_POST['_wpnonce'], 'jbp_contact' ) && ! empty( $_POST['jbp_contact_type'] ) ) {
				$type = $_POST['jbp_contact_type'];
				$id   = $_POST['id'];
				//build content
				switch ( $type ) {
					case 'pro':
						JobsExperts_Core_Controllers_Pro::send_contact( $id );
						break;
					case 'job':
						JobsExperts_Core_Controllers_Job::send_contact( $id );
						break;
				}
			} elseif ( isset( $_POST['delete_job'] ) && isset( $_POST['job_id'] ) ) {
				if ( wp_verify_nonce( $_POST['_wpnonce'], 'delete_job_' . $_POST['job_id'] ) ) {
					JobsExperts_Core_Controllers_Job::delete_job( $_POST['job_id'] );
					wp_redirect( add_query_arg( array(
						'post_status' => '2'
					), get_permalink( $page_module->page( $page_module::MY_JOB ) ) ) );
					exit;
				}
			} elseif ( isset( $_POST['delete_expert'] ) && isset( $_POST['expert_id'] ) ) {
				if ( wp_verify_nonce( $_POST['_wpnonce'], 'delete_expert_' . $_POST['expert_id'] ) ) {
					JobsExperts_Core_Controllers_Pro::delete_expert( $_POST['expert_id'] );
					wp_redirect( add_query_arg( array(
						'post_status' => '2'
					), get_permalink( $page_module->page( $page_module::MY_EXPERT ) ) ) );
					exit;
				}
			}
		}
	}

	function add_rewrite_rule() {
		$plugin = JobsExperts_Plugin::instance();

		$slug = $plugin->get_job_type()->has_archive;

		add_rewrite_rule( "{$slug}/author/([^/]+)",
			"index.php?post_type=jbp_job&author_name=\$matches[1]", 'top' );

		add_rewrite_rule( "{$slug}/author/([^/]+)/page/?([2-9][0-9]*)",
			"index.php?post_type=jbp_job&author_name=\$matches[1]&paged=\$matches[2]", 'top' );

		$slug = $plugin->get_expert_type()->has_archive;

		add_rewrite_rule( "{$slug}/author/([^/]+)",
			"index.php?post_type=jbp_pro&author_name=\$matches[1]", 'top' );

		add_rewrite_rule( "{$slug}/author/([^/]+)/page/?([2-9][0-9]*)",
			"index.php?post_type=jbp_pro&author_name=\$matches[1]&paged=\$matches[2]", 'top' );
	}

	/**
	 * This function will determine the current request type, and load the virtual page of each
	 *
	 * @param $template
	 *
	 * @return string
	 */
	function process_virtual_page_content( $template ) {
		global $wp_query;
		//this is for jobs section
		$page_factory = JobsExperts_Plugin::instance()->page_module();
		if ( get_query_var( 'post_type' ) == 'jbp_job' && ! is_404() ) {
			global $wp_query;
			$template = array( 'page.php', 'index.php' );
			if ( is_archive( 'jbp_job' ) ) {
				$vpost                = get_post( $page_factory->page( $page_factory::JOB_LISTING ) );
				$wp_query->posts      = array( $vpost );
				$wp_query->post_count = 1;
				$template             = array_merge( array( $vpost->post_name . '-page.php' ), $template );

			}
			$template = locate_template( $template );
		}
		//yah, experts time
		if ( get_query_var( 'post_type' ) == 'jbp_pro' ) {
			$template = locate_template( array( 'page.php', 'index.php' ) );
			$template = array( 'page.php', 'index.php' );
			if ( is_archive( 'jbp_job' ) ) {
				$vpost = get_post( $page_factory->page( $page_factory::JOB_LISTING ) );
				global $wp_query;
				$wp_query->posts      = array( get_post( $page_factory->page( $page_factory::EXPERT_LISTING ) ) );
				$wp_query->post_count = 1;
				$template             = array_merge( array( $vpost->post_name . '-page.php' ), $template );
			}
			$template = locate_template( $template );
		}

		if ( is_tax( array( 'jbp_category', 'jbp_skills_tag' ) ) ) {
			global $wp_query;
			$template = array( 'page.php', 'index.php' );
			if ( is_archive( 'jbp_job' ) ) {
				$vpost                = get_post( $page_factory->page( $page_factory::JOB_LISTING ) );
				$wp_query->posts      = array( $vpost );
				$wp_query->post_count = 1;
				$template             = array_merge( array( $vpost->post_name . '-page.php' ), $template );

			}
			$template = locate_template( $template );
		}

		return $template;
	}

	function job_single_content( $content ) {
		$page_factory = JobsExperts_Plugin::instance()->page_module();
		if ( is_singular( 'jbp_job' ) && ! $page_factory::is_core_page( get_the_ID() ) && ! is_404() ) {
			return do_shortcode( '[jbp-job-single-page]' );
		}

		return $content;
	}

	function pro_single_content( $content ) {
		$page_factory = JobsExperts_Plugin::instance()->page_module();
		if ( is_singular( 'jbp_pro' ) && ! $page_factory::is_core_page( get_the_ID() ) && ! is_404() ) {
			return do_shortcode( '[jbp-pro-single-page]' );
		}

		return $content;
	}

	function custom_title( $title ) {
		if ( is_tax( 'jbp_category' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'jbp_category' ), 'jbp_category' );

			return __( 'Job Category: ', JBP_TEXT_DOMAIN ) . ' ' . $term->name;
		}

		return $title;
	}

	private function is_current_core_page() {
		$page_factory = JobsExperts_Plugin::instance()->page_module();
		$v_id         = $page_factory::find_core_page_by_name( get_query_var( 'jbp_job' ) );
		if ( $v_id !== false ) {
			return get_post( $v_id );
		}

		return null;
	}
}
