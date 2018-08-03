<?php

/**
 * @author:Hoang Ngo
 */
class JE_Expert_Model extends IG_Post_Model
{
    public $id;
    public $name;
    public $biography;
    public $short_description;
    public $user_id;

    public $first_name;
    public $last_name;
    public $company;
    public $company_url;
    public $location;
    public $contact_email;
    public $social;
    public $skills;
    public $have_gavatar;
    public $views_count;
    public $likes_count;

    public $portfolios;

    public $status;
    public $text_domain = 'jbp';
    protected $table = 'jbp_pro';

    protected $defaults = array(
        'ping_status' => 'closed',
        'comment_status' => 'closed'
    );

    protected $mapped = array(
        'id' => 'ID',
        'name' => 'post_title',
        'user_id' => 'post_author',
        'biography' => 'post_content',
        'status' => 'post_status',
    );

    protected $relations = array(
        array(
            'type' => 'meta_array',
            'key' => '_ct_jbp_pro_First_Last',
            'map' => 'first_name|last_name',
            'array_key' => 'first|last',
            'format' => 'serialize'
        ),
        array(
            'type' => 'meta_array',
            'key' => '_ct_jbp_pro_Company_URL',
            'map' => 'company|company_url',
            'array_key' => 'link|url',
            'format' => 'json'
        ),
        array(
            'type' => 'meta',
            'key' => '_ct_jbp_pro_Location',
            'map' => 'location'
        ),
        array(
            'type' => 'meta',
            'key' => '_ct_jbp_pro_Contact_Email',
            'map' => 'contact_email'
        ),
        array(
            'type' => 'meta',
            'key' => '_ct_jbp_pro_short_description',
            'map' => 'short_description'
        ),
        array(
            'type' => 'meta',
            'key' => '_ct_jbp_pro_Social',
            'map' => 'social'
        ),
        array(
            'type' => 'meta',
            'key' => '_ct_jbp_pro_Skills',
            'map' => 'skills'
        ),
        array(
            'type' => 'meta',
            'key' => '_jbp_pro_portfolios',
            'map' => 'portfolios'
        ),
        array(
            'type' => 'meta',
            'key' => 'jbp_pro_view_count',
            'map' => 'views_count'
        ),
        array(
            'type' => 'meta',
            'key' => 'jbp_pro_like_count',
            'map' => 'likes_count'
        ),
    );

    public function __construct()
    {
        $this->virtual_attributes = apply_filters('je_expert_additions_field', $this->virtual_attributes);
        $this->relations = apply_filters('je_expert_relations', $this->relations);
        $this->mapped = apply_filters('je_expert_fields_mapped', $this->mapped);
        $this->defaults = apply_filters('je_expert_default_fields', $this->defaults);
    }

    public function before_validate()
    {
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'location' => 'required',
            'contact_email' => 'required|valid_email',
            'biography' => 'required|min_len,200',
            'company_url' => 'valid_url',
            'short_description' => 'max_len,100'
        );
        $this->rules = apply_filters('je_expert_validation_rules', $rules);
        $fields_text = array();
        $fields_text = apply_filters('je_expert_field_name', $fields_text);
        foreach ($fields_text as $key => $text) {
            GUMP::set_field_name($key, $text);
        }
    }

    public function before_save()
    {
        do_action('je_expert_before_save', $this);
        $this->defaults = array_merge($this->defaults, array(
            'post_name' => sanitize_title($this->name)
        ));
    }

    public function get_view_count()
    {
        if( $this->_alternate_view_count_method() ) {
            return intval( get_post_meta( $this->id, 'jbp_pro_alt_view_count', true ) );
        }else{
            return intval( get_post_meta( $this->id, 'jbp_pro_view_count', true ) );
        }
    }

    public function get_like_count()
    {
        return intval(get_post_meta($this->id, 'jbp_pro_like_count', true));
    }

    public function is_current_user_can_like($id = '')
    {
        if (empty($id)) {
            $id = get_current_user_id();
        }

        $user = get_user_by('id', $id);
        if ($user instanceof WP_User) {
            $likes = get_user_meta(get_current_user_id(), 'jbp_pro_liked');
            $has_like = false;
            foreach ($likes as $like) {
                if ($like == $this->id) {
                    $has_like = true;
                    break;
                }
            }

            return !$has_like;
        }

        return false;
    }

    public function get_avatar($size = 640, $use_ratio = false)
    {
        $avatar = get_post_meta($this->id, '_expert_avatar', true);
        if ($avatar) {
            $name = pathinfo($avatar, PATHINFO_FILENAME);
            $upload_dir = wp_upload_dir();
            //if avatar is url, convert to system path

            $apath = str_replace($upload_dir['baseurl'], '', $avatar);
            $apath = $upload_dir['basedir'] . $apath;

            $image = wp_get_image_editor($apath);
            if (!is_wp_error($image)) {
                //ratio
                $isize = @$image->get_size();

                $width = $size;
                $ratio = $isize['width'] / $isize['height'];
                $height = $width / $ratio;
                if ($use_ratio == false) {
                    //we will create round image
                    if ($ratio < 1) {
                        $width = $height;
                    } else {
                        $height = $width;
                    }
                }

                $new_path = $upload_dir['path'] . '/' . $name . '_' . $width . '-' . $height . '.jpg';
                $new_url = $upload_dir['url'] . '/' . $name . '_' . $width . '-' . $height . '.jpg';

                $is_overwrite = false;

                if (file_exists($new_path)) {
                    $is_overwrite = false;
                }

                $is_overwrite = true;

                if (!$is_overwrite) {
                    return '<img src="' . $new_url . '"/>';
                } else {
                    $image->resize($width, $width, true);
                    $image->save($new_path);

                    return '<img src="' . $new_url . '"/>';
                }
            }

        }

        return get_avatar($this->contact_email, $size);
    }

    public function add_view_count()
    {

        if( $this->_alternate_view_count_method() ) {
            if( ! isset( $_COOKIE['jbp_pro_alt_view_count_cookie'] ) ) {
                $view = get_post_meta( $this->id, 'jbp_pro_alt_view_count', true );
                if( ! isset( $view ) ) $view = 0;
                update_post_meta( $this->id, 'jbp_pro_alt_view_count', $view + 1 );

                $cookie_duration = apply_filters( 'je_view_count_cookie_duration', HOUR_IN_SECONDS );
                setcookie( 'jbp_pro_alt_view_count_cookie', 1, time() + $cookie_duration, "/" );
            }
        }

        $all_views = array_filter(get_post_meta($this->id, '_jbp_pro_view_count'));

        //gather information
        $view = array(
            'ip' => md5( $_SERVER['REMOTE_ADDR'] ),
            'user_id' => is_user_logged_in() ? get_current_user_id() : 0,
            'date_view' => date('Y-m-d H:i:s')
        );

        //if the author viewing, don't count
        if ($this->user_id == $view['user_id']) {
            return;
        }

        if (empty($all_views)) {
            //nothing added, no need to check
            add_post_meta($this->id, '_jbp_pro_view_count', $view);
            $view = count($all_views);
            update_post_meta($this->id, 'jbp_pro_view_count', $view + 1);

            return;
        }

        $can_add = false;

        foreach ($all_views as $v) {
            if ($v['ip'] == $view['ip']) {
                //check if this time avalable for add count
                if (strtotime('+24 hours', strtotime($v['date_view'])) <= time()) {
                    $can_add = true;
                    break;
                }
            }
        }

        if ($can_add == true) {
            add_post_meta($this->id, '_jbp_pro_view_count', $view);
            $view = count($all_views);
            update_post_meta($this->id, 'jbp_pro_view_count', $view + 1);
        }
    }

    private function _alternate_view_count_method() {
            return defined( 'JS_ALTERNATE_VIEW_COUNT' ) && JS_ALTERNATE_VIEW_COUNT;
    }

    function is_current_owner()
    {
        if (current_user_can('manage_options')) {
            return true;
        }

        if (get_current_user_id() == $this->user_id) {
            return true;
        }

        return false;
    }

    public function get_all_skills($refresh = false)
    {
        $data = wp_cache_get('jbp_pro_skill');
        if ($data && $refresh == false) {
            return $data;
        } else {
            global $wpdb;
            $sql = $wpdb->prepare('SELECT * FROM ' . $wpdb->postmeta . ' WHERE meta_key=%s', '_expert_skill');
            $raw = $wpdb->get_results($sql);
            $skills = array();
            foreach ($raw as $key => $val) {
                $row = maybe_unserialize($val->meta_value);
                if ($row) {
                    $skills[] = $row['name'];
                }

            }
            $skills = array_filter(array_unique($skills));
            sort($skills);
            $skills = array_map('trim', $skills);
            wp_cache_set('jbp_pro_skill', $skills, null, 3600);

            return $skills;
        }
    }

    function count()
    {
        global $wpdb;
        $sql = "SELECT count(ID) FROM " . $wpdb->posts . " WHERE post_type=%s AND post_status IN (%s,%s) AND post_author=%d";
        $result = $wpdb->get_var($wpdb->prepare($sql, 'jbp_pro', 'publish', 'draft', get_current_user_id()));
        return $result;
    }

    function has_avatar()
    {
        $avatar = get_post_meta($this->id, '_expert_avatar', true);
        if ($avatar) {
            return true;
        }

        return false;
    }

    function get_location()
    {
        $country = IG_Form::country();
        $location = isset($country[$this->location]) ? $country[$this->location] : $this->location;
        return apply_filters('je_expert_get_location', $location, $this);
    }

    function get_status() {
        $status = $this->status;
        if ( $status == 'publish' ) {
            $status = __( 'published', je()->domain );
        } elseif ( $status == 'pending' ) {
            $status = __( 'pending', je()->domain );
        } elseif ( $status == 'draft' ) {
            $status = __( "draft", je()->domain );
        }

        return $status;
    }

    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }
}