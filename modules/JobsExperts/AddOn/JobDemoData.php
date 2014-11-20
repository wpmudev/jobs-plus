<?php

/**
 * Name: Job Demo Data
 * Description: Create random job records, for testing purposes.
 * Author: WPMU DEV
 */
class JobsExpert_Compnents_JobDemoData extends JobsExperts_AddOn
{
    public function __construct()
    {
        $this->_add_action('jbp_setting_menu', 'menu');
        $this->_add_action('jbp_setting_content', 'content', 10, 2);
        $this->_add_action('jbp_after_save_settings', 'save_setting');

        $this->_add_ajax_action('create_demo_job', 'create_demo');
        $this->_add_ajax_action('check_create_demo_job', 'check_status');
    }

    function create_demo()
    {
        if (wp_verify_nonce($_POST['_nonce'], 'create_demo_job')) {
            delete_option('jobs_demo_status');
            $data = $_POST['data'];
            parse_str($data, $data);
            $qty = $data['dummy_job_qty'];
            $prices = $data['dummy_job_price_range'];
            $skills = $data['dummy_skills'];
            $categories = $data['dummy_category'];
            $sample = isset($data['have_sample']) ? true : false;

            for ($i = 1; $i <= $qty; $i++) {
	            $this->normal_generator(explode('-', $prices), $categories, $skills, $sample);

                //cal percent
                $percent = ($i / $qty) * 100;
                update_option('jobs_demo_status', $percent);
            }
        }
    }

    function save_setting()
    {
        if (isset($_POST['create_jobs_dummy_data'])) {
            $qty = $_POST['dummy_job_qty'];
            $prices = $_POST['dummy_job_price_range'];
            $categories = $_POST['dummy_category'];
            $skills = $_POST['dummy_skills'];
            $have_sample = isset($_POST['have_sample']) ? true : false;

            //prepare data
            $prices = explode('-', $prices);
            if ($qty > 0) {
                for ($i = 0; $i < $qty; $i++) {
                    if (version_compare(phpversion(), '5.3.3') >= 0) {
                        $this->faker_generator($prices, $categories, $skills, $have_sample);
                    } else {
                        $this->normal_generator($prices, $categories, $skills, $have_sample);
                    }
                }
            }
        }
    }

    function check_status()
    {
        $status = get_option('jobs_demo_status');
        echo $status;
        /*$plugin = JobsExperts_Plugin::instance();
        $path = $plugin->_module_path . 'AddOn/DemoData/runtime';
        if (!is_writable($path)) {
            chmod($path, 777);
        }
        if (is_writable($path)) {
            if (file_exists($path . 'status')) {
                $current = file_get_contents($path . 'status');
                echo $current;
            }
        }*/
        exit;
    }

    function normal_generator($budgets, $categories, $skills, $have_file)
    {
        $open_for = array(3, 7, 14, 21);
        $weeks = array(1, 2, 3, 4);

        $model = new JobsExperts_Core_Models_Job();
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
        $model->save();
        //categories
        $categories = explode(',', $categories);
        $categories = array_filter($categories);
        $model->assign_categories($categories[array_rand($categories)]);
        $skills = explode(',', $skills);
        $skills = array_filter($skills);
        $tmp = array_rand($skills, 3);
        $rand_skills = array();
        foreach ($tmp as $t) {
            $rand_skills[] = $skills[$t];
        }

        $model->assign_skill_tag($rand_skills);
        if ($have_file) {
            //random generate 3 files
            $ids = array();
            for ($i = 0; $i < 3; $i++) {
                //get the random image
                $upload_dir = wp_upload_dir();
                $path = $upload_dir['path'] . '/' . uniqid() . '.jpg';
                $image_url = $this->content_bank('image');
                //download the image
                $this->download_image($image_url, $path);
                //now handler the file
                $att_id = $this->handler_upload($model->id, $path);
                //create media post type
                $media = new JobsExperts_Components_Uploader_Model();
                $media->description = jbp_filter_text($this->content_bank('scontent'));
                $media->file = $att_id;
                $media->url = 'http://wpmudev.org';
                $media->parent_id = $model->id;
                $media->save();
                update_post_meta($media->id, '_file', $att_id);

                $ids[] = $media->id;
            }
            $model->portfolios = implode(',', $ids);
            $model->save();
        }
    }

    function content_bank($type)
    {
        $plugin = JobsExperts_Plugin::instance();
        $data = file_get_contents($plugin->_module_path . 'AddOn/DemoData/job_data.txt');
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
                $c = $data['image_urls'];

                return $c[array_rand($c)];
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

    function menu()
    {
        $plugin = JobsExperts_Plugin::instance();
        ?>
        <li <?php echo $this->active_tab('job_demo_data') ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job_demo_data') ?>">
                <i class="dashicons dashicons-star-filled"></i> <?php _e('Jobs Demo Data', JBP_TEXT_DOMAIN) ?>
            </a></li>
    <?php
    }

    function content(JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model)
    {
        if ($this->is_current_tab('job_demo_data')) {
            ?>
            <fieldset>
                <div class="page-header" style="margin-top: 0">
                    <h4><?php _e('Create dummy data for jobs', JBP_TEXT_DOMAIN) ?></h4>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Dummy jobs amount', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="10" name="dummy_job_qty" class="form-control">

                        <p class="help-block"><?php _e('Number of dummy jobs to create', JBP_TEXT_DOMAIN) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Price Range', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="0-2000" name="dummy_job_price_range" class="form-control">
                        <p class="help-block"><?php _e('Range of price') ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Categories', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="Wordpress,Buddypress,General,WPMUDEV" name="dummy_category"
                               class="form-control">

                        <p class="help-block"><?php _e('Demo job categories, separated by commas. Will be randomly assigned.', JBP_TEXT_DOMAIN) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Skill tags', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <input type="text" value="Html5,PHP,MySQL,jQuery,Javascript,Css3,Media," name="dummy_skills"
                               class="form-control">

                        <p class="help-block"><?php _e('Demo job skills, separated by commas. Will be randomly assigned.', JBP_TEXT_DOMAIN) ?></p>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 label-control"><?php _e('Have Sample Files', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <p class="help-block">
                            <input type="checkbox" checked="checked" name="have_sample">
                            <?php _e('If you want these demo jobs to have sample files, check this box.', JBP_TEXT_DOMAIN) ?>
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
            </fieldset>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('#jobs-setting').submit(function () {
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
                                }, 2000);
                            },
                            success: function (data) {
                            }
                        })
                        return false;
                    })
                })
            </script>
        <?php
        }
    }
}

new JobsExpert_Compnents_JobDemoData();