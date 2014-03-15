<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/


/**
* is_certified - Is the $user_id marked as certified in his profile
* if $user_id is empty the the current_user will be used
* @user_id int
* return bool
*/

/**
* Prevent comments display
*
*/
function no_comments(){
	add_filter('comments_open', create_function('', 'return false;') );
}

function is_certified($user_id = 0){
	global $Jobs_Plus_Core;
	return $Jobs_Plus_Core->is_certified($user_id);
}

function get_the_rating( $post = 0, $before = '', $after = '' ){
	global $Jobs_Plus_Core;

	$post = get_post($post);
	return $before . sprintf('
	<div class="rateit"
	data-rateit-readonly="true"
	data-rateit-ispreset="true"
	data-rateit-value="%s"
	data-ajax="%s"
	data-nonce="%s"
	></div>',
	get_post_meta($post->ID, JBP_PRO_AVERAGE_KEY, true),
	esc_attr(admin_url('admin-ajax.php') ),
	wp_create_nonce('rating') ) . $after;
}

function the_rating( $post = 0, $before = '', $after = '' ){
	echo get_the_rating($post, $before, $after);
}

function get_rate_this( $post = 0, $before = '', $after = '', $allow_reset = false ) {
	global $Jobs_Plus_Core;

	$post = get_post($post);
	if( !is_user_logged_in() ) return '';
	$rating = get_user_meta(get_current_user_id(), JBP_PRO_VOTED_KEY, true);
	$rating = empty($rating[$post->ID]) ? 0 : $rating[$post->ID];

	return $before . sprintf('
	<div class="rateit"
	data-post_id="%s"
	data-rateit-ispreset="true"
	data-rateit-value="%s"
	data-rateit-resetable="%s"
	data-ajax="%s"
	data-nonce="%s"
	></div>',
	$post->ID, $rating, $allow_reset,
	esc_attr(admin_url('admin-ajax.php') ),
	wp_create_nonce('rating') ) . $after;
}

function rate_this( $post = 0, $before = '', $after = '', $allow_reset = false) {
	echo get_rate_this($post, $before, $after, $allow_reset);
}

function get_avatar_or_gravatar($id_or_email=0, $email = '', $size=96, $default='', $alt=false){
	if(is_email($email) ) {
		$gravatar = 'http://www.gravatar.com/avatar/'
		. md5( strtolower($email) )
		. '?s=200&d=404';
		$headers = @get_headers($gravatar);
		if( preg_match("|200|", $headers[0]) )
		return get_avatar($email, $size, $default, $alt);
	}
	return get_avatar($id_or_email, $size, $default, $alt);
}

/**
* locate_jbp_template Adds
*
*/
function locate_jbp_template($template_names) {
	global $Jobs_Plus_Core;
	return @$Jobs_Plus_Core->locate_jbp_template($template_names, $load, $require_once);
}

/**
* Helper functions for versions of PHP older then 5.3
*/

/**
* str_getcsv() substitute for PHP < 5.3
*
* @$input string - string to parse
*	@$delimiter char - default ','
* @$enclosure - default '"'
* @$escape - character to escape enclosures or delimeters
* @$eol - End of line character.
*/
if (!function_exists('str_getcsv')):
function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null) {
	$temp=fopen("php://memory", "rw");
	fwrite($temp, $input);
	fseek($temp, 0);
	$r = array();
	while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) {
		$r[] = $data;
	}
	fclose($temp);
	return $r;
}
endif;

/**
* array_replace() substitute for PHP < 5.3
*
* @$array
*	@$$array1 [, $... ]
*/
if (!function_exists('array_replace') ):
function array_replace(){
	$array=array();
	$n=func_num_args();
	while ($n-- >0) {
		$array+=func_get_arg($n);
	}
	return $array;
}
endif;

/**
* array_replace_recursive() substitute for PHP < 5.3
*
* @$array
*	@$$array1 [, $... ]
*/
if(! function_exists('array_replace_recursive') ):
function array_replace_recursive($base, $replacements)
{
	foreach (array_slice(func_get_args(), 1) as $replacements) {
		$bref_stack = array(&$base);
		$head_stack = array($replacements);
		do {
			end($bref_stack);
			$bref = &$bref_stack[key($bref_stack)];
			$head = array_pop($head_stack);
			unset($bref_stack[key($bref_stack)]);
			foreach (array_keys($head) as $key) {
				if (isset($key, $bref) && is_array($bref[$key]) && is_array($head[$key])) {
					$bref_stack[] = &$bref[$key];
					$head_stack[] = $head[$key];
				} else {
					$bref[$key] = $head[$key];
				}
			}
		} while(count($head_stack));
	}
	return $base;
}
endif;

/**
* print_filters_for() Usefull for listing added filters for an action in Wordpress.
*
*/
function print_filters_for( $hook = '' ) {
	global $wp_filter;
	if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
	return;

	print '<pre>';
	print_r( $wp_filter[$hook] );
	print '</pre>';
}
