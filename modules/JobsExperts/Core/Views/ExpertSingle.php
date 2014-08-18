<?php

/**
 * Author: Hoang Ngo
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
        <div class="jbp_pro_single">
        <div class="row">
            <div class="col-md-4 col-xs-12 col-sm-12" style="margin-left: 0">
                <div class="panel panel-default">
                    <div class="panel-body" style="padding: 0">
                        <div class="jbp_pro_avatar">
                            <?php echo get_avatar($model->contact_email, 240) ?>
                            <div class="jbp_pro_contact">
                                <a class="btn btn-small btn-primary" href="<?php echo add_query_arg(array(
                                    'contact' => get_post()->post_name
                                ), get_permalink($page_module->page($page_module::EXPERT_CONTACT))) ?>"><?php _e('Contact Me', JBP_TEXT_DOMAIN) ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $socials = !empty($model->social) ? json_decode(stripslashes($model->social), true) : array();
                $model->social = esc_attr(stripslashes($model->social));
                if (!is_array($socials)) {
                    $socials = array();
                }
                $socials = array_filter($socials);

                ?>
                <?php if (is_array($socials) && count($socials) > 0): ?>
                    <div class="panel panel-default">
                        <div class="jbo_pro_social">
                            <div class="panel-heading"><h4><?php _e('Social Profile', JBP_TEXT_DOMAIN) ?></h4></div>
                            <div class="panel-body">
                                <div class="social_list">
                                    <ul>
                                        <?php
                                        foreach ($socials as $social):?>
                                            <li>
                                                <a target="_blank" title="<?php echo ucwords($social['id']) ?>"
                                                   href="<?php echo $social['url'] ?>"">
                                                <img style="opacity:1"
                                                     src="<?php echo $plugin->_module_url ?>assets/social_icon/<?php echo $social['id'] ?>.png">
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div style="clear: both"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $skills = !empty($model->skills) ? json_decode(stripslashes($model->skills), true) : array();
                $model->skills = esc_attr(stripslashes($model->skills));
                if (!is_array($skills)) {
                    $skills = array();
                }
                if (is_array($skills) && count($skills)) {
                    ?>

                    <div class="panel panel-default">
                        <div class="jbo_pro_social">
                            <div class="panel-heading"><h4><?php _e('Skills', JBP_TEXT_DOMAIN) ?></h4></div>
                            <div class="panel-body">
                                <?php
                                $colors = array(
                                    array(
                                        'background: #d35400;',
                                        'background: #e67e22;'
                                    ),
                                    array(
                                        'background: #2980b9;',
                                        'background: #3498db;'
                                    ),
                                    array(
                                        'background: #2c3e50;',
                                        'background: #2c3e50;'
                                    ),
                                    array(
                                        'background: #2c3e50;',
                                        'background: #46465e;'
                                    ),
                                    array(
                                        'background: #2c3e50;',
                                        'background: #5a68a5;'
                                    ),
                                    array(
                                        'background: #333333;',
                                        'background: #525252;'
                                    ),
                                    array(
                                        'background: #27ae60;',
                                        'ackground: #2ecc71;'
                                    ),
                                    array(
                                        'background: #124e8c;',
                                        'background: #4288d0;'
                                    ),
                                );
                                $color = $colors[array_rand($colors)];
                                ?>
                                <div class="skill">
                                    <div class="jbp_skill_meta">
                                        <?php foreach ($skills as $skill):
                                            ?>
                                            <?php $ccolor = $colors[array_rand($colors)]; ?>
                                            <div class="jbp_skillbar edit-skill"
                                                 data-percent="<?php echo $skill['score'] ?>">

                                                <div class="jbp_skillbar-title" style="<?php echo $ccolor[1] ?>">
                                                    <span><?php echo $skill['name'] ?></span>
                                                </div>
                                                <div class="jbp_skillbar-bar" style="<?php echo $ccolor[0] ?>"></div>
                                                <div class="jbp_skillbar-percent"><?php echo $skill['score'] ?>%</div>
                                            </div>
                                            <!-- End Skill Bar -->
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-7 col-xs-12 col-sm-12" style="padding: 0">
                <?php if (!empty($model->short_description)): ?>
                    <div class="pro-tag-line"><?php echo $model->short_description ?></div>
                <?php endif; ?>

                <div class="pro-single-content">
                    <div class="jbp_pro_full_name">
                        <span class="text-info"><?php echo $model->first_name . ' ' . $model->last_name ?></span>
                        <?php echo sprintf(__(' is a member since %s', JBP_TEXT_DOMAIN), date("M Y", strtotime(get_the_author_meta('user_registered')))) ?>
                    </div>
                    <?php

                    if (!empty($model->company)): ?>
                        <div class="row">
                            <div class="col-md-12 col-xs-12 col-sm-12">
                                <p>
                                    <label><?php _e('Company:', JBP_TEXT_DOMAIN) ?></label> &nbsp;<a
                                        href="<?php echo $model->company_url ?>"><?php echo $model->company ?></a>
                                </p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <p><label><?php _e('Location:', JBP_TEXT_DOMAIN) ?></label> &nbsp;
                                <?php echo $model->location; ?></p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row jbp-pro-stat hidden-xs hidden-sm">
                        <div class="col-md-3">
                            <span><?php echo $model->get_view_count() ?></span>&nbsp;<i
                                class="glyphicon glyphicon glyphicon-eye-open"></i>
                            <small><?php _e('Views', JBP_TEXT_DOMAIN) ?></small>
                        </div>
                        <div class="col-md-3">
                            <span class="like-count"><?php echo $model->get_like_count() ?></span><i
                                class="glyphicon glyphicon glyphicon-heart"></i>
                            <small><?php _e('Likes', JBP_TEXT_DOMAIN) ?></small>
                        </div>
                        <div class="col-md-3">
                            <?php $disabled = $model->is_current_user_can_like() ? null : 'disabled="disabled"' ?>
                            <button <?php echo $disabled ?> type="button"
                                                            class="btn btn-danger jbp-pro-like"><?php _e('Like', JBP_TEXT_DOMAIN) ?></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row full">
                        <div class="col-md-12" style="margin-left: 0">
                            <label><?php _e('Biography', JBP_TEXT_DOMAIN) ?></label>
                        </div>
                        <div class="col-md-12">
                            <?php echo wpautop($model->biography) ?>
                        </div>
                        <div style="clear: both"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!empty($model->portfolios)): ?>
                                <h4><strong><?php _e('Sample Files', JBP_TEXT_DOMAIN) ?></strong></h4>
                                <table class="table table-bordered job-files">
                                    <thead>
                                    <th style="width: 150px"><?php _e('File', JBP_TEXT_DOMAIN) ?></th>
                                    <th style="width: 150px"><?php _e('Name', JBP_TEXT_DOMAIN) ?></th>
                                    <th style="width: 50px"></th>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $files = explode(',', $model->portfolios);
                                    $files = array_filter($files);
                                    foreach ($files as $file) {
                                        $type = explode('/', get_post_mime_type($file));
                                        $type = array_filter($type);
                                        if (empty($type)) {
                                            $img = 'N/A';
                                        } else {
                                            $img_url = $type[0] == 'image' ? wp_get_attachment_url($file) : wp_mime_type_icon(get_post_mime_type($file));
                                            $img = '<img style="max-width:150px;height:auto;max-height:1050px" src="' . $img_url . '">';
                                        }
                                        $link = get_post_meta($file, 'portfolio_link', true);
                                        $desc = get_post_meta($file, 'portfolio_des', true);
                                        ?>
                                        <tr>
                                            <td style="text-align: center"><?php echo $img ?></td>
                                            <td><?php echo !empty($type) ? jbp_shorten_text(pathinfo(get_attached_file($file), PATHINFO_BASENAME), 50) : $link ?></td>

                                            <td>
                                                <button data-toggle="modal" data-backdrop="static"
                                                        data-target="#modal_<?php echo $file ?>" class="btn btn-info btn-sm"
                                                        type="button"><?php _e('View', JBP_TEXT_DOMAIN) ?></button>
                                            </td>
                                            <div class="modal fade" id="modal_<?php echo $file ?>" tabindex="-1"
                                                 role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel">Sample
                                                                Information</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php if (!empty($type) && $type[0] == 'image'): ?>
                                                                <img src="<?php echo wp_get_attachment_url($file) ?>">
                                                            <?php endif; ?>
                                                            <?php echo !empty($desc) ? $desc : __('No Description', JBP_TEXT_DOMAIN) ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <?php if (!empty($type)): ?>
                                                                <a download class="btn btn-primary"
                                                                   href="<?php echo wp_get_attachment_url($file) ?>"><?php _e('Download', JBP_TEXT_DOMAIN) ?></a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($link)): ?>
                                                                <a class="btn btn-info"
                                                                   href="<?php echo $link ?>"><?php _e('Visit Sample\'s Link', JBP_TEXT_DOMAIN) ?></a>
                                                            <?php endif; ?>
                                                            <button type="button" class="btn btn-default"
                                                                    data-dismiss="modal">Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($model->is_current_owner()): ?>
                        <?php
                        $post = get_post($model->id);
                        $var = $post->post_status == 'publish' ? $post->post_name : $post->ID;
                        ?>
                        <div class="row" style="margin-top: 40px">
                            <div class="col-md-12" style="margin-left: 0">
                                <a class="btn btn-primary"
                                   href="<?php echo add_query_arg(array('pro' => $var), get_permalink($page_module->page($page_module::EXPERT_EDIT))) ?>">
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
                                    if (confirm('<?php _e('Are you sure?',JBP_TEXT_DOMAIN) ?>')) {

                                    } else {
                                        return false;
                                    }
                                })
                            })
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                map_skillbar_color();
                function map_skillbar_color() {
                    //map skill bar color
                    $('.jbp_skill_meta').find('.jbp_skillbar').each(function () {
                        $(this).find('.jbp_skillbar-bar').css('width', $(this).data('percent') + '%');
                    });
                }


                $('.jbp-pro-like').click(function () {
                    var that = $(this);
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php?action=jbp_pro_like') ?>',
                        data: {
                            id: '<?php echo get_the_ID() ?>',
                            user_id: '<?php echo get_current_user_id() ?>'
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
    <?php
    }
}