<div class="wrap">
    <h2><?php _e("Credit Rules", je()->domain) ?></h2><br/>

    <div class="ig-container">
        <div id="accordion">
            <?php do_action('je_credit_rules') ?>
        </div>
    </div>
</div>
<script>
    jQuery(function ($) {
        $('.panel-heading').css('cursor','pointer').click(function () {
            var body = $(this).next();
            if (body.is(':hidden')) {
                body.slideDown();
            } else {
                body.slideUp();
            }
        })
    });
</script>