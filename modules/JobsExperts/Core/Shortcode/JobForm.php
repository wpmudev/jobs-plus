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
 * @category JobsExperts
 * @package  Shorcode
 *
 * @since    1.0.0
 */
class JobsExperts_Core_Shortcode_JobForm extends JobsExperts_Shortcode {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_shortcode( 'jbp-job-update-page', 'shortcode' );
		//shortcode style
		$this->_add_action( 'wp_enqueue_scripts', 'scripts', 999 );
	}

	function scripts() {
		$plugin = JobsExperts_Plugin::instance();

		//validate
		wp_register_script( 'jbp_validate_script', $plugin->_module_url . 'assets/js/jquery.validationEngine.js' );
		wp_register_script( 'jbp_validate_script_en', $plugin->_module_url . 'assets/js/jquery.validationEngine-en.js' );
		wp_register_style( 'jbp_validate_style', $plugin->_module_url . 'assets/css/validationEngine.jquery.css' );

		wp_register_script( 'jbp_iframe_transport', $plugin->_module_url . 'assets/js/jquery-iframe-transport.js' );
		wp_register_script( 'jbp_select2', $plugin->_module_url . 'assets/js/select2.min.js' );
		wp_register_style( 'jbp_select2', $plugin->_module_url . 'assets/css/select2.css' );

		wp_register_script( 'jbp_datepicker', $plugin->_module_url . 'assets/datepicker/js/bootstrap-datepicker.js' );
		wp_register_style( 'jbp_datepicker', $plugin->_module_url . 'assets/datepicker/css/datepicker.css' );

	}

	public function shortcode( $atts ) {
		wp_enqueue_style( 'jobs-plus' );

		wp_enqueue_script( 'jbp_validate_script' );
		wp_enqueue_script( 'jbp_validate_script_en' );
		wp_enqueue_style( 'jbp_validate_style' );
		wp_enqueue_script( 'jbp_iframe_transport' );

		wp_enqueue_script( 'jbp_select2' );
		wp_enqueue_style( 'jbp_select2' );

		wp_enqueue_script( 'jbp_datepicker' );
		wp_enqueue_style( 'jbp_datepicker' );

		wp_enqueue_script( 'jbp_bootstrap' );

		$plugin      = JobsExperts_Plugin::instance();
		$page_module = $plugin->page_module();
		ob_start();
		echo '<div class="hn-container">';
		if ( ! is_user_logged_in() ) {
			//user still not login, we need to load login form
			$this->load_login_form();
		} else {
			///load model
			$model   = '';
			$is_edit = $page_module->page( $page_module::JOB_EDIT ) == get_the_ID();
			if ( $is_edit && isset( $_GET['job'] ) && ! empty( $_GET['job'] ) ) {
				$model = JobsExperts_Core_Models_Job::instance()->get_one( $_GET['job'], array( 'publish', 'draft', 'pending' ) );
			}

			if ( ! is_object( $model ) ) {
				$model = new JobsExperts_Core_Models_Job();
			}

			//bind
			//now we need to check does this user can add new job
			if ( $model->is_current_can_edit() == false ) {
				//oh no, he can not
				echo '<h4 style="text-align: center">' . _e( 'Sorry you do not have enough permission to add new job', JBP_TEXT_DOMAIN ) . '</h4>';
			} elseif ( $model->is_reach_max() && ! $is_edit ) {
				//this user can not add more
				echo '<h4 style="text-align: center">' . __( 'Sorry, you reach max amount of jobs', JBP_TEXT_DOMAIN ) . '</h4>';
			} else {
				echo '<div class="job-add-container">';
				//ok, load the form
				if ( isset( $plugin->global['jbp_job'] ) ) {
					$model = $plugin->global['jbp_job'];
				}
				$template = new JobsExperts_Core_Views_JobForm( array(
					'model'   => $model,
					'is_edit' => $is_edit
				) );
				$template->render();
				echo '</div>';
			}

		}
		echo '</div>';

		return apply_filters( 'jbp_job_form_output', ob_get_clean() );
	}

	function load_login_form() {
		?>
		<div class="row">
			<div class="jbp_login_form col-md-7 col-sm-12 col-sx-12 hidden-sm hidden-xs">
				<h1><?php _e( 'Please login', JBP_TEXT_DOMAIN ) ?>

					<?php
					$can_register = is_multisite() == true ? get_site_option( 'users_can_register' ) : get_option( 'users_can_register' );
					if ( $can_register ): ?>
						or <?php echo sprintf( '<a href="%s">%s</a>', wp_registration_url(), __( 'register here', JBP_TEXT_DOMAIN ) ) ?>
					<?php endif; ?>

				</h1>

				<div class="jbp_body">
					<?php echo wp_login_form( array( 'echo' => false ) ) ?>
				</div>
			</div>
			<div class="jbp_login_form col-sm-12 col-sx-12 hidden-md hidden-lg">
				<h1><?php _e( 'You must login to add a job', JBP_TEXT_DOMAIN ) ?></h1>

				<div class="jbp_body">
					<?php wp_login_form( array() ); ?>

				</div>
			</div>

			<div style="clear: both"></div>
		</div>
	<?php
	}
}

new JobsExperts_Core_Shortcode_JobForm;