<div class="wrap">
    <div class="ig-container">
        <div class="page-header">
            <h2><?php _e("Add an Expert", je()->domain) ?></h2>
        </div>
        <?php $this->render_partial('backend/experts/_form', array(
            'model' => $model
        )) ?>
    </div>
</div>