<?php

/**
 * @author:Hoang Ngo
 */
class JE_Shared_Controller extends IG_Request {
	public function __construct() {
		add_action( 'query_vars', array( &$this, 'add_my_var' ) );
		add_filter( 'user_has_cap', array( &$this, 'update_cap' ), 10, 4 );
		add_filter( 'ajax_query_attachments_args', array( &$this, 'restrict_user' ) );
		add_action( 'wp_loaded', array( &$this, 'delete_expert' ) );
		add_action( 'wp_loaded', array( &$this, 'delete_job' ) );
	}

	function delete_expert() {
		if ( ! isset( $_POST['delete_expert'] ) ) {
			return;
		}
		$id = je()->post( 'expert_id', 0 );
		if ( ! wp_verify_nonce( je()->post( '_wpnonce' ), 'delete_expert_' . $id ) ) {
			return '';
		}

		$model = JE_Expert_Model::model()->find( $id );
		if ( is_object( $model ) ) {
			$model->delete();
			$this->set_flash( 'profile_deleted', __( "Your expert profile has been deleted.", je()->domain ) );
			$this->redirect( get_permalink( je()->pages->page( JE_Page_Factory::MY_EXPERT ) ) );
		}
	}

	function delete_job() {
		if ( ! isset( $_POST['delete_job'] ) ) {
			return;
		}
		$id = je()->post( 'job_id', 0 );
		if ( ! wp_verify_nonce( je()->post( '_wpnonce' ), 'delete_job_' . $id ) ) {
			return '';
		}

		$model = JE_Job_Model::model()->find( $id );
		if ( is_object( $model ) ) {
			$model->delete();
			$this->set_flash( 'job_deleted', __( "Your job has been deleted." ) );
			$this->redirect( get_permalink( je()->pages->page( JE_Page_Factory::MY_JOB ) ) );
		}
	}


	function restrict_user( $args ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			$args['author'] = get_current_user_id();
		}

		return $args;
	}

	function add_my_var( $public_query_vars ) {
		if ( ! is_admin() ) {
			$public_query_vars[] = 'je-paged';
		}

		return $public_query_vars;
	}

	function update_cap( $allcaps, $caps, $args, $user ) {
		if ( in_array( 'upload_files', $caps ) ) {
			if ( ! isset( $allcaps['upload_files'] ) ) {
				$flag = false;
				if ( je()->post( 'action' ) == 'query-attachments' ) {
					///just query media belong to someone
					$flag = true;
				} elseif ( je()->post( 'action' ) == 'upload-attachment' ) {
					//case upload a file, we only allow when upload via je uploader
					if ( je()->post( 'igu_uploading' ) == 1 ) {
						$flag = true;
					}
				}
				if ( $flag == true ) {
					//check
					// var_dump($_POST);die;
					$allowed = je()->settings()->allow_attachment;
					if ( ! is_array( $allowed ) ) {
						$allowed = array();
					}
					$allowed = array_filter( $allowed );
					foreach ( $user->roles as $role ) {
						if ( in_array( $role, $allowed ) ) {
							$allcaps['upload_files'] = true;
							break;
						}
					}
				}
			}
		}

		//die;
		return $allcaps;
	}
}