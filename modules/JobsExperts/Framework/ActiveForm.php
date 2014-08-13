<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_ActiveForm {
	public $model;
	public $clientValidation;


	/**
	 * An empty construct since we will do factory the form
	 */
	private function __construct( $model ) {

	}

	public static function generateForm( $model ) {
		$form = new JobsExperts_Framework_ActiveForm( $model );

		return $form;
	}

	public function openForm( $action = '', $method = '', $htmlOptions = array() ) {
		echo JobsExperts_Framework_Form::createForm( $action, $method, $htmlOptions );
	}

	/**
	 *
	 */
	public function endForm() {
		echo JobsExperts_Framework_Form::endForm();
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function textField( $model, $attribute, $htmlOptions = array() ) {
		echo JobsExperts_Framework_Form::textField( $this->buildFormElementName( $model, $attribute ), $model->$attribute, $htmlOptions );
		$this->renderError( $model, $attribute );
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function passwordField( $model, $attribute, $htmlOptions = array() ) {
		echo JobsExperts_Framework_Form::passWordField( $this->buildFormElementName( $model, $attribute ), $model->$attribute, $htmlOptions );
		$this->renderError( $model, $attribute );
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function hiddenField( $model, $attribute, $htmlOptions = array() ) {
		$value = isset( $htmlOptions['value'] ) ? $htmlOptions['value'] : $model->$attribute;
		echo JobsExperts_Framework_Form::hiddenField( $this->buildFormElementName( $model, $attribute ), $value, $htmlOptions );
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $data
	 * @param array $htmlOptions
	 */
	public function dropDownList( $model, $attribute, $data = array(), $htmlOptions = array() ) {
		$selected = $model->$attribute;
		if ( ! is_array( $selected ) ) {
			$selected = array( $selected );
		}
		echo JobsExperts_Framework_Form::dropDownList( $this->buildFormElementName( $model, $attribute ), $selected, $data, $htmlOptions );
		$this->renderError( $model, $attribute );
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function checkBox( $model, $attribute, $htmlOptions = array() ) {
		$checked = false;
		if ( in_array( $model->$attribute, array( 1, true, 'on' ,'1'), true ) ) {
			$checked = true;
		}
		echo JobsExperts_Framework_Form::checkBox( $this->buildFormElementName( $model, $attribute ), $checked, $htmlOptions );
		$this->renderError( $model, $attribute );
	}

	/**
	 * @param       $mode
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function radioButton( $model, $attribute, $value, $htmlOptions = array() ) {
		$checked              = $model->$attribute == $value;
		$htmlOptions['value'] = $value;
		echo JobsExperts_Framework_Form::radioButton( $this->buildFormElementName( $model, $attribute ), $checked, $htmlOptions );;
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function textArea( $model, $attribute, $htmlOptions = array() ) {
		echo JobsExperts_Framework_Form::textArea( $this->buildFormElementName( $model, $attribute ), $model->$attribute, $htmlOptions );
	}

	/**
	 * @param       $model
	 * @param       $attribute
	 * @param array $htmlOptions
	 */
	public function countryDropDown( $model, $attribute, $htmlOptions = array() ) {
		$selected = $model->$attribute;
		if ( ! is_array( $selected ) ) {
			$selected = array( $selected );
		}

		echo JobsExperts_Framework_Form::countryDropdown( $this->buildFormElementName( $model, $attribute ), $selected, $htmlOptions );
		$this->renderError( $model, $attribute );
	}

	/**
	 * @param $model
	 * @param $attribute
	 */
	private function renderError( $model, $attribute ) {
		if ( $model->has_error( $attribute ) ) {
			echo '<span class="error_message">' . $model->get_error( $attribute ) . '</span>';
		}
	}

	/**
	 * @param $model
	 * @param $attribute
	 *
	 * @return string
	 */
	private function buildFormElementName( $model, $attribute ) {
		$model_class_name = get_class( $model );
		$frm_element_name = $model_class_name . "[$attribute]";

		return $frm_element_name;
	}
}