<?php

/**
 * Name: Jobs & Experts Pages
 * Description: Use standard pages rather than virtual pages
 * Author: WPMU DEV
 */
class JE_Pages_Manager
{
    public function __construct()
    {
        include(dirname(__FILE__) . '/je-pages-manager/model.php');
        add_action('jbp_setting_menu', array(&$this, 'menu'));
        add_action('je_settings_content_pages_manager', array(&$this, 'content'));

        add_action('wp_ajax_jbp_create_wp_page', array(&$this, 'create_page'));
        add_action('je_saved_setting', array(&$this, 'save_setting'));
        add_filter('jbp_page_factory_get_page', array(&$this, 'page_id'), 10, 2);

        add_filter('je_jobs_archive_url', array(&$this, 'job_archive'));
        add_filter('je_experts_archive_url', array(&$this, 'expert_archive'));
    }

    function job_archive($link)
    {
        $model = new JE_Pages_Manager_Model();
        $model->load();
        if (!empty($model->list_jobs)) {
            return get_permalink($model->list_jobs);
        }
        return $link;
    }

    function expert_archive($link)
    {
        $model = new JE_Pages_Manager_Model();
        $model->load();
        if (!empty($model->list_experts)) {
            return get_permalink($model->list_experts);
        }
        return $link;
    }

    function page_id($page_id, $page)
    {
        $model = new JE_Pages_Manager_Model();
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

    function save_setting()
    {
        if (isset($_POST['JE_Pages_Manager_Model'])) {
            $model = new JE_Pages_Manager_Model();
            $model->load();
            $model->import(je()->post('JE_Pages_Manager_Model'));
            $model->save();
        }
    }

    function menu()
    {
        ?>
        <li <?php echo je()->get('tab') == 'pages_manager' ? 'class="active"' : null ?>>
            <a href="<?php echo admin_url('edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=pages_manager') ?>">
                <i class="fa fa-send"></i> <?php _e('Pages Manager', je()->domain) ?>
            </a></li>
    <?php
    }

    function create_page()
    {
        if (isset($_POST['type'])) {
            $page_module = je()->pages;
            $model = new JE_Pages_Manager_Model();
            $model->load();
            switch ($_POST['type']) {
                case 'add_new_job':
                    $vid = $page_module->page(JE_Page_Factory::JOB_ADD, true);
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
                    $vid = $page_module->page(JE_Page_Factory::JOB_EDIT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->edit_job = $new_id;
                    $model->save();
                    break;
                case 'contact_job':
                    $vid = $page_module->page(JE_Page_Factory::JOB_CONTACT, true);
                    $vpage = get_post($vid);
                    //reset the post data
                    $page = $this->reset_page($vpage)->to_array();
                    //insert page
                    $new_id = wp_insert_post($page);
                    $model->contact_job = $new_id;
                    $model->save();
                    break;
                case 'list_jobs':
                    $vid = $page_module->page(JE_Page_Factory::JOB_LISTING, true);
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
                    $vid = $page_module->page(JE_Page_Factory::MY_JOB, true);
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
                    $vid = $page_module->page(JE_Page_Factory::EXPERT_ADD, true);
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
                    $vid = $page_module->page(JE_Page_Factory::EXPERT_EDIT, true);
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
                    $vid = $page_module->page(JE_Page_Factory::EXPERT_CONTACT, true);
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
                    $vid = $page_module->page(JE_Page_Factory::EXPERT_LISTING, true);
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
                    $vid = $page_module->page(JE_Page_Factory::MY_EXPERT, true);
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

    function content()
    {
        $model = new JE_Pages_Manager_Model();
        $model->load();
        $data = array_combine(wp_list_pluck(get_pages(), 'ID'), wp_list_pluck(get_pages(), 'post_title'));
        ?>
        <?php $form = new IG_Active_Form($model);
        $form->open(array("attributes" => array("class" => "form-horizontal"))); ?>
        <div id="page-creator">
            <div>
                <fieldset>
                    <legend style="padding-bottom: 7px"><?php _e('Job Pages', je()->domain) ?></legend>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('Add new Job', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('add_new_job',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="add_new_job"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('Edit Job', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('edit_job',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="edit_job"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label
                            class="col-md-3 control-label"><?php _e('Job Listing', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('list_jobs',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="list_jobs"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('My Jobs', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('my_jobs',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="my_jobs"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('Contact Job', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('contact_job',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="contact_job"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend style="padding-bottom: 7px"><?php _e('Expert Pages', je()->domain) ?></legend>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('Add new Expert', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('add_new_expert',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="add_new_expert"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('Edit Expert', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('edit_expert',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="edit_expert"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label
                            class="col-md-3 control-label"><?php _e('Expert Listing', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('list_experts',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="list_experts"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('My Profiles', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('my_profiles',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="my_profiles"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php _e('Contact Expert', je()->domain) ?></label>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $form->select('contact_expert',
                                        array(
                                            'data' => $data,
                                            'attributes' => array(
                                                'class' => 'form-control'
                                            ),
                                            'nameless' => __('--Choose--', je()->domain)
                                        ));
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" data-id="contact_expert"
                                            class="button button-primary create-page"><?php _e('Create Page', je()->domain) ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </fieldset>
            </div>
            <?php wp_nonce_field('je_settings', '_je_setting_nonce') ?>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit"
                            class="button button-primary"><?php _e("Save Changes", je()->domain) ?></button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <?php $form->close() ?>
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
                            that.attr('disabled', 'disabled').text('<?php echo esc_js(__('Creating...',je()->domain)) ?>');
                        },
                        success: function (data) {
                            var element = that.parent().parent().find('select').first();
                            $.get('<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>', function (html) {
                                html = $(html);
                                var clone = html.find('select[name="' + element.attr('name') + '"]');
                                element.replaceWith(clone);
                                that.removeAttr('disabled').text('<?php echo esc_js(__('Create Page',je()->domain)) ?>');
                            });
                        }
                    })
                })
            })
        </script>
    <?php
    }
}

new JE_Pages_Manager();