<?php

/**
 * This class is for Post Type Model
 * Author: Hoang Ngo
 */
abstract class JobsExperts_Framework_PostModel extends JobsExperts_Framework_Model {
	/**
	 * post id
	 * @var int
	 */
	public $id;

	/**
	 * post type slug
	 * @var string
	 */
	public $slug;

	/**
	 * @var
	 */
	public $_raw_post;

	/**
	 * This function is save post,base on the data
	 * @return int|WP_Error
	 */
	public function save() {
		$data = $this->prepare_import_data();
		$this->before_save();
		if ( ! $this->is_new_record() ) {
			//update
			$post_id = wp_update_post( $data['post'] );
			//having post_id, import the meta
			foreach ( $data['meta'] as $key => $val ) {
				update_post_meta( $post_id, $key, $val );
			}
			//
		} else {
			//insert
			$post_id = wp_insert_post( $data['post'] );
			//having post_id, import the meta
			foreach ( $data['meta'] as $key => $val ) {
				update_post_meta( $post_id, $key, $val );
			}
		}
		$this->after_save();
		$this->id = $post_id;

		return $this->id;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_all( $args = array() ) {
		//get only need to get ids
		$args['fields']    = 'ids';
		$args['post_type'] = $this->storage_name();
		$query             = new WP_Query( $args );
		//init return value
		$posts = array(
			'data'        => array(),
			'total_pages' => 0
		);
		foreach ( $query->posts as $post_id ) {
			$model = $this->get_one( $post_id );
			if ( $model ) {
				$posts['data'][] = $model;
			}
		}
		$posts['total_pages'] = $query->max_num_pages;

		return $posts;
	}

	/**
	 * @param        $id_or_slug
	 * @param string $status
	 *
	 * @return null
	 */
	public function get_one( $id_or_slug, $status = 'publish' ) {
		$class_name = get_called_class();
		if ( ! empty( $id_or_slug ) ) {
			if ( filter_var( $id_or_slug, FILTER_VALIDATE_INT ) ) {
				$post = get_post( $id_or_slug );
			} else {
				$posts = get_posts( array(
					'name'        => $id_or_slug,
					'post_type'   => $this->storage_name(),
					'post_status' => $status
				) );
				if ( is_array( $posts ) && count( $posts ) ) {
					$post = $posts[0];
				}
			}

			if ( isset( $post ) && is_object( $post ) ) {
				$model = new $class_name;
				$model->prepare_load_data( $post );

				return $model;
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function is_new_record() {
		$model = '';
		//check by id
		if ( $this->id ) {
			$model = $this->get_one( $this->id, array( 'publish', 'draft', 'review' ) );
		} elseif ( $this->slug ) {
			$model = $this->get_one( $this->slug, array( 'publish', 'draft', 'review' ) );
		}
		if ( is_object( $model ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Find original post for this model
	 * @return null|WP_Post
	 */
	public function get_raw_post() {
		if ( empty( $this->_raw_post ) ) {
			$this->_raw_post = get_post( ( $this->id ) );
		}

		return $this->_raw_post;
	}

	/**
	 * @param array $terms - Array of term name or id
	 * @param       $taxonomy
	 */
	public function assign_terms( $terms = array(), $taxonomy, $append = true ) {
		$ids = array();
		foreach ( $terms as $term ) {
			//is id
			if ( filter_var( $term, FILTER_VALIDATE_INT ) ) {
				$check = get_term( $term, $taxonomy, ARRAY_A );
			} else {
				$check = term_exists( $term, $taxonomy );
			}

			//no term is found
			if ( ! $check ) {
				$check = wp_insert_term( $term, $taxonomy );
			}

			//refresh
			$obj = get_term( $check['term_id'], $taxonomy );
			//assign to this job
			$ids[] = $obj->term_id;
		}
		wp_set_object_terms( $this->id, $ids, $taxonomy, $append );
	}

	/**
	 * @param $taxonomy
	 *
	 * @return array|WP_Error
	 */
	public function find_terms( $taxonomy ) {
		$terms = wp_get_post_terms( $this->id, $taxonomy );

		return $terms;
	}

	/**
	 * @param $id_or_slug
	 * @param $taxonomy
	 */
	public function find_term( $id_or_slug, $taxonomy ) {
		$terms = $this->find_terms( $taxonomy );
		foreach ( $terms as $term ) {
			if ( filter_var( $id_or_slug, FILTER_VALIDATE_INT ) ) {
				if ( $term->term_id == $id_or_slug ) {
					return $term;
				}
			} else {
				if ( $term->slug == $id_or_slug ) {
					return $term;
				}
			}
		}

		return null;
	}

	/**
	 * This function must be clarify to map model properly to wordpress native propery,
	 * Also can map custom post type
	 *
	 *
	 * @return array
	 */
	abstract function prepare_import_data();

	/**
	 * This function must be clarify, use to map WP_POST property to this model property
	 *
	 * @param WP_Post $post
	 *
	 * @return mixed
	 */
	abstract function prepare_load_data( WP_Post $post );

}