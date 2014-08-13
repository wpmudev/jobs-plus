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
class JobsExperts_Core_Shortcode_JobContact extends JobsExperts_Shortcode {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_shortcode( 'jbp-job-contact-page', 'shortcode' );
		//shortcode style
		$this->_add_action( 'wp_enqueue_scripts', 'scripts', 999 );
	}

	function scripts() {
		$plugin = JobsExperts_Plugin::instance();
		wp_register_script( 'jbp_validate_script', $plugin->_module_url . 'assets/jquery-validation-engine/js/jquery.validationEngine.js' );
		wp_register_script( 'jbp_validate_script_en', $plugin->_module_url . 'assets/jquery-validation-engine/js/languages/jquery.validationEngine-en.js' );
		wp_register_style( 'jbp_validate_style', $plugin->_module_url . 'assets/jquery-validation-engine/css/validationEngine.jquery.css' );
	}

	public function shortcode( $atts ) {
		wp_enqueue_style( 'jobs-plus' );
		wp_enqueue_style( 'jbp_shortcode' );
		wp_enqueue_script( 'jbp_validate_script' );
		wp_enqueue_script( 'jbp_validate_script_en' );
		wp_enqueue_style( 'jbp_validate_style' );

		//get plugin instance
		$plugin    = JobsExperts_Plugin::instance();
		$slug      = isset( $_GET['contact'] ) ? $_GET['contact'] : null;
		$model     = JobsExperts_Core_Models_Job::instance()->get_one( $slug );
		$post_type = get_post_type_object( get_post_type() );
		if ( is_object( $model ) ) {
			ob_start();
			?>
			<div class="hn-container">
				<ol class="breadcrumb">
					<li><a href="<?php echo home_url() ?>"><?php _e( 'Home', JBP_TEXT_DOMAIN ) ?></a></li>
					<li>
						<a href="<?php echo get_post_type_archive_link( get_post_type() ) ?>"><?php echo $post_type->labels->name ?></a>
					</li>
					<li>
						<a href="<?php echo get_permalink( $model->id ) ?>"><?php echo get_the_title( $model->id ) ?></a>
					</li>
					<li class="active">Contact</li>
				</ol>
				<?php if ( isset( $_GET['status'] ) ): ?>
					<?php if ( $_GET['status'] == 'success' ): ?>
					<div class="alert alert-success">
						<strong><?php _e( 'Your request has been sent. Thank you!', JBP_TEXT_DOMAIN ) ?></strong>
					</div>
				<?php else: ?>
					<div class="alert alert-danger">
						<strong><?php _e( 'Some error happened, please try later. Thank you!', JBP_TEXT_DOMAIN ) ?></strong>
					</div>
				<?php endif; ?>
				<?php else: ?>
					<form method="post" class="jbp-contact" role="form">
						<?php do_action( 'jbp_before_job_contact_form' ) ?>
						<div class="row">
							<div class="col-md-3">
								<label><?php _e( 'Your Name:', JBP_TEXT_DOMAIN ) ?></label>
							</div>
							<div class="col-md-6">
								<input type="text" name="name" class="validate[required]">
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php _e( 'Contact email:', JBP_TEXT_DOMAIN ) ?></label>
							</div>
							<div class="col-md-6">
								<input type="text" name="email" class="validate[required,custom[email]]">
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php _e( 'Contact:', JBP_TEXT_DOMAIN ) ?></label>
							</div>
							<div class="col-md-6">
								<textarea rows="5" name="content" class="validate[required]"></textarea>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php do_action( 'jbp_after_job_contact_form' ) ?>
						<div class="row">
							<div class="col-md-3">
								<?php wp_nonce_field( 'jbp_contact' ) ?>
								<input type="hidden" name="jbp_contact_type" value="job">
								<input type="hidden" name="id" value="<?php echo $model->id ?>">
							</div>
							<div class="col-md-6">
								<button type="submit" class="btn btn-primary"><?php _e( 'Send', JBP_TEXT_DOMAIN ) ?></button>
								<a class="btn btn-default" href=""><?php _e( 'Cancel', JBP_TEXT_DOMAIN ) ?></a>
							</div>
							<div class="clearfix"></div>
						</div>
					</form>
					<script type="text/javascript">
						jQuery(document).ready(function ($) {
							$('.jbp-contact').validationEngine('attach', {
								binded: false,
								scroll: false
							});
						})
					</script>
				<?php endif; ?>
			</div>

		<?php
		} else {
			echo '<h3>' . sprintf( __( '%s not found!', JBP_TEXT_DOMAIN ), $post_type->labels->singular_name ) . '</h3>';
		}

		return ob_get_clean();
	}
}

new JobsExperts_Core_Shortcode_JobContact;