<?php
/**
* @package Jobs+
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

if( !class_exists('Jobs_Plus_Core') ):
class Jobs_Plus_Core{

	public $plugin_dir    = JBP_PLUGIN_DIR;
	public $plugin_url    = JBP_PLUGIN_URL;
	public $text_domain	  = JBP_TEXT_DOMAIN;
	public $settings_name = JBP_SETTINGS_NAME;

	//metadata flags
	public $post_count_key    = JBP_POST_VIEWS_KEY;
	public $pro_certified_key = JBP_PRO_CERTIFIED_KEY;
	public $job_expires_key   = JBP_JOB_EXPIRES_KEY;

	public $rating_keys = array(
	'rating'      => JBP_PRO_RATING_KEY,
	'average'     => JBP_PRO_AVERAGE_KEY,
	'voters'      => JBP_PRO_VOTERS_KEY,
	'voted'       => JBP_PRO_VOTED_KEY,
	'reputation'  => JBP_PRO_REPUTATION_KEY,
	);

	//Virtual pages
	private $_add_job_page_id = 0;
	private $_add_pro_page_id = 0;

	//job file names for content substitutions
	public $job_content = array(
	'archive' => 'content-archive-job.php',
	'taxonomy' => 'content-taxonomy-job.php',
	'contact' => 'content-contact-job.php',
	//'search'  => 'content-search-job.php',
	//'search'  => 'content-search-job.php',
	'single'  => 'content-single-job.php',
	'update'  => 'content-update-job.php',
	);

	//Pro file names for content substitutions
	public $pro_content = array(
	'archive'  => 'content-archive-pro.php',
	'taxonomy' => 'content-taxonomy-pro.php',
	'contact'  => 'content-contact-pro.php',
	'search'   => 'content-search-pro.php',
	'single'   => 'content-single-pro.php',
	'update'   => 'content-single-pro.php',
	);

	public $pro_obj = null;
	public $job_obj = null;

	public $title = '';
	public $content = '';
	public $custom_type = '';


	public $jbp_errors = array();
	public $jbp_notices = array();

	public $job_labels = null;
	public $pro_labels = null;

	public $job_slug = null;
	public $pro_slug = null;

	function __construct(){
		register_activation_hook($this->plugin_dir . 'jobs-plus.php', array(&$this,'on_activate') );
		register_deactivation_hook($this->plugin_dir . 'jobs-plus.php', array(&$this,'on_deactivate') );

		add_action('plugins_loaded', array(&$this, 'on_plugins_loaded') );
		add_action('init', array(&$this, 'on_init') );
		add_action('wp_loaded', array(&$this, 'create_virtual_pages') );
		add_action('wp_print_scripts', array(&$this, 'on_print_scripts') );

		add_action('template_redirect', array(&$this, 'process_requests') );
		add_action('template_redirect', array(&$this, 'on_template_redirect') );
		add_filter('template_include', array( &$this, 'on_template_include' ) );

		//Ajax
		add_action('wp_ajax_rate_pro', array(&$this, 'on_ajax_rate_pro') );

		add_action( 'wp_ajax_jbp_job', array( &$this, 'on_ajax_jbp_job' ) );
		//add_action( 'wp_ajax_nopriv_jbp_job', array( &$this, 'on_ajax_jbp_job' ) );
		add_action( 'wp_ajax_jbp_job_status', array( &$this, 'on_ajax_jbp_job_status' ) );

		add_action( 'wp_ajax_jbp_pro', array( &$this, 'on_ajax_jbp_pro' ) );
		//add_action( 'wp_ajax_nopriv_jbp_pro', array( &$this, 'on_ajax_jbp_pro' ) );
		add_action( 'wp_ajax_jbp_pro_status', array( &$this, 'on_ajax_jbp_pro_status' ) );

		//Filters
		add_filter('request', array(&$this, 'on_request') );
		//add_filter('query_vars', array(&$this, 'on_query_vars') );
		add_filter('parse_query', array(&$this, 'on_parse_query') );
		add_filter('get_edit_post_link', array(&$this, 'on_get_edit_post_link') );
		add_filter('upload_dir', array(&$this,'custom_upload_directory') );

		add_filter('wp_mail', array(&$this,'on_wp_mail') );

		add_filter('ct_in_shortcode', array(&$this,'job_open_for_fix'), 10, 3);

		//Shortcodes
		add_shortcode( 'jbp-rating', array( &$this, 'rating_sc' ) );
		add_shortcode( 'jbp-rate-this', array( &$this, 'rate_this_sc' ) );

		add_shortcode( 'jbp-pro-gravatar', array( &$this, 'pro_gravatar_sc' ) );
		add_shortcode( 'jbp-pro-portfolio', array( &$this, 'pro_portfolio_sc' ) );
		add_shortcode( 'jbp-pro-skills', array( &$this, 'pro_skills_sc' ) );
		add_shortcode( 'jbp-pro-social', array( &$this, 'pro_social_sc' ) );

		add_shortcode( 'jbp-pro-archive', array( &$this, 'pro_archive_sc' ) );

		add_shortcode( 'jbp-pro-contact-btn', array( &$this, 'pro_contact_btn_sc' ) );
		add_shortcode( 'jbp-job-contact-btn', array( &$this, 'job_contact_btn_sc' ) );

		add_shortcode( 'jbp-job-portfolio', array( &$this, 'job_portfolio_sc' ) );
		add_shortcode( 'jbp-job-excerpt', array( &$this, 'job_excerpt_sc' ) );

	}

	/**
	* __get , __set and __isset to abstract the virtual pageids so they are only called if actually used.
	*
	*/
	function __get($name){
		$result = false;
		switch($name){
			case 'add_job_page_id':{
				if(empty($this->_add_job_page_id) ){
					$page = $this->get_page_by_meta('jbp_job', 'add_job_page' );
					$result = ($page && $page->ID > 0) ? $page->ID : 0;
					if(empty($result) ) $this->create_virtual_pages();
					$this->_add_job_page_id = $result; //Remember the number
				}
				$result = $this->_add_job_page_id;
				break;
			}

			case 'add_pro_page_id':{
				if(empty($this->_add_pro_page_id) ){
					$page = $this->get_page_by_meta('jbp_pro', 'add_pro_page' );
					$result = ($page && $page->ID > 0) ? $page->ID : 0;
					if(empty($result) ) $this->create_virtual_pages();
					$this->_add_pro_page_id = $result; //Remember the number
				}
				$result = $this->_add_pro_page_id;
				break;
			}
		}

		return $result;
	}

	function __set($name, $value){
		switch($name) {
			case 'add_job_page_id':{
				$this->_add_job_page_id = $value;
				break;
			}

			case 'add_pro_page_id':{
				$this->_add_pro_page_id = $value;
				break;
			}
		}
	}

	function __isset($name){
		switch($name) {
			case 'add_job_page_id': {
				$result = $this->_add_job_page_id > 0;
				break;
			}

			case 'add_pro_page_id': {
				$result = $this->_add_pro_page_id > 0;
				break;
			}
		}

		return $result;
	}


	/**
	* function get_setting
	* @param string $key A setting key, or -> separated list of keys to go multiple levels into an array
	* @param mixed $default Returns when setting is not set
	*
	* an easy way to get to our settings array without undefined indexes
	*/
	function get_setting($key, $default = null) {
		$settings = get_option( $this->settings_name );
		$keys = explode('->', $key);
		array_map('trim', $keys);
		if (count($keys) == 1)
		$setting = isset($settings[$keys[0]]) ? $settings[$keys[0]] : $default;
		else if (count($keys) == 2)
		$setting = isset($settings[$keys[0]][$keys[1]]) ? $settings[$keys[0]][$keys[1]] : $default;
		else if (count($keys) == 3)
		$setting = isset($settings[$keys[0]][$keys[1]][$keys[2]]) ? $settings[$keys[0]][$keys[1]][$keys[2]] : $default;
		else if (count($keys) == 4)
		$setting = isset($settings[$keys[0]][$keys[1]][$keys[2]][$keys[3]]) ? $settings[$keys[0]][$keys[1]][$keys[2]][$keys[3]] : $default;

		return apply_filters( "jobs-plus-setting".implode('', $keys), $setting, $default );
	}

	function make_clickable($ret) {

		$ret = make_clickable($ret);

		$ret = str_replace('"nofollow"', '"nofollow" target="_blank"', $ret);

		return $ret;
	}

	function on_activate(){
		flush_network_rewrite_rules();
	}

	function on_deactivate(){
		flush_network_rewrite_rules();
	}

	function on_plugins_loaded(){
		//Translations
		load_plugin_textdomain($this->text_domain, false, plugin_basename( $this->plugin_dir . 'languages/' ) );

		//If the activate flag is set then try to initalize the defaults
		if( get_site_option('jbp_activate', false))	{
			global $CustomPress_Core;
			require_once($this->plugin_dir . 'class/class-data.php');
			$CustomPress_Core->add_admin_capabilities();
			delete_site_option('jbp_activate');
		}
	}

	function on_init(){
		global $wp_rewrite, $wp_post_statuses;

		//Get custom type label references
		$jbp_job = get_post_type_object( 'jbp_job');
		$this->job_labels = $jbp_job->labels;
		$this->job_slug = $jbp_job->rewrite['slug'];


		$jbp_pro = get_post_type_object( 'jbp_pro');
		$this->pro_labels = $jbp_pro->labels;
		$this->pro_slug = $jbp_pro->rewrite['slug'];

		// add endpoints for front end special pages
		add_rewrite_endpoint('edit', EP_PAGES);
		add_rewrite_endpoint('contact', EP_PAGES);

		// post_status "virtual" for pages not to be displayed in the menus but that users should not be editing.
		register_post_status( 'virtual', array(
		'label' => __( 'Virtual', $this->text_domain ),
		'public' => false, //This trick prevents the virtual pages from appearing in the All Pages list but can be display on the front end.
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => false,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>' ),
		) );

		$this->pro_obj = get_post_type_object('jbp_pro');
		$this->job_obj = get_post_type_object('jbp_job');


		//Set the pro-thumbnail size. DEFAULT to 160x120
		$width = $this->get_setting('pro->thumb_width', 160);
		$height = $this->get_setting('pro->thumb_height', 120);
		add_image_size('pro-thumbnail', $width, $height, true);

		//Set the job-thumbnail size. DEFAULT to 160x120
		$width = $this->get_setting('job->thumb_width', 160);
		$height = $this->get_setting('job->thumb_height', 120);
		add_image_size('job-thumbnail', $width, $height, true);

		//Show ratings in comments
		if( $this->get_setting('pro->comment_ratings', false ) ) {
			add_filter('get_comment_author_link', 'on_comment_author_link');
		}
		//Need to register scripts and css early because we enqueue in
		//template_redirect so we know the page amd can only load what and when needed.
		$this->register_scripts();

		//		// Declare widget areas
		//		if(function_exists('register_sidebar') ){
		//			register_sidebar(array(
		//			'id' => 'stamp-widget',
		//			'name' => 'Pro Stamp',
		//			'description' => sprintf(__('%s widget area', $this->text_domain), $this->pro_obj->labels->name),
		//			'before_widget' => '<li id="%1$s" class="widget %2$s">' . "\n",
		//			'after_widget' => "</li>\n",
		//			'before_title' => '<h2 class="widgettitle">',
		//			'after_title' => '</h2>'
		//			));
		//		}
	}

	/**
	* Register any custom scripts/css for this plugin.
	*
	*/
	function register_scripts(){

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style('jobs-plus', $this->plugin_url . 'css/jobs-plus.css', array(), JOBS_PLUS_VERSION );
		//Look for and load any custom css defined, Priority, Child, Parent, Plugin css
		$custom_css = 'jobs-plus-custom.css';
		$custom_css_url = false;
		$custom_css_url = file_exists( $this->plugin_dir . 'css/' . $custom_css) ? $this->plugin_url . 'css/' . $custom_css : $custom_css_url;
		$custom_css_url = file_exists( trailingslashit(get_template_directory()) . $custom_css) ? trailingslashit(get_template_directory_uri()) . $custom_css : $custom_css_url;
		$custom_css_url = file_exists( trailingslashit(get_stylesheet_directory()) . $custom_css) ? trailingslashit(get_stylesheet_directory_uri()) . $custom_css : $custom_css_url;
		if($custom_css_url) wp_register_style('jobs-plus-custom', $custom_css_url, array(), JOBS_PLUS_VERSION);

		//Register styles and script
		wp_register_script('jquery-iframe-transport', $this->plugin_url . "js/jquery-iframe-transport.js", array('jquery'), JQUERY_IFRAME_TRANSPORT, true );

		wp_register_style('jquery-rateit', $this->plugin_url . "css/rateit.css", array(), JQUERY_RATEIT );
		wp_register_script('jquery-rateit', $this->plugin_url . "js/jquery.rateit$suffix.js", array('jquery'), JQUERY_RATEIT, true );

		wp_register_style('jqueryui-editable', $this->plugin_url . "css/jqueryui-editable.css", array(), JQUERYUI_EDITABLE );
		//		wp_register_script('jqueryui-editable', $this->plugin_url . "js/jqueryui-editable$suffix.js", array('jquery', 'jquery-ui-tooltip', 'jquery-ui-button'), JQUERYUI_EDITABLE, true );
		wp_register_script('jqueryui-editable', $this->plugin_url . "js/jqueryui-editable.js", array('jquery', 'jquery-ui-tooltip', 'jquery-ui-button'), JQUERYUI_EDITABLE, true );

		wp_register_style('magnific-popup', $this->plugin_url . "css/magnific-popup.css", array(), JQUERY_MAGNIFIC_POPUP );
		wp_register_script('jquery.magnific-popup', $this->plugin_url . "js/jquery.magnific-popup$suffix.js", array('jquery' ), JQUERY_MAGNIFIC_POPUP, true );

		wp_register_style('select2', $this->plugin_url . "css/select2.css", array('jobs-plus'), SELECT2 );
		wp_register_script('select2', $this->plugin_url . "js/select2$suffix.js", array('jquery' ), SELECT2, true);

		//Deregister and register newer Masonry to prevent interference
		wp_deregister_script('jquery-masonry');
		wp_register_script('jquery-masonry', $this->plugin_url . "js/masonry$suffix.js", array('jquery' ), MASONRY, true );

		wp_register_script('jquery-cookie', $this->plugin_url . 'js/jquery.cookie.js', array('jquery' ), JQUERY_COOKIE, true );
		wp_register_script('jquery-ellipsis', $this->plugin_url . 'js/jquery-ellipsis.js', array('jquery' ), JQUERY_ELLIPSIS, true );
		wp_register_script('jqueryui-editable-ext', $this->plugin_url . 'js/jqueryui-editable-ext.js', array('jqueryui-editable', 'jquery-iframe-transport' ), JOBS_PLUS_VERSION, true );
		wp_register_script('imagesloaded', $this->plugin_url . "js/imagesloaded$suffix.js", array('jquery' ), IMAGESLOADED, true );
		wp_register_script('jobs-plus', $this->plugin_url . 'js/jobs-plus.js', array('jquery', 'jquery-rateit' ), JOBS_PLUS_VERSION );
	}

	/**
	* Get a virtual page by meta value
	*
	* @return int $page[0] /bool false
	*/
	function get_page_by_meta( $post_type, $value ) {
		global $wpdb;

		//To avoid "the_posts" filters do a direct call to the database to find the post by meta
		$ids = array_keys(
		$wpdb->get_results($wpdb->prepare(
		"
		SELECT post_id
		FROM {$wpdb->postmeta}
		WHERE meta_key= %s
		AND meta_value=%s
		", "_{$post_type}", $value), OBJECT_K )
		);

		if( count($ids) != 1 ) { //There can be only one.
			foreach( $ids as $id ) { //Delete all and start over.
				wp_delete_post($id, true);
			}
			return false;
		}

		if( get_post_status( $ids[0]) == 'trash' ){ //no trash
			wp_delete_post($ids[0], true);
			return false;
		}

		if ( isset( $ids[0] ) && 0 < $ids[0] ){
			return get_post($ids[0]);
		}

		return false;
	}

	/**
	* Create the default virtual pages.
	* @return void
	*/
	function create_virtual_pages() {
		/* Create neccessary pages */

		$post_content  = __("<p>Virtual page. Editing this page won\'t change anything.<br />", $this->text_domain);
		$post_content .= __("You may edit the Title and/or the slug only.</p>", $this->text_domain);

		$current_user = wp_get_current_user();

		//jbp_job Add Job

		$page = $this->get_page_by_meta('jbp_job', 'add_job_page' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf('Add %s', $this->job_labels->singular_name),
			'post_name'      => sprintf('add-%s', $this->job_slug ),
			'post_status'    => 'virtual',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_job',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, '_jbp_job', 'add_job_page');
		} else {
			//Make sure it stays Virtual
			if( !in_array($page->post_status, array('virtual', 'trash') ) ) wp_update_post( array('ID' => $page_id, 'post_status' => 'virtual') );
		}
		$this->_add_job_page_id = $page_id; //Remember the number

		//jbp_pro Add Pro
		$page = $this->get_page_by_meta('jbp_pro', 'add_pro_page' );
		$page_id = ($page && $page->ID > 0) ? $page->ID : 0;
		if ( empty($page_id) ) {
			/* Construct args for the new post */
			$args = array(
			'post_title'     => sprintf('Add %s', $pro_labels->singular_name),
			'post_name'      => sprintf('add-%s', $this->pro_slug),
			'post_status'    => 'virtual',
			'post_author'    => $current_user->ID,
			'post_type'      => 'jbp_pro',
			'post_content'   => $post_content,
			'ping_status'    => 'closed',
			'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $args );
			$page = get_post($page_id);
			add_post_meta( $page_id, '_jbp_pro', 'add_pro_page');
		} else {
			//Make sure it stays Virtual
			if( !in_array($page->post_status, array('virtual', 'trash') ) ) wp_update_post( array('ID' => $page_id, 'post_status' => 'virtual') );
		}
		$this->_add_pro_page_id = $page_id; //Remember the number
	}

	/**
	* count_user_posts_by_type
	* @$user_id int ID of the user to count
	* @$post_type string post_type to count
	*
	* @return count int
	*/
	function count_user_posts_by_type($user_id = 0, $post_type='post') {
		global $wpdb;
		$where = get_posts_by_author_sql($post_type, TRUE, $user_id);
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );
		return apply_filters('get_usernumposts', $count, $user_id);
	}

	/**
	* on_print_script
	*/
	function on_print_scripts(){
		?>
		<script type="text/javascript">
			//translatable
			var jobs_plus_plugin_url = '<?php echo $this->plugin_url; ?>';
			var tooltipvalues = <?php
			printf("['%s', '%s', '%s', '%s', '%s'];",
			__('Not so great', $this->text_domain),
			__('Quite good', $this->text_domain),
			__('Good', $this->text_domain),
			__('Great!', $this->text_domain),
			__('Excellent!', $this->text_domain) );
			?>
		</script>
		<?php
	}

	/**
	* locate_jbp_template
	* Does not support include here so that shortcode atts will be included;
	* Uses locate_template
	*
	*/
	function locate_jbp_template( $template_names ) {

		$template = locate_template($template_names, $load, $require_once);

		if(empty($template) ) { //Try the plugin directory
			$located = '';
			foreach ( (array) $template_names as $template_name ) {
				if ( !$template_name )
				continue;
				if ( file_exists($this->plugin_dir . 'ui-front/' . $template_name)) {
					$located = $this->plugin_dir . 'ui-front/' . $template_name;
					break;
				}
			}
		}
		return $located;
	}


	/**
	* Handle special pages.
	*
	*/
	function process_requests(){
		global $wp_query;

		$_POST = stripslashes_deep($_POST);

		if ( !empty($_REQUEST['jbp_error']) ) sanitize_text_field($this->error_message($_REQUEST['jbp_error']) );
		if ( !empty($_REQUEST['jbp_notice']) ) sanitize_text_field($this->notice_message($_REQUEST['jbp_notice']) );

		if( empty($_REQUEST['_wpnonce']) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'verify') ) return;

		//Is this a jbp_job update?
		if(! empty($_POST['jbp-job-update']) ) {
			$id = $this->update_job($_POST);
			wp_redirect( add_query_arg('jbp_notice', urlencode(sprintf( __('The %s has been updated', $this->text_domain), $this->job_obj->labels->name ) ),

			trailingslashit(get_permalink($id) ) ) );
			exit;
		}

		//Is this a jbp_pro update?
		if(! empty($_POST['jbp-pro-update'] ) ) {
			$id = $this->update_pro($_POST);
			if( !empty($id) ){
				wp_redirect( add_query_arg('jbp_notice', urlencode(sprintf(__('This %s has been updated', $this->text_domain), $this->pro_obj->labels->name ) ),
				trailingslashit(get_permalink($id) ) ) );
				exit;
			}
		}

		//Is this a jbp_job contact?
		if( !empty($_POST['jbp-job-contact'] ) ) {
			$this->email_contact_job($_POST);
		}

		//Is this a jbp_pro contact?
		if( !empty($_POST['jbp-pro-contact'] ) ) {
			$this->email_contact_pro($_POST);
		}

		//var_dump($wp_query);
	}

	/**
	* Check for protected pages and redirect as necessary
	*
	*/
	function on_template_redirect(){
		global $wp_query;

		//printf('<pre>%s</pre>', print_r($wp_query, true) ); exit;

		// Check security redirection
		if ( isset( $wp_query ) ) {
			if( is_singular('jbp_job') ) 	{
				if( !current_user_can('edit_jobs') ) {

					set_query_var('edit', false);

					if($wp_query->post->ID == $this->add_job_page_id) {
						wp_redirect(add_query_arg('jbp_error',
						urlencode(sprintf(__('You must register and login to enter a %s.', $this->text_domain), $this->job_obj->labels->new_item) ),
						get_post_type_archive_link('jbp_job') ) );
						exit;
					}
				}
			}

			if( is_singular('jbp_pro') ) 	{
				if( !current_user_can('edit_pros') ) {
					set_query_var('edit', false);

					if($wp_query->post->ID == $this->add_pro_page_id) {
						wp_redirect(add_query_arg( 'jbp_error',
						urlencode(sprintf(__('You must register and login to enter a %s.', $this->text_domain), $this->pro_obj->labels->new_item) ),
						get_post_type_archive_link('jbp_pro') ) );
						exit;
					}
				}
			}
		}
	}

	/**
	* Substitute a template if requested. Any custom templates have been searched for and found.
	* Check for "customness" and let custom templates overide. Otherwise force the page template and filter the content and title.
	*
	*/
	function on_template_include($template = '') {
		global $wp_query, $post;

		//printf('<pre>%s</pre>', print_r($wp_query, true) ); exit;
		//var_dump($template);

		$this->title = '';
		$this->content = '';

		//Leave feeds alone
		if(is_feed()) return $template;

		//Default template has been selected by Wordpress at this point. Do we change it
		//Is this a custom template? Then leave it alone
		$is_custom = !in_array(pathinfo($template, PATHINFO_FILENAME), array(
		'404',
		'search',
		'front-page',
		'home',
		'taxonomy',
		'attachment',
		'single',
		'page',
		'category',
		'tag',
		'author',
		'date',
		'archive',
		'comments-popup',
		'index',
		) );

		//skip the rest
		if( $is_custom ) return $template;

		// Enqueue common for all jobs pages.
		if(in_array(get_query_var('post_type'), array('jbp_job', 'jbp_pro'))
		|| in_array(get_query_var('taxonomy'), array('jbp_category', 'jbp_tag', 'jbp_skills_tag')) ){

			wp_enqueue_style('jquery-rateit');
			wp_enqueue_style('jobs-plus');
			//wp_enqueue_style('jobs-plus-custom');
			wp_enqueue_style('magnific-popup');

			wp_enqueue_script('jquery-rateit');
			//			wp_enqueue_script('jqueryui-editable');
			//			wp_enqueue_script('jqueryui-editable-ext');
			//			wp_enqueue_style('jqueryui-editable');
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_script('jquery.magnific-popup');
			wp_enqueue_script('jquery-cookie');
			//			wp_enqueue_style('select2');
			//			wp_enqueue_script('select2');
			wp_enqueue_script('jobs-plus');
		}

		/**
		* Handle special endpoints edit, contact and search
		*/

		//Is this an jbp_job update?
		if( ( is_singular('jbp_job') && get_query_var('edit') )
		|| is_single($this->add_job_page_id) ){

			$limit = intval($this->get_setting('job->max_records', 1) );
			if( !current_user_can('create_jobs') ) {
				wp_redirect( add_query_arg('jbp_error',
				urlencode(sprintf(__('You do not have the permissions to enter a %s.', $this->text_domain), $this->job_obj->labels->new_item) ),
				get_post_type_archive_link('jbp_job') ) );
				exit;
			} elseif( !current_user_can('edit_jobs') ) {
				wp_redirect(add_query_arg('jbp_error',
				urlencode(sprintf(__('You do not have permission to edit this %s.', $this->text_domain), $this->job_obj->labels->singular_name) ),
				get_post_type_archive_link('jbp_job') ) );
				exit;
			} elseif( !get_query_var('edit') && $this->count_user_posts_by_type(get_current_user_id(), 'jbp_pro') >= $limit) {
				wp_redirect(add_query_arg('jbp_error',
				urlencode(sprintf(__('You have exceeded your quota of %s %s.', $this->text_domain), $limit, $this->job_obj->labels->name) ),
				get_post_type_archive_link('jbp_job') ) );
				exit;
			}
			//css for the edit Pages
			wp_enqueue_style('jobs-plus');
			//			wp_enqueue_style('jqueryui-editable');
			wp_enqueue_style('magnific-popup');
			wp_enqueue_script('jquery-ui-slider');
			//wp_enqueue_script('jqueryui-editable');
			//wp_enqueue_script('jqueryui-editable-ext');
			wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_script('jquery.magnific-popup');

			$this->content = $this->job_content['update'];
			$this->custom_type = 'jbp_job';

		}

		//Is this a jbp_pro update?
		elseif( (is_singular('jbp_pro') && get_query_var('edit') )
		|| (is_single($this->add_pro_page_id) ) ){

			if( is_single($this->add_pro_page_id) ){ //How many can they have
				$limit = intval($this->get_setting('pro->max_records', 1) );
				if( !current_user_can('create_pros') ) {
					wp_redirect( add_query_arg('jbp_error',
					urlencode(sprintf(__('You do not have the permissions to enter a %s.', $this->text_domain), $this->pro_obj->labels->new_item) ),
					get_post_type_archive_link('jbp_pro') ) );
					exit;
				} elseif( !current_user_can('edit_pros') ) {
					wp_redirect(add_query_arg('jbp_error',
					urlencode(sprintf(__('You do not have permission to edit this listing.', $this->text_domain), $this->pro_obj->labels->singular_name) ),
					get_post_type_archive_link('jbp_pro') ) );
					exit;
				} elseif( !get_query_var('edit') && $this->count_user_posts_by_type(get_current_user_id(), 'jbp_job') >= $limit) {
					wp_redirect(add_query_arg('jbp_error',
					urlencode(sprintf(__('You have exceeded your quota of %s %s.', $this->text_domain), $limit, $this->pro_obj->labels->name) ),
					get_post_type_archive_link('jbp_pro') ) );
					exit;
				}
			}

			//css for the edit Pages
			wp_enqueue_style('jobs-plus');
			//			wp_enqueue_style('jqueryui-editable');
			wp_enqueue_style('magnific-popup');

			wp_enqueue_script('jquery-ui-slider');
			//			wp_enqueue_script('jqueryui-editable');
			//			wp_enqueue_script('jqueryui-editable-ext');
			wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_script('jquery.magnific-popup');

			$this->title = 'custom_titles';
			$this->content = $this->pro_content['update'];
			$this->custom_type = 'jbp_pro';


		}
		//Is this a jbp_job search?
		elseif( is_search() && (get_query_var('post_type') == 'jbp_job') ){
			$this->title = 'custom_titles';
			$this->content = $this->job_content['search'];
			$this->custom_type = 'jbp_job';
		}

		//Is this a jbp_job contact?
		elseif( is_singular('jbp_job') && get_query_var('contact') ){
			//css for the edit Pages
			$this->title = 'custom_titles';
			$this->content = $this->job_content['contact'];
			$this->custom_type = 'jbp_job';
		}

		//Is this a job_pro search?
		elseif( is_search() && (get_query_var('post_type') == 'jbp_pro') ){
			$this->title = 'custom_titles';
			$this->content = $this->pro_content['search'];
			$this->custom_type = 'jbp_pro';
		}

		//Is this a job_pro contact?
		elseif( is_singular('jbp_pro') && get_query_var('contact') ){
			$this->title = 'custom_titles';
			$this->content = $this->pro_content['contact'];
			$this->custom_type = 'jbp_pro';
		}

		//Handle any default custom templates
		if( empty($this->content) ) {
			if( is_tax( array('jbp_category', 'jbp_skills_tag') ) ) {
				$this->title = 'custom_titles';
				$this->content = $this->job_content['taxonomy'];
				$this->custom_type = 'jbp_job';
			}

			elseif(is_post_type_archive('jbp_job') ) {
				$this->title = 'custom_titles';
				$this->content = $this->job_content['archive'];
				$this->custom_type = 'jbp_job';
			}

			elseif(is_singular('jbp_job') ) {
				$this->content = $this->job_content['single'];
				$this->custom_type = 'jbp_job';
			}

			elseif( is_tax('jbp_tag') ) {
				$this->title = 'custom_titles';
				$this->content = $this->pro_content['taxonomy'];
				$this->custom_type = 'jbp_pro';
			}

			elseif(is_post_type_archive('jbp_pro') ) {
				$this->title = 'custom_titles';
				$this->content = $this->pro_content['archive'];
				$this->custom_type = 'jbp_pro';
			}

			elseif(is_singular('jbp_pro') ) {
				$this->content = $this->pro_content['single'];
				$this->custom_type = 'jbp_pro';
			}

		}

		//var_dump($this->content);
		//Do the content filters
		if( !empty($this->content)
		&& file_exists($this->plugin_dir . 'ui-front/' . $this->content)) {
			//If substituting content then use page template. If post_type.php exists use it.
			$template = locate_template( array("{$this->custom_type}.php", 'page.php', 'index.php' ) );
			add_filter( 'the_content', array( &$this, 'content_template' ), 20 );
			if(!$wp_query->have_posts() ) $wp_query->current_post = -2;
			if( !empty($this->title) ) add_filter( 'the_title', array( &$this, $this->title ), 20 , 2 );
		}
		//var_dump($template);
		return $template;
	}

	function content_template($content) {
		global $wp_query;

		rewind_posts();
		remove_all_filters('the_title', 20);
		remove_all_filters('the_content', 20);

		//See if there is a child or theme version
		require locate_jbp_template( $this->content );

		$wp_query->post_count = 0;
	}

	/**
	* filters the titles for our custom pages
	*
	*/
	function custom_titles( $title, $id = false ) {
		global $wp_query, $post;

		//filter out nav titles
		if (!is_object($post) || $post->ID != $id ) return $title;

		//Contact form titles
		if (get_query_var('contact') ) return $title . __(" &raquo; Contact", $this->text_domain);

		//taxonomy title

		if(is_tax(array('jbp_category', 'jbp_tag', 'jbp_skills_tag') ) ){
			$taxonomy = get_taxonomy( get_query_var('taxonomy') );
			return single_term_title($taxonomy->labels->parent_item_colon, false);
		}

		//Search titles
		$post_type = get_query_var('post_type');
		if(is_search() && in_array($post_type, array('jbp_job', 'jbp_pro') ) ){
			switch ($post_type) {
				case 'jbp_pro': return sprintf('%s &raquo; Search &raquo; %s', $this->pro_obj->labels->name, $_GET['s']); break;
				case 'jbp_job': return sprintf('%s &raquo; Search &raquo; %s', $this->job_obj->labels->name, $_GET['s']); break;
				default: return $title;
			}
		}

		//archive titles
		if ( is_post_type_archive(array('jbp_job', 'jbp_pro') ) ){
			return post_type_archive_title();
		}
		return $title;
	}

	function job_search_form(){
		//See if there is a child or theme version
		include locate_jbp_template( 'search-form-job.php' );
	}

	function pro_search_form(){
		//See if there is a child or theme version
		require locate_jbp_template( 'search-form-pro.php');
	}

	function custom_search($phrase = '', $post_type = array()) {

		$all_ids = array();


		//Parse the search string breaking at commas and with quotes.
		$phrases = array_map('trim', str_getcsv(stripslashes($phrase)));

		//Standard String search
		foreach ($phrases as $phrase) {

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

		//Taxonomy search
		//Convert phrases to slugs for search
		$terms = array_map('sanitize_title', $phrases);

		//Get taxonomies associated with this post type
		$taxonomies = get_taxonomies(array('object_type' => (array) $post_type));

		if (!empty($taxonomies)) {
			$args = array(
			'jbp_custom' => true,
			'posts_per_page' => -1,
			'fields' => 'ids',
			'post_type' => $post_type,
			'tax_query' => array(
			'relation' => 'OR',
			),
			);

			foreach ($taxonomies as $taxonomy) {
				$args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'terms' => $terms,
				'field' => 'slug',
				'include_children' => true,
				'operator' => 'IN',
				);
				break;
			}

			$search_ids = get_posts($args);
			$all_ids = array_merge($all_ids, $search_ids);
		}

		//Metadata search
		global $CustomPress_Core;

		// Get array of Custom field ids for this post type
		$field_set = $CustomPress_Core->get_custom_fields_set($post_type);

		$custom_fields = array();
		foreach ($field_set as $key => $field) {
			$prefix = ( empty($field['field_wp_allow']) ) ? '_ct_' : 'ct_';
			$custom_fields[] = $prefix . $key;
		}

		foreach ($custom_fields as $custom_field) {
			$args = array(
			'jbp_custom' => true,
			'posts_per_page' => -1,
			'post_type' => $post_type,
			'fields' => 'ids',
			'meta_query' => array(
			'relation' => 'OR',
			),
			);

			foreach ($phrases as $phrase) {
				$args[meta_query][] = array(
				'key' => $custom_field,
				'value' => $phrase,
				'compare' => 'LIKE',
				);

				$search_ids = get_posts($args);
				$all_ids = array_merge($all_ids, $search_ids);
			}
		}
		//var_dump($all_ids);
		//		$search_ids = get_posts($args);
		//		$all_ids = array_merge($all_ids, $search_ids);

		rsort($all_ids);
		return array_unique($all_ids);
	}

	/**
	* return fancy pagination links.
	* @uses $wp_query
	*
	*/
	function pagination($show = true){
		if( !$show ) return '';

		ob_start();
		//check child and theme versions
		require locate_jbp_template('content-pagination.php');
		return apply_filters( 'jbp_pagination', ob_get_clean() );
	}

	/**
	* Prevent comments display
	*
	*/
	function no_comments(){
		add_filter('comments_open', create_function('', 'return false;') );
	}

	/**
	* Replace the get_edit_post_link with our front end editor link
	*
	*/
	function on_get_edit_post_link($link ='', $post_id = 0 ){

		if( !empty($link) && !is_admin() && in_array(get_post_type($post_id), array('jbp_job', 'jbp_pro') ) ) {
			$link = esc_attr(trailingslashit(get_permalink($post_id) ) . 'edit/');
		}

		return $link;
	}

	function on_request($vars){
		global $wp_query;

		//Make sure something is actually set
		if(isset($vars['edit']) ) $vars['edit'] = true;
		if(isset($vars['contact']) ) $vars['contact'] = true;

		return $vars;
	}

	function on_parse_query($query) {
		global $wp_query, $wp_post_statuses;

		// Do this so virtual pages don't show up in Archive pages, but can be found as singles
		if( is_singular() ) $wp_post_statuses['virtual']->public = true;

		return $query;
	}

	function on_posts_clauses($clauses){
		global $wp_query, $wpdb;
		//printf('<pre>%s</pre>', print_r($clauses, true) );
		//printf('<pre>%s</pre>', print_r($wp_query, true) );
		if( !is_main_query() || is_admin() ) return $clauses;

		if(is_post_type_archive('jbp_pro') ) {
			$clauses['where']  .= $wpdb->prepare(' AND (%1$spp_members.expiration > %2$d)', $wpdb->prefix, time() );
			$clauses['where']  .= $wpdb->prepare(' AND (%1$spp_members.level = \'gold\')', $wpdb->prefix);
			$clauses['where']  .= $wpdb->prepare(' AND (%1$susermeta.meta_key = \'bb_reputation\' OR %1$susermeta.meta_key = NULL) ', $wpdb->prefix);

			$clauses['join']    = $wpdb->prepare(' LEFT JOIN %1$susers on (%1$susers.ID = %1$sposts.post_author)', $wpdb->prefix );
			$clauses['join']   .= $wpdb->prepare(' LEFT JOIN %1$susermeta on (%1$susermeta.user_id = %1$susers.ID)', $wpdb->prefix );
			$clauses['join']   .= $wpdb->prepare(' LEFT JOIN %1$spp_members on (%1$spp_members.user_id = %1$susers.ID) ', $wpdb->prefix );

			$clauses['orderby'] = $wpdb->prepare(' CAST(%1$susermeta.meta_value AS SIGNED) DESC ', $wpdb->prefix);

			$clauses['limits']  = sprintf(' LIMIT 0,%s ', intval( $this->get_setting( 'job->per_page', 48) ) );
		}
		elseif(is_post_type_archive('jbp_job') ) {
			//Sort order
			//$sortby = json_decode(stripslashes($_COOKIE['prjSort']) );
			$sortby = empty($_GET['prj-sort']) ? '' : $_GET['prj-sort'];
			$clauses['orderby'] = $wpdb->prepare(" %sposts.post_date DESC", $wpdb->prefix);
			if( !empty($sortby) ){
				if($sortby[0]->value == 'ending'){
					$clauses['orderby'] = $wpdb->prepare(" STR_TO_DATE(%spostmeta.meta_value, '%%b %%e, %%Y') DESC", $wpdb->prefix);
				}
			}
		}

		//printf('<pre>%s</pre>', print_r($clauses, true) );
		return $clauses;
	}

	function on_pre_get_posts(&$query){
		if ( !$query->is_main_query() )
		return $query;

		//printf('<pre>%s</pre>', print_r($query, true) );
		//		return $query;
		//Check for Custom post_type searches

		if(is_search()
		&& in_array( $query->query_vars['post_type'], array('jbp_job', 'jbp_pro') )
		&& empty($query->query_vars['jbp_custom'])
		&& !is_admin() ){
			$s = get_query_var('s');
			$ids = $this->custom_search( $s, $query->query_vars['post_type'] );
			if(count($ids) > 0) {
				$query->query_vars['post__in'] = $ids;
				$query->query_vars['s'] = '';
			}
		}

		if (is_author()
		&& is_post_type_archive(array('jbp_pro', 'jbp_job'))
		&& ($user = get_user_by('slug', get_query_var('author_name')) )
		) {
			//if ($user->ID == get_current_user_id())
			{
				$query->set('post_status', array('publish', 'pending', 'draft'));
				//				printf('<pre>%s</pre>', print_r($query, true) ); exit;
			}
		}

		if(is_post_type_archive('jbp_job')
		&& !is_admin() ){

			$query->set('meta_key', '_ct_jbp_job_Due');
			$query->set('orderby', 'meta_value');
			$query->set( 'posts_per_page', intval( $this->get_setting( 'project->per_page', 20) ) );

			//			//jobs_category
			//			//$cats = json_decode(stripslashes($_COOKIE['prjSearchCategories']) );
			//			if(!empty($cats) ){
			//				$terms = array();
			//				foreach((array)$cats as $cat){
			//					$terms[] = $cat->value;
			//				}
			//				$tax_query = array(
			//				'relation' => 'OR',
			//				array(
			//				'taxonomy' => 'jbp_category',
			//				'field' => 'term_id',
			//				'terms' => $terms,
			//				)
			//				);
			//				$query->set( 'tax_query', $tax_query );
			//			}
			//
			//			//Price Slider
			//			//$price = json_decode(stripslashes($_COOKIE['prjSearchPrice']) );
			//			if(!empty($price) ){
			//				$meta_query = array(
			//				'relation' => 'OR',
			//				array(
			//				'key' => '_ct_jbp_job_Budget_text',
			//				'value' => array($price->min, $price->max),
			//				'compare' => 'BETWEEN',
			//				'type' => 'NUMERIC',
			//				)
			//				);
			//				$query->set( 'meta_query', $meta_query );
			//			}

		}

		if(is_post_type_archive('jbp_pro')
		&& is_main_query()
		&& !is_admin() ){
			//$query->set('posts_per_page', 1);
		}
		return $query;
	}

	function error_message($msg = '') {
		$this->jbp_errors[] =  $msg;
		add_action('jbp_error', array($this,'display_errors') );
	}

	function notice_message($msg = '') {

		$this->jbp_notices[] = $msg;
		add_action('jbp_notice', array($this,'display_notices') );
	}

	function display_errors(){
		foreach($this->jbp_errors as $error){
			echo '<div class="error">' . $error . '</div>';
		}
	}

	function display_notices(){
		foreach($this->jbp_notices as $notice){
			echo '<br clear="all"/><div class="updated">' . sanitize_text_field($notice) . '</div><br clear="all"/>';
		}
	}

	function email_replace( $content = '' ){
		global $post, $authordata;

		$result =
		str_replace('SITE_NAME', get_bloginfo('name'),
		str_replace('POST_TITLE', esc_html($post->post_title),
		str_replace('POST_LINK', make_clickable( get_permalink($post->ID) ),
		str_replace('TO_NAME', $authordata->nicename,
		str_replace('FROM_NAME', sanitize_text_field($_POST['name']),
		str_replace('FROM_EMAIL', sanitize_email($_POST['email']),
		str_replace('FROM_MESSAGE', $_POST['content'],
		$content) ) ) ) ) ) );

		return $result;
	}

	/**
	* Email to a Job poster
	*
	*/
	function email_contact_job($params = array() ){
		global $post, $uathordata;

		if( !empty($params['jbp-job-contact']) ){
			if( ! ($post = get_post($params['post_id']) ) ) return;

			setup_postdata($post);

			$to = do_shortcode('[ct id="_ct_jbp_job_Contact_Email"]');
			if(! is_email($to) ) {
				$to = $authordata->user_email;
			}

			$subject = $this->email_replace( $this->get_setting('job->email_subject') );
			$message = wpautop($this->email_replace( $this->get_setting('job->email_content') ) );
			$from = sprintf('%s <%s>', sanitize_text_field( $params['name'] ), sanitize_email( $params['email'] ) );

			$message_headers = array();
			$message_headers[] = "MIME-Version: 1.0";
			$message_headers[] = "From: $from";
			$message_headers[] = "Reply-To: $from";
			$message_headers[] = sprintf("Content-Type: text/html; charset=\"%s\"", get_option('blog_charset') );

			if( $this->get_setting('job->cc_admin') ) {
				$message_headers[] = "Cc: " . get_option('admin_email');
			}

			if( $this->get_setting('job->cc_sender') ) {
				$message_headers[] = "Cc: $from";
			}

			if( wp_mail(
			$contact_email,
			$subject,
			$message,
			$message_headers)
			) {
				$message = urlencode(__('Message Sent, Thank You', $this->text_domain) );
				wp_redirect(add_query_arg('jbp_notice', $message, get_permalink($params['post_id']) ) );
				exit;
			} else {
				$this->error_message(__('Email is not Responding', $this->text_domain) );
			}

		}
	}

	/**
	* Email to a Pro
	*
	*/
	function email_contact_pro($params= array() ){
		global $post, $authordata;

		if( !empty($params['jbp-pro-contact']) ){
			if( ! ($post = get_post($params['post_id']) ) ) return;

			setup_postdata($post);

			$to = do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]');
			if(! is_email($to) ) {
				$to = $authordata->user_email;
			}

			$subject = $this->email_replace( $this->get_setting( 'job->email_subject' ) );
			$message = wpautop($this->email_replace( $this->get_setting( 'job->email_content' ) ) );
			$from = sprintf('%s <%s>', sanitize_text_field($params['name']), sanitize_email( $params['email'] ) );

			$message_headers = array();
			$message_headers[] = "MIME-Version: 1.0";
			$message_headers[] = "From: $from";
			$message_headers[] = "Replt-To: $from";
			$message_headers[] = sprintf("Content-Type: text/html; charset=\"%s\"", get_option('blog_charset') );

			if( $this->get_setting('pro->cc_admin') ) {
				$message_headers[] = "Cc: " . get_option('admin_email');
			}

			if( $this->get_setting('pro->cc_sender') ) {
				$message_headers[] = "Cc: $from";
			}

			if( wp_mail(
			$to,
			$subject,
			$message,
			$message_headers)
			) {
				$message = urlencode(__('Message Sent, Thank You', $this->text_domain) );
				wp_redirect(add_query_arg('jbp_notice', $message, get_permalink($params['post_id']) ) );
				exit;
			} else {
				$this->error_message(__('Email is not Responding', $this->text_domain) );
			}
		}
	}

	function on_wp_mail($args){
		return $args;
	}


	/**
	*
	*
	*/
	function custom_upload_directory( $args ) {
		global $post_ID;

		if( !$post = get_post( $post_ID )) return $args;
		$parent_id = $post->post_parent;

		//var_dump($args);

		// Check the post-type of the current post
		if( "jbp_pro" == get_post_type( $post_ID ) || "jbp_pro" == get_post_type( $parent_id ) ) {
			$path = $this->get_setting('pro->upload_path');
			$path = empty($path) ? '/uploads/pro' : untrailingslashit($path);
		} elseif( "jbp_job" == get_post_type( $post_ID ) || "jbp_job" == get_post_type( $parent_id ) ) {
			$path = $this->get_setting('job->upload_path');
			$path = empty($path) ? '/uploads/job' : untrailingslashit($path);
		} else {
			return $args;
		}

		// Setup modified locations
		$url = content_url($path);
		$args['path'] = untrailingslashit(WP_CONTENT_DIR) . "$path/{$post_ID}";
		$args['url'] = "$url/{$post_ID}";
		$args['subdir'] = "$path/{$post_ID}"; // No date directories use post id
		$args['basedir'] = WP_CONTENT_DIR; // Anywhere under wp-content
		$args['baseurl'] = content_url(); // Anywhere under wp-content

		//var_dump($args); exit;
		return $args;
	}

	function is_certified($user_id = 0){
		$user_id = empty($user_id) ? get_current_user_id() : intval($user_id);
		$result =  get_user_meta($user_id, JBP_PRO_CERTIFIED_KEY, true);
		return $result;
	}

	/**
	*
	*/
	function on_ajax_rate_pro() {

		if( !is_user_logged_in()) return;
		if(! wp_verify_nonce($_POST['_wpnonce'],'rating') ) exit;

		echo json_encode( $this->save_rating($_POST['post_id'], $_POST['rating']) );

		exit;
	}

	/**
	* Handle rateit star ratings
	*/
	function save_rating($post_id = 0, $rating = 0) {

		if( ! ($user_id = get_current_user_id()) ) return;

		$voted = get_user_meta( $user_id, JBP_PRO_VOTED_KEY, true);
		$voted = ( empty($voted) ) ? array() : (array)$voted;

		$voters = get_post_meta( $post_id, JBP_PRO_VOTERS_KEY, true);
		$voters = ( empty($voters) ) ? 0 : intval($voters);

		$current_rating = get_post_meta( $post_id, JBP_PRO_RATING_KEY, true);
		$current_rating = ( empty($current_rating) ) ? 0 : intval($current_rating);

		$voters += (empty($voted[$post_id]) ) ? 1 : 0;
		$rating += (empty($voted[$post_id]) ) ? $current_rating : $current_rating - $voted[$post_id];
		$voted[$post_id] = $rating;

		update_user_meta($user_id, JBP_PRO_VOTED_KEY, $voted);
		update_post_meta($post_id, JBP_PRO_VOTERS_KEY, $voters);
		update_post_meta($post_id, JBP_PRO_RATING_KEY, $rating);
		update_post_meta($post_id, JBP_PRO_AVERAGE_KEY, ($rating / $voters) );

		$result = new stdClass;
		$result->post_id = $post_id;
		$result->user_id = $user_id;
		$result->voted   = $voted;
		$result->voters  = $voters;
		$result->rating  = $rating;
		$result->average = $rating / $voters;
		return $result;
	}

	function get_the_rating( $post_id = 0, $before = '', $after = '' ){
		global $post;

		$post_id = empty($post_id) ? $post->ID : $post_id;
		return $before . sprintf('
		<span class="rateit" 
		data-rateit-readonly="true" 
		data-rateit-ispreset="true" 
		data-rateit-value="%s" 
		></span>',
		get_post_meta($post_id, JBP_PRO_AVERAGE_KEY, true) ) . $after;
	}

	function get_rate_this( $post = 0, $before = '', $after = '', $allow_reset = false ) {

		$post = get_post($post);
		if( !is_user_logged_in() ) return '';
		$rating = get_user_meta( get_current_user_id(), JBP_PRO_VOTED_KEY, true);
		$rating = empty($rating[$post->ID]) ? 0 : $rating[$post->ID];

		return $before . sprintf('
		<span class="rateit"
		data-post_id="%s"
		data-rateit-ispreset="true"
		data-rateit-value="%s"
		data-rateit-resetable="%s"
		data-ajax="%s"
		data-nonce="%s"
		></span>',
		$post->ID, $rating, $allow_reset,
		esc_attr(admin_url('admin-ajax.php') ),
		wp_create_nonce('rating') ) . $after;
	}


	/**
	* get_default_custom_post - If post_id empty then create a new Auto draft listing to be edited
	* @$post_type string - post type to create
	* #$post_id int - Post to use or create if 0
	*/
	function get_default_custom_post($post_type = 'post', $post_id = 0){

		if(empty($post_id) ) {
			$post_id = wp_insert_post( array( 'post_title' => __('Auto Draft', $this->text_domain), 'post_type' => $post_type, 'post_status' => 'auto-draft' ) );
			if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) && get_option( 'default_post_format' ) )
			set_post_format( $post, get_option( 'default_post_format' ) );
			$post = get_post($post_id);
			$post->post_title ='';
		} else {
			$post = get_post($post_id);
		}

		return $post;
	}

	/**
	* expand_url for urls without http adds it and validates It.
	*
	*/
	function expand_url($url){

		$url = trim($url);
		if( empty($url) ) return '';
		if ( substr( $url, 0, 2 ) === '//' )  $url = 'http:' . $url;
		if( ! strpos( $url, '//') !== false) $url = 'http://'. $url;
		return set_url_scheme($url);
	}

	function ajax_error( $status_text, $params){
		$is_iframe = ( !empty($params['X-Requested-With']) && $params['X-Requested-With'] == 'IFrame');
		header('HTTP/1.0 403 ' . $statusText); //error for standard Ajax
		if($is_iframe) {
			exit( sprintf($response_iframe, '403', $statusText, $status_text . ': ' . print_r( $params, true) ) );
		} else {
			exit($status_text . ': ' . print_r(($params ), true ) );
		}
	}

	/**
	* @on_ajax_jbp_pro
	* Handles update of pros data via ajax and iframe transport
	* data may arrive from standard ajax or from iframe transport uploads.
	* Iframe uploads will be identified by a post field of "X-Requested-With" set to Iframe
	* Since IFrames cannot identify headers the response to and Iframe must be wrapped
	* in a <textarea data-type="application/json" data-status="http status code" data-statusText="status message"></textarea> wrapper.
	* This allows ajax iframe calls to mimic standard ajax but will work on file uploads.
	*/
	function on_ajax_jbp_pro(){
		global $post_ID;

		if( !current_user_can('edit_pros') ) exit;

		$response_iframe = '<textarea date-type="application/json" data-status="%s" data-statusText="%s">%s</textarea>';
		$params = stripslashes_deep($_REQUEST);
		//Is this an iframe upload
		$is_iframe = ( !empty($params['X-Requested-With']) && $params['X-Requested-With'] == 'IFrame');

		//Good nonce?
		if( !wp_verify_nonce($params['_wpnonce'], 'jbp_pro') ){
			$this->ajax_error('Forbidden No Nonce Sins', $params);
		}

		// get the post id for x-editable
		$post_id = (empty($params['pk']) ) ? 0 : $params['pk'];

		$name = strstr($params['name'], '[', true);
		$name = $name ? $name : $params['name'];

		$index = strstr($params['name'], '[', false);
		$index = $index ? intval(trim( $index, '[]') ) : false;

		$value = (empty($params['value']) ) ? '' : $params['value'];

		if( empty($post_id) ) exit;

		$post_ID = $post_id; //So media will know which post we're working on.

		$post = array('ID' => $post_id);

		switch ($name) {
			case 'post_title':
			if( empty( $value ) ){
				$this->ajax_error( __('This field must have a valid value', JBP_TEXT_DOMAIN), $value);
			} else {
				$post['post_title'] = trim( wp_strip_all_tags($value) );
			}
			break;

			case 'post_content': $post['post_content'] = nl2br(wp_strip_all_tags($value)); break;

			//Custom fields
			//json multi part fields
			case '_ct_jbp_pro_First_Last'  :
			if( empty($value) ){
				$this->ajax_error(__('This field must have a valid value', JBP_TEXT_DOMAIN), $value);
			} else {
				$v =  json_encode( array_map('sanitize_text_field', $value) );
				update_post_meta($post_id, $name, $v );
				exit(sprintf('{"newValue": %s}', $v ) );
			}
			break;

			case '_ct_jbp_pro_Company_URL' :
			$value = array_map('sanitize_text_field', $value);
			$value['url'] = $this->expand_url($value['url']);
			$v =  json_encode( array_map('sanitize_text_field', $value) );
			update_post_meta($post_id, $name, $v );
			exit(sprintf('{"newValue": %s}', $v ) );
			break;

			case '_ct_jbp_pro_Facebook_URL': update_post_meta($post_id, $name, json_encode($value) ); break;
			case '_ct_jbp_pro_LinkedIn_URL': update_post_meta($post_id, $name, json_encode($value) ); break;
			case '_ct_jbp_pro_Twitter_URL' : update_post_meta($post_id, $name, json_encode($value) ); break;
			case '_ct_jbp_pro_Skype_URL'   : update_post_meta($post_id, $name, json_encode($value) ); break;

			case '_ct_jbp_pro_Location'    : update_post_meta($post_id, $name, sanitize_text_field($value) ); break;

			case '_ct_jbp_pro_Contact_Email': {
				$value = sanitize_email( strtolower($value) );
				if(is_email( $value )) {
					update_post_meta($post_id, $name, $value);
				} else {
					$this->ajax_error( __('Not a valid email address', JBP_TEXT_DOMAIN), $value);
				}
				break;
			}

			case '_ct_jbp_pro_Portfolio' : {
				//could be multiple images so add to object
				$group = get_post_meta($post_id, $name, true );
				$group = empty($group) ? new stdClass : json_decode($group);

				if($value['remove'] == 'remove') {
					unset( $group->$value['attachment_id'] );
					//remove the files
					wp_delete_attachment($value['attachment_id'], true);
					update_post_meta($post_id, $name, json_encode($group) );
					// send back empty record.
					exit(sprintf($response_iframe, '200', 'OK', '{"newValue": null}' ));
				}

				$value = array_map('sanitize_text_field', $value);
				$value['url'] = $this->expand_url($value['url']);

				$value['caption'] = str_replace("\r\n",'<br/>',strip_tags($value['caption']) );

				if ( isset($_FILES['file']) && empty( $_FILES['file']['error'] )) {
					/* Require WordPress utility functions for handling media uploads */
					require_once( ABSPATH . '/wp-admin/includes/media.php' );
					require_once( ABSPATH . '/wp-admin/includes/image.php' );
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
					/* Upload the image ( handles creation of thumbnails etc. ), set featured image  */
					//remove the files
					if(isset($group->$value['attachment_id']) ){
						unset( $group->$value['attachment_id'] );
						wp_delete_attachment($value['attachment_id'], true);
					}
					$value['attachment_id'] = media_handle_upload( 'file', $post_id, array('post_content' => $value['caption'] ) );
					$src = wp_get_attachment_image_src( $value['attachment_id'],'pro_thumbnail' );
					$value['src'] = $src[0];
				}

				$group->$value['attachment_id'] = $value; // Keep key associative
				//save json string
				update_post_meta($post_id, $name, json_encode($group) );

				$value = json_encode($value);
				//wrap the new value with a json newValue object to update the calling page
				exit(sprintf($response_iframe, '200', 'OK', '{"newValue": '. $value . '}' ));
				break;
			}

			case '_ct_jbp_pro_Skills': {
				//could be multiple skills so add to object


				$group = get_post_meta($post_id, $name, true );
				$group = empty($group) ? new stdClass : json_decode($group);

				if( empty($value['skill']) || $value['remove'] == 'remove') {
					if(isset($group->$value['skill_id']) ) unset( $group->$value['skill_id'] );
					update_post_meta($post_id, $name, json_encode($group) );
					// send back empty record.
					exit('{"newValue": null}');
				}

				if( empty($value['skill_id']) ) $value['skill_id'] = uniqid();

				$group->$value['skill_id'] = $value; // Keep key associative

				//save json string
				update_post_meta($post_id, $name, json_encode($group) );

				$value = json_encode($value);
				//wrap the new value with a json newValue object to update the calling page
				exit(sprintf('{"newValue": %s}', $value ));
				break;
			}

			case '_ct_jbp_pro_Social': {
				//could be multiple skills so add to object

				$group = get_post_meta($post_id, $name, true );
				$group = empty($group) ? new stdClass : json_decode($group);

				if( ($value['remove'] == 'remove') || empty($value['social']) || empty($value['url']) || empty($value['social_id'])) {
					if(isset($group->$value['social_id'])) unset( $group->$value['social_id'] );
					update_post_meta($post_id, $name, json_encode($group) );
					// send back empty record.
					exit('{"newValue": null}');
				}

				$value = array_map('sanitize_text_field', $value);

				if($value['social_id'] != 'sk') {
					$value['url'] = $this->expand_url($value['url']);
				}

				$group->$value['social_id'] = $value; // Keep key associative
				//save json string
				update_post_meta($post_id, $name, json_encode($group) );

				$value = json_encode($value);
				//wrap the new value with a json newValue object to update the calling page
				exit(sprintf('{"newValue": %s}', $value ));
				break;
			}

		}
		if(count($post) > 1) {
			$post['post_status'] = 'draft';

			wp_update_post($post, true); //See if fields added
		}
		exit;
	}

	function on_ajax_jbp_pro_status(){

		if( !current_user_can('edit_pros') ) exit;
		$params = stripslashes_deep($_REQUEST);

		//Good nonce?
		if( !wp_verify_nonce($params['_wpnonce'], 'jbp_pro') ){
			$this->ajax_error('Forbidden No Nonce Sins', $params);
		}

		$post_id = (empty($params['post_id']) ) ? 0 : intval($params['post_id']);
		$post_status = (empty($params['post_status']) ? 0 : $params['post_status']);

		if( empty($post_id) ) exit;
		if ($this->get_setting("pro->moderation->{$post_status}" != 1) ) exit;
		if( get_post_type($post_id) != 'jbp_pro') exit;

		$id = wp_update_post(array(
		'ID' => $post_id,
		'post_status' => $post_status,
		) );

		$this->notice_message( sprintf(__('This %s has been updated', $this->text_domain), $this->pro_labels->singular_name ) );

		$redirect = new stdClass();

		if (!empty($this->jbp_errors)) {
			$redirect->redirect = add_query_arg('jbp_error', urlencode(implode(', ', $this->jbp_errors)), get_permalink($id));
		} else {
			$redirect->redirect = add_query_arg('jbp_notice', urlencode(implode(', ', $this->jbp_notices)), get_permalink($id));
		}
		exit(json_encode($redirect));
	}


	function rate_this_sc($atts = null, $content = ''){
		extract( shortcode_atts( array(
		'post' => null,
		'resetable' => false,
		), $atts ) );
		wp_enqueue_script('jquery-rateit');
		wp_enqueue_style('jquery-rateit');

		return $this->get_rate_this($post, '', '', $resetable);
	}

	function rating_sc($atts = null, $content = ''){
		extract( shortcode_atts( array(
		'post' => null,
		), $atts ) );

		return $this->get_the_rating($post,'','');
	}

	/**
	* Shortcode jbp-pro-gravatar
	*/
	function pro_gravatar_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Gravatar', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		ob_start();
		require locate_jbp_template((array)'sc-pro-gravatar.php');
		return ob_get_clean();
	}

	/**
	* jbp-pro-portfolio
	*/
	function pro_portfolio_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Portfolio', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		ob_start();
		require locate_jbp_template((array)'sc-pro-portfolio.php');
		return ob_get_clean();
	}

	/**
	* jbp-pro-skills
	*/
	function pro_skills_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Skills', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		ob_start();
		require locate_jbp_template((array)'sc-pro-skills.php');
		return ob_get_clean();
	}

	/**
	* jbp-pro-social_id
	*/
	function pro_social_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Social', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		ob_start();
		require locate_jbp_template((array)'sc-pro-social.php');
		return ob_get_clean();
	}

	function pro_contact_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Contact Me', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		'post' => null,
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$post = get_post($post);

		$content = (empty($content)) ? $text : $content;
		$result = sprintf('<button class="jbp-button pro-contact-btn %s" type="button" onclick="window.location.href=\'%s\';" >%s</button>',
		$class, esc_attr(trailingslashit(get_permalink($post) . 'contact/' ) ), $content);

		return $result;
	}

	/**
	* jbp-pro-Archive
	*/
	function pro_archive_sc( $atts, $content = null ){
		extract( shortcode_atts( array(
		'size' => 'small',
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		global $post;
		$post = get_post($post);

		ob_start();
		require locate_jbp_template((array)'sc-pro-archive.php' );
		return ob_get_clean();
	}

	/**
	* jbp-job-portfolio
	*/
	function job_portfolio_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Portfolio', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		ob_start();
		require locate_jbp_template((array)'sc-job-portfolio.php');
		return ob_get_clean();
	}

	/**
	* jbp-job-excerpt shortcode_atts
	*/
	function job_excerpt_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Job Excerpt', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		), $atts ) );

		ob_start();
		require locate_jbp_template((array)'sc-job-excerpt.php');
		return ob_get_clean();
	}

	function job_contact_btn_sc( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'text' => __('Contact Me', $this->text_domain),
		'view' => 'both', //loggedin, loggedout, both
		'class' => '',
		'post' => null,
		), $atts ) );

		$view = strtolower($view);
		if(is_user_logged_in())	{if($view == 'loggedout') return '';}
		else if($view == 'loggedin') return '';

		$post = get_post($post);

		$content = (empty($content)) ? $text : $content;
		$result = sprintf('<button class="jbp-button job-contact-btn %s" type="button" onclick="window.location.href=\'%s\';" >%s</button>',
		$class, esc_attr(trailingslashit(get_permalink($post) . 'contact/' ) ), $content);

		return $result;
	}


	/**
	* Update a jbp_pro
	*
	*/
	function update_pro($params = array()){

		if(! current_user_can( 'edit_pro', $params['post_id']) ) return;
		//var_dump($params); //exit;
		/* Construct args for the new post */
		$args = $params['data'];


		//		$args = array(
		//		/* If empty ID insert Ad instead of updating it */
		//		'ID'             => ( isset( $params['data']['ID'] ) ) ?  $params['data']['ID'] : '',
		//		'post_title'     => wp_strip_all_tags($params['data']['post_title']),
		//		'post_name'      => '',
		//		'post_content'   => $params['data']['post_content'],
		//		'post_excerpt'   => (empty($params['data']['post_excerpt'])) ? '' : $params['data']['post_excerpt'],
		//		'post_status'    => $params['data']['post_status'],
		//		//'post_author'    => get_current_user_id(),
		//		'post_type'      => 'jbp_pro',
		//		'ping_status'    => 'closed',
		//		//'comment_status' => 'open'
		//		);

		/* Insert page and get the ID */
		if(empty($args['ID']) ){
			$post_id = wp_insert_post( $args, true );

		} else {
			$post_id = wp_update_post( $args, true );
		}
		//var_dump($args);

		//var_dump($post_id); exit;

		if ( ! empty($post_id) ) {
			//Save custom tags
			if(is_array($params['tag_input'])){
				foreach($params['tag_input'] as $key => $tags){
					wp_set_post_terms($post_id, $params['tag_input'][$key], $key);
				}
			}

			//Save categories
			if(is_array($params['post_category'])){
				wp_set_post_terms($post_id, $params['post_category'], 'category');
			}

			//Save custom terms
			if(is_array($params['tax_input'])){
				foreach($params['tax_input'] as $key => $term_ids){
					if ( is_array( $params['tax_input'][$key] ) ) {
						wp_set_post_terms($post_id, $params['tax_input'][$key], $key);
					}
				}
			}

			//			if ( class_exists( 'CustomPress_Core' ) ) {
			//				global $CustomPress_Core;
			//				$CustomPress_Core->save_custom_fields( $post_id );
			//			}

			if ( isset($_FILES['jbp_pro_image']) && empty( $_FILES['jbp_pro_image']['error'] )) {
				/* Require WordPress utility functions for handling media uploads */
				require_once( ABSPATH . '/wp-admin/includes/media.php' );
				require_once( ABSPATH . '/wp-admin/includes/image.php' );
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				/* Upload the image ( handles creation of thumbnails etc. ), set featured image  */
				$thumbnail_id = media_handle_upload( 'jbp_pro_image', $post_id );
				set_post_thumbnail( $post_id, $thumbnail_id );
				//remove_filter( 'upload_dir', array($this,'custom_upload_directory') );
			}


			return $post_id;
		}
	}

	/**
	* Update a jbp_job
	*
	*/
	function update_job($params = array()){

		var_dump($params);
		if(! current_user_can( 'edit_job', $params['data']['ID']) ) return;

		/* Construct args for the new post */
		$args = array(
		/* If empty ID insert Ad instead of updating it */
		'ID'             => ( isset( $params['data']['ID'] ) ) ?  $params['data']['ID'] : '',
		'post_title'     => wp_strip_all_tags($params['data']['post_title']),
		'post_name'      => '',
		'post_content'   => $params['data']['post_content'],
		'post_excerpt'   => (empty($params['data']['post_excerpt'])) ? '' : $params['data']['post_excerpt'],
		//'post_author'    => get_current_user_id(),
		'post_type'      => 'jbp_job',
		'ping_status'    => 'closed',
		//'comment_status' => 'open'
		);

		if( !empty($params['data']['post_status']) ){
			if( get_setting("job->moderation->{$params['data']['post_status']}") ){
				$args['post_status'] = $params['data']['post_status'];
			}
		}

		/* Insert page and get the ID */
		if(empty($args['ID']) ) {
			$post_id = wp_insert_post( $args );
		} else {
			$post_id = wp_update_post( $args );
		}

		if ( ! empty($post_id) ) {
			//Save custom tags
			if(is_array($params['tag_input'])){
				foreach($params['tag_input'] as $key => $tags){
					wp_set_post_terms($post_id, $params['tag_input'][$key], $key);
				}
			}

			//			//Save categories
			//			if(is_array($params['post_category'])){
			//				wp_set_post_terms($post_id, $params['post_category'], 'category');
			//			}
			//
			//Save custom terms
			if(is_array($params['tax_input'])){
				foreach($params['tax_input'] as $key => $term_ids){
					if ( is_array( $params['tax_input'][$key] ) ) {
						wp_set_post_terms($post_id, $params['tax_input'][$key], $key);
					}
				}
			}

			if(! empty($params['_ct_jbp_job_Open_for']) ){
				$expires = strtotime("+{$params['_ct_jbp_job_Open_for']}");
				update_post_meta($post_id, JBP_JOB_EXPIRES_KEY, $expires );
				update_post_meta($post_id, '_ct_jbp_job_Open_for', '' );
			}

			//			if ( class_exists( 'CustomPress_Core' ) ) {
			//				global $CustomPress_Core;
			//				$CustomPress_Core->save_custom_fields( $post_id );
			//			}

			return $post_id;
		}
	}

	/**
	* @on_ajax_jbp_job
	* Handles update of jobs data via ajax and iframe transport
	* data may arrive from standard ajax or from iframe transport uploads.
	* Iframe uploads will be identified by a post field of "X-Requested-With" set to Iframe
	* Since IFrames cannot identify headers the response to and Iframe must be wrapped
	* in a <textarea data-type="application/json" data-status="http status code" data-statusText="status message"></textarea> wrapper.
	* This allows ajax iframe calls to mimic standard ajax but will work on file uploads.
	*/
	function on_ajax_jbp_job(){
		global $post_ID;

		if( !current_user_can('edit_jobs') ) exit;

		$response_iframe = '<textarea date-type="application/json" data-status="%s" data-statusText="%s">%s</textarea>';
		$params = stripslashes_deep($_REQUEST);
		//Is this an iframe upload
		$is_iframe = ( !empty($params['X-Requested-With']) && $params['X-Requested-With'] == 'IFrame');

		//Good nonce?
		if( !wp_verify_nonce($params['_wpnonce'], 'jbp_job') ){
			$statusText = 'Forbidden No Nonce Sins';
			header('HTTP/1.0 403 ' . $statusText); //error for standard Ajax
			if($is_iframe) exit( sprintf($response_iframe, '403', $statusText, $statusText . ': ' . print_r( $params, true) ) );
			else exit($statusText . ': ' . print_r(($params ), true ) );
		}

		// get the post id for x-editable
		$post_id = (empty($params['pk']) ) ? 0 : $params['pk'];

		$name = strstr($params['name'], '[', true);
		$name = $name ? $name : $params['name'];

		$index = strstr($params['name'], '[', false);
		$index = $index ? intval(trim( $index, '[]') ) : false;

		$value   = (empty($params['value']) ) ? '' : $params['value'];

		if( empty($post_id) ) exit;

		$post_ID = $post_id; //So media will know which post we're working on.

		$post = array('ID' => $post_id);

		switch ($name) {
			case 'post_title':
			$v = sanitize_text_field($value);
			if( empty($v) ){
				$statusText = __('This field must have a valid value', JBP_TEXT_DOMAIN);
				header('HTTP/1.0 403 ' . $statusText); //error for standard Ajax
				exit(sprintf('%s: %s', $statusText, $v));
			} else {
				$post['post_title'] = $v;
			}
			break;

			case 'post_content': $post['post_content'] = wp_strip_all_tags($value);
			break;

			//Custom fields
			//json multi part fields

			case '_ct_jbp_job_Location'    : update_post_meta($post_id, $name, sanitize_text_field($value) ); break;

			case '_ct_jbp_job_Portfolio' : {

				//could be multiple images so add to object
				$group = get_post_meta($post_id, $name, true );
				$group = empty($group) ? new stdClass : json_decode($group);

				if($value['remove'] == 'remove') {
					unset( $group->$value['attachment_id'] );
					//remove the files
					wp_delete_attachment($value['attachment_id'], true);
					update_post_meta($post_id, $name, json_encode($group) );
					// send back empty record.
					exit(sprintf($response_iframe, '200', 'OK', '{"newValue": null}' ));
				}

				$value = array_map('sanitize_text_field', $value);
				$value['url'] = $this->expand_url($value['url']);

				$value['caption'] = str_replace("\r\n",'<br/>',strip_tags($value['caption']) );

				if ( isset($_FILES['file']) && empty( $_FILES['file']['error'] )) {
					/* Require WordPress utility functions for handling media uploads */
					require_once( ABSPATH . '/wp-admin/includes/media.php' );
					require_once( ABSPATH . '/wp-admin/includes/image.php' );
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
					/* Upload the image ( handles creation of thumbnails etc. ), set featured image  */
					//remove existing files
					if(isset($group->$value['attachment_id']) ) {
						unset( $group->$value['attachment_id'] );
						wp_delete_attachment($value['attachment_id'], true);
					}
					$value['attachment_id'] = media_handle_upload( 'file', $post_id, array('post_content' => $value['caption'] ) );
					$src = wp_get_attachment_image_src( $value['attachment_id'],'job_thumbnail' );
					$value['src'] = $src[0];
				}

				$group->$value['attachment_id'] = $value; // Keep key associative

				//save json string
				update_post_meta($post_id, $name, json_encode($group) );

				$value = json_encode($value);
				//wrap the new value with a json newValue object to update the calling page
				exit(sprintf($response_iframe, '200', 'OK', '{"newValue": '. $value . '}' ));
				break;
			}

		}

		if(count($post) > 1) {
			$post['post_status'] = 'draft';
			wp_update_post($post, true); //See if fields added
		}
		exit;
	}

	function on_ajax_jbp_job_status(){

		if( !current_user_can('edit_jobs') ) exit;
		$params = stripslashes_deep($_REQUEST);

		//Good nonce?
		if( !wp_verify_nonce($params['_wpnonce'], 'jbp_job') ){
			$this->ajax_error('Forbidden No Nonce Sins', $params);
		}

		$post_id = (empty($params['post_id']) ) ? 0 : intval($params['post_id']);
		$post_status = (empty($params['post_status']) ? 0 : $params['post_status']);

		if( empty($post_id) ) exit;
		if ($this->get_setting("job->moderation->{$post_status}" != 1) ) exit;
		if( get_post_type($post_id) != 'jbp_job') exit;

		$id = wp_update_post(array(
		'ID' => $post_id,
		'post_status' => $post_status,
		) );

		$this->notice_message( sprintf(__('This %s has been updated', $this->text_domain), $this->job_labels->singular_name ) );

		$redirect = new stdClass();

		if (!empty($this->jbp_errors)) {
			$redirect->redirect = add_query_arg('jbp_error', urlencode(implode(', ', $this->jbp_errors)), get_permalink($id));
		} else {
			$redirect->redirect = add_query_arg('jbp_notice', urlencode(implode(', ', $this->jbp_notices)), get_permalink($id));
		}
		exit(json_encode($redirect));
	}

	/**
	* Modifies the select box for Open For to display the expiration date if available
	*/
	function job_open_for_fix($result='', $atts=array(), $content=null) {
		global $post;

		extract( shortcode_atts( array(
		'id' => '',
		'property' => 'input',
		'class' => '',
		), $atts ) );

		if( $id == '_ct_jbp_job_Open_for' && $property == 'input') {
			if( ! empty($post->ID) ) {
				$expires = get_post_meta($post->ID, JBP_JOB_EXPIRES_KEY, true);
				$expires_date = ( empty($expires) ) ? '' : date_i18n( get_option('date_format'), $expires );
				if(empty($expires_date) ){
					$result = preg_replace('#</option>#',
					__('How long is this Job open from today?</option>', JBP_TEXT_DOMAIN),
					$result, 1);
				} else {
					$result = preg_replace('#value=""#', 'value="0"', $result, 1); //Give it zero value so it will validate but not change anything.
					$result = preg_replace('#</option>#',
					sprintf('%s %s</option>', __('Expires on', JBP_TEXT_DOMAIN),
					$expires_date),
					$result, 1);
				}
			}
		}
		return $result;
	}


}
endif;

// Set flag on activation to trigger initial data
add_action('activated_plugin', 'jbp_flag_activation', 1);
function jbp_flag_activation($plugin=''){
	//Flag we're activating
	if($plugin == JBP_PLUGIN) add_site_option('jbp_activate', true);
}

global $Jobs_Plus_Core;

if (is_admin()) {
	require JBP_PLUGIN_DIR . 'class/class-admin.php';
	$Jobs_Plus_Core = new Jobs_Plus_Admin;
} else {
	$Jobs_Plus_Core = new Jobs_Plus_Core;
}
