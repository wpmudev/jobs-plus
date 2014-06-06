<?php
/**
 * @package Jobs +
 * @author  Arnold Bailey
 * @since   version 1.0
 * @license GPL2+
 */

if ( ! class_exists( 'Jobs_Plus_Admin' ) ):

	class Jobs_Plus_Admin extends Jobs_Plus_Core {

		public $menu_page = null;

		function __construct() {
			parent::__construct();

			add_action( 'init', array( &$this, 'on_init' ) );
			add_action( 'admin_menu', array( &$this, 'on_admin_menu' ) );
			//add_action('admin_enqueue_scripts', array(&$this, 'on_enqueue_scripts'), 1000 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'on_admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'wp_pointer_load' ) );

			add_action( 'personal_options', array( &$this, 'on_personal_options' ) );
			add_action( 'personal_options_update', array( &$this, 'on_edit_user_profile_update' ) );
			add_action( 'edit_user_profile_update', array( &$this, 'on_edit_user_profile_update' ) );

			add_action( 'wp_ajax_jbp_get_caps', array( &$this, 'on_ajax_get_caps' ) );
			add_action( 'wp_ajax_jbp_save_caps', array( &$this, 'on_ajax_save_caps' ) );

			//add action for handler the page creation
			add_action( 'init', array( &$this, 'create_placeholder_page' ), 20 );
			add_action( 'jpb_after_save_settings', array( &$this, 'flag_page_with_meta' ), 10, 2 );

		}

		function on_init() {
			$this->handle_post();
			//Now init
			parent::on_init();
		}

		function on_admin_menu() {
			$this->jobs_menu_page = add_submenu_page( 'edit.php?post_type=jbp_job',
				__( 'Settings', JBP_TEXT_DOMAIN ),
				__( 'Settings', JBP_TEXT_DOMAIN ),
				'manage_options', 'jobs-plus-menu',
				array( $this, 'admin_menu_page_job' ),
				$this->plugin_url . 'img/job.svg' );

			$this->pros_menu_page = add_submenu_page( 'edit.php?post_type=jbp_pro',
				__( 'Settings', JBP_TEXT_DOMAIN ),
				__( 'Settings', JBP_TEXT_DOMAIN ),
				'manage_options', 'jobs-plus-menu',
				array( $this, 'admin_menu_page_pro' ),
				$this->plugin_url . 'img/job.svg' );

			add_action( 'load-' . $this->jobs_menu_page, array( &$this, 'on_load_menu' ) );
			add_action( 'load-' . $this->pros_menu_page, array( &$this, 'on_load_menu' ) );
		}

		function admin_menu_page_job() {
			$current_tab = ( empty( $_GET['tab'] ) ) ? 'job' : $_GET['tab'];

			switch ( $current_tab ) {
				case 'settings':
					include $this->plugin_dir . 'ui-admin/settings.php';
					break;
				case 'job':
					include $this->plugin_dir . 'ui-admin/job.php';
					break;
				case 'pro':
					include $this->plugin_dir . 'ui-admin/pro.php';
					break;
				case 'shortcodes':
					include $this->plugin_dir . 'ui-admin/shortcodes.php';
					break;
				case 'about':
					include $this->plugin_dir . 'ui-admin/about.php';
					break;
				case 'page_creation':
					include $this->plugin_dir . 'ui-admin/page-creation.php';
					break;
				default:
					include $this->plugin_dir . 'ui-admin/job.php';
					break;
			}
		}

		function admin_menu_page_pro() {
			$current_tab = ( empty( $_GET['tab'] ) ) ? 'pro' : $_GET['tab'];
			switch ( $current_tab ) {
				case 'settings':
					include $this->plugin_dir . 'ui-admin/settings.php';
					break;
				case 'job':
					include $this->plugin_dir . 'ui-admin/job.php';
					break;
				case 'pro':
					include $this->plugin_dir . 'ui-admin/pro.php';
					break;
				case 'shortcodes':
					include $this->plugin_dir . 'ui-admin/shortcodes.php';
					break;
				case 'about':
					include $this->plugin_dir . 'ui-admin/about.php';
					break;
				case 'page_creation':
					include $this->plugin_dir . 'ui-admin/page-creation.php';
					break;
				default:
					include $this->plugin_dir . 'ui-admin/job.php';
					break;
			}
		}

		function on_admin_enqueue_scripts() {
			wp_enqueue_style( 'jobs-plus-admin-css', $this->plugin_url . 'css/jobs-plus-admin.css' );
			wp_enqueue_script( 'jobs-plus-admin' );
		}

		function on_load_menu() {

		}

		function render_tabs( $current_tab = 'settings' ) {
			$tabs = array(
				'settings'      => esc_html__( 'General', JBP_TEXT_DOMAIN ),
				'job'           => esc_html( sprintf( __( '%s Options', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ) ),
				'pro'           => esc_html( sprintf( __( '%s Options', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ) ),
				'shortcodes'    => esc_html__( 'Shortcodes', JBP_TEXT_DOMAIN ),
				'about'         => esc_html__( 'Getting Started with Jobs+', JBP_TEXT_DOMAIN ),
				'page_creation' => __( 'Page Creation', JBP_TEXT_DOMAIN ),
			);

			$current_tab = ( empty( $_GET['tab'] ) ) ? $current_tab : $_GET['tab'];

			?>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab => $title ):
					$class = ( $tab === $current_tab ) ? 'nav-tab-active' : '';
					?>
					<a class="nav-tab <?php echo $class ?>" href="<?php echo esc_attr( "?post_type=jbp_job&page=jobs-plus-menu&tab=$tab" ); ?>"><img style=" vertical-align: middle; width: 20px;" src="<?php echo $this->plugin_url . "img/{$tab}.svg"; ?>" /> <?php echo $title; ?>
					</a>
				<?php endforeach; ?>
			</h2>

		<?php
		}

		/**
		 * Handle posts from the various jobs board menus
		 *
		 */
		function handle_post() {

			if ( defined( 'DOING_AJAX' ) || empty( $_POST['jobs-plus-settings'] ) ) {
				return;
			}

			check_admin_referer( 'jobs-plus-settings' );

			$params = stripslashes_deep( $_POST );

			if ( ! empty( $params['jbp'] ) ) {
				$settings = get_option( $this->settings_name );
				$settings = array_replace( (array) $settings, $params['jbp'] );
				update_option( $this->settings_name, $settings );

				do_action( 'jpb_after_save_settings', $this->settings_name, $settings );
			}
		}

		function on_personal_options( $profileuser = 0 ) {
			if ( ! current_user_can( 'promote_users' ) || ! $this->get_setting( 'general->use_certification' ) ) {
				return false;
			}
			$certification = esc_html( $this->get_setting( 'general->certification', __( 'Jobs + Certified', JBP_TEXT_DOMAIN ) ) );
			?>
			<tr class="jbp-certified">
				<th scope="row"><?php echo $certification; ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo $certification; ?></span></legend>
						<label for="jbp_certified">
							<input name="jbp_certified" type="checkbox" id="jbp_certified" value="1"<?php checked( is_jbp_certified( $profileuser->ID ) ); ?> />
							<?php printf( __( 'User is %s', JBP_TEXT_DOMAIN ), $certification ); ?>
						</label><br />
					</fieldset>
				</td>
			</tr>
		<?php
		}

		function on_edit_user_profile_update( $user_id = 0 ) {
			if ( ! current_user_can( 'promote_users' ) || ! $this->get_setting( 'general->use_certification' ) ) {
				return false;
			}

			update_user_meta( $user_id, JBP_PRO_CERTIFIED_KEY, $_POST['jbp_certified'] );
		}

		/**
		 * wp_pointer_load - Loads the WordPress tips pointers for Jobs.
		 *
		 */
		function wp_pointer_load() {

			//var_dump(get_current_screen()->id);

			$term_images_content = __( '<p>Jobs + works best when you use images for Job Categories, please enable them here and configure the image size that best fits your website</p>', JBP_TEXT_DOMAIN );

			//Setup any new feature notices
			include_once $this->plugin_dir . 'class/class-wp-help-pointers.php';

			$pointers = array(

				array(
					'id'       => 'jbp_term_images', // unique id for this pointer
					'screen'   => 'edit-jbp_category', // this is the page hook we want our pointer to show on
					'target'   => '#jbp-term-images', // the css selector for the pointer to be tied to, best to use ID's
					'title'    => __( 'Term Images Setup', JBP_TEXT_DOMAIN ),
					'content'  => $term_images_content,
					'position' => array(
						'edge'  => 'left', //top, bottom, left, right
						'align' => 'middle' //top, bottom, left, right, middle
					)
				),

				array(
					'id'       => 'jbp_term_images', // unique id for this pointer
					'screen'   => 'edit-jbp_skills_tag', // this is the page hook we want our pointer to show on
					'target'   => '#jbp-term-images', // the css selector for the pointer to be tied to, best to use ID's
					'title'    => __( 'Term Images Setup', JBP_TEXT_DOMAIN ),
					'content'  => $term_images_content,
					'position' => array(
						'edge'  => 'left', //top, bottom, left, right
						'align' => 'middle' //top, bottom, left, right, middle
					)
				),

				array(
					'id'       => 'jbp_cookies', // unique id for this pointer
					'screen'   => 'plugins', // this is the page hook we want our pointer to show on
					'target'   => '#toplevel_page_jbp-settings', // the css selector for the pointer to be tied to, best to use ID's
					'title'    => __( 'NEW - WHMCS WordPress Integration Cookie syncing', JBP_TEXT_DOMAIN ),
					'content'  => $cookie_content,
					'position' => array(
						'edge'  => 'left', //top, bottom, left, right
						'align' => 'right' //top, bottom, left, right, middle
					)
				),

				array(
					'id'       => 'jbp_cookies', // unique id for this pointer
					'screen'   => 'toplevel_page_jbp-settings', // this is the page hook we want our pointer to show on
					'target'   => '#toplevel_page_jbp-settings', // the css selector for the pointer to be tied to, best to use ID's
					'title'    => __( 'NEW - WHMCS WordPress Integration Cookie syncing', JBP_TEXT_DOMAIN ),
					'content'  => $cookie_content,
					'position' => array(
						'edge'  => 'left', //top, bottom, left, right
						'align' => 'right' //top, bottom, left, right, middle
					)
				),

				// more as needed
			);

			new WP_Help_Pointer( $pointers );
		}

		/**
		 * Ajax callback which gets the post types associated with each page.
		 *
		 * @return JSON Encoded string
		 */
		function on_ajax_get_caps() {
			if ( ! current_user_can( 'manage_options' ) ) {
				die( - 1 );
			}
			if ( empty( $_REQUEST['role'] ) ) {
				die( - 1 );
			}

			global $wp_roles, $CustomPress_Core;

			$role      = $_REQUEST['role'];
			$post_type = $_REQUEST['post_type'];

			if ( ! $wp_roles->is_role( $role ) ) {
				die( - 1 );
			}

			$role_obj = $wp_roles->get_role( $role );
			$all_caps = $CustomPress_Core->all_capabilities( $post_type );

			$response = array_intersect( array_keys( $role_obj->capabilities ), $all_caps );
			$response = array_flip( $response );

			wp_send_json( $response );
		}

		/**
		 * Save admin options.
		 *
		 * @return void die() if _wpnonce is not verified
		 */
		function on_ajax_save_caps() {

			check_admin_referer( 'jobs-plus-settings' );

			if ( ! current_user_can( 'manage_options' ) ) {
				die( - 1 );
			}

			// add/remove capabilities
			global $wp_roles, $CustomPress_Core;

			$role      = $_POST['roles'];
			$post_type = $_REQUEST['post_type'];

			$all_caps = $CustomPress_Core->all_capabilities( $post_type );

			$to_add    = array_keys( (array) $_REQUEST['capabilities'] );
			$to_remove = array_diff( $all_caps, $to_add );

			foreach ( $to_remove as $capability ) {
				$wp_roles->remove_cap( $role, $capability );
			}

			foreach ( $to_add as $capability ) {
				$wp_roles->add_cap( $role, $capability );
			}

			die( 1 );
		}

		public function create_placeholder_page() {
			$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
			if ( ! empty( $action ) ) {
				$tab_url = 'edit.php?post_type=' . $_GET['post_type'] . '&page=' . $_GET['page'] . '&tab=' . $_GET['tab'];

				$post_content = __( "<p>Virtual page. Editing this page won\'t change anything.<br />", JBP_TEXT_DOMAIN );
				$post_content .= __( "You may edit the Title and/or the slug only.</p>", JBP_TEXT_DOMAIN );

				global $Jobs_Plus_Core;

				switch ( $action ) {
					case 'jbp_create_add_job_page':
						check_admin_referer( 'jbp_create_add_job_page' );
						//get the post type info
						$post_type = get_post_type_object( 'jbp_job' );
						//check does the page exist
						$args = array(
							'post_title'     => sprintf( 'Add %s', $post_type->labels->singular_name ),
							'post_name'      => sprintf( 'add-%s', $post_type->rewrite['slug'] ),
							'post_status'    => 'publish',
							'post_author'    => get_current_user_id(),
							'post_type'      => 'page',
							'post_content'   => $post_content,
							'ping_status'    => 'closed',
							'comment_status' => 'closed'
						);
						$id   = wp_insert_post( $args );
						wp_safe_redirect( admin_url( $tab_url . '&add_job_page_id=' . $id ) );
						break;
					case 'jbp_create_add_pro_page':
						check_admin_referer( 'jbp_create_add_pro_page' );
						$post_type = get_post_type_object( 'jbp_pro' );
						$args      = array(
							'post_title'     => sprintf( 'Add %s', $post_type->labels->singular_name ),
							'post_name'      => sprintf( 'add-%s', $post_type->rewrite['slug'] ),
							'post_status'    => 'publish',
							'post_author'    => get_current_user_id(),
							'post_type'      => 'page',
							'post_content'   => $post_content,
							'ping_status'    => 'closed',
							'comment_status' => 'closed'
						);
						$id        = wp_insert_post( $args );
						wp_safe_redirect( admin_url( $tab_url . '&jbp_add_pro_page_id=' . $id ) );
						break;
				}
			}
		}

		function flag_page_with_meta( $setting_name, $settings ) {
			$defined_page = $settings['pages_define'];

			if ( ! empty( $defined_page['add_job'] ) ) {
				//lookup all page and remove the meta
				global $Jobs_Plus_Core;
				$flag_pages = $Jobs_Plus_Core->find_page_by_meta( 'page', 'jbp_job', 'add_job_page' );
				foreach ( $flag_pages as $page ) {
					delete_post_meta( $page, 'jbp_job' );
				}
				update_post_meta( $defined_page['add_job'], 'jbp_job', 'add_job_page' );
			}

			if ( ! empty( $defined_page['add_pro'] ) ) {
				//lookup all page and remove the meta
				global $Jobs_Plus_Core;
				$flag_pages = $Jobs_Plus_Core->find_page_by_meta( 'page', 'jbp_pro', 'add_pro_page' );
				foreach ( $flag_pages as $page ) {
					delete_post_meta( $page, 'jbp_pro' );
				}
				update_post_meta( $defined_page['add_pro'], 'jbp_pro', 'add_pro_page' );
			}
		}

	}

endif;