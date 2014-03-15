<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

if( !class_exists('Jobs_Plus_Admin') ):

class Jobs_Plus_Admin extends Jobs_Plus_Core{

	public $menu_page = null;

	function __construct(){
		parent::__construct();

		add_action('init', array(&$this, 'on_init') );
		add_action('admin_menu', array(&$this, 'on_admin_menu'));
		//add_action('admin_enqueue_scripts', array(&$this, 'on_enqueue_scripts'), 1000 );
		add_action('admin_enqueue_scripts', array(&$this, 'on_admin_enqueue_scripts') );
		add_action( 'personal_options', array( &$this, 'on_personal_options' ) );
		add_action( 'personal_options_update', array( &$this, 'on_edit_user_profile_update' ) );
		add_action( 'edit_user_profile_update', array( &$this, 'on_edit_user_profile_update' ) );

	}

	function on_init(){
		$this->handle_post();
		//Now init
		parent::on_init();
	}

	function on_admin_menu( ){
		$this->jobs_menu_page = add_submenu_page('edit.php?post_type=jbp_job',
		__('Settings', JBP_TEXT_DOMAIN),
		__('Settings', JBP_TEXT_DOMAIN),
		'manage_options', 'jobs-plus-menu',
		array($this, 'admin_menu_page'),
		$this->plugin_url . 'img/jobs-plus-16.png' );

		$this->pros_menu_page = add_submenu_page('edit.php?post_type=jbp_pro',
		__('Settings', JBP_TEXT_DOMAIN),
		__('Settings', JBP_TEXT_DOMAIN),
		'manage_options', 'jobs-plus-menu',
		array($this, 'admin_menu_page'),
		$this->plugin_url . 'img/jobs-plus-16.png' );

		add_action('load-' . $this->jobs_menu_page, array(&$this, 'on_load_menu') );
		add_action('load-' . $this->pros_menu_page, array(&$this, 'on_load_menu') );
	}

	function admin_menu_page(){
		$current_tab = (empty($_GET['tab']) )? 'settings' : $_GET['tab'];

		switch ($current_tab) {
			case 'settings': include $this->plugin_dir . 'ui-admin/settings.php'; break;
			case 'job': include $this->plugin_dir . 'ui-admin/job.php'; break;
			case 'pro': include $this->plugin_dir . 'ui-admin/pro.php';	break;
			case 'shortcodes': include $this->plugin_dir . 'ui-admin/shortcodes.php';	break;
			case 'convert': include $this->plugin_dir . 'ui-admin/convert.php';	break;
			default: include $this->plugin_dir . 'ui-admin/job.php'; break;
		}
	}

	function on_admin_enqueue_scripts	(){
		wp_enqueue_style('jobs-plus-admin-css', $this->plugin_url . 'css/jobs-plus-admin.css');
	}

	function on_load_menu(){

	}

	function render_tabs( $current_tab = 'settings'){
		$tabs = array(
		'settings' => __('Settings', JBP_TEXT_DOMAIN),
		'job' => sprintf(__('%s Options', JBP_TEXT_DOMAIN), $this->job_labels->singular_name),
		'pro' => sprintf(__('%s Options', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name),
		'shortcodes' => __('Shortcodes', JBP_TEXT_DOMAIN),
		//'convert' => __('Convert from WPMU Jobs', JBP_TEXT_DOMAIN),
		);

		$current_tab = (empty($_GET['tab']) )? 'settings' : $_GET['tab'];

		?>
		<h2 class="nav-tab-wrapper">
			<?php foreach( $tabs as $tab => $title):
			$class = ($tab === $current_tab) ? 'nav-tab-active' : '';
			?>
			<a class="nav-tab <?php echo $class ?>" href="<?php esc_attr_e("?post_type=jbp_job&page=jobs-plus-menu&tab=$tab"); ?>" ><img src="<?php echo $this->plugin_url . "img/{$tab}16.png";?>" /> <?php echo $title; ?></a>
				<?php endforeach; ?>
			</h2>

			<?php
		}

		/**
		* Handle posts from the various jobs board menus
		*
		*/
		function handle_post(){

			if( defined('DOING_AJAX') || empty($_POST['jobs-plus-settings']) ) return;

			check_admin_referer('jobs-plus-settings');

			$params = stripslashes_deep($_POST);

			if(! empty($params['jbp']) ) {
				$settings = get_option($this->settings_name);
				$settings = array_replace((array)$settings, $params['jbp'] );
				update_option($this->settings_name, $settings);
			}
		}

		function on_personal_options($profileuser = 0){
			if ( !current_user_can( 'promote_users') || !$this->get_setting('general->use_certification') ) return false;
			$certification = $this->get_setting('general->certification', __('Jobs Plus + Certified', JBP_TEXT_DOMAIN) );
			?>
			<tr class="jbp-certified">
				<th scope="row"><?php echo $certification; ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo $certification; ?></span></legend>
						<label for="jbp_certified">
							<input name="jbp_certified" type="checkbox" id="jbp_certified" value="1"<?php checked( is_certified( $profileuser->ID ) ); ?> />
							<?php printf( __('User is ', JBP_TEXT_DOMAIN ), $certification); ?>
						</label><br />
					</fieldset>
				</td>
			</tr>
			<?php
		}

		function on_edit_user_profile_update($user_id = 0){
			if ( !current_user_can( 'promote_users') || !$this->get_setting('general->use_certification') ) return false;

			update_user_meta($user_id, JBP_PRO_CERTIFIED_KEY, $_POST['jbp_certified']);
		}

	}

	endif;