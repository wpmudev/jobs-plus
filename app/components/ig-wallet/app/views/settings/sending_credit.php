<div class="page-header">
    <h4><?php _e("Sending free credit to user", je()->domain) ?></h4>
</div>
<?php $form = new IG_Active_Form($model);
$form->open(array("attributes" => array("class" => "form-horizontal"))); ?>
<div class="form-group <?php echo $model->has_error("amount") ? "has-error" : null ?>">
    <?php $form->label("amount", array("text" => "Amount", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
    <div class="col-lg-10">
        <?php $form->number("amount", array("attributes" => array("class" => "form-control"))) ?>
        <span class="help-block m-b-none error-amount"><?php $form->error("amount") ?></span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group <?php echo $model->has_error("user_id") ? "has-error" : null ?>">
    <?php $form->label("user_id", array("text" => "User", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
    <div class="col-lg-10">
        <?php
        $users = get_users();
        $form->select('user_id', array(
            'data' => array_combine(wp_list_pluck($users, 'ID'), wp_list_pluck($users, 'user_login')),
            "attributes" => array("class" => "form-control"),
            'nameless' => __("---Choose---")
        )) ?>
        <span class="help-block m-b-none error-user_id"><?php $form->error("user_id") ?></span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group <?php echo $model->has_error("reason") ? "has-error" : null ?>">
    <?php $form->label("reason", array("text" => "Reason", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
    <div class="col-lg-10">
        <?php $form->text_area("reason", array("attributes" => array("class" => "form-control"))) ?>
        <span class="help-block m-b-none error-reason"><?php $form->error("reason") ?></span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <div class="col-md-10 col-md-offset-2">
        <button class="btn btn-primary" name="je-credit-send" value="1"><?php _e("Submit", je()->domain) ?></button>
    </div>
</div>
<?php $form->close(); ?>