<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Views_JobList extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        //lite is for landing page
        if (isset($this->lite)) {
            $lite = $this->lite;
        } else {
            $lite = false;
        }

        foreach ($this->chunks as $chunk): ?>
            <div class="row">
                <?php foreach ($chunk as $key => $col): ?>
                    <?php
                    $color = '';
                    if ($this->colors) {
                        $color = $this->colors[array_rand($this->colors)];
                    }
                    $model = $col['item'];
                    $size = $col['class'];

                    setup_postdata($model->get_raw_post());
                    ?>
                    <div style="<?php echo($key == 0 ? 'margin-left:0' : null) ?>"
                         class="jbp_job_item <?php echo $size; ?>">
                        <div class="jbp_job_except <?php echo $color ?>">
                            <div class="jbp_inside">
                                <h4>
                                    <a href="<?php echo get_permalink($model->id) ?>"><?php echo jbp_shorten_text($model->job_title, 50) ?></a>
                                </h4>
                                <?php if ($lite == false): ?>
                                    <div class="ellipsis">
                                        <?php
                                        $content = get_the_content();
                                        //cal the except words count base on element width
                                        $sub = count($chunk);
                                        if (isset($col['text_length'])) {
                                            $sub = $col['text_length'];
                                        }
                                        $charlength = 200 / $sub;
                                        echo apply_filters('jbp_job_list_content', jbp_shorten_text($content, $charlength), $content, $charlength);

                                        ?>
                                    </div>
                                <?php endif; ?>
                                <div class="jbp_job_bottom">
                                    <div class="jbp_terms">
                                        <?php echo the_terms($model->id, 'jbp_category', __('Categories: ', JBP_TEXT_DOMAIN), ', ', ''); ?>
                                    </div>
                                    <div class="jbp_meta">
                                        <div class="pull-left">
                                            <?php esc_html_e('Due: ', JBP_TEXT_DOMAIN); ?><?php echo $model->get_end_date() ?>
                                        </div>
                                        <div class="pull-right">
                                            <?php esc_html_e('Budget: ', JBP_TEXT_DOMAIN); ?>
                                            <?php
                                            $model->render_prices('max');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div style="clear: both"></div>
            </div>
        <?php endforeach; ?>
        <?php
        $paging = new JobsExperts_Core_Views_Pagination(array(
            'total_pages' => $this->total_pages
        ));
        $paging->render();
        ?>
    <?php
    }
}