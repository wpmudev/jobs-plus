<ul class="jbp-socials" style="padding-left: 0">
    <?php foreach ($models as $model) {
        echo '<li>';
        $this->render_partial('_icon', array(
            'data' => $model->export(),
            'social' => ig_social_wall()->social($model->name)
        ));
        echo '</li>';
    } ?>
</ul>
<script type="text/javascript">
    jQuery(function ($) {
        if ($.fn.tooltip != undefined) {
            $('.je-tooltip').tooltip({
                position: {
                    my: "center bottom-15",
                    at: "center top",
                    using: function (position, feedback) {
                        $(this).css(position);
                        $("<div>")
                            .addClass("arrow bottom")
                            .addClass(feedback.vertical)
                            .addClass(feedback.horizontal)
                            .appendTo(this);
                    },
                    open: function (event, ui) {
                        console.log(event);
                        console.log(ui);
                    }
                },
                tooltipClass: 'ig-container'
            });
        }
    })
</script>