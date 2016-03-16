<?php

/**
 * @author:Hoang Ngo
 */
class JE_Job_Model extends IG_Post_Model {
	//model own property
	public $id;
	public $job_title;
	public $categories;
	public $skills;
	public $description;
	public $budget;
	public $contact_email;
	public $dead_line;
        public $job_img;
	public $open_for;
	public $portfolios;
	public $status;
	public $min_budget;
	public $max_budget;
	public $owner;

	public $text_domain = 'jbp';

	protected $table = 'jbp_job';
	protected $defaults = array(
		'ping_status'    => 'closed',
		'comment_status' => 'closed'
	);

	protected $mapped = array(
		'id'          => 'ID',
		'job_title'   => 'post_title',
		'owner'       => 'post_author',
		'description' => 'post_content',
		'status'      => 'post_status'
	);

	protected $relations = array(
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_Budget',
			'map'  => 'budget'
		),
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_Contact_Email',
			'map'  => 'contact_email'
		),
		array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_Due',
			'map'  => 'dead_line'
		),
                array(
			'type' => 'meta',
			'key'  => '_ct_jbp_job_img',
			'map'  => 'job_img'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_expires',
			'map'  => 'open_for'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_portfolios',
			'map'  => 'portfolios'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_budget_min',
			'map'  => 'min_budget'
		),
		array(
			'type' => 'meta',
			'key'  => '_jbp_job_budget_max',
			'map'  => 'max_budget'
		),
		array(
			'type' => 'taxonomy',
			'key'  => 'jbp_category',
			'map'  => 'categories'
		),
		array(
			'type' => 'taxonomy',
			'key'  => 'jbp_skills_tag',
			'map'  => 'skills'
		),
	);

	public function __construct() {
		$this->virtual_attributes = apply_filters( 'je_job_additions_field', $this->virtual_attributes );
		$this->relations          = apply_filters( 'je_job_relations', $this->relations );
		$this->mapped             = apply_filters( 'je_job_fields_mapped', $this->mapped );
		$this->defaults           = apply_filters( 'je_job_default_fields', $this->defaults );
	}

	public function before_validate() {
		$rules = array(
			'job_title'     => 'required',
			'contact_email' => 'required|valid_email',
			'dead_line'     => 'required',
			'open_for'      => 'required',
			'description'   => 'required',
		);
		if ( je()->settings()->job_budget_range == 1 ) {
			$rules['min_budget'] = 'required|numeric|min_numeric,0';
			$rules['max_budget'] = 'required|numeric';
		} else {
			$rules['budget'] = 'required|numeric';
		}

		$rules       = apply_filters( 'je_job_validation_rules', $rules );
		$this->rules = $rules;

		$fields_text = array(
			'dead_line' => __( 'Completion Date', je()->domain ),
			'open_for'  => __( 'Job open for', je()->domain )
		);
		$fields_text = apply_filters( 'je_job_field_name', $fields_text );
		foreach ( $fields_text as $key => $text ) {
			GUMP::set_field_name( $key, $text );
		}
	}

	public function before_save() {
		if ( $this->is_expired() ) {
			update_post_meta( $this->id, 'jbp_job_post_day', date( 'Y-m-d H:i:s' ) );
		}

		$this->defaults = array_merge( $this->defaults, array(
			'post_name' => sanitize_title( $this->job_title )
		) );
	}

	public function after_validate() {
		if ( je()->settings()->job_budget_range == 1 && $this->min_budget > $this->max_budget ) {
			$this->set_error( 'min_budget', __( "Min Budget should less than Max Budget", je()->domain ) );

			return false;
		}

		return true;
	}

	public function get_price() {
		if ( je()->settings()->job_budget_range == 1 ) {
			//use range
			if ( strlen( $this->min_budget ) && strlen( $this->max_budget ) ) {
				return array( $this->min_budget, $this->max_budget );
			} else {
				//fallback to normal budget
				return $this->budget;
			}
		} else {
			return $this->budget;
		}
	}

	public function render_prices( $return = '' ) {
		$prices   = $this->get_price();
		$currency = je()->settings()->currency;
		if ( is_array( $prices ) ) {
			?>
			<?php if ( empty( $return ) ): ?>
				<?php echo JobsExperts_Helper::format_currency( $currency, $this->min_budget ) ?> -
				<?php echo JobsExperts_Helper::format_currency( $currency, $this->max_budget ) ?>
			<?php else: ?>
				<?php echo JobsExperts_Helper::format_currency( $currency, $this->max_budget ) ?>
			<?php endif; ?>
			<?php
		} else {
			?>
			<?php echo JobsExperts_Helper::format_currency( $currency, $this->budget ) ?>
			<?php
		}
	}

	public function get_due_day() {
		$post = get_post( $this->id );
		if ( $post ) {
			$created_date = get_post_meta( $post->ID, 'jbp_job_post_day', true );
			if ( ! $created_date ) {
				$created_date = $post->post_date;
			}
			$expire_date = strtotime( '+ ' . $this->open_for . ' days', strtotime( $created_date ) );

			return $this->days_hours( $expire_date );
		}
	}

	function count() {
		global $wpdb;
		$sql    = "SELECT count(ID) FROM " . $wpdb->posts . " WHERE post_type=%s AND post_status IN (%s,%s) AND post_author=%d";
		$result = $wpdb->get_var( $wpdb->prepare( $sql, 'jbp_job', 'publish', 'draft', get_current_user_id() ) );

		return $result;
	}

	private function days_hours( $expires ) {
		$date = intval( $expires );
		$secs = $date - time();
		if ( $secs > 0 ) {
			$days  = floor( $secs / ( 60 * 60 * 24 ) );
			$hours = round( ( $secs - $days * 60 * 60 * 24 ) / ( 60 * 60 ) );

			return sprintf( __( '%d Days %dhrs', je()->domain ), $days, $hours );
		} else {
			return __( 'Expired', je()->domain );
		}
	}

	function get_end_date() {
		return date_i18n( get_option( 'date_format' ), strtotime( $this->dead_line ) );
	}

	protected function count_user_posts_by_type( $user_id = 0, $post_type = 'post' ) {
		global $wpdb;

		$where = get_posts_by_author_sql( $post_type, true, $user_id );

		if ( in_array( $post_type, array( 'jbp_pro', 'jbp_job' ) ) ) {
			$where = str_replace( "post_status = 'publish'", "post_status = 'publish' OR post_status = 'draft'", $where );
		}
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

		return apply_filters( 'get_usernumposts', $count, $user_id );
	}

	function is_expired() {
		if ( $this->get_due_day() == __( 'Expired', je()->domain ) ) {
			return true;
		}

		return false;
	}

	function is_current_owner() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( get_current_user_id() == $this->owner ) {
			return true;
		}

		return false;
	}

	function get_status() {
		$status = $this->status;
		if ( $status == 'publish' ) {
			$status = __( 'published', je()->domain );
		} elseif ( $status == 'pending' ) {
			$status = __( 'pending', je()->domain );
		} elseif ( $status == 'draft' ) {
			$status = __( "draft", je()->domain );
		}

		return $status;
	}

	public static function model( $class_name = __CLASS__ ) {
		return parent::model( $class_name );
	}
}