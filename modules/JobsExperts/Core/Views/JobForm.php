<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_JobForm extends JobsExperts_Framework_Render {

	public function __construct( $data = array() ) {
		parent::__construct( $data );
	}

	public function _to_html() {
		$model  = $this->model;
		$plugin = JobsExperts_Plugin::instance();

		?>
		<div class="row">
			<div class="col-md-12">
				<?php
				$form = JobsExperts_Framework_ActiveForm::generateForm( $model );
				$form->openForm( '', 'POST', array( 'class' => 'form-horizontal', 'id' => 'jbp_job_form' ) );
				$form->hiddenField( $model, 'id' );
				if ( isset( $_GET['job_title'] ) ) {
					$model->job_title = $_GET['job_title'];
				}
				?>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'Choose a category', JBP_TEXT_DOMAIN ) ?></label>

					<div class="col-md-7">
						<?php
						if(!$model->is_new_record()){
							$model->categories = wp_list_pluck($model->categories,'term_id');
						}
						?>
						<?php $form->dropDownList( $model, 'categories',
							array_combine( wp_list_pluck( get_terms( 'jbp_category', 'hide_empty=0' ), 'term_id' ), wp_list_pluck( get_terms( 'jbp_category', 'hide_empty=0' ), 'name' ) ),
							array( 'class' => 'validate[required]' ) ) ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'Give your job a title', JBP_TEXT_DOMAIN ) ?></label>

					<div class="col-md-7">
						<?php echo $form->textField( $model, 'job_title', array( 'class' => 'validate[required]' ) ) ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'Describe the work to be done', JBP_TEXT_DOMAIN ) ?></label>

					<div class="col-md-7">
						<?php echo $form->textArea( $model, 'description', array( 'rows' => 4 ) ) ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'What skills are needed?', JBP_TEXT_DOMAIN ) ?></label>
					<?php
					$skills = $model->find_terms( 'jbp_skills_tag' );
					if ( is_array( $skills ) && count( $skills ) ) {
						$skills = implode( ',', wp_list_pluck( $skills, 'name' ) );
					} else {
						$skills = '';
					}
					$model->skills = $skills;
					?>
					<div class="col-md-6">
						<?php echo $form->hiddenField( $model, 'skills', array( 'id' => 'jbp_skill_tag', 'style' => 'width:100%' ) ) ?>
						<script type="text/javascript">
							jQuery(document).ready(function ($) {
								$('#jbp_skill_tag').select2({
									tags           : <?php echo json_encode(get_terms('jbp_skills_tag', array('fields'=>'names', 'get' => 'all' ) ) ); ?>,
									placeholder    : "<?php esc_attr_e('Add a tag, use commas to separate'); ?>",
									tokenSeparators: [","]
								});
							});
						</script>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php if ( ! $plugin->settings()->job_budget_range ): ?>
					<div class="form-group">
						<label class="col-md-3"><?php _e( 'Budget', JBP_TEXT_DOMAIN ) ?></label>

						<div class="col-md-7">
							<div class="input-group pull-left" style="width: 40%">
								<span class="input-group-addon">$</span>
								<?php echo $form->textField( $model, 'budget', array( 'class' => 'validate[required,custom[number],min[1]]' ) ) ?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				<?php else: ?>
					<div class="form-group">
						<label class="col-md-3"><?php _e( 'Budget Range', JBP_TEXT_DOMAIN ) ?></label>

						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="input-group pull-left" style="width: 40%">
										<span class="input-group-addon">$</span>
										<?php echo $form->textField( $model, 'min_budget', array( 'class' => 'validate[required,funcCall[checkMax],min[1],custom[number]]' ) ) ?>
									</div>
									<div style="width: 20%;text-align: center" class="pull-left">
										<?php _e( 'to', JBP_TEXT_DOMAIN ) ?>
									</div>
									<div class="input-group pull-right" style="width: 40%">
										<span class="input-group-addon">$</span>
										<?php echo $form->textField( $model, 'max_budget', array( 'class' => 'validate[required,min[1],custom[number]] max_budget' ) ) ?>
									</div>
								</div>

								<div class="clearfix"></div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<script type="text/javascript">
						function checkMax(field, rules, i, options) {
							if (parseFloat(field.val()) > parseFloat(jQuery('.max_budget').first().val())) {
								return '<?php _e('Budget min field can not larger than max field ',JBP_TEXT_DOMAIN) ?>';
							}
						}
					</script>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'Contact Email', JBP_TEXT_DOMAIN ) ?></label>

					<div class="col-md-7">
						<?php $form->textField( $model, 'contact_email', array(
							'class'          => 'validate[required,custom[email]] job-tool-tip',
							'data-toggle'    => "tooltip",
							'title'          => __( 'Contact email address for the job offer', JBP_TEXT_DOMAIN ),
							'data-placement' => 'bottom'
						) ) ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'Completion Date', JBP_TEXT_DOMAIN ) ?></label>

					<div class="col-md-7">
						<?php
						$form->textField( $model, 'dead_line', array(
							'class'          => 'validate[required,funcCall[no_past_date]] datepicker job-tool-tip',
							'data-toggle'    => "tooltip",
							'title'          => __( 'When must this job be completed by? Or NA for not applicable.', JBP_TEXT_DOMAIN ),
							'data-placement' => 'bottom'
						) ) ?>
					</div>
					<div class="clearfix"></div>
					<script type="text/javascript">
						function no_past_date(field, rules, i, options) {
							var value = new Date(field.val());
							var current_date = new Date();
							current_date.setHours(0, 0, 0, 0);

							var diff = value - current_date;
							if (diff < 0) {
								return '<?php _e('*Must be a future date',JBP_TEXT_DOMAIN) ?>';
							}
						}
					</script>
				</div>
				<div class="form-group">
					<label class="col-md-3"><?php _e( 'Job Open for', JBP_TEXT_DOMAIN ) ?></label>

					<div class="col-md-7">
						<?php $days = $plugin->settings()->open_for_days;
						$days = array_filter( explode( ',', $days ) );
						$data = array();
						foreach ( $days as $day ) {
							$data[$day] = $day . ' ' . __( 'Days', JBP_TEXT_DOMAIN );
						}

						$form->dropDownList( $model, 'open_for', $data, array(
							'prompt'         => '--Select--', 'class' => 'validate[required] job-tool-tip',
							'data-toggle'    => "tooltip",
							'title'          => __( 'How long is this job open for from Today?', JBP_TEXT_DOMAIN ),
							'data-placement' => 'bottom',
						) );
						?>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php
				$uploader = new JobsExperts_Core_Views_Uploader( array(
					'model'     => $model,
					'attribute' => 'portfolios',
					'text'      => __( 'Attach specs examples or extra information', JBP_TEXT_DOMAIN ),
					'form'      => $form
				) );
				$uploader->render();
				?>
				<?php if ( $plugin->settings()->job_new_job_status == 'publish' ): ?>
					<button class="btn btn-primary" name="status" value="publish" type="submit"><?php _e( 'Publish', JBP_TEXT_DOMAIN ) ?></button>
				<?php else: ?>
					<button class="btn btn-primary" name="status" value="pending"><?php _e( 'Submit for review', JBP_TEXT_DOMAIN ) ?></button>
				<?php endif; ?>
				<?php if ( $plugin->settings()->job_allow_draft == 1 ): ?>
					<button class="btn btn-info" name="status" value="draft" type="submit"><?php _e( 'Save Draft', JBP_TEXT_DOMAIN ) ?></button>
				<?php endif; ?>
				<?php echo wp_nonce_field( 'jbp_add_job' ) ?>

				<button onclick="location.href='<?php echo get_post_type_archive_link( 'jbp_job' ) ?>'" type="button" class="btn btn-default pull-right"><?php _e( 'Cancel', JBP_TEXT_DOMAIN ) ?></button>
				<div class="clearfix"></div>

				<?php
				echo $form->endForm();
				?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$("#jbp_job_form").validationEngine('attach', {
					binded: false,
					scroll: false
				});
				$('.job-tool-tip').tooltip();
				$('.datepicker').datepicker({
					autoclose     : true,
					format        : "M d, yyyy",
					todayHighlight: true,
					startDate     : '<?php echo date('M d, Y') ?>'
				});
			})
		</script>
	<?php
	}
}