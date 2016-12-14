<div class="ig-container">
	<div class="hn-container">
		<div class="jbp-job-single">
			<?php
			if ( isset( $model->job_img ) && $model->job_img != '' && is_numeric( $model->job_img ) ) {
				$image = wp_get_attachment_url( $model->job_img );
				?>
				<div class="row">
					<div style="margin-bottom: 20px;">
						<img src="<?php echo $image ?>">
					</div>
				</div>
			<?php } ?>
			<div class="row hn-border hn-border-round jobs-meta">
				<div class="col-md-3 jobs-meta-row">
					<h5><?php _e( 'Job Budget', je()->domain ); ?></h5>
					<small class="text-warning"><?php $model->render_prices() ?></small>
				</div>
				<div class="col-md-3 jobs-meta-row">
					<h5><?php _e( 'This job open for', je()->domain ) ?></h5>
					<small class="text-warning"><?php echo $model->get_due_day() ?></small>
				</div>
				<div class="col-md-3 jobs-meta-row">
					<h5><?php _e( 'Must be completed by', je()->domain ) ?></h5>
					<?php if ( strtotime( $model->dead_line ) ): ?>
						<small
							class="text-warning"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $model->dead_line ) ); ?></small>
					<?php else: ?>
						<small class="text-warning"><?php _e( 'N/A', je()->domain ) ?></small>
					<?php endif; ?>
				</div>

				<div class="col-md-3 jobs-meta-row">
					<?php if ( strtolower( $model->get_due_day() ) != 'expired' && je()->settings()->job_contact_form == 0 ): ?>
						<?php if ( JobsExperts_Helper::is_user_pro( get_current_user_id() ) ): ?>
							<?php ob_start(); ?>
							<a class="btn btn-info btn-sm jbp_contact_job"
							   href="<?php echo esc_url( add_query_arg( array(
								   'contact' => get_post()->post_name
							   ), apply_filters( 'jbp_job_contact_link', get_permalink( je()->pages->page( JE_Page_Factory::JOB_CONTACT ) ), get_the_ID() ) ) ) ?>"><?php _e( 'Contact', je()->domain ) ?></a>
							<?php $content = ob_get_clean();
							echo apply_filters( 'jbp_job_contact_btn', $content, $model );
							?>
						<?php else: ?>
							<a class="btn btn-info btn-sm"
							   href="<?php echo get_permalink( je()->pages->page( JE_Page_Factory::EXPERT_ADD ) ) ?>"><?php _e( 'Become Expert', je()->domain ) ?></a>
						<?php endif; ?>
					<?php else: ?>
						<a disabled class="btn btn-info btn-sm"
						   href="#"><?php _e( 'Contact', je()->domain ) ?></a>
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="row job-content">
				<div class="col-md-12">
					<?php echo wpautop( JobsExperts_Helper::jbp_html_beautifier( wp_kses( $model->description, wp_kses_allowed_html() ) ) ) ?>
				</div>
				<div class="col-md-12">
					<?php
					$skills = $model->skills;
					if ( ! empty( $skills ) ): ?>
						<div class="job_skills">
							<?php
							echo get_the_term_list( $model->id, 'jbp_skills_tag', __( '<h4>You will need to have these skills:', je()->domain ) . '</h4><ul><li>', '</li><li>', '</li></ul>' )
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php do_action( 'je_job_single_before_attachments', $model ) ?>
			<div class="row">
				<div class="col-md-12">
					<?php
					$files = array_unique( array_filter( explode( ',', $model->portfolios ) ) );
					if ( ! empty( $files ) ): ?>
						<div class="row-fluid full">
							<div class="page-header">
								<label><?php _e( 'Sample Files', je()->domain ) ?></label>
							</div>
							<?php
							ig_uploader()->show_media( $model, 'portfolios' );
							?>
							<div class="clearfix"></div>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<?php if ( $model->is_current_owner() ): ?>
						<br/>
					<?php $post = get_post( $model->id );
					$var        = $post->post_status == 'publish' ? $post->post_name : $post->ID;
					?>
						<a class="btn btn-primary"
						   href="<?php echo esc_url( add_query_arg( array( 'job' => $var ), apply_filters( 'job_edit_button_link', get_permalink( je()->pages->page( JE_Page_Factory::JOB_EDIT ) ) ) ) ) ?>">
							<?php _e( 'Edit', je()->domain ) ?>
						</a>
						<form class="frm-delete" method="post" style="display: inline-block">
							<input name="job_id" type="hidden" value="<?php echo $model->id ?>">
							<?php wp_nonce_field( 'delete_job_' . $model->id ) ?>
							<button name="delete_job" class="btn btn-danger"
							        type="submit"><?php _e( 'Trash', je()->domain ) ?></button>
						</form>
						<script type="text/javascript">
							jQuery(document).ready(function ($) {
								$('.frm-delete').submit(function () {
									if (confirm('<?php echo esc_js( __( 'Are you sure?', je()->domain ) ) ?>')) {

									} else {
										return false;
									}
								})
							})
						</script>
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>