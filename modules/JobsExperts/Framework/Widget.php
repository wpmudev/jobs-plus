<?php

/**
 * Base class for widget
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_Widget extends JobsExperts_Framework_Module {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_action( 'widgets_init', 'widget_init' );
	}

	function widget_init() {
		//include the widgets
		$widgets = glob( JBP_PLUGIN_DIR . '/classes/JobsExperts_Framework/Widgets/*.php' );
		foreach ( $widgets as $widget ) {
			if ( file_exists( $widget ) ) {
				include $widget;
			}
		}
	}
}