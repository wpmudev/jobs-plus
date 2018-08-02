<?php
/**
 * Authors: Hoang Ngo, Konstantinos Xenos
 */

class JE_GDPR_Controller {
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_plugin_exporter' ), 10 );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_plugin_eraser' ), 10 );
		add_action( 'admin_init', array( $this, 'privacy_policy_suggested_text' ) );
	}

	public function register_plugin_eraser( $erasers ) {
		$erasers['job_plus'] = array(
			'eraser_friendly_name' => __( 'Jobs & Experts', je()->domain ),
			'callback'             => array( $this, 'plugin_eraser' ),
		);

		return $erasers;
	}

	public function register_plugin_exporter( $exporters ) {
		$exporters['job_plus'] = array(
			'exporter_friendly_name' => __( 'Jobs & Experts', je()->domain ),
			'callback'               => array( $this, 'plugin_exporter' ),
		);

		return $exporters;
	}

	public function plugin_eraser( $email, $page = 1 ) {
		$data_author   = get_user_by( 'email', $email );
		$jobs_count    = 0;
		$experts_count = 0;

		if ( ! empty( $data_author ) ) {
			$jobs = JE_Job_Model::model()->find_by_attributes( array(
				'owner' => $data_author->ID,
			) );

			$experts = JE_Expert_Model::model()->find_by_attributes( array(
				'user_id' => $data_author->ID,
			) );
		}

		if ( ! empty( $jobs ) ) {
			$jobs_count = count( $jobs );
			foreach ( $jobs as $job ) {
				$job->delete();
			}
		}

		if ( ! empty( $experts ) ) {
			$experts_count = count( $experts );
			foreach ( $experts as $expert ) {
				$expert->delete();
			}
		}

		$count_removed = (int) $jobs_count + $experts_count;

		return array(
			'items_removed'  => $count_removed,
			'items_retained' => false, // we will remove all
			'messages'       => array(),
			'done'           => true,
		);
	}

	public function plugin_exporter( $email, $page = 1 ) {

		$data_author  = get_user_by( 'email', $email );
		$export_items = array();

		if ( ! empty( $data_author ) ) {
			$jobs = JE_Job_Model::model()->find_by_attributes( array(
				'owner' => $data_author->ID,
			) );

			$experts = JE_Expert_Model::model()->find_by_attributes( array(
				'user_id' => $data_author->ID,
			) );
		}

		if ( ! empty( $jobs ) ) {
			foreach ( $jobs as $job ) {
				$item           = array(
					'group_id'    => 'je-jobs',
					'group_label' => __( 'Jobs & Experts - Jobs', je()->domain ),
					'item_id'     => 'je-job-' . $job->id,
					'data'        => array(
						array(
							'name'  => __( 'Job Name', je()->domain ),
							'value' => $job->job_title,
						),
						array(
							'name'  => __( 'Category', je()->domain ),
							'value' => implode( '<br/>', $job->categories ),
						),
						array(
							'name'  => __( 'Email', je()->domain ),
							'value' => $job->contact_email,
						),
						array(
							'name'  => __( 'Budget', je()->domain ),
							'value' => $job->render_prices( true ),
						),
						array(
							'name'  => __( 'Open For', je()->domain ),
							'value' => $job->get_due_day(),
						),
						array(
							'name'  => __( 'Completion Date', je()->domain ),
							'value' => $job->get_end_date(),
						),
						array(
							'name'  => __( 'Skills', je()->domain ),
							'value' => implode( '<br/>', $job->skills ),
						),
						array(
							'name'  => __( 'Status', je()->domain ),
							'value' => $job->get_status(),
						),
					),
				);
				$export_items[] = $item;
			}
		}

		if ( ! empty( $experts ) ) {
			foreach ( $experts as $expert ) {
				$socials      = explode( ',', $expert->social );
				$socials_text = array();
				$skills       = explode( ',', $expert->skills );
				$skills_text  = array();
				foreach ( $socials as $social ) {
					$model = Social_Wall_Model::model()->get_one( $social, $expert->id );
					if ( is_object( $model ) ) {
						$socials_text[] = $model->name . ' : <a href="' . $model->value . '">' . $model->value . '</a>';
					}
				}
				foreach ( $skills as $skill ) {
					if ( ! empty( $skill ) ) {
						$skills_text[] = $skill;
					}
				}
				$item           = array(
					'group_id'    => 'je-experts',
					'group_label' => __( 'Jobs & Experts - Experts', je()->domain ),
					'item_id'     => 'je-expert-' . $expert->id,
					'data'        => array(
						array(
							'name'  => __( 'Expert Name', je()->domain ),
							'value' => $expert->first_name . ' ' . $expert->last_name,
						),
						array(
							'name'  => __( 'Email', je()->domain ),
							'value' => $expert->contact_email,
						),
						array(
							'name'  => __( 'Location', je()->domain ),
							'value' => $expert->get_location(),
						),
						array(
							'name'  => __( 'Tag Line', je()->domain ),
							'value' => $expert->short_description,
						),
						array(
							'name'  => __( 'Biography', je()->domain ),
							'value' => $expert->biography,
						),
						array(
							'name'  => __( 'Company', je()->domain ),
							'value' => $expert->company,
						),
						array(
							'name'  => __( 'Company URL', je()->domain ),
							'value' => $expert->company_url,
						),
						array(
							'name'  => __( 'Social', je()->domain ),
							'value' => implode( '<br/>', $socials_text ),
						),
						array(
							'name'  => __( 'Skills', je()->domain ),
							'value' => implode( '<br/>', $skills_text ),
						),
					),
				);
				$export_items[] = $item;
			}
		}

		$export = array(
			'data' => $export_items,
			'done' => true,
		);

		return $export;
	}

	/**
	 * Adds the Privacy Policy Suggested Text
	 *
	 * @uses function_exists
	 * @uses ob_start
	 * @uses ob_get_clean
	 * @uses wp_add_privacy_policy_content
	 * @uses je
	 */
	public function privacy_policy_suggested_text() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			ob_start();
			include dirname( __FILE__ ) . '/policy-text.php';
			$content = ob_get_clean();
			if ( ! empty( $content ) ) {
				wp_add_privacy_policy_content( __( 'Jobs & Experts', je()->domain ), $content );
			}
		}
	}
}
