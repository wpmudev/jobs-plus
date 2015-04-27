<?php
/**
 * @author:Hoang Ngo
 */
$paged = (get_query_var('je-paged')) ? get_query_var('je-paged') : 1;
$pages = $total_pages;
if (!$pages) {
    $pages = 1;
}
$range = 4;
$showitems = ($range * 2) + 1;
?>


<div class="jbp-navigation group"><!--begin .jbp-navigation-->

    <?php if ($pages > 1) : ?>

        <div class="jbp-pagination"><!--begin .jbp-pagination-->

            <span><?php printf(__('Page %1$d of %2$d', je()->domain), $paged, $pages); ?></span>

            <?php if ($paged > 2 && $paged > $range + 1 && $showitems < $pages): ?>
                <a href="<?php echo esc_url(add_query_arg('je-paged', 1)); ?>">&laquo;<?php _e('First', je()->domain); ?></a>
            <?php endif; ?>

            <?php if ($paged > 1 && $showitems < $pages) : ?>
                <a href="<?php echo esc_url(add_query_arg('je-paged', $paged - 1)); ?>">&lsaquo;<?php _e('Previous', je()->domain); ?></a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++) :
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)):
                    echo ($paged == $i) ? '<span class="current">' . $i . '</span>' : '<a href="' . esc_url(add_query_arg('je-paged', $i)) . '" class="inactive">' . $i . '</a>';
                endif;
            endfor;

            if ($paged < $pages && $showitems < $pages) : ?>
                <a href="<?php echo esc_url(add_query_arg('je-paged', $paged + 1)); ?>"><?php _e('Next', je()->domain); ?>&rsaquo;</a>
            <?php endif; ?>

            <?php if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages): ?>
                <a href="<?php echo esc_url(add_query_arg('je-paged', $pages)); ?>"><?php _e('Last', je()->domain); ?>&raquo;</a>
            <?php endif; ?>

        </div> <!--end .jbp-pagination-->

    <?php endif; ?>
</div><!--end .jbp-navigation -->