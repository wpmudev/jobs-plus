<div class="ig-container">
    <?php if ($this->has_flash('job_deleted')): ?>
        <div class="alert alert-success">
            <?php echo $this->get_flash('job_deleted') ?>
        </div>
    <?php endif; ?>
    <table class="table table-hover table-striped table-bordered">
        <thead>
        <th><?php _e('Name', je()->domain) ?></th>
        <th><?php _e('Views', je()->domain) ?></th>
        <th><?php _e('Likes', je()->domain) ?></th>
        <th><?php _e('Status', je()->domain) ?></th>
        <th></th>
        </thead>
        <tbody>
        <?php if (count($models)): ?>
            <?php foreach ($models as $model): ?>
                <tr>
                    <td>
                        <a href="<?php echo get_permalink($model->id) ?>"><?php echo $model->first_name . ' ' . $model->last_name ?></a>
                    </td>
                    <td><?php echo $model->get_view_count() ?></td>
                    <td><?php echo $model->get_like_count() ?></td>
                    <td><?php echo ucfirst($model->status) ?></td>
                    <td style="width: 120px">
                        <a class="btn btn-primary btn-sm" href="<?php echo esc_url(add_query_arg(array(
                            'pro' => $model->id
                        ), get_permalink(je()->pages->page(JE_Page_Factory::EXPERT_EDIT)))) ?>"><?php _e('Edit', je()->domain) ?></a>

                        <form class="frm-delete" method="post" style="display: inline-block">
                            <input name="expert_id" type="hidden" value="<?php echo $model->id ?>">
                            <?php wp_nonce_field('delete_expert_' . $model->id) ?>
                            <button name="delete_expert" class="btn btn-danger btn-sm"
                                    type="submit"><?php _e('Delete', je()->domain) ?></button>
                        </form>

                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5"><?php _e('You don\'t have any profile.', je()->domain) ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.frm-delete').submit(function () {
            if (confirm('<?php echo esc_js(__('Are you sure?',je()->domain) )?>')) {

            } else {
                return false;
            }
        })
    })
</script>
