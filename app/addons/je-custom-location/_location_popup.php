<div class="hide" id="location-template">
    <form class="can-edit-form form-horizontal" style="width100%;max-width: 350px">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php _e("Location", je()->domain) ?></label>

            <div class="col-lg-9">
                <div class="input-group">
                    <?php echo IG_Form::text(array(
                        'name' => 'location',
                        'attributes' => array(
                            'class' => 'form-control input-sm clocation'
                        )
                    )) ?>
                    <?php
                    $api = get_option('je_custom_location_google_api');
                    if ($api):
                        ?>
                        <span class="input-group-btn">
                        <button type="button" disabled class="btn btn-sm btn-default location-detect">
                            <i class="fa fa-crosshairs"></i></button>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-3">
                <button type="submit" class="btn btn-xs btn-primary"><?php _e("Save", je()->domain) ?></button>
                <button type="button"
                        class="btn btn-xs btn-default can-edit-cancel"><?php _e("Cancel", je()->domain) ?></button>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>