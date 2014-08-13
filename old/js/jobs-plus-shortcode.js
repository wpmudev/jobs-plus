jQuery(document).ready(function ($) {
	$('.jbp-expert-wg-mask').hide();
	$('.expert-widgetbar li').mouseenter(function(){
		var mask = $(this).find('.jbp-expert-wg-mask').first().slideDown(200);
	}).mouseleave(function(){
		var mask = $(this).find('.jbp-expert-wg-mask').first().slideUp(200);
	})
})