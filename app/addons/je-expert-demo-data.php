<?php

/**
 * Name: Expert Demo Data
 * Description: Create random expert records, for testing purposes.
 * Author: WPMU DEV
 */
class JE_Expert_Demo_Data {
	public function __construct() {
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'jbp_setting_menu', array( &$this, 'menu' ) );
			add_action( 'je_settings_content_expert_demo_data', array( &$this, 'content' ) );
			add_action( 'wp_ajax_create_demo_expert', array( &$this, 'generate_data' ) );
			add_action( 'wp_ajax_check_create_demo_expert', array( &$this, 'check_status' ) );
		}
	}

	function generate_data() {
		if ( ! wp_verify_nonce( je()->post( '_nonce' ), 'create_demo_expert' ) ) {
			return;
		}
		delete_option( 'experts_demo_status' );
		$data = $_POST['data'];
		parse_str( $data, $data );
		$qty         = $data['dummy_expert_qty'];
		$socials     = isset( $data['expert_socials'] ) ? $data['expert_socials'] : null;
		$skills      = $data['dummy_skills'];
		$have_sample = isset( $data['have_sample'] ) ? true : false;

		//prepare data
		if ( $qty > 0 ) {
			for ( $i = 1; $i <= $qty; $i ++ ) {
				$this->_generate( $socials, $skills, $have_sample );
				//cal percent
				$percent = ( $i / $qty ) * 100;
				update_option( 'experts_demo_status', $percent );
			}
		}
	}

	function _generate( $socials, $skills, $have_file ) {
		$model                    = new JE_Expert_Model();
		$model->first_name        = $this->content_bank( 'fname' );
		$model->last_name         = $this->content_bank( 'lname' );
		$model->name              = $model->first_name . ' ' . $model->last_name;
		$model->biography         = $this->content_bank( 'content' );
		$model->short_description = $this->content_bank( 'scontent' );
		$model->location          = 'US';
		$model->contact_email     = $this->content_bank( 'email' );
		$model->user_id           = get_current_user_id();
		$model->status            = 'publish';
		$model->company           = 'WPMUDEV';
		$model->company_url       = 'http://wpmudev.org';
		$model->views_count       = rand( 0, 100 );
		$model->likes_count       = rand( 0, 100 );
		$model->save();
		//if empty social
		if ( empty( $socials ) ) {
			$st = ig_social_wall()->get_social_list();
			$t  = array_rand( $st, 5 );
			foreach ( $t as $v ) {
				$v         = ig_social_wall()->social( $v );
				$socials[] = $v['key'];
			}
		}
		$s_data = array();
		foreach ( $socials as $s ) {
			$smodel            = new Social_Wall_Model();
			$smodel->name      = $s;
			$smodel->value     = 'http://wpmudev.org';
			$smodel->parent_id = $model->id;

			$social       = ig_social_wall()->social( $s );
			$smodel->type = $social['type'];
			$smodel->save();
			$s_data[] = $smodel->name;
		}
		$model->social = implode( ',', $s_data );
		//skill
		$skills = explode( ',', $skills );
		$skills = array_filter( $skills );
		$t      = array_rand( $skills, 3 );
		$s_data = array();
		foreach ( $t as $v ) {
			//now skill
			$mskill            = new IG_Skill_Model();
			$mskill->value     = rand( 0, 100 );
			$mskill->name      = $skills[ $v ];
			$mskill->parent_id = $model->id;
			$csss              = array(
				'progress-bar progress-bar-warning',
				'progress-bar',
				'progress-bar progress-bar-info',
				'progress-bar progress-bar-success',
				'progress-bar progress-bar-danger',
				'progress-bar progress-bar-warning progress-bar-striped active',
				'progress-bar progress-bar-striped active',
				'progress-bar progress-bar-info progress-bar-striped active',
				'progress-bar progress-bar-success progress-bar-striped active',
				'progress-bar progress-bar-danger progress-bar-striped active',
			);
			$mskill->css       = $csss[ array_rand( $csss ) ];
			$mskill->save();


			$s_data[] = $mskill->name;
		}
		$model->skills = implode( ',', $s_data );
		$model->save();

		if ( $have_file ) {
			//random generate 3 files
			$ids = array();
			for ( $i = 0; $i < 3; $i ++ ) {
				//get the random image
				$upload_dir = wp_upload_dir();
				$path       = $upload_dir['path'] . '/' . uniqid() . '.jpg';
				$image_path = $this->content_bank( 'image' );
				//download the image
				//$this->download_image( $image_url, $path );
				copy( $image_path, $path );
				//now handler the file
				$att_id = $this->handler_upload( $model->id, $path );
				//create media post type
				$media            = new IG_Uploader_Model();
				$media->content   = jbp_filter_text( $this->content_bank( 'scontent' ) );
				$media->file      = $att_id;
				$media->url       = 'http://wpmudev.org';
				$media->attach_to = $model->id;
				$media->save();
				//update_post_meta($media->id, '_file', $att_id);

				$ids[] = $media->id;
			}
			$model->portfolios = implode( ',', $ids );
			$model->save();
		}

		$upload_dir = wp_upload_dir();
		$name       = uniqid() . '.jpg';
		$path       = $upload_dir['path'] . '/' . $name;
		$image_path = $this->content_bank( 'image' );
		//avatar
		//$this->download_image( $image_url, $path );
		copy( $image_path, $path );
		//update avatar
		update_post_meta( $model->id, '_expert_avatar', $upload_dir['url'] . '/' . $name );

		//now add some fake view count
		for ( $i = 0; $i < $model->views_count; $i ++ ) {
			$view = array(
				'ip'        => '0.0.0.0',
				'user_id'   => 0,
				'date_view' => date( 'Y-m-d H:i:s' )
			);
			add_post_meta( $model->id, '_jbp_pro_view_count', $view );
		}
	}

	function get_image_category() {
		$categories = array(
			'abstract',
			'animals',
			'business',
			'cats',
			'city',
			'food',
			'nightlife',
			'fashion',
			'people',
			'nature',
			'sports',
			'technics',
			'transport'
		);

		return $categories[ array_rand( $categories ) ];
	}

	function handler_upload( $parent_post_id, $filename ) {
		// Check the type of tile. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	function download_image( $url, $path ) {
		$ch = curl_init( $url );
		$fp = fopen( $path, 'wb' );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );
	}


	function content_bank( $type ) {
		$plugin = je();
		$data   = file_get_contents( $plugin->plugin_path . 'app/addons/je-expert-demo/data.txt' );
		$data   = json_decode( $data, true );

		switch ( $type ) {
			case 'fname':
				$c = $data['fname'];

				return $c[ array_rand( $c ) ];
			case 'lname':
				$c = $data['lname'];

				return $c[ array_rand( $c ) ];
			case 'content':
				$c = $data['contents'];

				return $c[ array_rand( $c ) ];
			case 'scontent':
				$c = $data['scontents'];

				return $c[ array_rand( $c ) ];
			case 'email':
				$c = $data['emails'];

				return $c[ array_rand( $c ) ];
			case 'image':
				$c = rand( 1, 10 );
				return dirname( __FILE__ ) . '/je-expert-demo/demo_images/' . $c . '.jpg';
		}
	}


	function check_status() {
		$status = get_option( 'experts_demo_status' );
		echo round( $status );
		exit;
	}

	function menu() {
		?>
		<li <?php echo je()->get( 'tab' ) == 'expert_demo_data' ? 'class="active"' : null ?>>
			<a href="<?php echo admin_url( 'edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert_demo_data' ) ?>">
				<i class="dashicons dashicons-star-filled"></i> <?php _e( 'Experts Demo Data', je()->domain ) ?>
			</a></li>
	<?php
	}

	function content() {
		?>
		<form method="post" id="expert-demo-data">
			<fieldset>
				<div class="page-header" style="margin-top: 0">
					<h4><?php _e( 'Create dummy data for experts', je()->domain ) ?></h4>
				</div>

				<div class="form-group">
					<label class="col-md-3 label-control"><?php _e( 'Dummy experts amount', je()->domain ) ?></label>

					<div class="col-md-9">
						<input type="text" value="10" name="dummy_expert_qty" class="form-control">

						<p class="help-block"><?php _e( 'Number of dummy experts to create', je()->domain ) ?></p>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3 label-control"><?php _e( 'Socials', je()->domain ) ?></label>

					<div class="col-md-9">
						<select name="expert_socials[]" multiple="multiple">
							<?php foreach ( ig_social_wall()->get_social_list() as $k => $v ): ?>
								<option value="<?php echo $v['key'] ?>"><?php echo $v['name'] ?></option>
							<?php endforeach; ?>
						</select>

						<p class="help-block"><?php _e( 'Social profile for experts. If empty, 3-5 profiles will be randomly added', je()->domain ) ?></p>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3 label-control"><?php _e( 'Skills', je()->domain ) ?></label>

					<div class="col-md-9">
						<input type="text" value="Html,CSS,Javascript,jQuery,Bootstrap,PHP,NodeJS,MySQL"
						       name="dummy_skills" class="form-control">

						<p class="help-block"><?php _e( 'Experts skills. Score will be random from 0-100.', je()->domain ) ?></p>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group">
					<label class="col-md-3 label-control"><?php _e( 'Sample files?', je()->domain ) ?></label>

					<div class="col-md-9">
						<p class="help-block">
							<input type="checkbox" checked="checked" name="have_sample">
							<?php _e( 'If you want the demo experts to have sample files, check this box.', je()->domain ) ?>
						</p>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="demo_status hide">
					<p><?php _e( 'Creating demo data,please wait....' ) ?></p>

					<div class="progress">
						<div class="progress-bar progress-bar-striped " role="progressbar" style="width: 0%">
							<span class="sr-only">0%</span>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-md-9 col-md-offset-3">
						<button type="submit" class="btn btn-primary"><?php _e( "Submit", je()->domain ) ?></button>
					</div>
				</div>
			</fieldset>
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('#expert-demo-data').submit(function () {
					var interval = '';
					$.ajax({
						type: 'POST',
						data: {
							data: $(this).serialize(),
							_nonce: '<?php echo wp_create_nonce('create_demo_expert') ?>',
							action: 'create_demo_expert'
						},
						url: '<?php echo admin_url('admin-ajax.php') ?>',
						beforeSend: function () {
							$('.demo_status').removeClass('hide');
							//reset data
							$('.progress-bar').text(1 + '%').css('width', 1 + '%');
							$('.demo_status').find('p').text('Creating demo data,please wait....');
							//triger load status
							interval = setInterval(function () {
								$.ajax({
									type: 'POST',
									data: {
										action: 'check_create_demo_expert'
									},
									url: '<?php echo admin_url('admin-ajax.php') ?>',
									success: function (data) {
										if (data.length) {
											if (data == 100) {
												clearInterval(interval);
												$('.demo_status').find('p').text('Done!');
												$('.progress-bar').text(data + '%').css('width', data + '%');
											} else {
												$('.progress-bar').text(data + '%').css('width', data + '%');
											}
										}

									}
								})
							}, 2000);
						},
						success: function (data) {
						}
					})
					return false;
				})
			})
		</script>
	<?php
	}
}

new JE_Expert_Demo_Data();