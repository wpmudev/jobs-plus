<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_Uploader extends JobsExperts_Framework_Render {
	public function  __construct( $data ) {
		parent::__construct( $data );
	}

	public function _to_html() {
		$model     = $this->model;
		$attribute = $this->attribute;
		$text      = $this->text;
		$form      = $this->form;

		echo $form->hiddenField( $model, $attribute, array( 'class' => 'cl-' . $attribute ) );
		?>

		<div style="margin-top: 0" class="page-header">
			<label><?php echo $text ?></label>
			<button type="button" class="btn btn-success btn-sm pull-right add-file">
				<?php _e( 'Add', JBP_TEXT_DOMAIN ) ?> <i class="glyphicon glyphicon-plus"></i>
			</button>
		</div>
		<table class="table table-bordered job-files">
			<thead>
			<th><?php _e( 'Image', JBP_TEXT_DOMAIN ) ?></th>
			<th><?php _e( 'Name', JBP_TEXT_DOMAIN ) ?></th>
			<th></th>
			</thead>
			<tbody>

			<?php
			$portfolios = explode( ',', $model->$attribute );
			$portfolios = array_filter( $portfolios );

			if ( empty( $portfolios ) ) {
				echo '<tr><td class="empty-row" colspan="3">' . __( 'No file attached!', JBP_TEXT_DOMAIN ) . '</td><tr/>';
			} else {
				foreach ( $portfolios as $porfolio ) {
					get_post_mime_type( $porfolio );
					$pdata = wp_get_attachment_image_src( $porfolio, 'medium' );
					$type  = explode( '/', get_post_mime_type( $porfolio ) );
					$type  = array_filter( $type );
					$image = '';
					if ( empty( $type ) ) {
						$image = 'N/A';
					} elseif ( $type[0] == 'image' ) {
						$image = '<img src="' . $pdata[0] . '" style="width: 150px; height: auto; max-height: 150px;">';
					} else {
						$image = '<img src="' . wp_mime_type_icon( get_post_mime_type( $porfolio ) ) . '" style="max-width: 150px; height: auto; max-height: 150px;float:left;background:none">';

					}
					$link = get_post_meta( $porfolio, 'portfolio_link', true );
					$desc = get_post_meta( $porfolio, 'portfolio_des', true );
					?>
					<tr data-id="<?php echo $porfolio ?>" data-link="<?php echo esc_attr( $link ) ?>" data-desc="<?php echo esc_attr( $desc ) ?>">
						<td>
							<?php echo $image ?>
						</td>
						<td>
							<?php echo ! empty( $type ) ? jbp_shorten_text( pathinfo( get_attached_file( $porfolio ), PATHINFO_BASENAME ), 50 ) : $link; ?>
						</td>
						<td style="max-width:50px">
							<button type="button" class="btn btn-info btn-xs edit-file" style="margin-right:5px">
								<i class="glyphicon glyphicon-pencil"></i></button>
							<button type="button" class="btn btn-danger btn-xs rm-file">
								<i class="glyphicon glyphicon-trash"></i>
							</button>
						</td>
					</tr>
				<?php
				}
			}
			?>
			</tbody>
		</table>
		<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var upload_data = [];
			var edit_upload = [];
			var main_input = $('.cl-<?php echo $attribute?>');
			$('.add-file').popover({
				content  : '<?php echo $this->get_file_upload_form() ?>',
				html     : true,
				trigger  : 'click',
				container: false,
				placement: 'auto'
			}).on('shown.bs.popover', function () {
				var that = $(this);
				//check the input file size
				var next = that.next();
				if (next.hasClass('popover')) {
					var form = next.find('form').first();
					//bind data
					$.each(upload_data, function (i, v) {
						form.find(':input[name="' + v.name + '"]').val(v.value);
					})

					form.find('input[type="file"]').on('change', function (e) {
						var file = e.target.files[0];
						var max_file_upload = <?php echo get_max_file_upload() * 1000000 ?>;
						//max file size
						if (file.size > max_file_upload) {
							alert('<?php _e('Your file is too large!',JBP_TEXT_DOMAIN) ?>');
							$(this).val("");
						}
						//check type
						var type = file.type;
						if (type != undefined && type.length > 0) {
							var allowed = <?php echo json_encode(array_values(get_allowed_mime_types())) ?>;
							if ($.inArray(type, allowed) == -1) {
								alert('<?php _e('Your file type is not supported!',JBP_TEXT_DOMAIN) ?>');
								$(this).val("");
							}
						}
					});
					form.find(':input').on('change', function () {
						upload_data = form.serializeArray();
					})
					form.find('.cancel_file').on('click', function () {
						that.popover('hide');
					})
					form.on('submit', function () {
						if (form.validationEngine('validate')) {
							var args = {
								data       : form.serialize(),
								processData: false,
								iframe     : true,
								method     : 'POST',
								url        : '<?php echo get_permalink(get_the_ID()) ?>'
							}
							var file = $(":file", this);
							if (file.val()) {
								args.files = file;
							}
							args.beforeSend = function () {
								form.find('button').attr('disabled', 'disabled');
							};
							args.success = function (data) {
								form.find('button').removeAttr('disabled');
								//because data return as html, we need to reparse
								data = $(data);
								var result = $.parseJSON(data.text());
								if (result.status == 'success') {
									//upload success, we will need to create new row
									$('.job-files tbody').find('.empty-row').remove();
									//build new element
									var tr = $('<tr/>').attr('data-id', result.id);
									//check does the file type is image
									var type = result.type.split('/');
									if (type[0] == 'image') {
										tr.append($('<td/>').html($('<img/>').attr('src', result.image_url).css({
											width       : 150,
											height      : 'auto',
											'max-height': 150
										})));
									} else {
										tr.append($('<td/>').html('N/A'));
									}
									if (result.name.length > 0) {
										tr.append($('<td>/').html(result.name));
									} else {
										tr.append($('<td>/').html(form.find(':input[name="portfolio_link"]').val()));
									}
									//action
									var actions = $('<td/>').attr('style', 'max-width:65px');
									actions.append($('<button/>').addClass('btn btn-info btn-xs edit-file').html('<i class="glyphicon glyphicon-pencil"></i>').attr({'style': 'margin-right:5px', 'type': 'button'}));
									actions.append($('<button/>').addClass('btn btn-danger btn-xs rm-file').html('<i class="glyphicon glyphicon-trash"></i>').attr('type', 'button'));
									tr.append(actions);
									//add to element
									$('.job-files tbody').append(tr);
									that.popover('hide');
									upload_data = [];
									//add this to edit data
									edit_upload[result.id] = [
										{
											name : 'portfolio_link',
											value: form.find(':input[name="portfolio_link"]').val()
										},
										{
											name : 'portfolio_des',
											value: form.find(':input[name="portfolio_des"]').val()
										}
									]
									main_input.val(main_input.val() + ',' + result.id);
									map_popover();
								} else {
									alert(result.msg);
								}
							}

							$.ajax(args)
						}
						return false;
					})
				}
			});
			//delete attachment
			$('body').on('click', '.rm-file', function () {
				var parent = $(this).closest('tr');
				var data = main_input.val().split(',');
				new_data = jQuery.grep(data, function (value) {
					return value != parent.data('id');
				});
				parent.remove();
				main_input.val(new_data.join(','));
			})
			map_popover();
			function map_popover() {
				$('.edit-file').popover({
					content  : '<?php echo $this->get_file_upload_form(true) ?>',
					html     : true,
					trigger  : 'click',
					container: false,
					placement: 'auto'
				}).on('shown.bs.popover', function () {
					var that = $(this);
					var next = that.next();
					if (next.hasClass('popover')) {
						var form = next.find('form').first();
						var parent = that.closest('tr');
						//bind element
						var form_data = edit_upload[parent.data('id')];
						if (form_data == undefined) {
							obj = {
								portfolio_link: parent.data('link'),
								portfolio_des : parent.data('desc')
							}
						} else {
							obj = {};
							$.each(form_data, function (i, v) {
								if (v.name == 'portfolio_link') {
									obj.portfolio_link = v.value;
								}

								if (v.name == 'portfolio_des') {
									obj.portfolio_des = v.value;
								}
							})
						}
						form.find('input[type="text"]').val(obj.portfolio_link);
						form.find('textarea').val(obj.portfolio_des);
						form.find('input[name="id"]').val(parent.data('id'));
						form.find(':input').on('change', function () {
							edit_upload[parent.data('id')] = form.serializeArray();
						})
						form.find('.cancel_file').on('click', function () {
							that.popover('hide');
						});
						form.on('submit', function () {
							if (form.validationEngine('validate')) {
								var args = {
									data       : form.serialize(),
									processData: false,
									iframe     : true,
									method     : 'POST',
									url        : '<?php echo get_permalink(get_the_ID()) ?>'
								}
								var file = $(":file", this);
								if (file.val()) {
									args.files = file;
								}
								args.beforeSend = function () {
									form.find('button').attr('disabled', 'disabled');
								};
								args.success = function (data) {
									form.find('button').removeAttr('disabled');
									//because data return as html, we need to reparse
									data = $(data);
									var result = $.parseJSON(data.text());
									if (result.status == 'success') {
										//upload success, we will need to create new row
										$('.job-files tbody').find('.empty-row').remove();
										var tr = parent;
										//check does the file type is image
										var type = result.type.split('/');
										if (type[0] == 'image') {
											tr.find('td:eq(0)').html($('<img/>').attr('src', result.image_url).css({
												width       : 150,
												height      : 'auto',
												'max-height': 150
											}));

										} else {
											tr.find('td:eq(0)').html('N/A');
										}
										if (result.name.length > 0) {
											tr.find('td:eq(1)').html(result.name);
										} else {
											tr.find('td:eq(1)').html(form.find(':input[name="portfolio_link"]').val());
										}
										//replace the data
										var old_id = parent.data('id');
										var new_data = main_input.val().replace(old_id, result.id);
										parent.data('id', result.id);
										main_input.val(new_data);
										//update the
										edit_upload[result.id] = [
											{
												name : 'portfolio_link',
												value: form.find(':input[name="portfolio_link"]').val()
											},
											{
												name : 'portfolio_des',
												value: form.find(':input[name="portfolio_des"]').val()
											}
										]
										that.popover('hide');
									} else {
										alert(result.msg);
									}
								}

								$.ajax(args)
							}
							return false;
						})
					}
				});
			}
		})
		</script>
	<?php
	}

	function get_file_upload_form( $is_edit = false ) {
		ob_start();
		?>
		<form method="post" class="">
			<input type="hidden" value="" name="id">

			<div class="row">
				<div class="col-md-12">
					<label style="display: block"><?php _e( 'Select image or file', JBP_TEXT_DOMAIN ) ?></label>
					<input type="file" name="p_images" style="max-width: 300px" class="p_images validate[groupRequired[file]]" id="p_images">
					<?php if ( $is_edit == true ): ?>
						<small><?php _e( 'Leave it blank for not modify the attachment', JBP_TEXT_DOMAIN ) ?></small>
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label style="display: block"><?php _e( 'Add link for more information', JBP_TEXT_DOMAIN ) ?></label>
					<input type="text" value="" style="max-width: 300px" class="validate[groupRequired[file],custom[url]]" name="portfolio_link">
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label style="display: block"><?php _e( 'Description', JBP_TEXT_DOMAIN ) ?></label>
					<textarea name="portfolio_des" style="max-width: 300px" rows="4"></textarea>
				</div>
				<div class="clearfix"></div>
			</div>
			<br />
			<?php wp_nonce_field( 'jbp_submit_image' ) ?>
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn btn-primary btn-sm">
						<?php _e( 'Done', JBP_TEXT_DOMAIN ); ?>
					</button>
					&nbsp;
					<button type="button" class="btn btn-sm btn-default cancel_file">
						<?php _e( 'Cancel', JBP_TEXT_DOMAIN ) ?>
					</button>
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
		<?php
		return preg_replace( '/^\s+|\n|\r|\s+$/m', '', ob_get_clean() );
	}
}