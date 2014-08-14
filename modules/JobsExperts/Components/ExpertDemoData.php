<?php

/**
 * Author: Hoang Ngo
 */
class JobsExpert_Compnents_ExpertDemoData extends JobsExperts_Components {
	public function __construct() {
		$this->_add_action( 'jbp_setting_menu', 'menu' );
		$this->_add_action( 'jbp_setting_content', 'content', 10, 2 );
		$this->_add_action( 'jbp_after_save_settings', 'save_setting' );
	}

	function faker_generator( $socials, $skills, $have_file ) {
		$plugin = JobsExperts_Plugin::instance();
		include_once $plugin->_module_path . 'Components/DemoData/faker/autoload.php';
		$faker = Faker\Factory::create();

		$model                    = new JobsExperts_Core_Models_Pro();
		$model->first_name        = $faker->firstName;
		$model->last_name         = $faker->lastName;
		$model->biography         = $faker->realText( 500, 4 );
		$model->short_description = $faker->text();
		$model->location          = $faker->country;
		$model->contact_email     = $faker->safeEmail;
		$model->user_id           = get_current_user_id();
		$model->status            = 'publish';
		$model->company           = 'WPMUDEV';
		$model->company_url       = 'http://wpmudev.org';
		$model->views_count       = rand( 0, 100 );
		$model->likes_count       = rand( 0, 100 );
		//if empty social
		if ( empty( $socials ) ) {
			$t = array_rand( $plugin->settings()->social_list, 5 );
			foreach ( $t as $v ) {
				$socials[] = $v;
			}
		}
		$s_data = array();
		foreach ( $socials as $s ) {
			$s_data[] = array(
				'id'  => $s,
				'url' => 'http://wpmudev.org'
			);
		}
		$model->social = json_encode( $s_data );
		//skill
		$skills = explode( ',', $skills );
		$skills = array_filter( $skills );
		$t      = array_rand( $skills, 3 );
		$s_data = array();
		foreach ( $t as $v ) {
			$s_data[] = array(
				'name'  => $skills[$v],
				'score' => rand( 0, 100 )
			);
		}
		$model->skills = json_encode( $s_data );
		$model->save();

		if ( $have_file ) {
			//random generate 3 files
			$ids = array();
			for ( $i = 0; $i < 3; $i ++ ) {
				//get the random image
				$upload_dir = wp_upload_dir();
				$path       = $upload_dir['path'] . '/' . uniqid() . '.jpg';
				$image_url  = \Faker\Provider\Image::imageUrl();
				//download the image
				$this->download_image( $image_url, $path );
				//now handler the file
				$att_id = $this->handler_upload( $model->id, $path );
				update_post_meta( $att_id, 'portfolio_link', 'http://wpmudev.org' );
				update_post_meta( $att_id, 'portfolio_des', jbp_filter_text( $faker->realText( 300 ) ) );
				$ids[] = $att_id;
			}
			$model->portfolios = implode( ',', $ids );
			$model->save();
		}
	}

	function normal_generator( $socials, $skills, $have_file ) {
		$plugin = JobsExperts_Plugin::instance();

		$model                    = new JobsExperts_Core_Models_Pro();
		$model->first_name        = $this->content_bank( 'fname' );
		$model->last_name         = $this->content_bank( 'lname' );
		$model->biography         = $this->content_bank( 'content' );
		$model->short_description = $this->content_bank( 'scontent' );
		$model->location          = 'United State';
		$model->contact_email     = $this->content_bank( 'email' );
		$model->user_id           = get_current_user_id();
		$model->status            = 'publish';
		$model->company           = 'WPMUDEV';
		$model->company_url       = 'http://wpmudev.org';
		$model->views_count       = rand( 0, 100 );
		$model->likes_count       = rand( 0, 100 );
		//if empty social
		if ( empty( $socials ) ) {
			$t = array_rand( $plugin->settings()->social_list, 5 );
			foreach ( $t as $v ) {
				$socials[] = $v;
			}
		}
		$s_data = array();
		foreach ( $socials as $s ) {
			$s_data[] = array(
				'id'  => $s,
				'url' => 'http://wpmudev.org'
			);
		}
		$model->social = json_encode( $s_data );
		//skill
		$skills = explode( ',', $skills );
		$skills = array_filter( $skills );
		$t      = array_rand( $skills, 3 );
		$s_data = array();
		foreach ( $t as $v ) {
			$s_data[] = array(
				'name'  => $skills[$v],
				'score' => rand( 0, 100 )
			);
		}
		$model->skills = json_encode( $s_data );
		$model->save();

		if ( $have_file ) {
			//random generate 3 files
			$ids = array();
			for ( $i = 0; $i < 3; $i ++ ) {
				//get the random image
				$upload_dir = wp_upload_dir();
				$path       = $upload_dir['path'] . '/' . uniqid() . '.jpg';
				$image_url  = $this->content_bank( 'image' );
				//download the image
				$this->download_image( $image_url, $path );
				//now handler the file
				$att_id = $this->handler_upload( $model->id, $path );
				update_post_meta( $att_id, 'portfolio_link', 'http://wpmudev.org' );
				update_post_meta( $att_id, 'portfolio_des', jbp_filter_text( $this->content_bank( 'scontent' ) ) );
				$ids[] = $att_id;
			}
			$model->portfolios = implode( ',', $ids );
			$model->save();
		}
	}

	function content_bank( $type ) {
		$plugin = JobsExperts_Plugin::instance();
		$data   = file_get_contents( $plugin->_module_path . 'Components/DemoData/expert_data.txt' );
		$data   = json_decode( $data, true );

		switch ( $type ) {
			case 'fname':
				$c = $data['fname'];

				return $c[array_rand( $c )];
			case 'lname':
				$c = $data['lname'];

				return $c[array_rand( $c )];
			case 'content':
				$c = $data['contents'];

				return $c[array_rand( $c )];
			case 'scontent':
				$c = $data['scontents'];

				return $c[array_rand( $c )];
			case 'email':
				$c = $data['emails'];

				return $c[array_rand( $c )];
			case 'image':
				$c = $data['images'];

				return $c[array_rand( $c )];
		}
	}

	function save_setting() {
		if ( isset( $_POST['create_expert_dummy_data'] ) ) {
			$qty         = $_POST['dummy_expert_qty'];
			$socials     = isset( $_POST['expert_socials'] ) ? $_POST['expert_socials'] : null;
			$skills      = $_POST['dummy_skills'];
			$have_sample = isset( $_POST['have_sample'] ) ? true : false;

			//prepare data
			if ( $qty > 0 ) {
				for ( $i = 0; $i < $qty; $i ++ ) {
					if ( version_compare( phpversion(), '5.3.3' ) >= 0 ) {
						$this->faker_generator( $socials, $skills, $have_sample );
					} else {
						$this->normal_generator( $socials, $skills, $have_sample );
					}
				}
			}
		}
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

	function menu() {
		$plugin = JobsExperts_Plugin::instance();
		?>
		<li <?php echo $this->active_tab( 'expert_demo_data' ) ?>>
			<a href="<?php echo admin_url( 'edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=expert_demo_data' ) ?>">
				<i class="dashicons dashicons-star-filled"></i> <?php _e( 'Experts Demo Data', JBP_TEXT_DOMAIN ) ?>
			</a></li>
	<?php
	}

	function content( JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model ) {
		if ( $this->is_current_tab( 'expert_demo_data' ) ) {
			?>
			<fieldset>
				<div class="page-header" style="margin-top: 0">
					<h4><?php _e( 'Create dummy data for experts', JBP_TEXT_DOMAIN ) ?></h4>
				</div>
				<table class="form-table">
					<tr>
						<th role="col"><?php _e( 'Dummy experts amount', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="text" value="10" name="dummy_expert_qty" class="large-text">

							<div class="clearfix"></div>
							<small><?php _e( 'Number of dummy experts to create', JBP_TEXT_DOMAIN ) ?></small>
						</td>
					</tr>
					<tr>
						<th role="col"><?php _e( 'Socials', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<select name="expert_socials[]" multiple="multiple">
								<?php foreach ( JobsExperts_Plugin::instance()->settings()->social_list as $k => $v ): ?>
									<option value="<?php echo $k ?>"><?php echo $v ?></option>
								<?php endforeach; ?>
							</select>

							<div class="clearfix"></div>
							<small><?php _e( 'Social profile for expert, if empty, random pick 3-5 profiles' ) ?></small>
						</td>
					</tr>
					<tr>
						<th role="col"><?php _e( 'Skills', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="text" value="Html,CSS,Javascript,jQuery,Bootstrap,PHP,NodeJS,MySQL" name="dummy_skills" class="large-text">

							<div class="clearfix"></div>
							<small><?php _e( 'Experts skill, score will be random from 0-100', JBP_TEXT_DOMAIN ) ?></small>
						</td>
					</tr>

					<tr>
						<th role="col"><?php _e( 'Have Sample Files', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="checkbox" checked="checked" name="have_sample">

							<div class="clearfix"></div>
							<small><?php _e( 'If you want this expert have sample files, check this field', JBP_TEXT_DOMAIN ) ?></small>
						</td>
						<input type="hidden" name="create_expert_dummy_data">
					</tr>
				</table>
			</fieldset>
		<?php
		}
	}
}

new JobsExpert_Compnents_ExpertDemoData();