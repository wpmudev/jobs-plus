<?php

/**
 * Name: Job Demo Data
 * Description: Create random job records, for testing purposes.
 * Author: WPMU DEV
 */
class JE_Job_Demo_Data
{
    public function __construct()
    {
        if (current_user_can('manage_options')) {
            add_action('jbp_setting_menu', array(&$this, 'menu'));
            add_action('je_settings_content_job_demo_data', array(&$this, 'content'));
            add_action('wp_ajax_create_demo_job', array(&$this, 'generate_data'));
            add_action('wp_ajax_check_create_demo_job', array(&$this, 'check_status'));
        }
    }

    function generate_data()
    {
        if (!wp_verify_nonce(je()->post('_nonce'), 'create_demo_job')) {
            return;
        }
        delete_option('jobs_demo_status');
        $data = $_POST['data'];
        parse_str($data, $data);
        $qty = $data['dummy_job_qty'];
        $prices = $data['dummy_job_price_range'];
        $skills = $data['dummy_skills'];
        $categories = $data['dummy_category'];
        $sample = isset($data['have_sample']) ? true : false;

        for ($i = 1; $i <= $qty; $i++) {
            $this->_generate(explode('-', $prices), $categories, $skills, $sample);

            //cal percent
            $percent = ($i / $qty) * 100;
            update_option('jobs_demo_status', $percent);
        }
    }

    function _generate($budgets, $categories, $skills, $have_file)
    {
        $open_for = array(3, 7, 14, 21);
        $weeks = array(1, 2, 3, 4);

        $model = new JE_Job_Model();
        $model->job_title = $this->content_bank('title');
        $model->description = $this->content_bank('content');
        $model->budget = rand($budgets[0], $budgets[1]);
        $model->min_budget = rand($budgets[0], $budgets[1]);
        $model->max_budget = rand($model->min_budget, $budgets[1]);
        $model->status = 'publish';
        $model->contact_email = $this->content_bank('email');
        $model->open_for = $open_for[array_rand($open_for)];
        $model->dead_line = date('Y-m-d', strtotime('+' . $weeks[array_rand($weeks)] . ' week'));
        $model->owner = get_current_user_id();
        //save dummy data
        //categories
        $categories = explode(',', $categories);
        $categories = array_filter($categories);
        $model->categories = ($categories[array_rand($categories)]);
        $skills = explode(',', $skills);
        $skills = array_filter($skills);
        $tmp = array_rand($skills, 3);
        $rand_skills = array();
        foreach ($tmp as $t) {
            $rand_skills[] = $skills[$t];
        }

        $model->skills = (implode(',', $rand_skills));
        $model->save();
        if ($have_file) {
            //random generate 3 files
            $ids = array();
            for ($i = 0; $i < 3; $i++) {
                //get the random image
                $upload_dir = wp_upload_dir();
                $path = $upload_dir['path'] . '/' . uniqid() . '.jpg';
                $image_path = $this->content_bank('image');
                //download the image
                //$this->download_image($image_url, $path);
	            copy($image_path,$path);
                //now handler the file
                $att_id = $this->handler_upload($model->id, $path);
                //create media post type
                $media = new IG_Uploader_Model();
                $media->content = jbp_filter_text($this->content_bank('scontent'));
                $media->file = $att_id;
                $media->url = 'http://wpmudev.org';
                $media->attach_to = $model->id;
                $media->save();
                //update_post_meta($media->id, '_file', $att_id);

                $ids[] = $media->id;
            }
            $model->portfolios = implode(',', $ids);
            $model->save();
        }
    }

    function handler_upload($parent_post_id, $filename)
    {
        // Check the type of tile. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype(basename($filename), null);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    function download_image($url, $path)
    {
        $ch = curl_init($url);
        $fp = fopen($path, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }


    function content_bank($type)
    {
        $data = file_get_contents(dirname(__FILE__) . '/je-job-demo/data.json');
        $data = json_decode($data, true);

        switch ($type) {
            case 'title':
                $titles = $data['titles'];

                return $titles[array_rand($titles)];
            case 'content':
                $c = $data['contents'];

                return $c[array_rand($c)];
            case 'scontent':
                $c = $data['short_contents'];

                return $c[array_rand($c)];
            case 'email':
                $c = $data['emails'];

                return $c[array_rand($c)];
            case 'image':
	            $c = rand( 1, 10 );
	            return dirname( __FILE__ ) . '/je-job-demo/demo_images/' . $c . '.jpg';
        }
    }


    function check_status()
    {
        $status = get_option('jobs_demo_status');
        echo $status;
        exit;
    }

    function menu()
    {
        ?>
        <li <?php echo je()->get('tab') == 'job_demo_data' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job_demo_data') ?>">
                <i class="dashicons dashicons-star-filled"></i> <?php _e('Jobs Demo Data', je()->domain) ?>
            </a></li>
    <?php
    }

    function content()
    {
        ?>
        <form method="post" id="job-demo-data">
            <fieldset>
                <div class="page-header" style="margin-top: 0">
                    <h4><?php _e('Create dummy data for jobs', je()->domain) ?></h4>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Dummy jobs amount', je()->domain) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="10" name="dummy_job_qty" class="form-control">

                        <p class="help-block"><?php _e('Number of dummy jobs to create', je()->domain) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Price Range', je()->domain) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="0-2000" name="dummy_job_price_range" class="form-control">

                        <p class="help-block"><?php _e('Range of price') ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Categories', je()->domain) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="Wordpress,Buddypress,General,WPMUDEV" name="dummy_category"
                               class="form-control">

                        <p class="help-block"><?php _e('Demo job categories, separated by commas. Will be randomly assigned.', je()->domain) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Skill tags', je()->domain) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="Html5,PHP,MySQL,jQuery,Javascript,Css3,Media," name="dummy_skills"
                               class="form-control">

                        <p class="help-block"><?php _e('Demo job skills, separated by commas. Will be randomly assigned.', je()->domain) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Have Sample Files', je()->domain) ?></label>

                    <div class="col-md-9">
                        <p class="help-block">
                            <input type="checkbox" checked="checked" name="have_sample">
                            <?php _e('If you want these demo jobs to have sample files, check this box.', je()->domain) ?>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="demo_status hide">
                    <p><?php _e('Creating demo data,please wait....') ?></p>

                    <div class="progress">
                        <div class="progress-bar progress-bar-striped " role="progressbar" style="width: 0%">
                            <span class="sr-only">0%</span>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 col-md-offset-3">
                        <button type="submit" class="btn btn-primary"><?php _e("Submit", je()->domain) ?></button>
                    </div>
                </div>
            </fieldset>
        </form>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#job-demo-data').submit(function () {
                    var interval = '';
                    $.ajax({
                        type: 'POST',
                        data: {
                            data: $(this).serialize(),
                            _nonce: '<?php echo wp_create_nonce('create_demo_job') ?>',
                            action: 'create_demo_job'
                        },
                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                        beforeSend: function () {
                            $('.demo_status').removeClass('hide');
                            //reset data
                            $('.progress-bar').text(1 + '%').css('width', 1 + '%');
                            $('.demo_status').find('p').text('Creating demo data,please wait....');
                            //triger load status
                            interval = setInterval(function () {
                                $.ajax({
                                    type: 'POST',
                                    data: {
                                        action: 'check_create_demo_job'
                                    },
                                    url: '<?php echo admin_url('admin-ajax.php') ?>',
                                    success: function (data) {
                                        if (data.length) {
                                            if (data == 100) {
                                                clearInterval(interval);
                                                $('.demo_status').find('p').text('Done!');
                                                $('.progress-bar').text(data + '%').css('width', data + '%');
                                            } else {
                                                $('.progress-bar').text(data + '%').css('width', data + '%');
                                            }
                                        }

                                    }
                                })
                            }, 3000);
                        },
                        success: function (data) {

                        }
                    })
                    return false;
                });
            })
        </script>
    <?php
    }
}

new JE_Job_Demo_Data();