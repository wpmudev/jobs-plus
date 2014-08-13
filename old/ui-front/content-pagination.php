<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @package content-pagination.php
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
global $wp_query;

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$pages = $wp_query->max_num_pages;

if( ! $pages) $pages = 1;
$range = 4;
$showitems = ($range * 2) + 1;

?>
<div class="jbp-navigation group" ><!--begin .jbp-navigation-->

	<?php if ( $pages > 1 ) : ?>

	<div class="jbp-pagination"><!--begin .jbp-pagination-->

		<span><?php printf( __('Page %1$d of %2$d',JBP_TEXT_DOMAIN), $paged, $pages); ?></span>

		<?php if($paged > 2 && $paged > $range+1 && $showitems < $pages): ?>
		<a href="<?php echo get_pagenum_link(1); ?>">&laquo;<?php esc_html_e('First',JBP_TEXT_DOMAIN); ?></a>
		<?php endif; ?>

		<?php if($paged > 1 && $showitems < $pages) : ?>
		<a href="<?php echo get_pagenum_link($paged - 1); ?>">&lsaquo;<?php esc_html_e('Previous',JBP_TEXT_DOMAIN); ?></a>
		<?php endif; ?>

		<?php for ($i=1;$i <= $pages;$i++) :
		if (1 != $pages && ( !($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )):
		echo ($paged == $i) ? '<span class="current">' . $i . '</span>' : '<a href="' . get_pagenum_link($i) . '" class="inactive">' . $i . '</a>';
		endif;
		endfor;

		if ($paged < $pages && $showitems < $pages) : ?>
		<a href="<?php echo get_pagenum_link($paged + 1); ?>"><?php esc_html_e('Next',JBP_TEXT_DOMAIN); ?>&rsaquo;</a>
		<?php endif; ?>

		<?php if ($paged < $pages - 1 &&  $paged + $range - 1 < $pages && $showitems < $pages): ?>
		<a href="<?php echo get_pagenum_link($pages); ?>"><?php esc_html_e('Last', JBP_TEXT_DOMAIN); ?>&raquo;</a>
		<?php endif; ?>

	</div> <!--end .jbp-pagination-->

	<?php endif; ?>
</div><!--end .jbp-navigation -->
