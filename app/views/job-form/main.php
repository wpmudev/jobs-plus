<div class="ig-container">
    <?php $form = new IG_Active_Form($model);
    $form->open(array("attributes" => array("class" => "form-horizontal")));
    $form->hidden('id');
    ?>
    <?php do_action('je_job_before_form', $model, $form) ?>
    <?php do_action('je_before_cat_field', $model, $form) ?>
    <div class="form-group <?php echo $model->has_error("categories") ? "has-error" : null ?>">
        <?php $form->label("categories", array("text" => __("Choose a category", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <?php
            $cats = get_terms("jbp_category", array("hide_empty" => false));
            ?>
            <?php $form->select("categories", array("attributes" => array("class" => "form-control"), "data" => array_combine(wp_list_pluck($cats, "term_id"), wp_list_pluck($cats, "name")))) ?>
            <span class="help-block m-b-none error-categories"><?php $form->error("categories") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_cat_field', $model, $form) ?>
    <?php do_action('je_before_job_title_field', $model, $form) ?>
    <div class="form-group <?php echo $model->has_error("job_title") ? "has-error" : null ?>">
        <?php $form->label("job_title", array("text" => __("Give your job a title", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <?php $form->text("job_title", array("attributes" => array("class" => "form-control"))) ?>
            <span class="help-block m-b-none error-job_title"><?php $form->error("job_title") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_job_title_field', $model, $form) ?>
    <?php do_action('je_before_description_field', $model, $form) ?>
    <?php $form->hidden('owner', array('value' => get_current_user_id())) ?>
    <div class="form-group <?php echo $model->has_error("description") ? "has-error" : null ?>">
        <?php $form->label("description", array("text" => __("Describe the work to be done", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <?php $form->text_area("description", array("attributes" => array("class" => "form-control je_wysiwyg", "style" => "height:150px"))) ?>
            <span class="help-block m-b-none error-description"><?php $form->error("description") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_description_field', $model, $form) ?>
    <?php do_action('je_before_skill_field', $model, $form) ?>
    <div class="form-group <?php echo $model->has_error("skills") ? "has-error" : null ?>">
        <?php $form->label("skills", array("text" => __("What skills are needed?", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <?php $model->skills = !empty($model->skills) ? implode(',', $model->skills) : ''; ?>
            <?php $form->hidden('skills', array('attributes' => array('id' => 'jbp_skill_tag', 'style' => 'width:100%'))) ?>
            <span class="help-block m-b-none error-skills"><?php $form->error("skills") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_skill_field', $model, $form) ?>
    <?php do_action('je_before_price_field', $model, $form) ?>
    <?php if (je()->settings()->job_budget_range == 1): ?>
        <div class="form-group <?php echo $model->has_error("min_budget") ? "has-error" : null ?>">
            <?php $form->label("min_budget", array("text" => __("Min Budget", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
            <div class="col-lg-9">
                <div class="input-group">
                    <span
                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                    <?php $form->text("min_budget", array("attributes" => array("class" => "form-control"))) ?>
                </div>
                <span class="help-block m-b-none error-min_budget"><?php $form->error("min_budget") ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group <?php echo $model->has_error("max_budget") ? "has-error" : null ?>">
            <?php $form->label("max_budget", array("text" => __("Max Budget", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
            <div class="col-lg-9">
                <div class="input-group">
                    <span
                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                    <?php $form->text("max_budget", array("attributes" => array("class" => "form-control"))) ?>
                </div>
                <span class="help-block m-b-none error-max_budget"><?php $form->error("max_budget") ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php else: ?>
        <div class="form-group <?php echo $model->has_error("budget") ? "has-error" : null ?>">
            <?php $form->label("budget", array("text" => __("Budget", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
            <div class="col-lg-9">
                <div class="input-group">
                    <span
                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                    <?php $form->text("budget", array("attributes" => array("class" => "form-control"))) ?>
                </div>
                <span class="help-block m-b-none error-budget"><?php $form->error("budget") ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
    <?php do_action('je_after_price_field', $model, $form) ?>
    <?php do_action('je_before_email_field', $model, $form) ?>

    <div class="form-group <?php echo $model->has_error("contact_email") ? "has-error" : null ?>">
        <?php $form->label("contact_email", array("text" => __("Contact Email", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <div class="input-group">
                <span class="input-group-addon">@</span>
                <?php $form->text("contact_email", array("attributes" => array("class" => "form-control"))) ?>
            </div>
            <span class="help-block m-b-none error-contact_email"><?php $form->error("contact_email") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_email_field', $model, $form) ?>
    <?php do_action('je_before_complete_date_field', $model, $form) ?>
    <div class="form-group <?php echo $model->has_error("dead_line") ? "has-error" : null ?>">
        <?php $form->label("dead_line", array("text" => __("Completion Date", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <?php $form->text("dead_line", array("attributes" => array("class" => "form-control datepicker"))) ?>
            </div>
            <span class="help-block m-b-none error-dead_line"><?php $form->error("dead_line") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_complete_date_field', $model, $form) ?>
    <?php do_action('je_before_open_for_field', $model, $form) ?>
    <div class="form-group <?php echo $model->has_error("open_for") ? "has-error" : null ?>">
        <?php $form->label("open_for", array("text" => __("Job Open for", je()->domain), "attributes" => array("class" => "col-lg-3 control-label"))) ?>
        <div class="col-lg-9">
            <?php $days = je()->settings()->open_for_days;
            $days = array_filter(explode(',', $days));
            $data = array();
            foreach ($days as $day) {
                $data[$day] = $day . ' ' . __('Days', je()->domain);
            }
            $data = apply_filters('je_open_days_limit', $data);

            $form->select('open_for', array(
                'data' => $data,
                'nameless' => __('--Select--', je()->domain),
                'attributes' => array(
                    'class' => 'form-control'
                )
            )); ?>
            <span class="help-block m-b-none error-open_for"><?php $form->error("open_for") ?></span>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php do_action('je_after_open_for_field', $model, $form) ?>
    <?php do_action('je_job_after_form', $model, $form) ?>
    <?php $form->hidden('portfolios');
    wp_nonce_field('je_job_form') ?>
    <div class="row">
        <div class="col-md-12">
            <?php ig_uploader()->show_upload_control($model, 'portfolios', false, array(
                'title' => __("Attach specs examples or extra information", je()->domain)
            )) ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <?php if (je()->settings()->job_new_job_status == 'publish'): ?>
                <button class="btn btn-primary job-submit" name="status" value="publish"
                        type="submit"><?php _e('Publish', je()->domain) ?></button>
            <?php else: ?>
                <button type="submit" class="btn btn-primary job-submit" name="status"
                        value="pending"><?php _e('Submit for review', je()->domain) ?></button>
            <?php endif; ?>
            <?php if (je()->settings()->job_allow_draft == 1): ?>
                <button class="btn btn-info job-submit" name="status" value="draft"
                        type="submit"><?php _e('Save Draft', je()->domain) ?></button>
            <?php endif; ?>
        </div>
    </div>
    <?php $form->close(); ?>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $(".datepicker").datepicker({
            "dateFormat": "yy-mm-dd",
            minDate: "<?php echo date('Y-m-d') ?>",
            beforeShow: function (input, inst) {
                inst.dpDiv.wrap('<div class="ig-container"></div>');
            }
        });
        $('#jbp_skill_tag').select2({
            tags: <?php echo json_encode(get_terms('jbp_skills_tag', array('fields'=>'names', 'get' => 'all' ) ) ); ?>,
            placeholder: "<?php esc_attr_e('Add a tag, use commas to separate'); ?>",
            tokenSeparators: [","]
        });
        $('.job-submit').click(function () {
            $(this).addClass('disabled').text('<?php echo esc_js(__("Submitting...")) ?>');
        })
    })
</script>