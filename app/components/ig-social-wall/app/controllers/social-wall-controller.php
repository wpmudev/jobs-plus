<?php

/**
 * @author:Hoang Ngo
 */
class Social_Wall_Controller extends IG_Request
{
    public function __construct()
    {
        add_action('wp_ajax_social_wall_form', array(&$this, 'social_form'));
        add_action('wp_ajax_social_add', array(&$this, 'add_social'));
    }

    function add_social()
    {
        if (!wp_verify_nonce(ig_social_wall()->post('_wpnonce'), 'social_add')) {
            return;
        }
        $model = Social_Wall_Model::model()->get_one($_POST['name'], $_POST['parent_id']);
        if (!is_object($model)) {
            $model = new Social_Wall_Model();
        }
        $model->name = $_POST['name'];
        $model->value = $_POST['value'];
        $social = ig_social_wall()->social($model->name);
        $model->type = $social['type'];
        $model->parent_id = $_POST['parent_id'];
        $model->status = 0;

        if ($model->validate()) {
            $model->save();
            echo json_encode(array(
                'status' => 1,
                'html' => $this->render_partial('_icon', array(
                    'data' => $model->export(),
                    'social' => ig_social_wall()->social($model->name)
                ), false)
            ));
        } else {
            echo json_encode(array(
                'status' => 0,
                'errors' => implode('<br/>', $model->get_errors())
            ));
        }
        exit;
    }

    function social_form()
    {
        if (!wp_verify_nonce(ig_social_wall()->get('_wpnonce'), 'social_wall_form')) {
            return;
        }
        $id = ig_social_wall()->get('id', 0);
        $model = Social_Wall_Model::model()->get_one($id, ig_social_wall()->get('parent_id', 0));
        if (!is_object($model)) {
            $model = null;
        }
        $this->render('form', array(
            'model' => $model,
            'social' => !is_null($model) ? ig_social_wall()->social($model->name) : null
        ));
        die;
    }

    public function display($parent, $attributes, $element)
    {
        wp_enqueue_style('jbp-social');
        wp_enqueue_script('webuipopover');
        wp_enqueue_style('webuipopover');
        wp_enqueue_script('jquery-ui-tooltip');
        $models = array();
        $socials = explode(',', $parent->$attributes);
        foreach ($socials as $social) {
            $model = Social_Wall_Model::model()->get_one($social, $parent->id);
            if (is_object($model)) {
                $models[] = $model;
            }
        }
        $this->render('main', array(
            'models' => $models,
            'parent' => $parent,
            'element' => $element
        ));
    }

    public function show_front($parent, $attributes){
        wp_enqueue_style('jbp-social');
        wp_enqueue_script('jquery-ui-tooltip');
        $models = array();
        $socials = explode(',', $parent->$attributes);
        foreach ($socials as $social) {
            $model = Social_Wall_Model::model()->get_one($social, $parent->id);
            if (is_object($model)) {
                $models[] = $model;
            }
        }
        $this->render('front', array(
            'models' => $models,
            'parent' => $parent
        ));
    }
}