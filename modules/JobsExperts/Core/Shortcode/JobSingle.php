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
class JobsExperts_Core_Shortcode_JobSingle extends JobsExperts_Shortcode {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_shortcode( 'jbp-job-single-page', 'shortcode' );
		//shortcode style
		$this->_add_action( 'wp_enqueue_scripts', 'scripts', 999 );
	}

	function scripts() {

	}

	public function shortcode( $atts ) {
		wp_enqueue_style('jobs-plus');
		wp_enqueue_style( 'jbp_shortcode' );
		wp_enqueue_script( 'jbp_bootstrap' );

		add_thickbox();
		//get plugin instance
		$plugin = JobsExperts_Plugin::instance();
		$page_module = $plugin->page_module();

		$model = JobsExperts_Core_Models_Job::instance()->get_one( get_the_ID() );
		ob_start();
		?>
		<div class="hn-container">
		<?php echo do_shortcode( '<p style="text-align: center">[jbp-expert-post-btn][jbp-job-post-btn][jbp-expert-browse-btn][jbp-job-browse-btn][jbp-expert-profile-btn][jbp-my-job-btn]</p>' ); ?>
		<div class="jbp_job_single">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="job-meta panel panel-default">
						<div class="panel-body">
							<ul class="job-metadata">
								<li>
									<span><?php esc_html_e( 'Job Budget', JBP_TEXT_DOMAIN ); ?></span>

									<p class="text-warning"><?php $model->render_prices() ?></p>
								</li>
								<li>
									<span><?php esc_html_e( 'This job open for', JBP_TEXT_DOMAIN ) ?></span>

									<p class="text-success"><?php echo $model->get_due_day() ?></p>
								</li>
								<li>
									<span><?php _e( 'Must be complete by', JBP_TEXT_DOMAIN ) ?></span>
									<?php if ( strtotime( $model->dead_line ) ): ?>
										<p class="text-warning"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $model->dead_line ) ); ?></p>
									<?php else: ?>
										<p class="text-warning"><?php _e( 'N/A', JBP_TEXT_DOMAIN ) ?></p>
									<?php endif; ?>
								</li>
								<li>
									<?php if ( strtolower( $model->get_due_day() ) != 'expired' ): ?>
										<a class="btn btn-info" href="<?php echo add_query_arg( array(
											'contact' => get_post()->post_name
										), get_permalink( $page_module->page( $page_module::JOB_CONTACT ) ) ) ?>"><?php _e( 'Contact This Client', JBP_TEXT_DOMAIN ) ?></a>
									<?php endif; ?>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php echo $model->description ?>
				</div>
				<div class="col-md-12">
					<?php
					$skills = $model->find_terms( 'jbp_skills_tag' );
					if ( ! empty( $skills ) ): ?>
						<div class="job_skills">
							<?php
							echo get_the_term_list( get_the_ID(), 'jbp_skills_tag', __( '<h4>You will need to have these skills:', JBP_TEXT_DOMAIN ) . '</h4><ul><li>', '</li><li>', '</li></ul>' )
							?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-md-12 sample-files">
					<?php if ( ! empty( $model->portfolios ) ): ?>
						<h3><strong><?php _e( 'Sample Files', JBP_TEXT_DOMAIN ) ?></strong></h3>
						<table class="table table-bordered job-files">
							<thead>
							<th style="width: 150px"><?php _e( 'Image', JBP_TEXT_DOMAIN ) ?></th>
							<th style="width: 150px"><?php _e( 'Name', JBP_TEXT_DOMAIN ) ?></th>
							<th style="width: 50px"></th>
							</thead>
							<tbody>
							<?php
							$files = explode( ',', $model->portfolios );
							$files = array_filter( $files );
							foreach ( $files as $file ) {
								$type = explode( '/', get_post_mime_type( $file ) );
								$type = array_filter( $type );
								if ( empty( $type ) ) {
									$img = 'N/A';
								} else {
									$img_url = $type[0] == 'image' ? wp_get_attachment_url( $file ) : wp_mime_type_icon( get_post_mime_type( $file ) );
									$img     = '<img style="max-width:150px;height:auto;max-height:1050px" src="' . $img_url . '">';
								}
								$link = get_post_meta( $file, 'portfolio_link', true );
								$desc = get_post_meta( $file, 'portfolio_des', true );
								?>
								<tr>
									<td style="text-align: center"><?php echo $img ?></td>
									<td><?php echo ! empty( $type ) ? jbp_shorten_text( pathinfo( get_attached_file( $file ), PATHINFO_BASENAME ), 50 ) : $link ?></td>
									<td>
										<button data-toggle="modal" data-backdrop="static" data-target="#modal_<?php echo $file ?>" class="btn btn-info btn-sm" type="button"><?php _e( 'View', JBP_TEXT_DOMAIN ) ?></button>
									</td>
									<div class="modal fade" id="modal_<?php echo $file ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<h4 class="modal-title" id="myModalLabel"><?php _e( 'Sample Information', JBP_TEXT_DOMAIN ) ?></h4>
												</div>
												<div class="modal-body">
													<?php if ( ! empty( $type ) && $type[0] == 'image' ): ?>
														<img src="<?php echo wp_get_attachment_url( $file ) ?>">
													<?php endif; ?>
													<?php echo $desc; ?>
												</div>
												<div class="modal-footer">
													<?php if ( ! empty( $type ) ): ?>
														<a download class="btn btn-primary" href="<?php echo wp_get_attachment_url( $file ) ?>"><?php _e( 'Download', JBP_TEXT_DOMAIN ) ?></a>
													<?php endif; ?>
													<?php if ( ! empty( $link ) ): ?>
														<a class="btn btn-info" href="<?php echo $link ?>"><?php _e( 'Visit Sample\'s Link', JBP_TEXT_DOMAIN ) ?></a>
													<?php endif; ?>
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php if ( $model->is_current_owner() ): ?>
						<br />
						<?php $post = get_post( get_the_ID() );
						$var = $post->post_status == 'publish' ? $post->post_name : $post->ID;
						?>
						<a class="btn btn-primary" href="<?php echo add_query_arg( array( 'job' => $var ), get_permalink( $page_module->page( $page_module::JOB_EDIT ) ) ) ?>">
							<?php _e( 'Edit', JBP_TEXT_DOMAIN ) ?>
						</a>
						<form class="frm-delete" method="post" style="display: inline-block">
							<input name="job_id" type="hidden" value="<?php echo $model->id ?>">
							<?php wp_nonce_field( 'delete_job_' . $model->id ) ?>
							<button name="delete_job" class="btn btn-danger" type="submit"><?php _e( 'Trash', JBP_TEXT_DOMAIN ) ?></button>
						</form>
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
					<?php endif; ?>
				</div>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}
}

new JobsExperts_Core_Shortcode_JobSingle();