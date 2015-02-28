<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?php _e("Expert profile saved", je()->domain) ?></strong>
    </div>
    <div class="panel-body">
        <p><?php _e("Credits use for create new expert profile", je()->domain) ?></p>

        <?php $form = new IG_Active_Form($model);
        $form->open(array("attributes" => array("class" => "form-horizontal", "id" => "expert-saved-setting"))); ?>
        <div class="form-group <?php echo $model->has_error("status") ? "has-error" : null ?>">
            <?php $form->label("status", array("text" => "Enable", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
            <div class="col-lg-10">
                <div class="checkbox">
                    <label>
                        <?php $form->hidden('status', array(
                            'value' => 0,
                        )) ?>
                        <?php $form->checkbox("status", array(
                            "attributes" => array("class" => "", "value" => 1),
                        )) ?></label>

                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group <?php echo $model->has_error("credit_use") ? "has-error" : null ?>">
            <?php $form->label("credit_use", array("text" => "Credit spend", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
            <div class="col-lg-5">
                <?php $form->number("credit_use", array("attributes" => array(
                    "class" => "form-control",
                    "min" => 0
                ))) ?>
                <span class="help-block m-b-none error-credit_use"><?php $form->error("credit_use") ?></span>
                <span class="help-block">
                    <?php _e("How many credits spend for each profile", je()->domain) ?>
                </span>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group <?php echo $model->has_error("free_from") ? "has-error" : null ?>">
            <?php $form->label("free_from", array("text" => "Free From", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
            <div class="col-lg-5">
                <?php $form->number("free_from", array("attributes" => array("class" => "form-control", "min" => 0))) ?>
                <span class="help-block m-b-none error-free_from"><?php $form->error("free_from") ?></span>
                <span class="help-block">
                    <?php _e("How many profiles user need to pay before free submit", je()->domain) ?>
                </span>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group <?php echo $model->has_error("free_for") ? "has-error" : null ?>">
            <?php $form->label("free_for", array("text" => "Free For", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
            <div class="col-lg-5">
                <?php
                $roles = get_editable_roles();
                $data = array();
                foreach ($roles as $key => $role) {
                    $data[$key] = $role['name'];
                }
                $form->select("free_for", array(
                    "data" => $data,
                    "attributes" => array(
                        "class" => "form-control",
                        "multiple" => "multiple"
                    ))) ?>
                <span class="help-block">
                    <?php _e("Free submit for specific roles", je()->domain) ?>
                </span>
                <span class="help-block m-b-none error-free_for"><?php $form->error("free_for") ?></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-5 col-md-offset-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <?php _e("Save Changes", je()->domain) ?>
                </button>
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="hidden" name="action" value="expert_saved_setting">
        <?php $form->close(); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#expert-saved-setting").on('submit', function () {
            var that = $(this);
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: $(this).serialize(),
                beforeSend: function () {
                    that.find('button').attr('disabled', 'disabled');
                },
                success: function (data) {
                    that.find('button').removeAttr('disabled');
                    that.find('.m-b-none').html('');
                    if (data.status == 'fail') {
                        $.each(data.errors, function (index, value) {
                            $('.error-' + index).html(value);
                        })
                    } else {
                        location.reload();
                    }
                }
            })
            return false;
        })
    })
</script>