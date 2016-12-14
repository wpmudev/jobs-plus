<?php

/**
 * @author:Hoang Ngo
 */
class IG_Skill_Model extends IG_Model {
	public $id;
	public $parent_id;
	public $name;
	public $value;
	public $css;

	protected $rules = array(
		'name' => 'required'
	);

	public static function model( $class_name = __CLASS__ ) {
		return new $class_name;
	}

	public function get_one( $skill, $parent_id = null ) {
		if ( is_null( $parent_id ) ) {
			$parent_id = $this->parent_id;
		}
		$skills = get_post_meta( $parent_id, '_expert_skill' );
		if ( is_array( $skills ) && count( $skills ) ) {
			foreach ( $skills as $record ) {
				if ( $record['name'] == $skill ) {
					$model = new IG_Skill_Model();
					$model->import( $record );

					return $model;
				}
			}
		}

		return null;
	}

	public function get_all( $parent_id = null ) {
		$data   = get_post_meta( $parent_id, '_expert_skill' );
		$models = array();
		foreach ( $data as $record ) {
			$model = new IG_Skill_Model();
			$model->import( $record );
			$models[] = $model;
		}

		return $models;
	}

	public function delete() {
		delete_post_meta( $this->parent_id, '_expert_skill', $this->export() );
	}

	public function save() {
		$this->name  = esc_html( $this->name );
		$this->value = esc_html( $this->value );
		$this->css   = esc_html( $this->css );
		//get post meta
		$old = $this->get_one( $this->name, $this->parent_id );
		if ( is_object( $old ) ) {
			update_post_meta( $this->parent_id, '_expert_skill', $this->export(), $old->export() );
		} else {
			add_post_meta( $this->parent_id, '_expert_skill', $this->export() );
		}
	}


	public function find_css() {
		$csses = explode( ' ', $this->css );
		foreach ( $csses as $css ) {
			$class = str_replace( 'progress-bar-', '', $css );
			if ( in_array( $class, array( 'success', 'danger', 'info', 'warning' ) ) ) {
				return $class;
			}
		}
	}
}