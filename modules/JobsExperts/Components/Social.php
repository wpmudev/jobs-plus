<?php

/**
 * Author: WPMUDEV
 */
class JobsExpert_Components_Social extends JobsExperts_Components
{
    public function __construct()
    {
        $this->_add_action('admin_enqueue_scripts', 'scripts');
        $this->_add_action('wp_enqueue_scripts', 'scripts');
        $this->_add_ajax_action('jbp_social_add', 'add_social');
        $this->_add_ajax_action('jbp_social_remove', 'remove_social');
        //$this->_add_action('save_post_jbp_pro', 'after_parent_save', 10, 3);
    }

    function after_parent_save($post_ID, $post, $update)
    {
        if (in_array($post->post_status, array('pending', 'publish', 'draft'))) {
            //cleanup
            $parent = JobsExperts_Core_Models_Pro::instance()->get_one($post_ID);
            var_dump($parent);
            $socials = array_filter(array_unique(explode(',', $parent->social)));
            var_dump($socials);
            $tmp_socials = JobsExperts_Components_Social_Model::instance()->get_all($parent->id);
            //loop and remove the not saved
            foreach ($tmp_socials as $r) {
                var_dump($r);
                if (!in_array($r->name, $socials)) {
                    //$r->delete();
                }
            }
            die;
        }
    }

    function add_social()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'jbp_social_add')) {
            $model = JobsExperts_Components_Social_Model::instance()->get_one($_POST['name'], $_POST['parent_id']);

            if (!is_object($model)) {
                $model = new JobsExperts_Components_Social_Model();
            }
            $model->name = $_POST['name'];
            $model->value = $_POST['value'];
            $social = $this->social($model->name);
            $model->type = $social['type'];
            $model->parent_id = $_POST['parent_id'];
            $model->status = 0;

            if ($model->validate()) {
                $model->save();

                echo json_encode(array(
                    'status' => 1,
                    'html' => $this->template_social_display($model->export())
                ));
            } else {
                echo json_encode(array(
                    'status' => 0,
                    'errors' => implode('<br/>', $model->get_errors())
                ));
            }
        }
        exit;
    }

    function remove_social()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'jbp_social_remove')) {
            $model = JobsExperts_Components_Social_Model::instance()->get_one($_POST['name'], $_POST['parent_id']);
            if (is_object($model)) {
                $model->delete();
            }
            exit;
        }
    }

    function template_social_display($data)
    {
        ob_start();
        $social = $this->social($data['name']);
        $url = '#';
        if ($data['type'] == 'url') {
            $url = $data['value'];
        } elseif ($data['type'] == 'email') {
            $url = 'mailto:' . $data['value'];
        } else {
            $url = '#' . $data['value'];
        }

        ?>
        <a href="<?php echo $url ?>" data-id="<?php echo $data['name'] ?>"
           data-value="<?php echo esc_attr($data['value']) ?>"
           data-type="<?php echo $social['type'] ?>" class="jbp-social"
           data-toggle="tooltip" data-placement="auto"
           title="<?php echo $social['name'] . ' | ' . $data['value'] ?>">
            <img src="<?php echo $social['url'] ?>">
        </a>
        <?php
        return preg_replace('/^\s+|\n|\r|\s+$/m', '', ob_get_clean());
    }

    function scripts()
    {
        $plugin = JobsExperts_Plugin::instance();
        wp_register_style('jbp-social', $plugin->_module_url . 'Components/Social/style.css');
    }

    public function get_social_list()
    {
        $list = array();
        foreach (glob(JobsExperts_Plugin::instance()->_module_path . 'Components/Social/social_icon/*.png') as $file) {
            $list[pathinfo($file, PATHINFO_FILENAME)] = array(
                'key' => pathinfo($file, PATHINFO_FILENAME),
                'name' => ucfirst(pathinfo($file, PATHINFO_FILENAME)),
                'url' => JobsExperts_Plugin::instance()->_module_url . 'Components/Social/social_icon/' . pathinfo($file, PATHINFO_BASENAME)
            );
        }
        $plugin = JobsExperts_Plugin::instance();

        /*        echo '<pre>';
                var_export($list);
                echo '</pre>';*/
        $social_list = array(
            'blogger' =>
                array(
                    'key' => 'blogger',
                    'name' => 'Blogger',
                    'domain' => 'blogger.com',
                    'type' => 'url',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/blogger.png',
                ),
            'deviantart' =>
                array(
                    'key' => 'deviantart',
                    'name' => 'Deviantart',
                    'domain' => 'deviantart.com',
                    'type' => 'url',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/deviantart.png',
                ),
            'digg' =>
                array(
                    'key' => 'digg',
                    'name' => 'Digg',
                    'type' => 'url',
                    'domain' => 'digg.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/digg.png',
                ),
            'dribble' =>
                array(
                    'key' => 'dribble',
                    'name' => 'Dribble',
                    'type' => 'url',
                    'domain' => 'dribbble.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/dribble.png',
                ),
            'dropbox' =>
                array(
                    'key' => 'dropbox',
                    'name' => 'Dropbox',
                    'type' => 'url',
                    'domain' => 'dropbox.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/dropbox.png',
                ),
            'email' =>
                array(
                    'key' => 'email',
                    'name' => 'Email',
                    'type' => 'email',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/email.png',
                ),
            'engadget' =>
                array(
                    'key' => 'engadget',
                    'name' => 'Engadget',
                    'type' => 'url',
                    'domain' => 'engadget.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/engadget.png',
                ),
            'fb' =>
                array(
                    'key' => 'fb',
                    'name' => 'Facebook',
                    'type' => 'url',
                    'domain' => 'facebook.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/fb.png',
                ),
            'flickr' =>
                array(
                    'key' => 'flickr',
                    'name' => 'Flickr',
                    'type' => 'url',
                    'domain' => 'flickr.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/flickr.png',
                ),
            'google+' =>
                array(
                    'key' => 'google+',
                    'name' => 'Google Plus',
                    'type' => 'url',
                    'domain' => 'google.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/google+.png',
                ),
            'google_hangouts' =>
                array(
                    'key' => 'google_hangouts',
                    'name' => 'Google Hangouts',
                    'type' => 'text',
                    'domain' => '',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/google_hangouts.png',
                ),
            'instagram' =>
                array(
                    'key' => 'instagram',
                    'name' => 'Instagram',
                    'type' => 'url',
                    'domain' => 'instagram.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/instagram.png',
                ),
            'linkedin' =>
                array(
                    'key' => 'linkedin',
                    'name' => 'Linkedin',
                    'domain' => 'linkedin.com',
                    'type' => 'url',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/linkedin.png',
                ),
            'myspace' =>
                array(
                    'key' => 'myspace',
                    'name' => 'Myspace',
                    'type' => 'url',
                    'domain' => 'myspace.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/myspace.png',
                ),
            'pinterest' =>
                array(
                    'key' => 'pinterest',
                    'name' => 'Pinterest',
                    'type' => 'url',
                    'domain' => 'pinterest.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/pinterest.png',
                ),
            'reddit' =>
                array(
                    'key' => 'reddit',
                    'name' => 'Reddit',
                    'type' => 'url',
                    'domain' => 'reddit.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/reddit.png',
                ),
            'rss' =>
                array(
                    'key' => 'rss',
                    'name' => 'Rss',
                    'type' => 'url',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/rss.png',
                ),
            'skype' =>
                array(
                    'key' => 'skype',
                    'name' => 'Skype',
                    'type' => 'text',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/skype.png',
                ),
            'trillian' =>
                array(
                    'key' => 'trillian',
                    'name' => 'Trillian',
                    'type' => 'url',
                    'domain'=>'trillian.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/trillian.png',
                ),
            'tumblr' =>
                array(
                    'key' => 'tumblr',
                    'name' => 'Tumblr',
                    'type' => 'url',
                    'domain' => 'tumblr.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/tumblr.png',
                ),
            'twitter' =>
                array(
                    'key' => 'twitter',
                    'name' => 'Twitter',
                    'type' => 'url',
                    'domain' => 'twitter.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/twitter.png',
                ),
            'wordpress' =>
                array(
                    'key' => 'wordpress',
                    'name' => 'Wordpress',
                    'type' => 'url',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/wordpress.png',
                ),
            'xda' =>
                array(
                    'key' => 'xda',
                    'name' => 'Xda',
                    'type' => 'url',
                    'domain' => 'xda-developers.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/xda.png',
                ),
            'yahoo' =>
                array(
                    'key' => 'yahoo',
                    'name' => 'Yahoo',
                    'type' => 'text',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/yahoo.png',
                ),
            'yelp' =>
                array(
                    'key' => 'yelp',
                    'name' => 'Yelp',
                    'type' => 'url',
                    'domain' => 'yelp.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/yelp.png',
                ),
            'youtube' =>
                array(
                    'key' => 'youtube',
                    'name' => 'Youtube',
                    'type' => 'url',
                    'domain' => 'youtube.com',
                    'url' => $plugin->_module_url . '/Components/Social/social_icon/youtube.png',
                ),
        );
        return apply_filters('get_social_list', $social_list);
    }

    public function load_scripts()
    {
        wp_enqueue_style('jbp-social');
    }

    public function social($key)
    {
        $list = $this->get_social_list();
        return $list[$key];
    }
}

global $jbp_component_social;
$jbp_component_social = new JobsExpert_Components_Social();