<?php foreach ($models as $model): ?>
    <li class="je-dd-item" data-id="<?php echo $model->id ?>">
        <a href="">
            <?php echo $model->title ?> - <?php echo $model->type ?>
        </a>
        <a href="#" class="je-dd-item-open pull-right">
            <i class="fa fa-toggle-down"></i>
        </a>

        <!--<div class="je-dd-item-content">
            <form method="post">
                <label><?php /*_e("Validation", je()->domain) */?></label>
                <select name="validation_rule">

                </select>
            </form>
        </div>-->
    </li>
<?php endforeach; ?>