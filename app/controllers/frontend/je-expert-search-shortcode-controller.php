<?php

/**
 * @author:Hoang Ngo
 */
class JE_Expert_Search_Shortcode_Controller extends IG_Request
{
    public function __construct()
    {
        add_shortcode('jbp-expert-search', array(&$this, 'main'));
    }

    function main($atts)
    {
        $search = '';
        if ( isset( $_GET['query'] ) ) {
                $search = $args['s'] = $_GET['query'];
        }
        $atts = shortcode_atts( array(
                'search_placeholder' => __('Search For Expert', je()->domain)
	), $atts, 'jbp-expert-search' );
        
        ob_start();
        ?>
        <form method="get"
                  action="<?php echo get_post_type_archive_link('jbp_pro') ?>">
                <div class="search input-group input-group-lg has-feedback" role="search" id="mySearch">
                    <input style="box-sizing:border-box;border-radius: 0" name="query" value="<?php echo $search ?>" type="search"
                           class="form-control pro-search"
                           placeholder="<?php echo $atts['search_placeholder'] ?>"/>
                            <span class="input-group-btn">
                                <button class="btn btn-default" style="border-radius: 0" type="submit">
                                    <span class="glyphicon glyphicon-search"></span>
                                    <span class="sr-only">Search</span>
                                </button>
                              </span>
                </div>
            </form>
        <?php
        return ob_get_clean();
    }
}