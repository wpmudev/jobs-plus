<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

if( !class_exists('FJobs_Plus_Search')):

class FJobs_Plus_Search{

	public $phrases = array();
	public $post_types = array();



	function __construct( $search = '', $post_types = 'post' ){

		$this->phrases = $this->get_phrases($search);

		$this->post_types = $this->get_post_types($post_types);
	}

	/**
	* get_phrases Parse a search string as a CSV type string - "first key phrase", "keyword" etc.
	* @$phrase string Comma separated phrases.
	* @return array of search phrases
	*/
	function get_phrases($search = ''){
		//Parse the search string breaking at commas and with quotes.
		//Trim and remove empty terms.
		$phrases = array_filter(array_map( 'trim', str_getcsv( stripslashes($search), ' '	) ) );
		return $phrases;
	}

	/**
	* get_post_types Filter an array of post_types leving only valid items
	* @$phrase string Comma separated phrases.
	* @return array of search phrases
	*/
	function get_post_types($post_types = 'post'){
		$post_types = (array)$post_types;
		return array_filter( $post_types, 'post_type_exists');
	}

	function search_string($search_string = ''){

		//Standard String search
		foreach($phrases as $phrase) {

			$args = array(
			'jbp_custom' => true,
			'posts_per_page' => -1,
			'post_type' => $post_type,
			's' => $phrase,
			'fields' => 'ids',
			);
			$search_ids = get_posts($args);
			$all_ids = array_merge($all_ids, $search_ids);
		}


	}



}

endif;

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

