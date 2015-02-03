<?php

/**
 * Name: Expert Geo Location
 * Description: Let experts define their location.
 * Author: WPMU DEV
 */
class JE_Custom_Location extends IG_Request
{
    public function __construct()
    {
        add_filter('ig_view_file', array(&$this, 'custom_view'), 10, 2);
        add_filter('je_expert_form_location_field', array(&$this, 'location_span'), 10, 2);
        add_action('wp_footer', array(&$this, 'footer_script'));
        add_action('jbp_setting_menu', array(&$this, 'menu'));
        add_action('je_settings_content_custom_location', array(&$this, 'content'));
        add_action('wp_loaded', array(&$this, 'save_setting'));
        add_action('wp_ajax_geo_reverser', array(&$this, 'get_address'));
        add_filter('je_expert_get_location', array(&$this, 'expert_location'), 10, 2);
    }

    function expert_location($country, $model)
    {
        return $model->location;
    }

    function save_setting()
    {
        if (!wp_verify_nonce(je()->post('_je_location_setting'), 'je_settings')) {
            return '';
        }

        if (je()->post('api', null) !== null) {
            $long = '108.04362019999999';
            $lat = '12.687538199999999';
            $geo = $this->reverse_geo($long, $lat);
            if ($geo['status'] == true) {
                je()->get_logger()->log(var_export($geo, true));
                update_option('je_custom_location_google_api', je()->post('api'));;
                $this->set_flash('key', __("The API KEY has been saved successful!"));
                wp_redirect($_SERVER['REQUEST_URI']);
                exit;
            } else {
                je()->global['geo_error'] = $geo['error'];
            }
        }
    }

    function menu()
    {
        ?>
        <li <?php echo je()->get('tab') == 'custom_location' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=custom_location') ?>">
                <i class="fa fa-crosshairs"></i> <?php _e('Custom Expert Location', je()->domain) ?>
            </a></li>
    <?php
    }

    function content()
    {
        $api = get_option('je_custom_location_google_api');
        ?>

        <form method="post">
            <div class="page-header" style="margin-top: 0">
                <h3><?php _e('Custom expert location', je()->domain) ?></h3>
            </div>
            <?php if ($this->has_flash('key')): ?>
                <div class="alert alert-success">
                    <?php echo $this->get_flash('key') ?>
                </div>
            <?php endif; ?>
            <?php if (isset(je()->global['geo_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo je()->global['geo_error'] ?>
                </div>
            <?php endif; ?>
            <p><?php echo sprintf(__("To enable <strong>reverse geocoding</strong>, you'll need to input a valid <a href=\"%s\">API key</a> from Google."),
                    "https://developers.google.com/console/help/#generatingdevkeys") ?></p>
            <label><?php _e("API Key ", je()->domain) ?></label>&nbsp;
            <input value="<?php echo $api ?>" type="text" name="api"/>

            <div class="clearfix"></div>
            <br/>
            <?php wp_nonce_field('je_settings', '_je_location_setting') ?>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><?php _e("Save Changes", je()->domain) ?></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    <?php
    }

    function location_span($html, $model)
    {
        $html = '<span class="can-edit custom-location" data-placement="top-left" data-type="location">';
        $location = !empty($model->location) ? $model->location : __("Your Location", je()->domain);
        $html .= $location;
        $html .= '</span>';

        return $html;
    }

    function footer_script()
    {
        $api = get_option('je_custom_location_google_api');
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                $('body').on('je_expert_popup_form_location', function (event, element, form, data) {
                    element.html(form.find('input[name="location"]').val());
                    $('#je_expert_model-location').val(data.location);
                });
                <?php if($api): ?>
                if (navigator.geolocation) {
                    $('.location-detect').removeAttr('disabled');
                    $('body').on('click', '.location-detect', function () {
                        navigator.geolocation.getCurrentPosition(showPosition);
                    })
                }
                function showPosition(position) {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        data: {
                            action: 'geo_reverser',
                            _wpnonce: '<?php echo wp_create_nonce('geo_reverser') ?>',
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        },
                        beforeSend: function () {
                            $('.location-detect').attr('disabled', 'disabled');
                        },
                        success: function (data) {
                            $('.location-detect').removeAttr('disabled');
                            if (data.status == 'success') {
                                $('.clocation').val(data.address);
                            } else {
                                alert(data.error);
                            }
                        }
                    });
                }

                <?php endif; ?>
            });
        </script>
    <?php
    }

    function get_address()
    {
        if (!wp_verify_nonce(je()->post('_wpnonce'), 'geo_reverser')) {
            return;
        }
        $result = $this->reverse_geo(je()->post('lng'), je()->post('lat'));
        if ($result['status'] == true) {
            $address = $result['result'][0];
            wp_send_json(array(
                'status' => 'success',
                'address' => $address['formatted_address']
            ));
        } else {
            wp_send_json(array(
                'status' => 'fail',
                'error' => $result['error']
            ));
        }
        die;
    }

    function reverse_geo($long, $lat)
    {
        $api = $api = get_option('je_custom_location_google_api');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$long&key=$api";
        $result = wp_remote_get($url);
        if (!is_wp_error($result)) {
            $result = json_decode($result['body'], true);
            if (isset($result['error_message'])) {
                return array(
                    'status' => false,
                    'error' => $result['error_message']
                );
            } else {
                return array(
                    'status' => true,
                    'result' => $result['results']
                );
            }
        } else {
            return array(
                'status' => false,
                'error' => $result->get_error_message()
            );
        }
    }

    function custom_view($view_path, $view_name)
    {
        if ($view_name == 'expert-form/_location_popup') {
            $new_view = plugin_dir_path(__FILE__) . 'je-custom-location/_location_popup.php';
            return $new_view;
        }
        return $view_path;
    }
}

new JE_Custom_Location();