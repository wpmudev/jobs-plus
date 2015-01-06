<div class="ig-container">
    <div class="hn-container">
        <?php $form = new IG_Active_Form($model);
        $form->open(array("attributes" => array("class" => "form-horizontal")));
        ?>
        <div class="jobs-expert-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <?php $this->render_partial('expert-form/_avatar_upload', array(
                                'model' => $model
                            )) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tag-line">
                                <div class="panel panel-default" style="z-index: 0">
                                    <div class="panel panel-heading">
                                        <?php _e('Tag line', je()->domain) ?>
                                    </div>
                                    <div class="panel-body">
                                        <div class="can-edit" data-type="tagline" data-placement="top-right">
                                            <?php echo !empty($model->short_description) ? $model->short_description : __("Short version of yourself, maximum 100 characters. This will be use to display at listing page", je()->domain) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <?php if (is_array($model->get_errors()) && count($model->get_errors())): ?>
                        <div class="alert alert-danger">
                            <?php echo implode('<br/>', $model->get_errors()) ?>
                        </div>
                    <?php endif; ?>
                    <div class="page-header">
                        <h2 class="can-edit" data-placement="top-left"
                            data-type="name"><?php echo !empty($model->name) ? $model->name : __("Your Name (required)", je()->domain) ?></h2>
                        <h4><?php echo sprintf(__('Member since %s', je()->domain), date("M Y", strtotime(get_the_author_meta('user_registered', $model->user_id)))) ?></h4>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                            <label>
                                <i class="glyphicon glyphicon-briefcase"></i>
                                <?php _e('Company:', je()->domain) ?>
                            </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-sm-8">
                            <a class="can-edit" data-placement="top-left" data-type="company"
                               href="#"><?php echo !empty($model->company) ? $model->company : __("Your Company", je()->domain) ?></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                            <label>
                                <i class="glyphicon glyphicon-map-marker"></i> <?php _e('Location:', je()->domain) ?>
                            </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-sm-8">
                            <?php echo apply_filters('je_expert_form_location_field', '<span class="can-edit" data-placement="top-left"
                              data-type="location">' . (!empty($model->location) ? $model->get_location() : __("Your Location", je()->domain)) . '</span>', $model) ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-4 col-sm-4">
                            <label>
                                <i class="fa fa-envelope"></i> <?php _e('Contact Email:', je()->domain) ?>
                            </label>
                        </div>
                        <div class="col-md-8 col-xs-8 col-sm-8">
                        <span class="can-edit" data-placement="top-left"
                              data-type="email"><?php echo !empty($model->email) ? $model->email : __("Your Contact Email", je()->domain) ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Nav tabs -->
                            <br/>

                            <div id="expert-content-tabs">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li>
                                        <a href="#biograhy"><?php _e('Biography', je()->domain) ?></a>
                                    </li>
                                    <li><a href="#profile"><?php _e('Social & Skill', je()->domain) ?></a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane" id="biograhy">
                                        <div class="can-edit" data-type="biography">
                                            <?php echo !empty($model->biography) ? $model->biography : __("Tell us about yourself (required, at least 200 characters)", je()->domain) ?>
                                        </div>
                                    </div>
                                    <div class="tab-pane social-skill" id="profile">
                                        <?php ig_skill()->display($model, 'skills', 'skill-input') ?>
                                        <?php ig_social_wall()->display($model, 'social', 'social-input') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php ig_uploader()->show_upload_control($model, 'portfolios', false, array(
                        'title' => __("Attach specs examples or extra information", je()->domain)
                    )) ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <br/>

            <div class="row" style="margin-bottom: 5px;border-width: 1px;position:relative;">
                <div class="col-md-12" style="margin-left: 0">
                    <?php echo wp_nonce_field('jbp_add_pro') ?>
                    <?php if (je()->settings()->expert_new_expert_status == 'publish'): ?>
                        <button class="submit btn btn-small btn-primary" name="status" value="publish"
                                type="submit"><?php _e('Publish', je()->domain) ?></button>
                    <?php else: ?>
                        <button class="submit btn btn-small btn-primary" name="status"
                                type="submit" value="pending"><?php _e('Submit for review', je()->domain) ?></button>
                    <?php endif; ?>
                    <?php if (je()->settings()->expert_allow_draft == 1): ?>
                        <button class="submit btn btn-small btn-info" name="status" value="draft"
                                type="submit"><?php _e('Save Draft', je()->domain) ?></button>
                    <?php endif; ?>
                    <button onclick="location.href='<?php echo get_post_type_archive_link('jbp_pro') ?>'"
                            type="button"
                            class="btn btn-default btn-small pull-right"><?php _e('Cancel', je()->domain) ?></button>
                </div>
                <div style="clear: both"></div>
            </div>
            <?php
            $form->hidden('id');
            $form->hidden('first_name');
            $form->hidden('last_name');
            $form->text_area('biography', array(
                'attributes' => array(
                    'style' => 'width:0;height:0;opacity:0;position:relative;top:-100px;left:-100px'
                )
            ));
            $form->text_area('short_description', array(
                'attributes' => array(
                    'style' => 'width:0;height:0;opacity:0;position:relative;top:-100px;left:-100px'
                )
            ));
            if(empty($model->user_id)) {
                $form->hidden('user_id', array('value' => get_current_user_id()));
            }else{
                $form->hidden('user_id');
            }
            $form->hidden('company');
            $form->hidden('company_url');
            $form->hidden('location');
            $form->hidden('contact_email');
            $form->hidden('social', array('attributes' => array('id' => 'social-input')));
            $form->hidden('skills', array('attributes' => array('id' => 'skill-input')));
            $form->hidden('portfolios'); ?>
        </div>
        <?php $form->close() ?>
        <?php
        $this->render_partial('expert-form/_name_popup', array(
            'model' => $model
        ));
        $this->render_partial('expert-form/_company_popup', array(
            'model' => $model
        ));
        $this->render_partial('expert-form/_location_popup', array(
            'model' => $model
        ));
        $this->render_partial('expert-form/_email_popup', array(
            'model' => $model
        ));
        $this->render_partial('expert-form/_biography_popup', array(
            'model' => $model
        ));
        $this->render_partial('expert-form/_tagline_popup', array(
            'model' => $model
        ));
        ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $("#expert-content-tabs").tabs({
            active: 0,
            activate: function (event, ui) {
                ui.newTab.addClass('active');
                ui.oldTab.removeClass('active');
            },
            create: function (event, ui) {
                ui.tab.addClass('active');
            }
        });
        $(".can-edit-form").validationEngine({
            scroll: false
        });
        var instance;
        $('body').on('mouseenter', '.can-edit', function (e) {
            e.preventDefault();
            var type = $(this).data('type');
            var title = '';
            switch (type) {
                case 'name':
                    title = expert_form.name_title;
                    break;
                case 'company':
                    title = expert_form.company_title;
                    break;
                case 'location':
                    title = expert_form.location_title;
                    break;
                case 'email':
                    title = expert_form.email_title;
                    break;
                case 'biography':
                    title = expert_form.biography_title;
                    break;
                case 'tagline':
                    title = expert_form.tagline_title;
                    break;
            }
            if ($(this).data('plugin_webuiPopover') == undefined) {
                $(this).webuiPopover({
                    title: title,
                    content: function () {
                        var content = $('<div class="ig-container"></div>');
                        var html = $('#' + type + '-template').html();
                        content.html(html);
                        return content;
                    }
                }).on('show.webui.popover', function () {
                    instance = $(this);
                    window.je_popover = instance;
                }).on('shown.webui.popover', function () {
                    var pop = $(this).data('plugin_webuiPopover');
                    var holder = pop.$target;
                    if (type == 'biography') {
                        if ($.fn.sceditor != undefined) {
                            var textarea = holder.find('textarea').first();
                            textarea.sceditor({
                                plugins: "xhtml",
                                autoUpdate: true,
                                width: '98%',
                                resizeMinWidth: '-1',
                                resizeMaxWidth: '100%',
                                resizeMaxHeight: '100%',
                                resizeMinHeight: '-1',
                                readOnly: false,
                                emoticonsEnabled: false,
                                toolbar: "bold,italic,underline,strike|left,center,right,justify|font,size,color,removeformat|cut,copy,paste,pastetext|bulletlist,orderedlist,indent,outdent|link,unlink|date,time",
                                style: '<?php echo je()->plugin_url . 'app/addons/je-wysiwyg/sceditor/minified/jquery.sceditor.default.min.css'?>'
                            });
                            window.jetextarea = textarea.sceditor('instance');
                        }
                    }
                }).on('hidden.webui.popover', function () {
                    if (window.jetextarea != undefined) {
                        window.jetextarea.destroy();
                    }
                });
            }
        });
        $('body').on('submit', '.can-edit-form', function () {
                if ($(this).validationEngine('validate')) {
                    var type = instance.data('type');
                    var data = $(this).serializeAssoc();
                    switch (type) {
                        case 'name':
                            var name = data.first_name + ' ' + data.last_name;
                            instance.text(name);
                            $('#je_expert_model-first_name').val(data.first_name);
                            $('#je_expert_model-last_name').val(data.last_name);
                            break;
                        case 'company':
                            if ($.trim(data.company).length > 0) {
                                instance.text(data.company);
                                instance.attr('url', data.company_url);
                                $('#je_expert_model-company').val(data.company);
                                $('#je_expert_model-company_url').val(data.company_url);
                            }
                            break;
                        case 'location':
                            instance.text($(this).find('select option:selected').text());
                            $('#je_expert_model-location').val(data.location);
                            $(this).trigger('je_expert_popup_form_location', [instance, $(this), data]);
                            break;
                        case 'biography':
                            instance.html(data.biography);
                            $('#je_expert_model-biography').val(data.biography);
                            break;
                        case 'tagline':
                            instance.html(data.tagline);
                            $('#je_expert_model-short_description').val(data.tagline);
                            break;
                        case 'email':
                            instance.text(data.email);
                            $('#je_expert_model-contact_email').val(data.email)
                            break;
                    }
                    instance.webuiPopover('hide');
                }
                return false;
            }
        );
        $('body').on('click', '.can-edit-cancel', function () {
            instance.webuiPopover('hide');
        });
    })
</script>
