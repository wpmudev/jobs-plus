<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_Settings_General extends JobsExperts_Framework_Render {
	public function __construct( $data ) {
		parent::__construct( $data );
	}

	public function _to_html() {
		$form  = $this->form;
		$model = $this->model;
		$plugin      = JobsExperts_Plugin::instance();
		$job_labels  = JobsExperts_Plugin::instance()->get_job_type()->labels;
		$pro_labels  = JobsExperts_Plugin::instance()->get_expert_type()->labels;
		?>
		<div class="page-header">
			<h3 class="hndle"><span><?php esc_html_e( 'General Options', JBP_TEXT_DOMAIN ) ?></span></h3>
		</div>
		<table class="form-table">
			<tr>
				<th>
					<label><?php esc_html_e( 'Icon Colors', JBP_TEXT_DOMAIN ); ?></label>
				</th>
				<td>
					<label>
						<?php $form->radioButton( $model, 'theme', 'dark' ) ?>
						<?php printf( '%s, <span class="description">%s</span>', esc_html__( 'Dark Icons', JBP_TEXT_DOMAIN ), esc_html__( 'for light button backgrounds', JBP_TEXT_DOMAIN ) ); ?>
					</label><br />
					<label>
						<?php $form->radioButton( $model, 'theme', 'bright' ) ?>
						<?php printf( '%s, <span class="description">%s</span>', esc_html__( 'Bright Icons', JBP_TEXT_DOMAIN ), esc_html__( 'for dark button backgrounds', JBP_TEXT_DOMAIN ) ); ?>
					</label><br />
					<label>
						<?php $form->radioButton( $model, 'theme', 'none' ) ?>
						<?php printf( '%s, <span class="description">%s</span>', esc_html__( 'No Icons', JBP_TEXT_DOMAIN ), esc_html__( 'to remove the icons from buttons', JBP_TEXT_DOMAIN ) ); ?>
					</label>

					<br /><span class="description"><?php esc_html_e( 'Sets the default color of the button icons. May be overriden for individual buttons in the "class" attribute of the shortcode.', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>
		</table>
	<?php
	}
}