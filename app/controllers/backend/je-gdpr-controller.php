<?php
/**
 * Author: Hoang Ngo
 */

class JE_GDPR_Controller {
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_plugin_exporter' ), 10 );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_plugin_eraser' ), 10 );
	}

	public function register_plugin_eraser( $erasers ) {
		$erasers['appointments'] = array(
			'eraser_friendly_name' => __( "Jobs & Experts", je()->domain ),
			'callback'             => array( $this, 'plugin_eraser' ),
		);

		return $erasers;
	}

	public function register_plugin_exporter( $exporters ) {
		$exporters['job_plus'] = array(
			'exporter_friendly_name' => __( "Jobs & Experts", je()->domain ),
			'callback'               => array( $this, 'plugin_exporter' ),
		);

		return $exporters;
	}

	public function plugin_eraser( $email, $page = 1 ) {
		$jobs = JE_Job_Model::model()->find_by_attributes( array(
			'contact_email' => $email
		) );

		$experts = JE_Expert_Model::model()->find_by_attributes( array(
			'contact_email' => $email
		) );

		if ( count( $jobs ) ) {
			foreach ( $jobs as $job ) {
				$job->delete();
			}
		}
		if ( count( $experts ) ) {
			foreach ( $experts as $expert ) {
				$expert->delete();
			}
		}

		return array(
			'items_removed'  => count( $jobs ) + count( $experts ),
			'items_retained' => false, // we will remove all
			'messages'       => array(),
			'done'           => true,
		);
	}

	public function plugin_exporter( $email, $page = 1 ) {
		$jobs = JE_Job_Model::model()->find_by_attributes( array(
			'contact_email' => $email
		) );

		$experts = JE_Expert_Model::model()->find_by_attributes( array(
			'contact_email' => $email
		) );

		$export_items = array();
		if ( count( $jobs ) ) {
			foreach ( $jobs as $job ) {
				$item           = array(
					'group_id'    => 'je-jobs',
					'group_label' => __( "Jobs & Experts - Jobs Info", je()->domain ),
					'item_id'     => 'je-job-' . $job->ID,
					'data'        => array(
						array(
							'name'  => __( 'Job Name', je()->domain ),
							'value' => $job->job_title,
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
							'name'  => __( 'Status', je()->domain ),
							'value' => $job->get_status(),
						)
					),
				);
				$export_items[] = $item;
			}
		}
		if ( count( $experts ) ) {
			foreach ( $experts as $expert ) {
				$socials      = explode( ',', $expert->social );
				$socials_text = array();
				foreach ( $socials as $social ) {
					$model = Social_Wall_Model::model()->get_one( $social, $expert->id );
					if ( is_object( $model ) ) {
						$socials_text[] = $model->name . ':' . $model->value;
					}
				}
				$item           = array(
					'group_id'    => 'je-experts',
					'group_label' => __( "Jobs & Experts - Experts Info", je()->domain ),
					'item_id'     => 'je-expert-' . $expert->ID,
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
							'name'  => __( 'Company', je()->domain ),
							'value' => $expert->company,
						),
						array(
							'name'  => __( 'Company URL', je()->domain ),
							'value' => $expert->company_url,
						),
						array(
							'name'  => __( 'Location', je()->domain ),
							'value' => $expert->get_location(),
						),
						array(
							'name'  => __( 'Social', je()->domain ),
							'value' => implode( '<br/>', $socials_text ),
						)
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
}