/**
* @package Jobs Board
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

//Global variables
window.jbpAddPro = false;
window.jbpAddJob = false;
window.jbpPopupEnabled = false;
window.canEditPro = false;
window.canEditJob = false;

jQuery(document).ready( function($) {

	//rateit stars
	$(".rateit").on('over', function (event, value) { $(this).prop('title', tooltipvalues[value-1]); });

	$('.rateit').on('rated reset', function(e){
		$this = $(this);
		$.post( $this.data('ajax'), { action: "rate_pro", post_id: $this.data('post_id'), rating: $this.rateit('value'), _wpnonce: $this.data('nonce')}, function(data){}, 'text');
	});

	// Add Pro Portfolio
	$('#add-pro-portfolio-link').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		var $template = $($('#add-pro-portfolio').html()),
		$portfolio = $template.find('.editable'),
		$ul = $(this).siblings('ul'),
		count = $ul.find('li').size();
		$portfolio.attr('data-name', '_ct_jbp_pro_Portfolio['+count+']');
		$ul.append($template);
		$portfolio.editable('enable').editable('show');
	});

	// Add Job Portfolio
	$('#add-job-portfolio-link').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		var $template = $($('#add-job-portfolio').html()),
		$portfolio = $template.find('.editable'),
		$ul = $(this).siblings('ul'),
		count = $ul.find('li').size();
		$portfolio.attr('data-name', '_ct_jbp_job_Portfolio['+count+']');
		$ul.append($template);
		$portfolio.editable('enable').editable('show');
	});

	// Add Skills for sc-pro-skills.php
	$('#add-skill-link').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		var $template = $($('#add-skill').html()),
		$skill = $template.find('.editable'),
		$ul = $(this).siblings('ul'),
		count = $ul.find('li').size();
		$skill.attr('data-name', '_ct_jbp_pro_Skills['+count+']');
		$ul.append($template);
		$skill.editable('enable').editable('show');
	});

	// Add Social
	$('#add-social-link').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		var $template = $($('#add-social').html()),
		$social = $template.find('.editable'),
		$ul = $(this).siblings('ul'),
		count = $ul.find('li').size();
		$social.attr('data-name', '_ct_jbp_pro_Social['+count+']');
		$ul.append($template);
		$social.editable('enable').editable('show');
	});

});

(function($) {
	window.magnificPopupAttach = function( enable ){
		$('.portfolio').magnificPopup({
			delegate: 'a:first-child',
			type: 'image',
			gallery: { enabled: true },
			disableOn: function(){ return enable;}
		});
	};

	window.jbpPopup = function(){
		jbpPopupEnabled = !jbpPopupEnabled;
		if(jbpPopupEnabled){
			$('.show-on-edit').hide();
			$('.hide-on-edit').show();
			$('.editable').editable('disable');
		} else {
			$('.show-on-edit').show();
			$('.hide-on-edit').hide();
			if(jbpAddPro) {
			$(".editable[data-name='post_title']").editable('enable');
		} else {
			$('.editable').editable('enable');
		}
		}
		magnificPopupAttach(jbpPopupEnabled);
	};

	window.jbpFirstField = function( $editables  ){
		$field = jbpAddPro ? $editables.filter("[data-name='post_title']") : $editables.filter("[data-name='_ct_jbp_pro_First_Last']");
		$field.editable('show');
	};

	//Site wide defaults and fixup buttons
	window.jbpEditableDefaults = function(){

		$.fn.editable.defaults.mode = 'popup';
		//$.fn.editable.defaults.mode = 'inline';
		$.fn.editable.defaults.showbuttons = 'bottom';
		//$.fn.editable.defaults.disabled = true;

		// Overwrite done and cancel buttons
		$.fn.editableform.buttons = '<button type="button" class="editable-cancel">Cancel</button> '+
		'<button type="submit" class="editable-submit">Done</button>';

		$.extend($.fn.editableform.Constructor.prototype, {
			initButtons: function() {
				var $btn = this.$form.find('.editable-buttons');
				$btn.append($.fn.editableform.buttons);
				//if(this.options.showbuttons === 'bottom') {
				$btn.addClass('editable-buttons-bottom');
				//}
			}
		});
	};

}(window.jQuery));
