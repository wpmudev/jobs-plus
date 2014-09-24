<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Components_Uploader_Model extends JobsExperts_Framework_PostModel
{
    public $id;
    public $file;
    public $url;
    public $description;
    public $parent_id;

    public function storage_name()
    {
        return 'jbp_media';
    }

    public function prepare_load_data(WP_Post $post)
    {
        $this->id = $post->ID;
        $this->description = $post->post_content;
        $this->parent_id = $post->post_parent;
        $this->url = get_post_meta($post->ID, '_sample_link', true);
        $this->file = get_post_meta($post->ID, '_file', true);
    }

    public function prepare_import_data()
    {
        //core data
        $args = array(
            'post' => array(
                'ID' => !$this->is_new_record() ? $this->id : null,
                'post_title' => $this->name(),
                'post_content' => $this->description,
                'post_status' => 'publish',
                'post_type' => $this->storage_name(),
                'ping_status' => 'closed',
                'comment_status' => 'closed',
                'post_parent' => $this->parent_id
            ),
            'meta' => array(
                '_sample_link' => $this->url
            )
        );

        return $args;
    }

    public function after_save()
    {
        //do the upload
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        if (!empty($this->file)) {
            if (is_array($this->file)) {
                $media_id = media_handle_sideload($this->file, $this->parent_id, $this->description, array(
                    'post_status' => 'inherit'
                ));
                update_post_meta($this->id, '_file', $media_id);
                $this->file = $media_id;
                //update post title
                $post = get_post($this->parent_id);
                $post->post_title = $this->file['name'];
                wp_update_post($post->to_array());
            }else{
                update_post_meta($this->id, '_file', $this->file);
                $post = get_post($this->parent_id);
                $post->post_title = $this->name();
                wp_update_post($post->to_array());
            }
        }
    }

    public function rules()
    {
        return array(
            array('url', 'url')
        );
    }

    public function name()
    {
        if ($this->is_new_record()) {
            if (!empty($this->file)) {
                if (is_array($this->file)) {
                    return $this->file['name'];
                } else {
                    return pathinfo(wp_get_attachment_url($this->file), PATHINFO_BASENAME);
                }
            } else {
                return __('Link', JBP_TEXT_DOMAIN);
            }
        } else {
            $post = get_post($this->id);
            if (!empty($this->file)) {
                return pathinfo(wp_get_attachment_url($this->file), PATHINFO_BASENAME);
            } else {
                return __('Link', JBP_TEXT_DOMAIN);
            }
        }
    }

    public function addition_validate()
    {
        if ($this->is_new_record()) {
            if (empty($this->file) && empty($this->url)) {
                $this->set_error('id', __('You must upload a <strong>file</strong> or set an <strong>url</strong>', JBP_TEXT_DOMAIN));
            }

            if (!empty($this->file)) {
                if (is_array($this->file)) {
                    //validate files
                    $allowed = array_values(get_allowed_mime_types());

                    if (!in_array($this->file['type'], $allowed)) {
                        $this->set_error('file', __('File type not allow', JBP_TEXT_DOMAIN));
                    } elseif (jbp_format_bytes($this->file['size']) > get_max_file_upload() * 1000000) {
                        $this->set_error('file', __('File too large!', JBP_TEXT_DOMAIN));
                    }
                } else {

                }
            }

        }
    }

    public function mime_to_icon($mime = '')
    {
        if (empty($mime)) {
            $mime = get_post_mime_type($this->file);
        }
        $type = explode('/', $mime);
        $type = array_shift($type);
        $image = '';
        switch ($type) {
            case 'image':
                $image = '<i class="glyphicon glyphicon-picture"></i>';
                break;
            case 'video':
                $image = '<i class="glyphicon glyphicon-film"></i>';
                break;
            case 'text':
                $image = '<i class="glyphicon glyphicon-font"></i>';
                break;
            case 'audio':
                $image = '<i class="glyphicon glyphicon-volume-up"></i>';
                break;
            case 'application':
                $image = '<i class="glyphicon glyphicon-hdd"></i>';
                break;
        }

        if (empty($image)) {
            if (!empty($this->url)) {
                $image = '<i class="glyphicon glyphicon-globe"></i>';
            } else {
                $image = '<i class="glyphicon glyphicon-file"></i>';
            }
        }

        return $image;
    }
}