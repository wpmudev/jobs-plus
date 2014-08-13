<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Ajax extends JobsExperts_Framework_Module {
	const NAME = __CLASS__;

	public function __construct() {
		$this->_add_ajax_action( 'jbp_pro_like', 'handler_like' );
	}

	function handler_like() {
		if ( ! empty( $_POST['id'] ) ) {
			$model = JobsExperts_Core_Models_Pro::instance()->get_one( $_POST['id'] );
			$user  = @get_user_by( 'id', $_POST['user_id'] );
			if ( $user instanceof WP_User && is_object( $model ) ) {
				if ( $model->is_current_user_can_like( $user->ID ) ) {
					add_user_meta( $user->ID, 'jbp_pro_liked', $model->id );
					//update pro like
					update_post_meta( $model->id, 'jbp_pro_like_count', $model->get_like_count() + 1 );
					echo $model->get_like_count();
					die;
				}
			}
		}
	}
}