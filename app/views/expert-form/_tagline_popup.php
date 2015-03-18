<div class="hide" id="tagline-template">
    <form class="can-edit-form form-horizontal" style="width100%;max-width: 350px">
        <div class="form-group">
            <div class="col-lg-12">
                <textarea name="tagline" class="form-control input-sm validate[required]"
                          style="height: 150px"><?php echo $model->short_description ?></textarea>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-xs btn-primary"><?php _e("Save", je()->domain) ?></button>
                <button type="button"
                        class="btn btn-xs btn-default can-edit-cancel"><?php _e("Cancel", je()->domain) ?></button>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        
    })
</script>