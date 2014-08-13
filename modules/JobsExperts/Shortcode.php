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
 * Shortcode module.
 *
 * @category JobsExperts_Framework
 * @package  Module
 *
 * @since    1.0.0
 */
class JobsExperts_Shortcode extends JobsExperts_Framework_Module {
	const NAME = __CLASS__;

	/**
	 * The shortcode
	 * @var array
	 */
	public $shortcodes = array();

	public function __construct() {
		//load all shortcode
		$shortcodes = glob( JBP_PLUGIN_DIR . '/modules/JobsExperts/Core/Shortcode/*.php' );
		foreach ( $shortcodes as $shortcode ) {
			if ( file_exists( $shortcode ) ) {
				include $shortcode;
			}
		}
	}

	public function can_view( $view = 'both' ) {
		$view = strtolower( $view );
		if ( is_user_logged_in() ) {
			if ( $view == 'loggedout' ) {
				return false;
			}
		} else {
			if ( $view == 'loggedin' ) {
				return false;
			}
		}

		return true;
	}

	protected function custom_template($template){
		ob_start();
		include locate_template( $template );

		return ob_end_clean();
	}

	protected function count_user_posts_by_type( $user_id = 0, $post_type = 'post' ) {
		global $wpdb;

		$where = get_posts_by_author_sql( $post_type, TRUE, $user_id );

		if ( in_array( $post_type, array( 'jbp_pro', 'jbp_job' ) ) ) {
			$where = str_replace( "post_status = 'publish'", "post_status = 'publish' OR post_status = 'draft'", $where );
		}
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

		return apply_filters( 'get_usernumposts', $count, $user_id );
	}
}