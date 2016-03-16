<div class="ig-container">
    <div class="hn-container">
        <!--Search section-->
        <div class="expert-search">
            <form method="get"
                  action="<?php echo is_singular() ? get_permalink(get_the_ID()) : get_post_type_archive_link('jbp_pro') ?>">
                <div class="search input-group input-group-lg has-feedback" role="search" id="mySearch">
                    <input style="box-sizing:border-box;border-radius: 0" name="query" value="<?php echo $search ?>" type="search"
                           class="form-control pro-search"
                           placeholder="<?php echo __('Search For Expert', je()->domain) ?>"/>
<span class="input-group-btn">
    <button class="btn btn-default" style="border-radius: 0" type="submit">
        <span class="glyphicon glyphicon-search"></span>
        <span class="sr-only">Search</span>
    </button>
  </span>
                </div>
            </form>
            <?php do_action('jbp_expert_listing_after_search_form') ?>
            <div class="clearfix"></div>
        </div>
        <!--End search section-->
        <?php if (empty($chunks)): ?>
            <h2><?php echo __('No Expert Found', je()->domain); ?></h2>
        <?php else: ?>
            <div class="jbp-pro-list">
                <?php foreach ($chunks as $chunk): ?>
                    <div class="row no-margin">
                        <?php foreach ($chunk as $key => $col): ?>
                            <?php
                            $pro = $col['item'];
                            $size = $col['class'];
                            global $post;
                            setup_postdata(get_post($pro->id));

                            $avatar = $pro->get_avatar(640, true);
                            $name = $pro->name;
                            $charlength = 78 / ($col['text_length'] == 1 ? 1 : 1.3);

                            $name = wp_trim_words($name, $charlength);

                            ?>
                            <div style="<?php echo($key == 0 ? 'margin-left:0' : null) ?>"
                                 class="jbp_expert_item <?php echo $size; ?> no-padding">
                                <div class="jbp_pro_except">
                                    <div class="jbp_inside">
                                        <div class="meta_holder">
                                            <div class="expert-avatar">
                                                <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo $avatar ?></a>
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
                                            <a href="<?php echo get_permalink($pro->id) ?>"> <?php echo $name ?></a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="clear: both"></div>
                    </div>
                <?php endforeach; ?>
                <?php $this->render_partial('expert-archive/_paging', array(
                    'total_pages' => $total_pages
                )) ?>
            </div>
            <script type="text/javascript">
                jQuery(window).load(function () {
                    jQuery(document).ready(function ($) {
                        $('.meta_holder').mouseenter(function () {
                            $(this).find('.jbp_pro_meta').css('visibility', 'visible');
                        }).mouseleave(function () {
                            $(this).find('.jbp_pro_meta').css('visibility', 'hidden');
                        });

                        $('.text-shorten').each(function () {
                            var inner = $(this).find('.text-shorten-inner').first();
                            //cal inner height
                            if ($(this).innerHeight() < inner.height()) {
                                var text = $.trim(inner.text());
                                //height overflow, do something
                                var height = inner.height();
                                console.log(height + "-" + $(this).outerHeight());
                                var denst = text.length;
                                //cal the count word to trim
                                var c = ($(this).innerHeight() * denst) / height;
                                c = Math.floor(c);
                                //trim the word
                                var trimmed = text.substr(0, c);
                                trimmed = trimmed.substr(0, Math.min(trimmed.length, trimmed.lastIndexOf(" ") - 1));
                                //apply the text
                                inner.text(trimmed + '...');
                            }
                        });
                    })
                })
            </script>
        <?php endif; ?>
    </div>
</div>