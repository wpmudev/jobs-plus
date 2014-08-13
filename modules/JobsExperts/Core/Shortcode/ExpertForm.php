<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 *
 * This shortcode will render Become Expert Form
 *
 * Shortcode attributes list
 *
 *
 * @category JobsExperts
 * @package  Shorcode
 *
 * @since    1.0.0
 */
class JobsExperts_Core_Shortcode_ExpertForm extends JobsExperts_Shortcode {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_shortcode( 'jbp-expert-update-page', 'shortcode' );
		//shortcode style
		$this->_add_action( 'wp_enqueue_scripts', 'scripts', 999 );
	}

	function scripts() {
		$plugin = JobsExperts_Plugin::instance();

		//validate
		wp_register_script( 'jbp_validate_script', $plugin->_module_url . 'assets/jquery-validation-engine/js/jquery.validationEngine.js' );
		wp_register_script( 'jbp_validate_script_en', $plugin->_module_url . 'assets/jquery-validation-engine/js/languages/jquery.validationEngine-en.js' );
		wp_register_style( 'jbp_validate_style', $plugin->_module_url . 'assets/jquery-validation-engine/css/validationEngine.jquery.css' );

		wp_register_script( 'jbp_iframe_transport', $plugin->_module_url . 'assets/js/jquery-iframe-transport.js' );

		wp_register_script( 'jbp_json', $plugin->_module_url . 'assets/js/json2.js' );
	}

	public function shortcode( $atts ) {
		wp_enqueue_style( 'jobs-plus' );
		wp_enqueue_style( 'jbp_shortcode' );
		wp_enqueue_script( 'jbp_bootstrap' );

		wp_enqueue_script( 'jbp_validate_script' );
		wp_enqueue_script( 'jbp_validate_script_en' );
		wp_enqueue_style( 'jbp_validate_style' );

		wp_enqueue_script( 'jbp_iframe_transport' );

		wp_enqueue_script( 'jbp_json' );

		$plugin = JobsExperts_Plugin::instance();
		ob_start();
		echo '<div class="hn-container">';
		if ( ! is_user_logged_in() ) {
			//user still not login, we need to load login form
			$this->load_login_form();
		} else {
			$page_module = JobsExperts_Plugin::instance()->page_module();
			///load model
			$model   = '';
			$is_edit = $page_module->page( $page_module::EXPERT_EDIT ) == get_the_ID();
			if ( $is_edit && isset( $_GET['pro'] ) && ! empty( $_GET['pro'] ) ) {
				$model = JobsExperts_Core_Models_Pro::instance()->get_one( $_GET['pro'], array( 'publish', 'draft', 'pending' ) );
			}
			if ( ! is_object( $model ) ) {
				$model = new JobsExperts_Core_Models_Pro();
				if ( isset( $_GET['first_name'] ) ) {
					$model->first_name = $_GET['first_name'];
				}

				if ( isset( $_GET['last_name'] ) ) {
					$model->last_name = $_GET['last_name'];
				}
			}
			//now we need to check does this user can add new job
			if ( $model->is_current_can_edit() == false ) {
				//oh no, he can not
				echo '<h4 style="text-align: center">' . _e( 'Sorry you do not have enough permission to become an expert', JBP_TEXT_DOMAIN ) . '</h4>';
			} elseif ( $model->is_reach_max() ) {
				//this user can not add more
				echo '<h4 style="text-align: center">' . __( 'Sorry, you reach max amount of expert profile', JBP_TEXT_DOMAIN ) . '</h4>';
			} else {
				if ( isset( $plugin->global['jbp_pro'] ) ) {
					$model = $plugin->global['jbp_pro'];
				}
				//ok, load the form
				$template = new JobsExperts_Core_Views_ExpertForm( array(
					'model'   => $model,
					'is_edit' => $is_edit
				) );
				$template->render();
			}

		}
		echo '</div>';

		return apply_filters( 'jbp_pro_form_output', ob_get_clean() );
		?>
	<?php
	}

	function load_login_form() {
		?>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default jbp_login_form">
					<div class="panel-heading">
						<h3 class="panel-title">
							<?php _e( 'Please login', JBP_TEXT_DOMAIN ) ?>
							<?php
							$can_register = is_multisite() == true ? get_site_option( 'users_can_register' ) : get_option( 'users_can_register' );
							if ( $can_register ): ?>
								or <?php echo sprintf( '<a href="%s">%s</a>', wp_registration_url(), __( 'register here', JBP_TEXT_DOMAIN ) ) ?>
							<?php endif; ?>
						</h3>
					</div>
					<div class="panel-body">
						<?php echo wp_login_form( array( 'echo' => false ) ) ?>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}

new JobsExperts_Core_Shortcode_ExpertForm;