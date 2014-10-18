<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_AddOn_Message_Views_MessageAdmin extends JobsExperts_Framework_Render
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        ?>
        <div class="wrap">
            <div class="hn-container" style="width: 100%">
                <div class="row">
                    <div class="col-md-12">
                        <h3><?php _e("Message", JBP_TEXT_DOMAIN) ?></h3>
                        <?php
                        $tbl = new JobsExperts_AddOn_Message_Tables_Message(new JobsExperts_AddOn_Message_Models_Message());
                        $tbl->prepare_items();

                        ?>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php $tbl->search_box(__("Search", JBP_TEXT_DOMAIN), 'message_search'); ?>
                                <div class="clearfix"></div>
                                <?php $tbl->display(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    <?php
    }
}