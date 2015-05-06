<?php

/**
 * @author:Hoang Ngo
 */
class JE_Expert_Single_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_action('wp_ajax_expert_like', array(&$this, 'expert_like'));
        add_shortcode('jbp-job-pro-page', array(&$this, 'main'));
    }

    function expert_like()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'expert_like')) {
            $id = $_POST['id'];
            $model = JE_Expert_Model::model()->find($id);
            if (is_object($model)) {
                $user = @get_user_by('id', $_POST['user_id']);
                if ($user instanceof WP_User) {
                    if ($model->is_current_user_can_like($user->ID)) {
                        add_user_meta($user->ID, 'jbp_pro_liked', $model->id);
                        //update pro like
                        update_post_meta($model->id, 'jbp_pro_like_count', $model->get_like_count() + 1);
                        echo $model->get_like_count();
                    }
                }
            }
        }
        exit;
    }

    function main($atts)
    {
        je()->load_script('expert');
        $model = JE_Expert_Model::model()->find(get_the_ID());
        $model->add_view_count();
        return $this->render('expert-single/main', array(
            'model' => $model
        ), false);
    }
}