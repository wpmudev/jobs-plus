<?php


class JobsExperts_Core_Models_Pro extends JobsExperts_Framework_PostModel {
	public $id;
	public $name;
	public $biography;
	public $short_description;
	public $user_id;

	public $first_name;
	public $last_name;
	public $company;
	public $company_url;
	public $location;
	public $contact_email;
	public $social;
	public $skills;
	public $have_gavatar;
	public $views_count;
	public $likes_count;

	public $portfolios;

	public $status;

	public function storage_name() {
		return 'jbp_pro';
	}

	public function rules() {
		return array(
			array( 'required', 'name,location,contact_email,biography' )
		);
	}

	/**
	 * This function will prepare data from class to wordpress post array
	 */
	public function prepare_import_data() {
		//core data
		if ( empty( $this->user_id ) ) {
			$this->user_id = get_current_user_id();
		}
		$args = array(
			'post' => array(
				'ID'             => ! $this->is_new_record() ? $this->id : null,
				'post_title'     => $this->first_name . ' ' . $this->last_name,
				'post_content'   => $this->biography,
				'post_status'    => $this->status,
				'post_author'    => $this->user_id,
				'post_type'      => 'jbp_pro',
				'ping_status'    => 'closed',
				'comment_status' => 'closed',
			),
			'meta' => array(
				'_ct_jbp_pro_First_Last'        => json_encode( array(
					'first' => $this->first_name,
					'last'  => $this->last_name
				) ),
				'_ct_jbp_pro_Company_URL'       => json_encode( array(
					'link' => $this->company,
					'url'  => $this->company_url
				) ),
				'_ct_jbp_pro_Location'          => $this->location,
				'_ct_jbp_pro_Contact_Email'     => $this->contact_email,
				'_ct_jbp_pro_short_description' => $this->short_description,
				'_ct_jbp_pro_Social'            => $this->social,
				'_ct_jbp_pro_Skills'            => $this->skills,
				'_jbp_pro_portfolios'           => $this->portfolios,
				'jbp_pro_view_count'            => $this->views_count,
				'jbp_pro_like_count'            => $this->likes_count,
			),
		);

		return $args;
	}

	/**
	 * @param WP_Post $data
	 */
	public function prepare_load_data( WP_Post $post ) {
		$this->id        = $post->ID;
		$this->name      = $post->post_title;
		$this->biography = $post->post_content;
		$full_name       = json_decode( get_post_meta( $this->id, '_ct_jbp_pro_First_Last', true ), true );
		//meta
		$this->first_name        = $full_name['first'];
		$this->last_name         = $full_name['last'];
		$company                 = json_decode( get_post_meta( $this->id, '_ct_jbp_pro_Company_URL', true ), true );
		$this->company_url       = $company['url'];
		$this->company           = $company['link'];
		$this->location          = get_post_meta( $this->id, '_ct_jbp_pro_Location', true );
		$this->contact_email     = get_post_meta( $this->id, '_ct_jbp_pro_Contact_Email', true );
		$this->short_description = get_post_meta( $this->id, '_ct_jbp_pro_short_description', true );
		$this->skills            = get_post_meta( $this->id, '_ct_jbp_pro_Skills', true );
		$this->social            = get_post_meta( $this->id, '_ct_jbp_pro_Social', true );
		$this->user_id           = $post->post_author;
		$this->portfolios        = get_post_meta( $this->id, '_jbp_pro_portfolios', true );
		$this->views_count       = get_post_meta( $this->id, 'jbp_pro_view_count', true );
		$this->likes_count       = get_post_meta( $this->id, 'jbp_pro_like_count', true );

		if ( get_post_meta( $this->id, 'have_gavatar', true ) ) {
			$this->have_gavatar = 1;
		}
		$this->portfolios;
	}

	public function add_view_count() {
		$view = intval( get_post_meta( $this->id, 'jbp_pro_view_count', true ) );
		update_post_meta( $this->id, 'jbp_pro_view_count', $view + 1 );
	}

	public function get_view_count() {
		return intval( get_post_meta( $this->id, 'jbp_pro_view_count', true ) );
	}

	public function get_like_count() {
		return intval( get_post_meta( $this->id, 'jbp_pro_like_count', true ) );
	}

	public function is_current_user_can_like( $id = '' ) {
		if ( empty( $id ) ) {
			$id = get_current_user_id();
		} else {
			$id = get_current_user_id();
		}
		$user = get_user_by( 'id', $id );
		if ( $user instanceof WP_User ) {
			$likes    = get_user_meta( get_current_user_id(), 'jbp_pro_liked' );
			$has_like = false;
			foreach ( $likes as $like ) {
				if ( $like == $this->id ) {
					$has_like = true;
					break;
				}
			}

			return ! $has_like;
		}

		return false;
	}


	/////PERMISSIOn

	function is_current_owner() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( get_current_user_id() == $this->user_id ) {
			return true;
		}

		return false;
	}

	function is_current_can_edit() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return current_user_can( 'edit_jbp_pros' );
	}

	function is_reach_max() {
		if ( current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( $this->count_user_posts_by_type( get_current_user_id(), 'jbp_pro' ) >= JobsExperts_Plugin::instance()->settings()->expert_max_records ) {
			return true;
		}

		return false;
	}

	protected function count_user_posts_by_type( $user_id = 0, $post_type = 'post' ) {
		global $wpdb;

		$where = get_posts_by_author_sql( $post_type, TRUE, $user_id );

		if ( in_array( $post_type, array( 'jbp_pro', 'jbp_job' ) ) ) {
			$where = str_replace( "post_status = 'publish'", "post_status = 'publish' OR post_status = 'draft'", $where );
		}
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

		return apply_filters( 'get_usernumposts', $count, $user_id );
	}

	public function get_all_skills( $refresh = false ) {
		$data = wp_cache_get( 'jbp_pro_skill' );
		if ( $data && $refresh == false ) {
			return $data;
		} else {
			global $wpdb;
			$sql    = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->postmeta . ' WHERE meta_key=%s', '_ct_jbp_pro_Skills' );
			$raw    = $wpdb->get_results( $sql );
			$skills = array();
			foreach ( $raw as $key => $val ) {
				$row = json_decode( $val->meta_value, true );
				if ( $row ) {
					foreach ( $row as $s ) {
						if ( isset( $s['name'] ) ) {
							$skills[] = $s['name'];
						}
					}
				}

			}
			$skills = array_filter( array_unique( $skills ) );
			wp_cache_set( 'jbp_pro_skill', $skills );

			return $skills;
		}
	}

	public function after_save() {
		$this->get_all_skills( true );
	}
}