<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Components extends JobsExperts_Framework_Module {
	const NAME = __CLASS__;

	/**
	 * The shortcode
	 * @var array
	 */
	public $shortcodes = array();

	public function __construct() {
		//load all shortcode
		$coms = glob( JBP_PLUGIN_DIR . '/modules/JobsExperts/Components/*.php' );
		foreach ( $coms as $com ) {
			if ( file_exists( $com ) ) {
				include $com;
			}
		}
	}

	function active_tab( $id ) {
		if ( isset( $_GET['tab'] ) ) {
			if ( $id == $_GET['tab'] ) {
				return 'class="active"';
			}
		}

		return null;
	}
}