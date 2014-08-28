<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_Model
{

    /**
     * @var array
     */
    private $errors = array();

    /**
     * This function need to be override, which return the various things depend on model type
     * Example PostModel will need to be return post type, OptionModel will return option name
     * @return null|string
     */
    public function storage_name()
    {
        return null;
    }

    /**
     * This is validation rules defined for this model
     * @return array
     */
    public function rules()
    {
        return array();
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $this->errors = JobsExperts_Framework_Validator::validate($this->rules(), $this->export());
        if (empty($this->errors)) {
            $addition_validate = $this->addition_validate();
        }

        return empty($this->errors) ? true : false;
    }

    /**
     *
     */
    public function addition_validate()
    {

    }

    /**
     * @return mixed
     */
    public static function instance()
    {
        $class = get_called_class();

        return new $class;
    }

    /**
     * For Override
     */
    public function before_save()
    {
    }

    /**
     * For Override
     */
    public function after_save()
    {
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function has_error($attribute)
    {
        if (isset($this->errors[$attribute])) {
            return true;
        }

        return false;
    }

    /**
     * @param $field
     * @param $error
     */
    public function set_error($field, $error)
    {
        $this->errors[$field] = $error;
    }

    /**
     * @param $attribute
     *
     * @return string
     */
    public function get_error($attribute)
    {
        if (isset($this->errors[$attribute])) {
            $error = $this->errors[$attribute];

            return $error;
        }
    }

    /**
     * @return array
     */
    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * Export current class data to array
     * @return array
     */
    public function export()
    {
        $data = array();
        $ref_class = new ReflectionClass(get_called_class());
        foreach ($ref_class->getProperties() as $prop) {
            if ($prop->class == get_called_class()) {
                $data[$prop->name] = $this->{$prop->name};
            }
        }

        return $data;
    }

    /**
     * Import array data to this class
     *
     * @param array $data
     */
    public function import($data = array())
    {
        foreach ($data as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }
}