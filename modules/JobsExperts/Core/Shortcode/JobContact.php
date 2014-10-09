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
	}

	public function shortcode( $atts ) {
		wp_enqueue_style( 'jobs-contact' );
		wp_enqueue_script( 'jobs-noty' );

		$a = shortcode_atts( array(
			'success_text' => __( "Your request has been sent. Thank you!", JBP_TEXT_DOMAIN ),
			'error_text'   => __( "Some error happened, please try later. Thank you!", JBP_TEXT_DOMAIN ),
			'id'           => 0
		), $atts );

		//get plugin instance
		$plugin = JobsExperts_Plugin::instance();
		if ( $a['id'] != 0 ) {
			$model = JobsExperts_Core_Models_Job::instance()->get_one($a['id']);
		} else {
			$slug  = isset( $_GET['contact'] ) ? $_GET['contact'] : null;
			$model = JobsExperts_Core_Models_Job::instance()->get_one( $slug );
		}


		$contact = new JobsExperts_Core_Models_Contact();
		$form    = JobsExperts_Framework_ActiveForm::generateForm( $contact );

		$post_type = get_post_type_object( get_post_type() );
		if ( is_object( $model ) ) {
			ob_start();
			?>
			<div class="hn-container">
				<div class="jobs-contact">
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
							<strong><?php echo esc_html( $a['success_text'] ) ?></strong>
						</div>
					<?php else: ?>
						<div class="alert alert-danger">
							<strong><?php echo esc_html( $a['error_text'] ) ?></strong>
						</div>
					<?php endif; ?>
					<?php else: ?>
						<form method="post" class="form-horizontal jobs-contact-form" role="form">
							<?php do_action( 'jbp_before_job_contact_form' ) ?>
							<div class="form-group has-feedback">
								<label
									class="col-sm-3 control-label"><?php _e( 'Your Name:', JBP_TEXT_DOMAIN ) ?></label>

								<div class="col-sm-9">
									<?php  $form->textField( $contact, 'name', array(
										'class' => 'form-control'
									) ) ?>
									<span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="form-group has-feedback">
								<label
									class="col-sm-3 control-label"><?php _e( 'Contact email:', JBP_TEXT_DOMAIN ) ?></label>

								<div class="col-sm-9">
									<?php  $form->textField( $contact, 'email', array(
										'class' => 'form-control'
									) ) ?>
									<span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>

								</div>
								<div class="clearfix"></div>
							</div>
							<div class="form-group has-feedback">
								<label class="col-sm-3 control-label"><?php _e( 'Content:', JBP_TEXT_DOMAIN ) ?></label>

								<div class="col-sm-9">
									<?php echo $form->textArea( $contact, 'content', array(
										'class' => 'form-control',

									) ) ?>
									<span class="fa fa-circle-o-notch fa-spin form-control-feedback"></span>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php wp_nonce_field( 'jbp_contact' ) ?>
							<div class="row">
								<div class="col-md-3"></div>
								<div class="col-md-9">
									<button type="button"
									        class="submit btn btn-primary"><i
											class="fa fa-envelope"></i> <?php _e( 'Send Message', JBP_TEXT_DOMAIN ) ?>
									</button>
								</div>
							</div>

							<?php do_action( 'jbp_after_job_contact_form' ) ?>

						</form>
						<script type="text/javascript">
							jQuery(document).ready(function ($) {
								/*$('.jbp-contact').validationEngine('attach', {
								 binded: false,
								 scroll: false
								 });*/
								$('.form-control-feedback').hide();
								$(".jobs-contact-form").find(':input').blur(function () {
									var parent = $(this).closest('div');
									var top_parent = parent.parent();
									if (parent.hasClass('input-group')) {
										parent = parent.parent();
										top_parent = parent.parent();
									}
									$.ajax({
										type: 'POST',
										data: {
											'class': '<?php echo get_class($contact) ?>',
											'action': 'job_validate',
											'key': $(this).attr('name'),
											'data': $(".jobs-contact-form").serializeArray(),
											'_nonce': '<?php echo wp_create_nonce('job_validate') ?>'
										},
										url: '<?php echo admin_url('admin-ajax.php') ?>',
										beforeSend: function () {
											parent.find('.form-control-feedback').show();
										},
										success: function (data) {
											parent.find('.form-control-feedback').hide();
											data = jQuery.parseJSON(data);
											if (data.status == 1) {
												top_parent.removeClass('has-success has-error').addClass('has-success');
												parent.find('.help-block').remove();
											} else {
												top_parent.removeClass('has-success has-error').addClass('has-error');
												parent.find('.help-block').remove();
												parent.append('<p class="help-block">' + data.error + '</p>');
											}
										}
									})
								});

								$(".jobs-contact-form").find('.submit').on('click', function () {
									var that = $(this);
									var form = that.closest('form');
									//trigger validate
									var old_text = '';
									$.ajax({
										type: 'POST',
										data: {
											'class': '<?php echo get_class($contact) ?>',
											'action': 'send_email',
											'data': form.serializeArray(),
											'status': that.val(),
											'_nonce': '<?php echo wp_create_nonce('send_email') ?>',
											'type': 'job',
											'id': '<?php echo $model->id ?>'
										},
										url: '<?php echo admin_url('admin-ajax.php') ?>',
										beforeSend: function () {
											form.find('.submit').attr('disabled', 'disabled');
											old_text = that.html();
											that.text('Sending...');
										},
										success: function (data) {
											data = $.parseJSON(data);
											if (data.status == 1) {
												location.href = data.url;
											} else {
												//rebind
												form.find('.submit').removeAttr('disabled');
												that.html(old_text);
												//fill error
												$.each(data.errors, function (i, v) {
													//build name
													var class_name = '<?php echo get_class($contact) ?>';
													var name = class_name + '[' + i + ']';
													var input = form.find(':input[name="' + name + '"]');
													//get container
													var iparent = input.closest('div');
													var itop_parent = iparent.parent();
													if (iparent.hasClass('input-group')) {
														iparent = iparent.parent();
														itop_parent = iparent.parent();
													}
													itop_parent.removeClass('has-success has-error').addClass('has-error');
													iparent.find('.help-block').remove();
													iparent.append('<p class="help-block">' + v + '</p>');
												});
												//display noty
												var n = noty({
													text: '<?php echo esc_js(__('Error happen, please check the form data',JBP_TEXT_DOMAIN)) ?>',
													layout: 'centerRight',
													type: 'error',
													timeout: 5000
												});
											}
										}
									})
									return false;
								})
							})
						</script>
					<?php endif; ?>
				</div>
			</div>

		<?php
		} else {
			echo '<h3>' . sprintf( __( '%s not found!', JBP_TEXT_DOMAIN ), $post_type->labels->singular_name ) . '</h3>';
		}

		return ob_get_clean();
	}
}

new JobsExperts_Core_Shortcode_JobContact;