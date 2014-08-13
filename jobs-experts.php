<?php
/**
 * Plugin Name: Jobs and Experts
 * Plugin URI: http://premium.wpmudev.org/jobs-plus/
 * Description:
 * Version: 1.0 beta 24
 * Author:Hoang Ngo (WPMU DEV) Arnold Bailey(WPMU DEV)
 * Author URI: http://premium.wpmudev.org
 * Text Domain: jbp
 * Domain Path: languages
 * Network: false
 * WDP ID: ***
 * License: GPLv2 or later
 */

// Define plugin version
define( 'JBP_VERSION', '1.0' );

// define the plugin file signature
$pinfo = pathinfo( __FILE__ );
define( 'JBP_PLUGIN', basename( $pinfo['dirname'] ) . '/' . $pinfo['basename'] );
// define the plugin folder url
define( 'JBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// define the plugin folder dir
define( 'JBP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// The text domain for strings localization
define( 'JBP_TEXT_DOMAIN', 'jbp' );


/**
 * Automatically loads classes for the plugin.
 *
 * @since 1.0.0
 *
 * @param string $class The class name to autoload.
 *
 * @return boolean Returns TRUE if the class is located. Otherwise FALSE.
 */
function jobs_experts_autoloader( $class ) {
	$basedir = dirname( __FILE__ );
	//prevent class name too long
	$shortcuts = array(
		'JobsExperts_Modules'
	);

	$namespaces = array( 'JobsExperts', 'WPMUDEV' );

	foreach ( $namespaces as $namespace ) {
		if ( substr( $class, 0, strlen( $namespace ) ) == $namespace ) {
			$filename = $basedir . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $class ) . '.php';
			if ( is_readable( $filename ) ) {
				require $filename;

				return true;
			}
		}
	}

	return false;
}

//register autoload
spl_autoload_register( 'jobs_experts_autoloader' );

global $jobs_experts_plugin;

function jobs_experts_start() {
	global $jobs_experts_plugin;
	// instantiate the plugin
	$jobs_experts_plugin = JobsExperts_Plugin::instance();
	//load data
	$jobs_experts_plugin->set_module( JobsExperts_Core_CustomContent::NAME );

	if ( is_admin() ) {
		$jobs_experts_plugin->set_module( JobsExperts_Core_Backend::NAME );
	} else {
		$jobs_experts_plugin->set_module( JobsExperts_Core_Frontend::NAME );
		$jobs_experts_plugin->set_module( JobsExperts_Shortcode::NAME );
	}
	//load components
	$jobs_experts_plugin->set_module( JobsExperts_Components::NAME );
	//load ajax
	$jobs_experts_plugin->set_module( JobsExperts_Core_Ajax::NAME );
	//load widget
	//$jobs_experts_plugin->set_module( JobsExperts_Widget::NAME );
}

//some shorthand function needed
function get_max_file_upload() {
	$max_upload   = (int) ( ini_get( 'upload_max_filesize' ) );
	$max_post     = (int) ( ini_get( 'post_max_size' ) );
	$memory_limit = (int) ( ini_get( 'memory_limit' ) );
	$upload_mb    = min( $max_upload, $max_post, $memory_limit );

	return $upload_mb;
}

function jbp_format_bytes( $bytes, $precision = 2 ) {

	if ( $bytes >= 1073741824 ) {
		$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
	} elseif ( $bytes >= 1048576 ) {
		$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
	} elseif ( $bytes >= 1024 ) {
		$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
	} elseif ( $bytes > 1 ) {
		$bytes = $bytes . ' bytes';
	} elseif ( $bytes == 1 ) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}

function jbp_shorten_text( $content, $charlength ) {
	if ( mb_strlen( $content ) > $charlength ) {
		$subex   = mb_substr( $content, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut   = - ( mb_strlen( $exwords[count( $exwords ) - 1] ) );
		$content = $subex;
		$content = rtrim( $content ) . ' ...';

		return $content;
	}

	return $content;
}

function jbp_filter_text( $text ) {
	$allowed_tags = wp_kses_allowed_html( 'post' );

	return wp_kses( $text, $allowed_tags );
}

//add action to load language
add_action( 'plugins_loaded', 'jbp_load_languages' );
function jbp_load_languages() {
	load_plugin_textdomain( JBP_TEXT_DOMAIN, false, plugin_basename( JBP_PLUGIN_DIR . 'languages/' ) );
}

jobs_experts_start();
/* -------------------- WPMU DEV Dashboard Notice -------------------- */
global $wpmudev_notices;
$wpmudev_notices[] = array( 'id'      => 'xxx',
							'name'    => 'Jobs +',
							'screens' => array(
								'plugins',
								'jbp_job_page_jobs-plus-menu',
								'edit-jbp_job',
								'edit-jbp_category',
								'edit-jbp_tag',
								'edit-jbp_skills_tag',
								'jbp_job',
								'edit-jbp_pro',
								'jbp_pro',
							) );

include_once( JBP_PLUGIN_DIR . 'ext/wpmudev-dash-notification.php' );
