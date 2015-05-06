<div class="ig-container">
    <div class="hn-container">
        <div class="jbp-landing-page">
            <div class="row">
                <div class="col-md-6 col-xs-12 col-sm-12">
                    <div class="page-header">
                        <h3><?php echo __('Recently posted Jobs', je()->domain) ?></h3>
                    </div>
                    <?php if (empty($jobs)): ?>
                        <div class="empty-records">
                            <p><?php echo __('No Jobs Found', je()->domain); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="jbp-job-list">
                            <div class="row" style="margin-right: 0">
                                <?php foreach ($jobs as $job): ?>
                                    <div class="jbp_job_item no-padding">
                                        <div class="jbp_job_except <?php echo $colors[array_rand($colors)] ?>">
                                            <div class="jbp_inside">
                                                <h4>
                                                    <a href="<?php
                                                    echo get_permalink($job->id) ?>">
                                                        <?php echo wp_trim_words($job->job_title, 4) ?>
                                                    </a>
                                                </h4>

                                                <div class="ellipsis">
                                                    <?php echo wp_trim_words($job->description, 15) ?>
                                                </div>
                                                <div class="jbp_job_bottom">
                                                    <div class="jbp_terms">
                                                        <?php echo the_terms($job->id, 'jbp_category', __('Categories: ', je()->domain), ', ', ''); ?>
                                                        <div class="jbp_meta">
                                                            <div class="pull-left">
                                                                <?php _e('Due: ', je()->domain); ?><?php echo $job->get_end_date() ?>
                                                            </div>
                                                            <div class="pull-right">
                                                                <?php _e('Budget: ', je()->domain); ?>
                                                                <?php
                                                                $job->render_prices('max');
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="add-record">
                        <a class="btn btn-primary"
                           href="<?php echo apply_filters('jbp_add_new_job_url', get_permalink(je()->pages->page(JE_Page_Factory::JOB_ADD))) ?>"><?php _e('Add a Job', je()->domain) ?></a>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 col-sm-12">
                    <div class="page-header">
                        <h3><?php echo __('Recent Experts', je()->domain) ?></h3>
                    </div>
                    <div class="jbp-pro-list">
                        <?php if (!empty($pros)): ?>
                            <div class="row" style="margin-left: 0">
                                <?php foreach ($pros as $pro): ?>
                                    <div class="jbp_expert_item col-sm-6 no-padding">
                                        <div class="jbp_pro_except">
                                            <div class="jbp_inside">
                                                <div class="meta_holder">
                                                    <div class="expert-avatar">
                                                        <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo $pro->get_avatar(640, true); ?></a>
                                                    </div>
                                                    <?php
                                                    $text = !empty($pro->short_description) ? $pro->short_description : $pro->biography;
                                                    $text = strip_tags($text);
                                                    ?>
                                                    <div class="jbp_pro_meta hidden-sx hidden-sm">
                                                        <div class="text-shorten">
                                                            <div class="text-shorten-inner">
                                                                <?php echo apply_filters('jbp_pro_listing_biography', $text) ?>
                                                            </div>
                                                        </div>

                                                        <div class="row no-margin jbp-pro-stat">
                                                            <div class="col-md-6 no-padding">
                                                                <span><?php echo $pro->get_view_count() ?></span>&nbsp;<i
                                                                    class="glyphicon glyphicon-eye-open"></i>
                                                                <small><?php _e('Views', je()->domain) ?></small>
                                                            </div>
                                                            <div class="col-md-6 no-padding">
                                                                <span><?php echo $pro->get_like_count() ?></span><i
                                                                    class="glyphicon glyphicon-heart text-warning"></i>
                                                                <small><?php _e('Likes', je()->domain) ?></small>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p>
                                                    <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo wp_trim_words($pro->name, 2); ?></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="clearfix"></div>
                            </div>
                        <?php else: ?>
                            <div class="empty-records">
                                <p><?php echo __('No Expert found', je()->domain) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="add-record">
                            <a class="btn btn-primary"
                               href="<?php echo apply_filters('jbp_add_new_expert_url', get_permalink(je()->pages->page(JE_Page_Factory::EXPERT_ADD))) ?>"><?php _e('Become Expert', je()->domain) ?></a>
                        </div>

                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>