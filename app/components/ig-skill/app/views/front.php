<ul class="jbp-socials" style="padding-left: 0">
    <?php foreach ($models as $model): ?>
        <div class="skill-bar" data-value="<?php echo $model->value ?>" data-id="<?php echo $model->name ?>">
            <h5><?php echo $model->name ?></h5>

            <div class="progress edit-skill">
                <div class="<?php echo $model->css ?>" style="width: <?php echo $model->value ?>%;">
                    <?php echo $model->value ?>%
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</ul>