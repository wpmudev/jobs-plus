<div class="ig-container">
    <div id="tabs">
        <ul class="nav nav-tabs">
            <li><a href="#my-wallets"><?php _e("Wallets", je()->domain) ?></a></li>
            <li><a href="#purcharse-history"><?php _e("Purchase History", je()->domain) ?></a></li>
        </ul>
        <div class="tab-content" style="padding: 0 10px">
            <div id="my-wallets">
                <h3><?php _e("Credits Balance", je()->domain) ?></h3>

                <p><?php _e("Credits", je()->domain) ?> <span class="label label-info">
                    <?php echo User_Credit_Model::get_balance(get_current_user_id()) ?>
                </span>
                </p>

                <p>
                    <a class="btn btn-info" href="<?php echo get_permalink(ig_wallet()->settings()->plans_page) ?>">
                        <?php _e("Purchase Credits", je()->domain) ?>
                    </a>
                </p>
            </div>
            <div id="purcharse-history">
                <?php $logs = User_Credit_Model::get_logs(); ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th><?php _e("Date", je()->domain) ?></th>
                        <th><?php _e("Detail", je()->domain) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (is_array($logs) && count($logs)): ?>
                        <?php
                        $date_format = get_option('date_format');
                        $time_format = get_option('time_format');
                        ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo date($date_format . ' ' . $time_format, $log['date']) ?></td>
                                <td><?php echo $log['reason'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"><?php _e('No data available', je()->domain) ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function ($) {
        $("#tabs").tabs();
    });
</script>