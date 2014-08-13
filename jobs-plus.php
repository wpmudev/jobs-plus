<?php
/**
 * Plugin Name: Jobs and Experts
 * Plugin URI: http://premium.wpmudev.org/jobs-plus/
 * Description:
 * Version: 1.0 beta 24
 * Author:Arnold Bailey Hoang Ngo (WPMU DEV)
 * Author URI: http://premium.wpmudev.org
 * Text Domain: jbp
 * Domain Path: languages
 * Network: false
 * WDP ID: ***
 * License: GPLv2 or later
 */
/*

Copyright 2013 Incsub, (http://incsub.com)

Author - Arnold Bailey - http://webwrights.com

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Define plugin version
define( 'JOBS_PLUS_VERSION', '1.0' );

// define the plugin file signature
$pinfo = pathinfo( __FILE__ );
define( 'JBP_PLUGIN', basename( $pinfo['dirname'] ) . '/' . $pinfo['basename'] );
// define the plugin folder url
define( 'JBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// define the plugin folder dir
define( 'JBP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
// The text domain for strings localization
define( 'JBP_TEXT_DOMAIN', 'jbp' );
// The key for the options array
define( 'JBP_SETTINGS_NAME', 'jobs_plus_settings' );
// The key for the options array
define( 'JBP_CAPTCHA', 'jbp_captcha' );

//Script Versions
define( 'IMAGESLOADED', 'v3.0.4' );
define( 'JQUERYUI_EDITABLE', 'v1.4.6' );
define( 'JQUERY_COOKIE', 'v1.3.1' );
define( 'JQUERY_ELLIPSIS', 'v1.0.8' );
define( 'JQUERY_FORMAT_CURRENCY', 'v1.4.0' );
define( 'JQUERY_IFRAME_TRANSPORT', 'v1.1' );
define( 'JQUERY_MAGNIFIC_POPUP', 'v0.9.5' );
define( 'JQUERY_RATEIT', 'v1.0.9' );
define( 'SELECT2', 'v3.4.3' );
define( 'MASONRY', 'v3.1.5' );

//Rating  metadata keys
define( 'JBP_PRO_RATING_KEY', '_jbp_pro_rating' );
define( 'JBP_PRO_AVERAGE_KEY', '_jbp_pro_average' );
define( 'JBP_PRO_VOTERS_KEY', '_jbp_pro_voters' );
define( 'JBP_PRO_VOTED_KEY', '_jbp_pro_voted' );
define( 'JBP_PRO_REPUTATION_KEY', '_jbp_pro_reputation' );

//metadata keys
define( 'JBP_POST_VIEWS_KEY', '_jbp_post_views_count' );
define( 'JBP_PRO_CERTIFIED_KEY', '_jbp_pro_certified' );
define( 'JBP_JOB_EXPIRES_KEY', '_jbp_job_expires' );

define( 'JBP_PRO_VIRTUAL_KEY', '_jbp_pro' );

define( 'JBP_PRO_ARCHIVE_FLAG', 'pro-archive-page' );
define( 'JBP_PRO_TAXONOMY_FLAG', 'pro-taxonomy-page' );
define( 'JBP_PRO_CONTACT_FLAG', 'pro-contact-page' );
define( 'JBP_PRO_SEARCH_FLAG', 'pro-search-page' );
define( 'JBP_PRO_SINGLE_FLAG', 'pro-single-page' );
define( 'JBP_PRO_UPDATE_FLAG', 'pro-update-page' );
define( 'JBP_PRO_EMPTY_FLAG', 'pro_empty_page' );

define( 'JBP_JOB_VIRTUAL_KEY', '_jbp_job' );

define( 'JBP_JOB_ARCHIVE_FLAG', 'job-archive-page' );
define( 'JBP_JOB_TAXONOMY_FLAG', 'job-taxonomy-page' );
define( 'JBP_JOB_CONTACT_FLAG', 'job-contact-page' );
define( 'JBP_JOB_SEARCH_FLAG', 'job-search-page' );
define( 'JBP_JOB_SINGLE_FLAG', 'job-single-page' );
define( 'JBP_JOB_UPDATE_FLAG', 'job-update-page' );
define( 'JBP_JOB_EMPTY_FLAG', 'job_empty_page' );

define( 'JBP_DEMO_VIRTUAL_KEY', '_jbp_demo' );

define( 'JBP_DEMO_LANDING_FLAG', 'job-landing-page' );
define( 'JBP_DEMO_PRO_FLAG', 'job-demo-pro-page' );
define( 'JBP_DEMO_JOB_FLAG', 'job-demo-job-page' );


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
	$basedir    = dirname( __FILE__ );
	$namespaces = array( 'JobsExperts', 'WPMUDEV' );
	foreach ( $namespaces as $namespace ) {
		if ( substr( $class, 0, strlen( $namespace ) ) == $namespace ) {
			$filename = $basedir . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $class ) . '.php';
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

//Load everything
//if need to check for the custompress init
/*require_once JBP_PLUGIN_DIR . 'class/class-data.php';


require_once JBP_PLUGIN_DIR . 'class/class-core.php';
require_once JBP_PLUGIN_DIR . 'class/functions.php';
require_once JBP_PLUGIN_DIR . 'class/class-term-img.php';*/
require_once JBP_PLUGIN_DIR . 'lib/custompress/loader.php';
global $jobs_experts_plugin;

function jobs_experts_start() {
	global $jobs_experts_plugin;
	// instantiate the plugin
	$jobs_experts_plugin = JobsExperts_Plugin::instance();
	//load data
	$jobs_experts_plugin->set_module( JobsExperts_Module_CustomContent::NAME );

	if ( is_admin() ) {
		$jobs_experts_plugin->set_module( JobsExperts_Module_Backend::NAME );
	} else {
		$jobs_experts_plugin->set_module( JobsExperts_Module_Frontend::NAME );
		$jobs_experts_plugin->set_module( JobsExperts_Shortcode::NAME );
	}
	//term image
	$jobs_experts_plugin->set_module( JobsExperts_Module_TermImage::NAME );
	//load widget
	$jobs_experts_plugin->set_module( JobsExperts_Widget::NAME );
}

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
