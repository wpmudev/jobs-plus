<?php
$jbp_widgets = array(
	'class-widget-recent-job-posts' => 'WP_Widget_Recent_Job_Posts',
	'class-widget-recent-experts'   => 'WP_Widget_Recent_Experts',
	'class-widget-search-jobs'      => 'WP_Widget_Search_Jobs',
	'class-widget-search-experts'   => 'WP_Widget_Search_Experts',
	'class-widget-landing-page'     => 'WP_Widget_Landing_Page'
);

foreach ( $jbp_widgets as $file => $widget ) {
	$file_path = JBP_PLUGIN_DIR . 'class/' . $file . '.php';
	if ( file_exists( $file_path ) ) {
		include $file_path;
		add_action( 'widgets_init', create_function( '', 'register_widget( "' . $widget . '" );' ) );
	}
}