<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Components_Uploader_Model extends JobsExperts_Framework_PostModel
{
    public $id;
    public $file;
    public $url;
    public $description;
    public $parent_id;

    public $file_upload;

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

    /**
     * After the attachment saved, we will check and upload file if nessesary for frontend,
     * backend, already uploaded and only return the wordpress attachment id
     */
    public function after_save()
    {
        if (is_admin()) {
            if (filter_var($this->file, FILTER_VALIDATE_INT)) {
                //case file pass, save it
                update_post_meta($this->id, '_file', $this->file);
                //refresh the name
                $post = get_post($this->id);
                $post->post_title = pathinfo(wp_get_attachment_url($this->file), PATHINFO_BASENAME);
                wp_update_post($post->to_array());
            }
        } else {
            //because the ealier we already check valid file or not, so no need to validate again
            if (is_array($this->file_upload)) {
                //do the upload
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                $media_id = media_handle_sideload($this->file_upload, $this->parent_id, $this->description, array(
                    'post_status' => 'inherit'
                ));
                if (is_wp_error($media_id)) {
                    //oh, error here, we need to log it
                    JobsExperts_Core_Models_Logger::log(serialize(array(
                        'scenario' => 'upload',
                        'object' => $this->file,
                        'msg' => $media_id->get_error_message()
                    )));
                    //
                    return $media_id;
                } else {
                    //all good, store the value now
                    update_post_meta($this->id, '_file', $media_id);
                    //update post title
                    $post = get_post($this->id);
                    $post->post_title = $this->file_upload['name'];
                    wp_update_post($post->to_array());
                }
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
            if (is_array($this->file_upload) && isset($this->file_upload['name']) && !empty($this->file_upload['name'])) {
                return $this->file_upload['name'];
            } elseif (filter_var($this->file, FILTER_VALIDATE_INT)) {
                //id passed
                return pathinfo(wp_get_attachment_url($this->file), PATHINFO_BASENAME);
            } else {
                return __('Link', JBP_TEXT_DOMAIN);
            }
        } else {
            $post = get_post($this->id);
            return $post->post_title;
        }
    }

    /**
     * This is use for validate the attachment, the rule is, attachment must have
     * file or url
     *
     * In frontend, user need to upload a file, so as this state, we just need to validate does it exist or not
     *  and set the errors if exist
     * Inbackend, since we using the native media upload of wordpress, so this should an int
     *
     */
    public function addition_validate()
    {
        if (is_admin()) {
            if (empty($this->file) && empty($this->url)) {
                $this->set_error('id', __('You must upload a <strong>file</strong> or set an <strong>url</strong>', JBP_TEXT_DOMAIN));
            }
            //in frontend just need to validate this case, as the image must be attachment id
        } else {
            //empty both, set errors
            if (empty($this->file_upload) && empty($this->url)) {
                $this->set_error('id', __('You must upload a <strong>file</strong> or set an <strong>url</strong>', JBP_TEXT_DOMAIN));
            } elseif (is_array($this->file_upload) && isset($this->file_upload['tmp_name']) && is_uploaded_file($this->file_upload['tmp_name'])) {
                //case the file has been filled
                $allowed = array_values(get_allowed_mime_types());
                if (!in_array($this->file_upload['type'], $allowed)) {
                    $this->set_error('file_upload', __('File type not allow', JBP_TEXT_DOMAIN));
                } elseif (jbp_format_bytes($this->file_upload['size']) > get_max_file_upload() * 1000000) {
                    $this->set_error('file_upload', __('File too large!', JBP_TEXT_DOMAIN));
                }
            } elseif (empty($this->url)) {
                //the upload is not good, also, url is empty, throw error
                $this->set_error('id', __('Can not upload the <strong>file</strong>, also <strong>url</strong> is empty!', JBP_TEXT_DOMAIN));
            }
            //come to here,validate thing must be settled
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

	public static function instance( $class = __CLASS__ ) {
		return parent::instance( $class );
	}
}