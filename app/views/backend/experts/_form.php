<?php if ($this->has_flash('expert_saved')): ?>
    <div class="alert alert-success">
        <?php echo $this->get_flash('expert_saved') ?>
    </div>
<?php endif; ?>
<?php $form = new IG_Active_Form($model);
$form->open(array("attributes" => array("class" => "form-horizontal", "id" => "je-expert-form"))); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e('General Information', je()->domain) ?></strong>
                </div>
                <div class="panel-body">
                    <?php $form->hidden('id'); ?>
                    <div class="form-group <?php echo $model->has_error("first_name") ? "has-error" : null ?>">
                        <?php $form->label("first_name", array("text" => "First Name", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->text("first_name", array("attributes" => array("class" => "form-control"))) ?>
                            <span
                                class="help-block m-b-none error-first_name"><?php $form->error("first_name") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("last_name") ? "has-error" : null ?>">
                        <?php $form->label("last_name", array("text" => "Last Name", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->text("last_name", array("attributes" => array("class" => "form-control"))) ?>
                            <span class="help-block m-b-none error-last_name"><?php $form->error("last_name") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("biography") ? "has-error" : null ?>">
                        <?php $form->label("biography", array("text" => "Biography", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php wp_editor($model->biography, 'biography', array(
                                'name' => $form->build_name('biography'),
                                'textarea_rows' => '8',
                                'media_buttons' => false
                            )) ?>
                            <span class="help-block m-b-none error-biography"><?php $form->error("biography") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group <?php echo $model->has_error("user_id") ? "has-error" : null ?>">
                        <?php $form->label("user_id", array("text" => "Profile Owner", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php
                            $users = get_users();
                            $form->select('user_id', array(
                                'data' => array_combine(wp_list_pluck($users, 'ID'), wp_list_pluck($users, 'user_login')),
                                "attributes" => array("class" => "form-control")
                            )) ?>
                            <span class="help-block m-b-none error-user_id"><?php $form->error("user_id") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="form-group <?php echo $model->has_error("status") ? "has-error" : null ?>">
                        <?php $form->label("status", array("text" => "Job Status", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->select("status", array("attributes" => array("class" => "form-control"), "data" => array("publish" => "Publish", "draft" => "Draft"))) ?>
                            <span class="help-block m-b-none error-status"><?php $form->error("status") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php _e("Addition Information", je()->domain) ?></strong>
                </div>
                <div class="panel-body">
                    <div class="form-group <?php echo $model->has_error("contact_email") ? "has-error" : null ?>">
                        <?php $form->label("contact_email", array("text" => "Contact Email", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->text("contact_email", array("attributes" => array("class" => "form-control"))) ?>
                            <span
                                class="help-block m-b-none error-contact_email"><?php $form->error("contact_email") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("location") ? "has-error" : null ?>">
                        <?php $form->label("location", array("text" => "Location", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php
                            $form->select('location', array(
                                'data' => IG_Form::country(),
                                "attributes" => array("class" => "form-control")
                            ));?>
                            <span class="help-block m-b-none error-location"><?php $form->error("location") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("company") ? "has-error" : null ?>">
                        <?php $form->label("company", array("text" => "Company Name", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->text("company", array("attributes" => array("class" => "form-control"))) ?>
                            <span class="help-block m-b-none error-company"><?php $form->error("company") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("company_url") ? "has-error" : null ?>">
                        <?php $form->label("company_url", array("text" => "Company Url", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->text("company_url", array("attributes" => array("class" => "form-control"))) ?>
                            <span
                                class="help-block m-b-none error-company_url"><?php $form->error("company_url") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group <?php echo $model->has_error("short_description") ? "has-error" : null ?>">
                        <?php $form->label("short_description", array("text" => "Short Description", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                        <div class="col-lg-10">
                            <?php $form->text_area("short_description", array("attributes" => array("class" => "form-control", "style" => "height:70px"))) ?>
                            <span
                                class="help-block m-b-none error-short_description"><?php $form->error("short_description") ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php $form->hidden('social', array('attributes' => array('id' => 'social-input'))) ?>
        <div class="col-md-5">
            <?php ig_social_wall()->display($model, 'social', 'social-input');
            $form->hidden('skills', array('attributes' => array('id' => 'skill-input')));
            ig_skill()->display($model, 'skills', 'skill-input')
            ?>
        </div>
        <div class="clearfix"></div>
        <?php wp_nonce_field('ig_expert_add') ?>
        <?php $form->hidden('portfolios') ?>
        <div class="col-md-12">
            <?php ig_uploader()->show_upload_control($model, 'portfolios', true,
                array('title' => __("Attach specs examples or extra information", je()->domain))); ?>
        </div>
        <div class="col-md-12">
            <button type="submit" class="button button-primary"><?php _e("Submit", je()->domain) ?></button>
        </div>
    </div>
<?php $form->close() ?>