<div class="wrap">
    <h2><?php _e("Credit Plans", je()->domain) ?>
        <a href="" class="add-new-h2"><?php _e("Add new", je()->domain) ?></a></h2>

    <div class="ig-container">
        <?php if ($this->has_flash('plan_save')): ?>
            <div class="alert alert-success">
                <?php echo $this->get_flash('plan_save') ?>
            </div>
        <?php endif; ?>
        <br/>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-hover table-stripe">
                            <thead>
                            <tr>
                                <th><?php _e("Name", je()->domain) ?></th>
                                <th><?php _e("Credits", je()->domain) ?></th>
                                <th><?php _e("Cost", je()->domain) ?></th>
                                <th><?php _e("Sale price", je()->domain) ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($models)): ?>
                                <?php foreach ($models as $row): ?>
                                    <tr>
                                        <td>
                                            <?php echo $row->title ?>
                                        </td>
                                        <td>
                                            <?php echo $row->credits ?>
                                        </td>
                                        <td>
                                            <?php echo JobsExperts_Helper::format_currency('', $row->cost) ?>
                                        </td>
                                        <td>
                                            <?php echo $row->sale_price ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-xs"
                                               href="<?php echo admin_url(add_query_arg('id', $row->product_id, 'admin.php?page=ig-credit-plans')) ?>">
                                                <?php _e("Edit") ?></a>

                                            <form style="display: inline;" method="post">
                                                <?php wp_nonce_field('je_delete_plan', 'je_delete_plan_nonce') ?>
                                                <input type="hidden" name="id" value="<?php echo $row->product_id ?>">
                                                <button onclick="return confirm('Are you sure?')" type="submit"
                                                        class="btn btn-xs btn-danger">
                                                    <?php _e("Delete", je()->domain) ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <?php _e("No data available", je()->domain) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php $form = new IG_Active_Form($model);
                        $form->open(array("attributes" => array("class" => "form-horizontal"))); ?>
                        <div class="form-group <?php echo $model->has_error("title") ? "has-error" : null ?>">
                            <?php $form->label("title", array("text" => "Title", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <?php $form->text("title", array("attributes" => array("class" => "form-control"))) ?>
                                <span class="help-block m-b-none error-title"><?php $form->error("title") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("description") ? "has-error" : null ?>">
                            <?php $form->label("description", array("text" => "Description", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <?php $form->text_area("description", array("attributes" => array("class" => "form-control"))) ?>
                                <span
                                    class="help-block m-b-none error-description"><?php $form->error("description") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("credits") ? "has-error" : null ?>">
                            <?php $form->label("credits", array("text" => "Credits", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <?php $form->text("credits", array("attributes" => array("class" => "form-control"))) ?>
                                <span class="help-block m-b-none error-credits"><?php $form->error("credits") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("cost") ? "has-error" : null ?>">
                            <?php $form->label("cost", array("text" => "Cost", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <span
                                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                                    <?php $form->text("cost", array("attributes" => array("class" => "form-control"))) ?>
                                </div>
                                <span class="help-block m-b-none error-cost"><?php $form->error("cost") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group <?php echo $model->has_error("sale_price") ? "has-error" : null ?>">
                            <?php $form->label("sale_price", array("text" => "Sale price", "attributes" => array("class" => "col-lg-2 control-label"))) ?>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <span
                                        class="input-group-addon"><?php echo JobsExperts_Helper::format_currency(je()->settings()->currency) ?></span>
                                    <?php $form->text("sale_price", array("attributes" => array("class" => "form-control"))) ?>
                                </div>
                                <span class="help-block m-b-none error-cost"><?php $form->error("sale_price") ?></span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <?php $form->hidden('product_id') ?>
                        <hr class="no-margin">
                        <div class="form-group">
                            <div class="checkbox col-md-10 col-md-offset-2">
                                <label>
                                    <?php
                                    $form->hidden('append_credits_info', array(
                                        'value' => 0
                                    ));
                                    $form->checkbox('append_credits_info', array(
                                        'attributes' => array(
                                            'value' => 1
                                        )
                                    )) ?> <?php _e("Append credit info after product name & price, eg: <em>10$ for 20 credits</em>") ?>
                                </label>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button type="submit" name="je_credit_submit"
                                        value="<?php echo wp_create_nonce('ig_wallet_save_plan') ?>"
                                        class="btn btn-primary"><?php _e("Save", je()->domain) ?></button>
                                <a href="<?php echo admin_url('admin.php?page=ig-credit-plans') ?>"
                                   class="btn btn-default"><?php _e("Reset form", je()->domain) ?></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <?php $form->close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>