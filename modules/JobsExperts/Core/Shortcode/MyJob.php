<?php

/**
 * Author: WPMUDEV
 */
class JobsExpert_Core_Shortcode_MyJob extends JobsExperts_Shortcode {
	public function  __construct() {
		$this->_add_shortcode( 'jbp-my-job-page', 'shortcode' );
	}

	function shortcode( $atts ) {
		wp_enqueue_style( 'jobs-plus' );
		/*wp_enqueue_style( 'jbp_shortcode' );*/
		$plugin      = JobsExperts_Plugin::instance();
		$page_module = $plugin->page_module();
		$data        = JobsExperts_Core_Models_Job::instance()->get_all( array(
			'post_status'   => array( 'publish', 'draft', 'pending' ),
			'post_per_page' => $plugin->settings()->job_per_page,
			'author'        => get_current_user_id()
		) );
		ob_start();
		?>
		<div class="hn-container">
			<?php if ( isset( $_GET['post_status'] ) ) {
				switch ( $_GET['post_status'] ) {
					case 1:
						?>
						<div class="alert alert-success">
							<?php _e( 'Job post successful!', JBP_TEXT_DOMAIN ) ?>
						</div>
						<?php
						break;
					case 2:
						?>
						<div class="alert alert-success">
							<?php _e( 'Job delete successful!', JBP_TEXT_DOMAIN ) ?>
						</div>
						<?php
						break;
				}
			} ?>
			<table class="table table-hover table-striped table-bordered">
				<thead>
				<th><?php _e( 'Title', JBP_TEXT_DOMAIN ) ?></th>
				<th><?php _e( 'Price', JBP_TEXT_DOMAIN ) ?></th>
				<th><?php _e( 'Status', JBP_TEXT_DOMAIN ) ?></th>
				<th></th>
				</thead>
				<tbody>
				<?php if ( count( $data['data'] ) ): ?>
					<?php foreach ( $data['data'] as $model ): ?>
						<tr>
							<td><a href="<?php echo get_permalink( $model->id ) ?>"><?php echo $model->job_title ?></a>
							</td>
							<td><?php echo $model->render_prices() ?></td>
							<td><?php echo ucfirst( $model->get_status() ) ?></td>
							<td style="width: 120px">
								<a class="btn btn-primary btn-sm" href="<?php echo add_query_arg( array(
									'job' => $model->get_raw_post()->ID
								), get_permalink( $page_module->page( $page_module::JOB_EDIT ) ) ) ?>"><?php _e( 'Edit', JBP_TEXT_DOMAIN ) ?></a>

								<form class="frm-delete" method="post" style="display: inline-block">
									<input name="job_id" type="hidden" value="<?php echo $model->id ?>">
									<?php wp_nonce_field( 'delete_job_' . $model->id ) ?>
									<button name="delete_job" class="btn btn-danger btn-sm" type="submit"><?php _e( 'Delete', JBP_TEXT_DOMAIN ) ?></button>
								</form>

							</td>

						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4"><?php _e( 'You don\'t have any job.', JBP_TEXT_DOMAIN ) ?></td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('.frm-delete').submit(function () {
					if (confirm('<?php _e('Are you sure?',JBP_TEXT_DOMAIN) ?>')) {

					} else {
						return false;
					}
				})
			})
		</script>
		<?php
		return ob_get_clean();
	}
}

new JobsExpert_Core_Shortcode_MyJob();