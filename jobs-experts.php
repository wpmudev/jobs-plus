<?php
/**
 * Plugin Name: Jobs and Experts
 * Plugin URI: http://premium.wpmudev.org/jobs-plus/
 * Description: Match people with projects to industry professionals – it’s more than your average WordPress jobs board.
 * Version: 1.0.1.4
 * Author:WPMU DEV
 * Author URI: http://premium.wpmudev.org
 * Text Domain: jbp
 * Domain Path: languages
 * Network: false
 * WDP ID: 912971
 * License: GPLv2 or later
 */

// Define plugin version
define('JBP_VERSION', '1.0.1.4');

// define the plugin file signature
$pinfo = pathinfo(__FILE__);
define('JBP_PLUGIN', basename($pinfo['dirname']) . '/' . $pinfo['basename']);
// define the plugin folder url
define('JBP_PLUGIN_URL', plugin_dir_url(__FILE__));
// define the plugin folder dir
define('JBP_PLUGIN_DIR', plugin_dir_path(__FILE__));
// The text domain for strings localization
define('JBP_TEXT_DOMAIN', 'jbp');


/**
 * Automatically loads classes for the plugin.
 *
 * @since 1.0.0
 *
 * @param string $class The class name to autoload.
 *
 * @return boolean Returns TRUE if the class is located. Otherwise FALSE.
 */
function jobs_experts_autoloader($class)
{
    $basedir = dirname(__FILE__);
    //prevent class name too long
    $shortcuts = array(
        'JobsExperts_Modules'
    );

    $namespaces = array('JobsExperts', 'WPMUDEV');

    foreach ($namespaces as $namespace) {
        if (substr($class, 0, strlen($namespace)) == $namespace) {
            $filename = $basedir . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            if (is_readable($filename)) {
                require $filename;

                return true;
            }
        }
    }

    return false;
}

//register autoload
spl_autoload_register('jobs_experts_autoloader');

global $jobs_experts_plugin;

function jobs_experts_start()
{
    global $jobs_experts_plugin;
    // instantiate the plugin
    $jobs_experts_plugin = JobsExperts_Plugin::instance();
    //load data
    $jobs_experts_plugin->set_module(JobsExperts_Core_CustomContent::NAME);

    if (is_admin()) {
        $jobs_experts_plugin->set_module(JobsExperts_Core_Backend::NAME);
    } else {
        $jobs_experts_plugin->set_module(JobsExperts_Core_Frontend::NAME);
        $jobs_experts_plugin->set_module(JobsExperts_Shortcode::NAME);
    }
    //load components
    $jobs_experts_plugin->set_module(JobsExperts_Components::NAME);
    $jobs_experts_plugin->set_module(JobsExperts_AddOn::NAME);
    //load ajax
    $jobs_experts_plugin->set_module(JobsExperts_Core_Ajax::NAME);
    //load widget
    //$jobs_experts_plugin->set_module( JobsExperts_Widget::NAME );
}

//some shorthand function needed
function get_max_file_upload()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $upload_mb = min($max_upload, $max_post, $memory_limit);

    return $upload_mb;
}

function jbp_format_bytes($bytes, $precision = 2)
{

    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function jbp_shorten_text($content, $charlength, $is_html = false)
{
    if ($is_html) {
        return truncate_html($charlength, $content);
    } else {
        if (mb_strlen($content) > $charlength) {
            $subex = mb_substr($content, 0, $charlength - 5);
            $exwords = explode(' ', $subex);
            $excut = -(mb_strlen($exwords[count($exwords) - 1]));
            $content = $subex;
            $content = rtrim($content) . ' ...';

            return $content;
        }
    }

    return $content;
}

if (!function_exists('money_format')) {
    function money_format($format, $number)
    {
        $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
            '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar' => preg_match('/\=(.)/', $fmatch[1], $match) ?
                    $match[1] : ' ',
                'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                    $match[0] : '+',
                'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft' => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                    ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                    $csuffix;
            } else {
                $currency = '';
            }
            $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],
                $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                    STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }
}

function get_model_instance($class)
{
    $instance = call_user_func(array($class,'instance'));
    return $instance;
}

function truncate_html($maxLength, $html, $isUtf8 = true)
{
    ob_start();
    $printedLength = 0;
    $position = 0;
    $tags = array();

    // For UTF-8, we need to count multibyte sequences as one character.
    $re = $isUtf8
        ? '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;|[\x80-\xFF][\x80-\xBF]*}'
        : '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}';

    while ($printedLength < $maxLength && preg_match($re, $html, $match, PREG_OFFSET_CAPTURE, $position)) {
        list($tag, $tagPosition) = $match[0];

        // Print text leading up to the tag.
        $str = substr($html, $position, $tagPosition - $position);
        if ($printedLength + strlen($str) > $maxLength) {
            print(substr($str, 0, $maxLength - $printedLength));
            $printedLength = $maxLength;
            break;
        }

        print($str);
        $printedLength += strlen($str);
        if ($printedLength >= $maxLength) break;

        if ($tag[0] == '&' || ord($tag) >= 0x80) {
            // Pass the entity or UTF-8 multibyte sequence through unchanged.
            print($tag);
            $printedLength++;
        } else {
            // Handle the tag.
            $tagName = $match[1][0];
            if ($tag[1] == '/') {
                // This is a closing tag.

                $openingTag = array_pop($tags);
                assert($openingTag == $tagName); // check that tags are properly nested.

                print($tag);
            } else if ($tag[strlen($tag) - 2] == '/') {
                // Self-closing tag.
                print($tag);
            } else {
                // Opening tag.
                print($tag);
                $tags[] = $tagName;
            }
        }

        // Continue after the tag.
        $position = $tagPosition + strlen($tag);
    }

    // Print any remaining text.
    if ($printedLength < $maxLength && $position < strlen($html))
        print(substr($html, $position, $maxLength - $printedLength));

    // Close any open tags.
    while (!empty($tags))
        printf('</%s>', array_pop($tags));
    return ob_get_clean();
}

function jbp_filter_text($text)
{
    $allowed_tags = wp_kses_allowed_html('post');

    return wp_kses($text, $allowed_tags);
}

//add action to load language
add_action('plugins_loaded', 'jbp_load_languages');
function jbp_load_languages()
{
    load_plugin_textdomain(JBP_TEXT_DOMAIN, false, plugin_basename(JBP_PLUGIN_DIR . 'languages/'));
}

jobs_experts_start();
/* -------------------- WPMU DEV Dashboard Notice -------------------- */
global $wpmudev_notices;
$wpmudev_notices[] = array('id' => 'xxx',
    'name' => 'Jobs +',
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
    ));

include_once(JBP_PLUGIN_DIR . 'ext/wpmudev-dash-notification.php');
