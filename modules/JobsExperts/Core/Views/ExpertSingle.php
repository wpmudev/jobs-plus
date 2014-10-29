<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Views_ExpertSingle extends JobsExperts_Framework_Render
{

    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        $model = $this->model;
        $plugin = JobsExperts_Plugin::instance();
        $page_module = $plugin->page_module();
        ?>
        <div class="expert-single">
        <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="expert-avatar">
                        <div class="panel panel-default">
                            <div class="panel-body no-padding">
                                <?php echo $model->get_avatar(420) ?>
                            </div>
                            <div class="panel-footer">
                                <?php ob_start(); ?>
                                <a class="btn btn-sm btn-primary jbp_contact_expert"
                                   href="<?php echo add_query_arg(array(
                                       'contact' => get_post()->post_name
                                   ), apply_filters('jbp_expert_contact_link', get_permalink($page_module->page($page_module::EXPERT_CONTACT)))) ?>"><?php _e('Contact Me', JBP_TEXT_DOMAIN) ?></a>
                                <?php $content = ob_get_clean();
                                echo apply_filters('jbp_expert_contact_btn', $content, $model);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hn-widget hn-grey-alt">
                <div class="hn-widget-body">
                    <i class="fa fa-heart"></i>
                </div>
                <div class="hn-widget-footer">
                    <div class="row no-margin">
                        <div class="col-md-6 col-xs-6 col-sm-6 no-padding">
                            <div class="hn-widget-cell border">
                                <span class="like-count"><?php echo $model->get_like_count() ?></span>
                                <small><?php _e('likes', JBP_TEXT_DOMAIN) ?></small>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6 col-sm-6 no-padding">
                            <div class="hn-widget-cell">
                                <p style="line-height: 38px">
                                    <?php if (!is_user_logged_in() || !$model->is_current_user_can_like()): ?>
                                        <button disabled class="btn btn-primary btn-sm btn-danger" type="button"><i
                                                class="fa fa-thumbs-up expert-like"></i></button>
                                    <?php else: ?>
                                        <button class="btn btn-primary btn-sm btn-danger expert-like" type="button"><i
                                                class="fa fa-thumbs-up"></i></button>
                                        <script type="text/javascript">
                                            jQuery(document).ready(function ($) {
                                                $('.expert-like').click(function () {
                                                    var that = $(this);
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: '<?php echo admin_url('admin-ajax.php') ?>',
                                                        data: {
                                                            id: '<?php echo get_the_ID() ?>',
                                                            user_id: '<?php echo get_current_user_id() ?>',
                                                            action: 'expert_like',
                                                            _nonce: '<?php echo wp_create_nonce('expert_like') ?>'
                                                        },
                                                        beforeSend: function () {
                                                            that.attr('disabled', 'disabled');
                                                        },
                                                        success: function (data) {
                                                            $('.like-count').text(data);
                                                        }
                                                    })
                                                })
                                            })
                                        </script>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="cleafix"></div>
                    </div>
                </div>
            </div>
            <div class="hn-widget hn-blue">
                <div class="hn-widget-body">
                    <i class="fa fa-eye"></i>
                </div>
                <div class="hn-widget-footer">
                    <div class="row no-margin">
                        <div class="col-md-6 col-xs-6 col-sm-6 no-padding">
                            <div class="hn-widget-cell border">
                                <span><?php echo $model->get_view_count() ?></span>
                                <small><?php _e('views', JBP_TEXT_DOMAIN) ?></small>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-6 col-sm-6 no-padding">
                            <div class="hn-widget-cell">
                                <p><i class="fa fa-toggle-up fa-2x"></i></p>
                            </div>
                        </div>
                        <div class="cleafix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-header">
                <h2><?php echo $model->first_name . ' ' . $model->last_name ?></h2>
                <h4><?php echo sprintf(__('Member since %s', JBP_TEXT_DOMAIN), date("M Y", strtotime(get_the_author_meta('user_registered')))) ?></h4>
            </div>
            <?php if (!empty($model->company)): ?>
                <div class="row">
                    <div class="col-md-5">
                        <label>
                            <i class="glyphicon glyphicon-briefcase"></i>
                            <?php _e('Company:', JBP_TEXT_DOMAIN) ?>
                        </label>
                    </div>
                    <div class="col-md-7">
                        <a href="<?php echo $model->company_url ?>"><?php echo $model->company ?></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-5">
                    <label>
                        <i class="glyphicon glyphicon-map-marker"></i> <?php _e('Location:', JBP_TEXT_DOMAIN) ?>
                    </label>
                </div>
                <div class="col-md-7">
                    <?php echo $model->location; ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#biograhy" role="tab"
                               data-toggle="tab"><?php _e('Biography', JBP_TEXT_DOMAIN) ?></a>
                        </li>
                        <li><a href="#profile" role="tab"
                               data-toggle="tab"><?php _e('Social & Skill', JBP_TEXT_DOMAIN) ?></a>
                        </li>

                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="biograhy">
                            <?php echo JobsExperts_Helper::jbp_html_beautifier(wpautop($model->biography)) ?>
                        </div>
                        <div class="tab-pane social-skill" id="profile">

                            <div class="page-header">
                                <label><i class="fa fa-flask"></i> <?php _e('Skills', JBP_TEXT_DOMAIN) ?></label>
                            </div>
                            <?php $skills = array_unique(array_filter(explode(',', $model->skills)));
                            if (count($skills)) {
                                ?>

                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="jbp-socials" style="padding-left: 0">
                                            <?php foreach ($skills as $key) {
                                                $skill = JobsExperts_Components_Skill_Model::instance()->get_one($key, $model->id);
                                                global $jbp_component_skill;
                                                if (is_object($skill)) {
                                                    echo $jbp_component_skill->skill_bar_template($skill, false);
                                                }
                                            } ?>
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            <?php
                            } else {
                                _e('This member hasn\'t added any skills yet', JBP_TEXT_DOMAIN);
                            } ?>
                            <br/>

                            <div class="page-header">
                                <label><i class="fa fa-globe"></i> <?php _e('Social Profile', JBP_TEXT_DOMAIN) ?>
                                </label>
                            </div>
                            <?php $socials = array_unique(array_filter(explode(',', $model->social)));
                            if (count($socials)) {
                                ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="jbp-socials" style="padding-left: 0">
                                            <?php foreach ($socials as $key) {
                                                $social = JobsExperts_Components_Social_Model::instance()->get_one($key, $model->id);
                                                global $jbp_component_social;
                                                if (is_object($social)) {
                                                    echo '<li>' . $jbp_component_social->template_social_display($social->export()) . '</li>';
                                                }
                                            } ?>
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            <?php
                            } else {
                                _e('This member still not add any social profile.', JBP_TEXT_DOMAIN);
                                echo '<br/>';
                            } ?>

                        </div>
                        <div class="tab-pane" id="portfolio"></div>
                    </div>
                </div>
            </div>
            <?php
            $files = array_unique(array_filter(explode(',', $model->portfolios)));
            if (!empty($files)): ?>
                <div class="row-fluid full">
                    <div class="page-header">
                        <label><?php _e('Sample Files', JBP_TEXT_DOMAIN) ?></label>
                    </div>
                    <?php
                    global $jbp_component_uploader;
                    $jbp_component_uploader->show_on_front($model->id, $files);
                    ?>
                    <div class="clearfix"></div>
                </div>
            <?php endif; ?>

            <?php if ($model->is_current_owner()): ?>
                <?php
                $post = get_post($model->id);
                $var = $post->post_status == 'publish' ? $post->post_name : $post->ID;
                ?>
                <div class="row" style="margin-top: 40px">
                    <div class="col-md-12" style="margin-left: 0">
                        <a class="btn btn-primary"
                           href="<?php echo add_query_arg(array('pro' => $var), apply_filters('expert_edit_button_link', get_permalink($page_module->page($page_module::EXPERT_EDIT)))) ?>">
                            <?php _e('Edit', JBP_TEXT_DOMAIN) ?>
                        </a>

                        <form class="frm-delete" method="post" style="display: inline-block">
                            <input name="expert_id" type="hidden" value="<?php echo $model->id ?>">
                            <?php wp_nonce_field('delete_expert_' . $model->id) ?>
                            <button name="delete_expert" class="btn btn-danger"
                                    type="submit"><?php _e('Trash', JBP_TEXT_DOMAIN) ?></button>
                        </form>
                    </div>
                    <div style="clear: both"></div>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        $('.frm-delete').submit(function () {
                            if (confirm('<?php echo esc_js(__('Are you sure?',JBP_TEXT_DOMAIN)) ?>')) {

                            } else {
                                return false;
                            }
                        })
                    })
                </script>
            <?php endif; ?>
        </div>
        <div class="clearfix"></div>
        </div>
        </div>
        <?php do_action('jbp_after_single_expert') ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('[data-toggle="tooltip"]').tooltip()
            })
        </script>
    <?php
    }
}