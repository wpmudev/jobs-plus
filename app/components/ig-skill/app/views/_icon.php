<div class="skill-bar" data-id="<?php echo $model->name ?>">
    <h5><?php echo $model->name ?></h5>

    <div title="<?php esc_attr_e("Please click here for update data", ig_skill()->domain) ?>"
         class="progress edit-skill" id="indicator-<?php echo $model->name ?>">
        <div class="<?php echo $model->css ?>" role="progressbar" aria-valuenow="<?php echo $model->value ?>"
             style="width: <?php echo $model->value ?>%;">
            <?php echo $model->value ?>%
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function ($) {
        $("#indicator-<?php echo $model->name ?>").progressbar({
            value: '<?php echo $model->value ?>'
        });
    })
</script>