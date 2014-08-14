<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_ExpertForm extends JobsExperts_Framework_Render {

	public function __construct( $data = array() ) {
		parent::__construct( $data );
	}

	public function _to_html() {
		$model  = $this->model;
		$plugin = JobsExperts_Plugin::instance();

		$form = JobsExperts_Framework_ActiveForm::generateForm( $model );
		$form->openForm( '', 'POST', array( 'id' => 'main_form' ) );
		$form->hiddenField( $model, 'id' );
		$edit_icon = '<i style="font-size: 18px;position: relative;top:3px" class="dashicons dashicons-edit"></i>'
		?>
		<div class="row">
		<div class="col-md-4 col-xs-12 col-sm-12" style="margin-left: 0">
		<div class="panel panel-default">
			<div class="panel-body" style="padding: 0">
				<div class="jbp_pro_avatar">
					<?php echo get_avatar( $model->user_id, 240 ) ?>
					<a href="http://gravatar.com/emails/"><?php _e( 'Change gravatar', JBP_TEXT_DOMAIN ) ?></a>

					<div class="jbp_pro_contact">
						<a class="btn btn-small btn-primary" href=""><?php _e( 'Contact Me', JBP_TEXT_DOMAIN ) ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="jbo_pro_social">
				<div class="panel-heading"><h4><?php _e( 'Social Profile', JBP_TEXT_DOMAIN ) ?></h4></div>
				<div class="panel-body">
					<div class="social_list">
						<ul>
							<?php
							$socials = ! empty( $model->social ) ? json_decode( stripslashes( $model->social ), true ) : array();
							$model->social = esc_attr( stripslashes( $model->social ) );
							if ( ! is_array( $socials ) ) {
								$socials = array();
							}
							foreach ( $plugin->settings()->social_list as $key => $val ): ?>
								<?php
								$found = '';
								foreach ( $socials as $soc ) {
									if ( $key == $soc['id'] ) {
										$found = $soc;
										break;
									}
								}
								?>
								<li>
									<a data-value="<?php echo is_array( $found ) ? $found['url'] : null ?>" data-id="<?php echo $key ?>" class="add-social-<?php echo $key ?> popup-edit" href="#" title="<?php echo $val ?>">
										<img style="<?php echo is_array( $found ) ? 'opacity:1' : null ?>" src="<?php echo $plugin->_module_url ?>assets/social_icon/<?php echo $key ?>.png">
									</a>
									<script type="text/javascript">
										jQuery(document).ready(function ($) {
											///////// SOCIAL FORM
											$('.add-social-<?php echo $key ?>').popover({
												content  : '<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $this->get_social_template()); ?>',
												html     : true,
												trigger  : 'click',
												container: false,
												placement: function (context, source) {
													if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
														return 'top';
													}
													return "right";
												}
											}).click(function (e) {
												e.preventDefault();
											}).on('shown.bs.popover', function () {
												//get the form
												var next = $(this).next();
												var _this = $(this);
												if (next.hasClass('popover')) {
													var form = next.find('form').first();
													var main_input = $('input[name="JobsExperts_Core_Models_Pro[social]"]');
													form.validationEngine('attach', {
														binded: false,
														scroll: false
													});

													var data = main_input.val();
													var is_edit = false;
													if (data != undefined && data.length > 0) {
														data = JSON.parse(data);
														$.each(data, function (i, v) {
															if (v.id == _this.data('id')) {
																form.find('input[type="text"]').val(v.url);
																is_edit = true;
															}
														})
													}
													if (is_edit == true) {
														form.find('.social_remove').removeClass('hide').on('click', function () {
															_this.find('img').css('opacity', 0.5);
															//remove the url
															$.each(data, function (i, v) {
																if (v.id == _this.data('id')) {
																	data.splice(i, 1);
																	return;
																}
															})
															main_input.val(JSON.stringify(data));
															_this.popover('hide');
														})
													}

													//rebind data
													form.on('submit', function (e) {
														e.preventDefault();
														if (form.validationEngine('validate')) {
															var url = form.find('input[type="text"]').first().val();

															var data = main_input.val();
															if (data != undefined && data.length > 0) {
																data = JSON.parse(data);
															} else {
																data = [];
															}

															//check for does this value having, if yes, we update
															var check = $.map(data, function (val) {
																if (val.id == _this.data('id')) {
																	val.url = url;
																	return true;
																}
															});
															if (check.length == 0) {
																data.push({
																	id : _this.data('id'),
																	url: url
																});
															}
															console.log(JSON.stringify(data));
															main_input.val(JSON.stringify(data));
															_this.find('img').css('opacity', 1);

															_this.popover('hide');
														}
														return false;
													})
													form.find('.social_cancel').on('click', function () {
														_this.popover('hide');
													});
												}
											})
										})
									</script>
								</li>
							<?php endforeach; ?>
						</ul>
						<div style="clear: both"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="jbo_pro_social">
				<div class="panel-heading">
					<h4><?php _e( 'Skills', JBP_TEXT_DOMAIN ) ?></h4>
					<small><?php _e( 'If there any skill bar, please click on the skill to edit', JBP_TEXT_DOMAIN ) ?></small>
				</div>
				<div class="panel-body" style="padding: 0">
					<?php
					$colors = array(
						array(
							'background: #d35400;',
							'background: #e67e22;'
						),
						array(
							'background: #2980b9;',
							'background: #3498db;'
						),
						array(
							'background: #2c3e50;',
							'background: #2c3e50;'
						),
						array(
							'background: #2c3e50;',
							'background: #46465e;'
						),
						array(
							'background: #2c3e50;',
							'background: #5a68a5;'
						),
						array(
							'background: #333333;',
							'background: #525252;'
						),
						array(
							'background: #27ae60;',
							'ackground: #2ecc71;'
						),
						array(
							'background: #124e8c;',
							'background: #4288d0;'
						),
					);
					$color = $colors[array_rand( $colors )];
					?>
					<div class="skill">
						<div class="jbp_skill_meta">
							<?php
							$skills = ! empty( $model->skills ) ? json_decode( stripslashes( $model->skills ), true ) : array();
							$model->skills = esc_attr( stripslashes( $model->skills ) );
							if ( ! is_array( $skills ) ) {
								$skills = array();
							}
							foreach ( $skills as $skill ):
								?>
								<?php $ccolor = $colors[array_rand( $colors )]; ?>
								<div class="jbp_skillbar_container" style="position: relative">
									<i class="dashicons dashicons-trash remove-skill" style="position: absolute;right:-21px;color: red;cursor: pointer"></i>

									<div class="jbp_skillbar edit-skill" data-percent="<?php echo $skill['score'] ?>">

										<div class="jbp_skillbar-title" style="<?php echo $ccolor[1] ?>">
											<span><?php echo $skill['name'] ?></span>
										</div>
										<div class="jbp_skillbar-bar" style="<?php echo $ccolor[0] ?>"></div>
										<div class="jbp_skillbar-percent"><?php echo $skill['score'] ?> %</div>
									</div>
								</div>
								<!-- End Skill Bar -->
							<?php endforeach; ?>

						</div>
					</div>
					<div class="jbp_pro_contact">
						<button type="button" class="add-skill btn btn-small btn-primary"><?php _e( 'Add Skill' ) ?></button>
					</div>
				</div>
			</div>
		</div>
		</div>
		<div class="col-md-7 col-xs-12 col-sm-12" style="padding: 0">
			<div class="jbp_pro_main_form">
				<?php
				$errors = $model->get_errors();
				if ( ! empty( $errors ) ): ?>
					<div class="alert alert-danger" role="alert">
						<?php echo implode( '<br/>', $model->get_errors() ) ?>
					</div>
				<?php endif; ?>
				<div class="jbp_pro_full_name">
					<?php
					$empty_text = $edit_icon . '<p class="can-edit">' . sprintf( 'Your Name (required) is a member since %s', date( "M Y", strtotime( get_the_author_meta( 'user_registered' ) ) ) ) . '</p>';
					$display_text = $edit_icon . '<p class="can-edit">' . sprintf( '{{first_name}} {{last_name}} is a member since %s', date( "M Y", strtotime( get_the_author_meta( 'user_registered' ) ) ) ) . '</p>';
					$mapped = array(
						'first_name' => array(
							'type'  => 'textField',
							'label' => 'First Name',
							'id'    => 'first_name',
							'class' => 'validate[required]'
						),
						'last_name'  => array(
							'type'  => 'textField',
							'label' => 'Last Name',
							'id'    => 'last_name',
							'class' => 'validate[required]'
						)
					);

					$fullname = new JobsExperts_Framework_EditableForm( $model, $display_text, $empty_text, $mapped, 'hn-container jbp_text_popup' );
					echo $fullname->render();
					?>
				</div>
				<div class="row">
					<div class="col-md-4 col-xs-12 col-sm-4" style="margin-left: 0;width: 32%">
						<label><?php _e( 'Company:', JBP_TEXT_DOMAIN ) ?></label>
					</div>
					<div class="col-md-6 col-xs-12 col-sm-6">
						<?php $company = new JobsExperts_Framework_EditableForm( $model, $edit_icon . '<p class="can-edit"><a href="javascript::void(0)">{{company}}</a></p>', $edit_icon . '<p class="can-edit">' . __( 'Your Company', JBP_TEXT_DOMAIN ) . '</p>', array(
							'company'     => array(
								'type'  => 'textField',
								'label' => __( 'Company:', JBP_TEXT_DOMAIN ),
								'id'    => 'company'
							),
							'company_url' => array(
								'type'  => 'textField',
								'label' => __( 'Company Url:', JBP_TEXT_DOMAIN ),
								'id'    => 'company_url'
							)
						), 'hn-container jbp_text_popup' );
						echo $company->render();
						?>
					</div>
					<div style="clear: both"></div>
				</div>
				<div class="row">
					<div class="col-md-4 col-xs-12 col-sm-4" style="margin-left: 0;width: 32%">
						<label><?php _e( 'Location*:', JBP_TEXT_DOMAIN ) ?></label>
					</div>
					<div class="col-md-6 col-xs-12 col-sm-6">
						<?php $company = new JobsExperts_Framework_EditableForm( $model, $edit_icon . '<p class="can-edit">{{location}}</p>', $edit_icon . '<p class="can-edit">' . __( 'Your Location', JBP_TEXT_DOMAIN ) . '</p>', array(
							'location' => array(
								'type'  => 'countryDropdown',
								'label' => __( 'Location:', JBP_TEXT_DOMAIN ),
								'id'    => 'location',
								'class' => 'validate[required]'
							)
						), 'hn-container jbp_text_popup' );
						echo $company->render();
						?>
					</div>
					<div style="clear: both"></div>
				</div>
				<div class="row">
					<div class="col-md-4 col-xs-12 col-sm-4" style="margin-left: 0;width: 32%">
						<label><?php _e( 'Contact email*:', JBP_TEXT_DOMAIN ) ?></label>
					</div>
					<div class="col-md-6 col-xs-12 col-sm-6">
						<?php $company = new JobsExperts_Framework_EditableForm( $model, $edit_icon . '<p class="can-edit">{{contact_email}}</p>', $edit_icon . '<p class="can-edit">' . __( 'Your Contact Email', JBP_TEXT_DOMAIN ) . '</p>', array(
							'contact_email' => array(
								'type'  => 'textField',
								'label' => __( 'Email:', JBP_TEXT_DOMAIN ),
								'id'    => 'contact_email',
								'class' => 'validate[required,custom[email]]'
							)
						), 'hn-container jbp_text_popup' );
						echo $company->render();
						?>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-12">
						<p class="text-info"><?php _e( 'Your email will not be published over the site', JBP_TEXT_DOMAIN ) ?></p>
					</div>
					<div style="clear: both"></div>
				</div>
				<div class="row full">
					<div class="col-md-12" style="margin-left: 0">
						<label><?php _e( 'Short Biography', JBP_TEXT_DOMAIN ) ?></label>
					</div>
					<div class="col-md-12">
						<?php
						$sdesc = new JobsExperts_Framework_EditableForm( $model, $edit_icon . '<p class="can-edit">{{short_description}}</p>', $edit_icon . '<p class="can-edit">' . __( 'Short version of yourself, maximum 100 characters', JBP_TEXT_DOMAIN ) . '</p>', array(
							'short_description' => array(
								'type'  => 'textArea',
								'label' => __( 'Short Description', JBP_TEXT_DOMAIN ),
								'id'    => 'short_description',
								'class' => 'validate[required,maxSize[100]]'
							)
						), 'hn-container jbp_text_popup' );
						echo $sdesc->render();
						?>
					</div>
					<div style="clear: both"></div>
				</div>
				<div class="row full">
					<div class="col-md-12" style="margin-left: 0">
						<label><?php _e( 'Full Biography*', JBP_TEXT_DOMAIN ) ?></label>
					</div>
					<div class="col-md-12">
						<?php
						$desc = new JobsExperts_Framework_EditableForm( $model, $edit_icon . '<p class="can-edit">{{biography}}</p>', $edit_icon . '<p class="can-edit">' . __( 'Tell us about yourself (required, at least 200 characters)', JBP_TEXT_DOMAIN ) . '</p>', array(
							'biography' => array(
								'type'  => 'textArea',
								'label' => __( 'Biography', JBP_TEXT_DOMAIN ),
								'id'    => 'biography',
								'class' => 'validate[required,minSize[200]]'
							)
						), 'hn-container jbp_text_popup' );
						echo $desc->render();
						?>
					</div>
					<div style="clear: both"></div>
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
				<div class="row" style="margin-top: 40px">
					<div class="col-md-12" style="margin-left: 0">
						<?php echo wp_nonce_field( 'jbp_add_pro' ) ?>
						<?php if ( $plugin->settings()->expert_new_expert_status == 'publish' ): ?>
							<button class="jbp_publish btn btn-small btn-primary" name="status" value="publish" type="submit"><?php _e( 'Publish', JBP_TEXT_DOMAIN ) ?></button>
						<?php else: ?>
							<button class="jbp_publish btn btn-small btn-primary" name="status" value="pending"><?php _e( 'Submit for review', JBP_TEXT_DOMAIN ) ?></button>
						<?php endif; ?>
						<?php if ( $plugin->settings()->expert_allow_draft == 1 ): ?>
							<button class="jbp_save_draft btn btn-small btn-info" name="status" value="draft" type="submit"><?php _e( 'Save Draft', JBP_TEXT_DOMAIN ) ?></button>
						<?php endif; ?>
						<button onclick="location.href='<?php echo get_post_type_archive_link( 'jbp_pro' ) ?>'" type="button" class="btn btn-default btn-small pull-right"><?php _e( 'Cancel', JBP_TEXT_DOMAIN ) ?></button>
					</div>
					<div style="clear: both"></div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php
		$form->hiddenField( $model, 'skills' );
		$form->hiddenField( $model, 'social' );
		echo $form->endForm() ?>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				////////SKILL
				function handler_skill_form(form, _this) {
					var name = form.find('input[name="skill_name"]').first().val();
					var score = form.find('input[name="skill_score"]').first().val();
					var main_input = $('input[name="JobsExperts_Core_Models_Pro[skills]"]');
					if (main_input.val() != undefined && main_input.val().length > 0) {
						skill_data = JSON.parse(main_input.val());
					} else {
						skill_data = [];
					}

					var data = {
						'name' : name,
						'score': score
					}
					if (_this.hasClass('update-skill')) {
						var target = $('.jbp_skillbar:eq(' + _this.data('target') + ')');
						target.data('percent', score);
						target.find('span').first().html(name);
						target.find('.jbp_skillbar-percent').text(score + '%');
						map_skillbar_color();
						//find the data
						skill_data[_this.data('target')] = data;
						$('input[name="JobsExperts_Core_Models_Pro[skills]"]').val(JSON.stringify(skill_data));
					} else {
						//build thml
						var colors = <?php echo json_encode($colors) ?>;
						var color = colors[Math.floor(Math.random() * colors.length)];
						var html = '<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $this->get_skillbar_template()) ?>';
						html = html.replace('{{percent}}', data.score)
							.replace('{{name}}', data.name)
							.replace('{{name}}', data.name)
							.replace('{{c1}}', color[0])
							.replace('{{c2}}', color[1])
							.replace('{{percent}}', data.score)
							.replace('{{percent}}', data.score);
						$('.jbp_skill_meta').append(html);
						map_skillbar_color();
						skill_data.push(data);
						$('input[name="JobsExperts_Core_Models_Pro[skills]"]').val(JSON.stringify(skill_data));
					}
					//close
					_this.popover('hide');
				}

				$('.add-skill').popover({
					content  : '<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $this->get_skill_template()); ?>',
					html     : true,
					trigger  : 'click',
					container: false,
					placement: function (context, source) {
						if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
							return 'top';
						}
						return "right";
					}
				}).on('shown.bs.popover',function () {
					var _this = $(this);
					var next = _this.next();
					if (next.hasClass('popover')) {
						var form = next.find('form').first();
						console.log(form.size());
						if (form.size() > 0) {
							form.find('.skill_score').on('change mousemove', function () {
								form.find('span').first().text($(this).val());
							});
							form.find('.cancel_skill').on('click', function () {
								_this.popover('hide');
							});
							form.on('submit', function (e) {
								e.preventDefault();
								if (form.validationEngine('validate')) {
									handler_skill_form(form, _this);
								}
								return false;
							})
						}
					}
				}).on('hidden.bs.popover', function () {
					//$(this).popover('destroy');

				});
				///skill color
				map_skillbar_color();
				function map_skillbar_color() {
					//map skill bar color
					$('.jbp_skill_meta').find('.jbp_skillbar').each(function () {
						$(this).find('.jbp_skillbar-bar').css('width', $(this).data('percent') + '%');
					});
				};

				$('body').on('click', '.remove-skill', function (e) {
					var parent = $(this).closest('div');
					var index = parent.index();
					var input = $('input[name="JobsExperts_Core_Models_Pro[skills]"]');
					var skill_data = JSON.parse(input.val());
					skill_data.splice(index, 1);
					input.val(JSON.stringify(skill_data));
					$(this).closest('div').remove();
				});
				$('body').on('mouseover', '.jbp_skillbar', function () {
					var _this = $(this);
					//check if this already init or not
					if (_this.hasClass('bind-popover'))
						return;
					//prepare content
					var content = '<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $this->get_skill_template()) ?>';
					//we need to bind value
					var score = _this.data('percent');
					var name = _this.find('span').first().html();
					content = $(content);
					content.find('input[name="skill_name"]').first().val(name);
					content.find('input[name="skill_score"]').first().val(score);
					//add some needed param
					_this.addClass('update-skill bind-popover').attr('data-target', _this.parent().index());
					//init
					_this.popover({
						content  : content,
						html     : true,
						trigger  : 'click',
						placement: function (context, source) {
							if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
								return 'top';
							}
							return "right";
						}
					}).on('shown.bs.popover', function () {
						var _this = $(this);
						_this.addClass('update-skill');
						var next = _this.next();
						if (next.hasClass('popover')) {
							var form = next.find('form').first();
							form.find('.skill_score').on('change mousemove', function () {
								form.find('span').first().text($(this).val());
							});
							form.find('.cancel_skill').on('click', function () {
								_this.popover('hide');
							});
							form.on('submit', function (e) {
								e.preventDefault();
								if (form.validationEngine('validate')) {
									handler_skill_form(form, _this);
								}
								return false;
							})

						}
					})
					//init all
					_this.trigger('mouseover');
				})
			})
		</script>
	<?php
	}

	function get_social_template() {
		ob_start();
		?>
		<form class="social_form" method="post">
			<div class="form-group">
				<input type="text" value="http://" class="social_url validate[custom[url]]">
			</div>
			<button class="btn btn-xs btn-primary" type="submit">
				<span class="dashicons dashicons-yes"></span></button>
			&nbsp;
			<button class="btn btn-xs btn-danger social_remove hide" type="button">
				<i class="dashicons dashicons-trash"></i></button>
			&nbsp;
			<button class="btn btn-xs btn-default social_cancel" type="button">
				<span class="dashicons dashicons-no-alt"></span></button>
		</form>
		<?php
		return ob_get_clean();
	}

	function get_skill_template() {
		ob_start();
		?>
		<form method="post" class="skill-add-form" style="width: 200px">
			<div class="form-group" style="margin-bottom: 4px">
				<label style="font-size: 12px;font-weight: normal;display: block"><?php _e( 'Skill', JBP_PLUGIN_URL ) ?></label>
				<?php echo JobsExperts_Framework_Form::textField( 'skill_name', '', array( 'class' => 'validate[required]', 'style' => 'width:85%' ) ) ?>
			</div>
			<div class="form-group">
				<label style="font-size: 12px;font-weight: normal;display: block"><?php _e( 'Score', JBP_PLUGIN_URL ) ?></label>
				<input name="skill_score" style="padding: 0;margin:0;position: relative;top:3px;width: 88%;display: inline" type="range" min="0" max="100" class="skill_score" />
				<span style="font-size: 12px" class="pull-right">50</span>
			</div>
			<div class="form-group">
				<button class="btn btn-xs btn-primary" type="submit">
					<i class="dashicons dashicons-yes"></i></button>
				&nbsp;
				<button class="btn btn-xs btn-default cancel_skill" type="button">
					<i class="dashicons dashicons-no-alt"></i></button>
			</div>
		</form>
		<?php
		return ob_get_clean();
	}

	function get_skillbar_template() {
		ob_start();
		?>
		<div class="jbp_skillbar_container" style="position: relative">
			<i class="dashicons dashicons-trash remove-skill" style="position: absolute;right:-21px;color: red"></i>

			<div class="jbp_skillbar edit-skill"  data-percent="{{percent}}">
				<div class="jbp_skillbar-title" style="{{c2}}"><span>{{name}}</span>
				</div>
				<div class="jbp_skillbar-bar" style="{{c1}}"></div>
				<div class="jbp_skillbar-percent">{{percent}}%</div>

			</div>
		</div>

		<?php
		return ob_get_clean();
	}
}