<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_OptionModel extends JobsExperts_Framework_Model {

	/**
	 * Load this model from database, because option model is unique, so no need other finding
	 */
	public function load() {
		$data = get_option( $this->storage_name() );
		if ( $data ) {
			$this->import( $data );

			return true;
		}

		return false;
	}

	/**
	 * Save data to option
	 */
	public function save() {
		$this->before_save();
		$data = $this->export();
		update_option( $this->storage_name(), $data );
		$this->after_save();
	}
}