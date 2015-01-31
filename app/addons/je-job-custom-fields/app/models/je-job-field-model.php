<?php

/**
 * @author:Hoang Ngo
 */
class JE_Job_Field_Model extends IG_Post_Model
{
    public $id;
    public $title;
    public $cp_id;
    public $type;
    public $value;
    public $description;
    public $options;
    public $validation_rule;
    public $position;
    public $priority;

    protected $table = 'je_job_field';

    protected $mapped = array(
        'id' => 'ID',
        'title' => 'post_title',
        'description' => 'post_content',
    );

    protected $relations = array(
        array(
            'type' => 'meta',
            'key' => 'cp_id',
            'map' => 'cp_id'
        ),
        array(
            'type' => 'meta',
            'key' => 'type',
            'map' => 'type'
        ),
        array(
            'type' => 'meta',
            'key' => 'value',
            'map' => 'value'
        ),
        array(
            'type' => 'meta',
            'key' => 'options',
            'map' => 'options'
        ),
        array(
            'type' => 'meta',
            'key' => 'validation_rule',
            'map' => 'validation_rule'
        ),
        array(
            'type' => 'meta',
            'key' => 'position',
            'map' => 'position'
        ),
        array(
            'type' => 'meta',
            'key' => 'priority',
            'map' => 'priority'
        ),
    );

    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }
}