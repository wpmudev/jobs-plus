<?php

/**
 * Name: Jobs & Experts Pages
 * Description: Instead of using virtual pages, using your wordpress page
 * Author: WPMU DEV
 */
include __DIR__ . '/MoveToNormalPage/model.php';

class JobsExpert_Compnents_MoveToNormalPage extends JobsExperts_AddOn
{
    public function __construct()
    {
        $this->_add_filter('jpb_virtual_status_public', 'disable_virtual_status');
        $this->_add_filter('jpb_virtual_status_show_in_admin_status_list', 'disable_virtual_status');
        $this->_add_filter('jbp_use_core_front_request', 'disable_virtual_status');

        $this->_add_action('jbp_setting_menu', 'menu');
        $this->_add_action('jbp_setting_content', 'content', 10, 2);

        $this->_add_ajax_action('jbp_create_wp_page', 'create_page');

        $this->_add_action('jbp_after_save_settings', 'save_setting');

        $this->_add_filter('jbp_page_factory_get_page', 'page_id', 10, 2);

        if (!is_admin()) {
            $this->_add_filter('jbp_button_url', 'update_button_link', 10, 2);
            //update archive link
            $this->_add_filter('jbp_job_archive_slug', 'job_archive');
            $this->_add_filter('jbp_pro_archive_slug', 'pro_archive');
            //update core page
            $this->_add_filter('the_content', 'job_single_content');
            $this->_add_filter('the_content', 'pro_single_content');
            //
            $this->_add_filter('get_edit_post_link', 'hide_edit_post_link');
            //update ocntact link
            /*$this->_add_filter('jbp_job_contact_link', 'job_contact_link', 10, 2);
            $this->_add_filter('job_edit_button_link', 'job_edit_button_link');
            $this->_add_filter('jbp_is_job_edit', 'jbp_is_job_edit');
            $this->_add_filter('jbp_my_jobs_url', 'jbp_my_jobs_url');
            $this->_add_filter('jbp_add_new_job_url', 'jbp_add_new_job_url');

            $this->_add_filter('jbp_expert_contact_link', 'expert_contact_link');
            $this->_add_filter('expert_edit_button_link', 'expert_edit_button_link');
            $this->_add_filter('jbp_is_expert_edit', 'jbp_is_expert_edit');
            $this->_add_filter('jbp_my_experts_url', 'jbp_my_profiles_url');
            $this->_add_filter('jbp_add_new_expert_url', 'jbp_add_new_expert_url');*/
        }
    }

    function page_id($page_id, $page)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        switch ($page) {
            case 'job_add':
                return $model->add_new_job;
            case 'my_job':
                return $model->my_jobs;
            case 'job_edit':
                return $model->edit_job;
            case 'job_listing':
                return $model->list_jobs;
            case 'job_contact':
                return $model->contact_job;

            case 'expert_add':
                return $model->add_new_expert;
            case 'my_expert':
                return $model->my_profiles;
            case 'expert_edit':
                return $model->edit_expert;
            case 'expert_listing':
                return $model->list_experts;
            case 'expert_contact':
                return $model->contact_expert;
        }
        return $page_id;
    }

    function jbp_add_new_job_url($url)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->add_new_job) {
            return get_permalink($model->add_new_job);
        }
        return $url;
    }

    function jbp_add_new_expert_url($url)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->add_new_expert) {
            return get_permalink($model->add_new_expert);
        }
        return $url;
    }

    function jbp_my_profiles_url($url)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->my_profiles) {
            return $model->my_profiles;
        }
        return $url;
    }

    function jbp_is_expert_edit($is_edit)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->edit_expert && $model->edit_expert == get_the_ID()) {
            return true;
        }
        return $is_edit;
    }

    function expert_edit_button_link($link)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->edit_expert) {
            return get_permalink($model->edit_expert);
        }
        return $link;
    }

    function expert_contact_link($link)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->contact_expert) {
            return get_permalink($model->contact_expert);
        }
        return $link;
    }

    function jbp_my_jobs_url($url)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->my_jobs) {
            return $model->my_jobs;
        }
        return $url;
    }

    function jbp_is_job_edit($is_edit)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->edit_job && $model->edit_job == get_the_ID()) {
            return true;
        }
        return $is_edit;
    }

    function job_edit_button_link($link)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->edit_job) {
            return get_permalink($model->edit_job);
        }
        return $link;
    }

    function job_contact_link($link, $id)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if ($model->contact_job) {
            return get_permalink($model->contact_job);
        }
        return $link;
    }

    function hide_edit_post_link($link)
    {
        //hide if this is page
        if ($this->is_core_page(get_the_ID()) || is_singular('jbp_job') || is_singular('jbp_pro')) {
            return null;
        }
        return $link;

    }

    function job_single_content($content)
    {
        $page_factory = JobsExperts_Plugin::instance()->page_module();
        if (is_singular('jbp_job') && !$page_factory::is_core_page(get_the_ID()) && !is_404()) {
            return do_shortcode('[jbp-job-single-page]');
        }

        return $content;
    }

    function pro_single_content($content)
    {
        $page_factory = JobsExperts_Plugin::instance()->page_module();
        if (is_singular('jbp_pro') && !$page_factory::is_core_page(get_the_ID()) && !is_404()) {
            return do_shortcode('[jbp-pro-single-page]');
        }

        return $content;
    }

    function is_core_page($id)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        foreach ($model->export() as $key => $val) {
            if ($id == $val) {
                return true;
            }
        }
        return false;
    }

    function job_archive($link)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if (!empty($model->list_jobs)) {
            $p = get_post($model->list_jobs);
            return $p->post_name;
        }
        return $link;
    }

    function pro_archive($link)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        if (!empty($model->list_experts)) {
            $p = get_post($model->list_experts);
            return $p->post_name;
        }
        return $link;
    }

    function save_setting()
    {
        if (isset($_POST['JobsExpert_AddOn_MoveToNormalPage_Model'])) {
            $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
            $model->load();
            $model->import($_POST['JobsExpert_AddOn_MoveToNormalPage_Model']);
            $model->save();
        }
    }

    function update_button_link($old_link, $type)
    {
        $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
        $model->load();
        $page_id = $model->$type;
        if ($page_id) {
            $c = get_post($page_id);
            if ($c instanceof WP_Post && $c->post_status == 'publish') {
                return get_permalink($c->ID);
            }
        }
        return $old_link;

        $buttons = array(
            'jbp-expert-post-btn' => 'add_new_expert',
            'jbp-job-post-btn' => 'add_new_job',
            'jbp-job-browse-btn' => 'list_jobs',
            'jbp-expert-profile-btn' => 'my_profiles',
            'jbp-my-job-btn' => 'my_jobs',
            'jbp-expert-browse-btn' => 'list_experts',
        );
        $post = get_post($post_id);
        $shortcodes = "[jbp-job-browse-btn][jbp-expert-browse-btn][jbp-job-post-btn][jbp-expert-post-btn][jbp-my-job-btn][jbp-expert-profile-btn]";
        foreach ($buttons as $key => $val) {
            $updated = false;
            if (!empty($model->$val)) {
                //check does page exist
                $c = get_post($model->$val);
                if ($c instanceof WP_Post && $c->post_status == 'publish') {
                    $updated = true;
                    $shortcodes = str_replace('[' . $key . ']', '[' . $key . ' url="' . get_permalink($model->$val) . '"]', $shortcodes);
                }
            }
            //we will remove the default url
            if ($updated == false) {
                $shortcodes = str_replace('[' . $key . ']', '[' . $key . ' url=""]', $shortcodes);
            }
        }
        return $shortcodes;
    }


    function create_page()
    {
        if (isset($_POST['type'])) {
            $page_module = JobsExperts_Plugin::instance()->page_module();
            $model = new JobsExpert_AddOn_MoveToNormalPage_Model();
            $model->load();
            switch ($_POST['type']) {
                case 'add_new_job':
                    $vid = $page_module->page($page_module::JOB_ADD, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->add_new_job = $new_id;
                    $model->save();
                    //update

                    echo $new_id;
                    break;
                case 'edit_job':
                    $vid = $page_module->page($page_module::JOB_EDIT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->edit_job = $new_id;
                    $model->save();
                    break;
                case 'contact_job':
                    $vid = $page_module->page($page_module::JOB_CONTACT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->contact_job = $new_id;
                    $model->save();
                    break;
                case 'list_jobs':
                    $vid = $page_module->page($page_module::JOB_LISTING, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->list_jobs = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
                case 'my_jobs':
                    $vid = $page_module->page($page_module::MY_JOB, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->my_jobs = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
                case 'add_new_expert':
                    $vid = $page_module->page($page_module::EXPERT_ADD, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->add_new_expert = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
                case 'edit_expert':
                    $vid = $page_module->page($page_module::EXPERT_EDIT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->edit_expert = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
                case 'contact_expert':
                    $vid = $page_module->page($page_module::EXPERT_CONTACT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->contact_expert = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
                case 'list_experts':
                    $vid = $page_module->page($page_module::EXPERT_LISTING, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->list_experts = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
                case 'my_profiles':
                    $vid = $page_module->page($page_module::MY_EXPERT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->my_profiles = $new_id;
                    $model->save();
                    echo $new_id;
                    break;
            }
        }
        exit;
    }

    function reset_page($vpage)
    {
        $vpage->post_status = 'publish';
        $vpage->post_type = 'page';
        $vpage->post_date = null;
        $vpage->post_date_gmt = null;
        $vpage->ID = null;

        return $vpage;
    }

    function menu()
    {
        $plugin = JobsExperts_Plugin::instance();
        ?>
        <li <?php echo $this->active_tab('job_demo_normal_page') ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=job_demo_normal_page') ?>">
                <i class="fa fa-send"></i> <?php _e('Pages Manager', JBP_TEXT_DOMAIN) ?>
            </a></li>
    <?php
    }

    function content(JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model)
    {
        if ($this->is_current_tab('job_demo_normal_page')) {
            $m = new JobsExpert_AddOn_MoveToNormalPage_Model();
            $m->load();
            ?>
            <div id="page-creator">
            <div>
            <fieldset>
                <legend style="padding-bottom: 7px"><?php _e('Job Pages', JBP_TEXT_DOMAIN) ?></legend>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Add new Job', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'add_new_job',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="add_new_job"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Edit Job', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'edit_job',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="edit_job"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label
                        class="col-md-3 control-label"><?php _e('Job Listing', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'list_jobs',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="list_jobs"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('My Jobs', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'my_jobs',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="my_jobs"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Contact Job', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'contact_job',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="contact_job"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </fieldset>
            <fieldset>
                <legend style="padding-bottom: 7px"><?php _e('Expert Pages', JBP_TEXT_DOMAIN) ?></legend>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Add new Expert', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'add_new_expert',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="add_new_expert"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Edit Expert', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'edit_expert',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="edit_expert"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label
                        class="col-md-3 control-label"><?php _e('Expert Listing', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'list_experts',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="list_experts"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('My Profiles', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'my_profiles',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="my_profiles"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php _e('Contact Expert', JBP_TEXT_DOMAIN) ?></label>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $form->dropDownList($m, 'contact_expert',
                                    array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title')),
                                    array('class' => 'form-control', 'prompt' => __('--Choose--', JBP_TEXT_DOMAIN))
                                );
                                ?>
                            </div>
                            <div class="col-md-6">
                                <button type="button" data-id="contact_expert"
                                        class="button button-primary create-page"><?php _e('Create Page', JBP_TEXT_DOMAIN) ?></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </fieldset>
            </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('body').on('click', '.create-page', function () {
                        var that = $(this);
                        $.ajax({
                            type: 'POST',
                            data: {
                                type: $(this).data('id'),
                                action: 'jbp_create_wp_page'
                            },
                            url: '<?php echo admin_url('admin-ajax.php') ?>',
                            beforeSend: function () {
                                that.attr('disabled', 'disabled').text('<?php echo esc_js(__('Creating...',JBP_TEXT_DOMAIN)) ?>');
                            },
                            success: function (data) {
                                $('#page-creator').load("<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?> #page-creator", function (html) {
                                    that.removeAttr('disabled').text('<?php echo esc_js(__('Create Page',JBP_TEXT_DOMAIN)) ?>');
                                });
                            }
                        })
                    })
                })
            </script>
        <?php
        }
    }

    function disable_virtual_status()
    {
        return false;
    }
}

new JobsExpert_Compnents_MoveToNormalPage();