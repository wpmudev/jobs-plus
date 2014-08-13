<?php

/**
 * Author: Hoang Ngo
 */
class JobsExpert_Compnents_DemoData extends JobsExperts_Components {
	public function __construct() {
		$this->_add_action( 'jbp_setting_menu', 'menu' );
		$this->_add_action( 'jbp_setting_content', 'content', 10, 2 );
		$this->_add_action( 'jbp_after_save_settings', 'save_setting' );
	}

	function faker_generator( $budgets, $categories, $skills, $have_file ) {
		$plugin = JobsExperts_Plugin::instance();
		include_once $plugin->_module_path . 'Components/DemoData/faker/autoload.php';
		$faker    = Faker\Factory::create();
		$open_for = array( 3, 7, 14, 21 );
		$weeks    = array( 1, 2, 3, 4 );

		$model                = new JobsExperts_Core_Models_Job();
		$model->job_title     = $faker->sentence();
		$model->description   = $faker->realText( 500, 4 );
		$model->budget        = rand( $budgets[0], $budgets[1] );
		$model->min_budget    = rand( $budgets[0], $budgets[1] );
		$model->max_budget    = rand( $model->min_budget, $budgets[1] );
		$model->status        = 'publish';
		$model->contact_email = $faker->safeEmail;
		$model->open_for      = $open_for[array_rand( $open_for )];
		$model->dead_line     = date( 'Y-m-d', strtotime( '+' . $weeks[array_rand( $weeks )] . ' week' ) );
		$model->owner         = get_current_user_id();
		//save dummy data
		$model->save();
		//categories
		$categories = explode( ',', $categories );
		$categories = array_filter( $categories );
		$model->assign_categories( $categories[array_rand( $categories )] );
		$skills      = explode( ',', $skills );
		$skills      = array_filter( $skills );
		$tmp         = array_rand( $skills, 3 );
		$rand_skills = array();
		foreach ( $tmp as $t ) {
			$rand_skills[] = $skills[$t];
		}

		$model->assign_skill_tag( $rand_skills );
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

	function normal_generator( $budgets, $categories, $skills, $have_file ) {
		$open_for = array( 3, 7, 14, 21 );
		$weeks    = array( 1, 2, 3, 4 );

		$model                = new JobsExperts_Core_Models_Job();
		$model->job_title     = $this->content_bank( 'title' );
		$model->description   = $this->content_bank( 'content' );
		$model->budget        = rand( $budgets[0], $budgets[1] );
		$model->min_budget    = rand( $budgets[0], $budgets[1] );
		$model->max_budget    = rand( $model->min_budget, $budgets[1] );
		$model->status        = 'publish';
		$model->contact_email = $this->content_bank( 'email' );
		$model->open_for      = $open_for[array_rand( $open_for )];
		$model->dead_line     = date( 'Y-m-d', strtotime( '+' . $weeks[array_rand( $weeks )] . ' week' ) );
		$model->owner         = get_current_user_id();
		//save dummy data
		$model->save();
		//categories
		$categories = explode( ',', $categories );
		$categories = array_filter( $categories );
		$model->assign_categories( $categories[array_rand( $categories )] );
		$skills      = explode( ',', $skills );
		$skills      = array_filter( $skills );
		$tmp         = array_rand( $skills, 3 );
		$rand_skills = array();
		foreach ( $tmp as $t ) {
			$rand_skills[] = $skills[$t];
		}

		$model->assign_skill_tag( $rand_skills );
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
				update_post_meta( $att_id, 'portfolio_des', jbp_filter_text( $this->content_bank('scontent') ) );
				$ids[] = $att_id;
			}
			$model->portfolios = implode( ',', $ids );
			$model->save();
		}
	}

	function content_bank( $type ) {
		$plugin = JobsExperts_Plugin::instance();
		$data   = file_get_contents( $plugin->_module_path . 'Components/DemoData/job_data.txt' );
		$data   = json_decode( $data, true );

		switch ( $type ) {
			case 'title':
				$titles = $data['titles'];

				return $titles[array_rand( $titles )];
			case 'content':
				$c = $data['contents'];

				return $c[array_rand( $c )];
			case 'scontent':
				$c = $data['short_contents'];

				return $c[array_rand( $c )];
			case 'email':
				$c = $data['emails'];

				return $c[array_rand( $c )];
			case 'image':
				$c = $data['image_urls'];

				return $c[array_rand( $c )];
		}
	}

	function save_setting() {
		if ( isset( $_POST['create_jobs_dummy_data'] ) ) {
			$qty         = $_POST['dummy_job_qty'];
			$prices      = $_POST['dummy_job_price_range'];
			$categories  = $_POST['dummy_category'];
			$skills      = $_POST['dummy_skills'];
			$have_sample = isset( $_POST['have_sample'] ) ? true : false;

			//prepare data
			$prices = explode( '-', $prices );
			if ( $qty > 0 ) {
				for ( $i = 0; $i < $qty; $i ++ ) {
					if ( version_compare( phpversion(), '5.3.3' ) >= 0 ) {
						$this->faker_generator( $prices, $categories, $skills, $have_sample );
					} else {
						$this->normal_generator( $prices, $categories, $skills, $have_sample );
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
		<li <?php echo $this->active_tab( 'job_demo_data' ) ?>>
			<a href="<?php echo admin_url( 'edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job_demo_data' ) ?>">
				<i class="dashicons dashicons-star-filled"></i> <?php _e( 'Jobs Demo Data', JBP_TEXT_DOMAIN ) ?>
			</a></li>
	<?php
	}

	function content( JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model ) {
		if ( $this->is_current_tab( 'job_demo_data' ) ) {
			?>
			<fieldset>
				<div class="page-header" style="margin-top: 0">
					<h4><?php _e( 'Create dummy data for jobs', JBP_TEXT_DOMAIN ) ?></h4>
				</div>
				<table class="form-table">
					<tr>
						<th role="col"><?php _e( 'Dummy jobs amount', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="text" value="10" name="dummy_job_qty" class="large-text">

							<div class="clearfix"></div>
							<small><?php _e( 'Number of dummy jobs to create', JBP_TEXT_DOMAIN ) ?></small>
						</td>
					</tr>
					<tr>
						<th role="col"><?php _e( 'Price Range', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="text" value="0-2000" name="dummy_job_price_range" class="large-text">

							<div class="clearfix"></div>
							<small><?php _e( 'Range of price' ) ?></small>
						</td>
					</tr>
					<tr>
						<th role="col"><?php _e( 'Categories', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="text" value="Wordpress,Buddypress,General,WPMUDEV" name="dummy_category" class="large-text">

							<div class="clearfix"></div>
							<small><?php _e( 'Categories of those jobs,separate by comma. Will use random to assign', JBP_TEXT_DOMAIN ) ?></small>
						</td>
					</tr>
					<tr>
						<th role="col"><?php _e( 'Skill tags', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="text" value="Html5,PHP,MySQL,jQuery,Javascript,Css3,Media," name="dummy_skills" class="large-text">

							<div class="clearfix"></div>
							<small><?php _e( 'Skill of those jobs,separate by comma. Will use random to assign', JBP_TEXT_DOMAIN ) ?></small>
						</td>
					</tr>
					<tr>
						<th role="col"><?php _e( 'Have Sample Files', JBP_TEXT_DOMAIN ) ?></th>
						<td>
							<input type="checkbox" checked="checked" name="have_sample">

							<div class="clearfix"></div>
							<small><?php _e( 'If you want this job have sample files, checked this field', JBP_TEXT_DOMAIN ) ?></small>
						</td>
						<input type="hidden" name="create_jobs_dummy_data">
					</tr>
				</table>
			</fieldset>
		<?php
		}
	}
}

new JobsExpert_Compnents_DemoData();