<?php
/**
Plugin Name: Jobs +
Plugin URI: http://premium.wpmudev.org/jobs-plus/
Description:
Version: 1.0 beta 16
Author: WPMU DEV
Author URI: http://premium.wpmudev.org
Text Domain: jbp
Domain Path: languages
Network: false
WDP ID: ***
License: GPLv2 or later
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
$pinfo = pathinfo(__FILE__);
define( 'JBP_PLUGIN', basename($pinfo['dirname']) .'/'. $pinfo['basename'] );
// define the plugin folder url
define( 'JBP_PLUGIN_URL', plugin_dir_url(__FILE__) );
// define the plugin folder dir
define( 'JBP_PLUGIN_DIR', plugin_dir_path(__FILE__) );
// The text domain for strings localization
define( 'JBP_TEXT_DOMAIN', 'jbp' );
// The key for the options array
define( 'JBP_SETTINGS_NAME', 'jobs_plus_settings' );
// The key for the options array
define( 'JBP_CAPTCHA', 'jbp_captcha' );

//Script Versions
define( 'IMAGESLOADED',            'v3.0.4' );
define( 'JQUERYUI_EDITABLE',       'v1.4.6' );
define( 'JQUERY_COOKIE',           'v1.3.1' );
define( 'JQUERY_ELLIPSIS',         'v1.0.8' );
define( 'JQUERY_FORMAT_CURRENCY',  'v1.4.0' );
define( 'JQUERY_IFRAME_TRANSPORT', 'v1.1'   );
define( 'JQUERY_MAGNIFIC_POPUP',   'v0.9.5' );
define( 'JQUERY_RATEIT',           'v1.0.9' );
define( 'SELECT2',                 'v3.4.3' );
define( 'MASONRY',                 'v3.1.5' );

//metadata keys
define('JBP_POST_VIEWS_KEY',    '_jbp_post_views_count');
define('JBP_PRO_CERTIFIED_KEY', '_jbp_pro_certified');
define('JBP_JOB_EXPIRES_KEY',   '_jbp_job_expires');

define('JBP_PRO_PATTERN_KEY',   '_jbp_pro');

define('JBP_PRO_ARCHIVE_FLAG',  'pro-archive-page');
define('JBP_PRO_TAXONOMY_FLAG', 'pro-taxonomy-page');
define('JBP_PRO_CONTACT_FLAG',  'pro-contact-page');
define('JBP_PRO_SEARCH_FLAG',   'pro-search-page');
define('JBP_PRO_SINGLE_FLAG',   'pro-single-page');
define('JBP_PRO_UPDATE_FLAG',   'pro-update-page');


define('JBP_JOB_PATTERN_KEY',   '_jbp_job');

define('JBP_JOB_ARCHIVE_FLAG',  'job-archive-page');
define('JBP_JOB_TAXONOMY_FLAG', 'job-taxonomy-page');
define('JBP_JOB_CONTACT_FLAG',  'job-contact-page');
define('JBP_JOB_SEARCH_FLAG',   'job-search-page');
define('JBP_JOB_SINGLE_FLAG',   'job-single-page');
define('JBP_JOB_UPDATE_FLAG',   'job-update-page');


//Rating  metadata keys
define('JBP_PRO_RATING_KEY',    '_jbp_pro_rating');
define('JBP_PRO_AVERAGE_KEY',   '_jbp_pro_average');
define('JBP_PRO_VOTERS_KEY' ,   '_jbp_pro_voters');
define('JBP_PRO_VOTED_KEY',     '_jbp_pro_voted');
define('JBP_PRO_REPUTATION_KEY','_jbp_pro_reputation');

//Load everything
require_once JBP_PLUGIN_DIR . 'lib/custompress/loader.php';
require_once JBP_PLUGIN_DIR . 'class/class-core.php';
require_once JBP_PLUGIN_DIR . 'class/functions.php';
require_once JBP_PLUGIN_DIR . 'class/class-term-img.php';
require_once JBP_PLUGIN_DIR . 'class/class-widgets.php';

@include_once 'class/class-widgets.php';

/* -------------------- WPMU DEV Dashboard Notice -------------------- */
global $wpmudev_notices;
$wpmudev_notices[] = array( 'id'=> 'xxx',
'name'=> 'Jobs +',
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

include_once( JBP_PLUGIN_DIR .'lib/wpmudev-dash-notification.php' );
