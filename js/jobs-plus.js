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
		console.log( e );
		$this = $(this);
		$.post( $this.data('ajax'), {
			action: "rate_pro",
			post_id: $this.data('post_id'),
			rating: $this.rateit('value'),
			_wpnonce: $this.data('nonce')
		},
		function(data){},
		'text');
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
		$portfolio.editable().editable('show');
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
		$skill.editable().editable('show');;
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

	$('.pro-profile-wrapper').on('click', '.pro-content-editable', function(e) {
		e.stopPropagation();

		//if link clicked
		if(e.target.href && ! popupEnabled ) {
			//cludge to force focus on the new tab or window
			w = window.open( e.target.href, 'company' );
		}
		//if label clicked
		$('.editable').editable('hide');
		$(this).find('.editable').editable('show');
		//$('head').append('<style>.pro-content-editable:before{background-position: 0px 25px;}</style>');
		//$('head').append('<style>.pro-content-editable-open:before{background-position: 0px 25px;}</style>');
	});

	$('.jbp-button').contrastColor();
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
			$('#custom-fields-form .ct-field').prop('readonly', true);
			$('#custom-fields-form .description').css('visibility', 'hidden');
			$('.pro-content-editable').removeClass('pro-content-edit');
		} else {
			$('.show-on-edit').show();
			$('.hide-on-edit').hide();
			$('#custom-fields-form .ct-field').prop('readonly', false);
			$('#custom-fields-form .description').css('visibility', 'visible');
			$('.pro-content-editable').addClass('pro-content-edit');
			if(jbpAddPro) {
				$(".editable[data-name='post_title']").editable('enable');
			} else {
				$('.editable').editable('enable');
			}
		}
		magnificPopupAttach(jbpPopupEnabled);
	};

	window.jbpFirstField = function( $editables  ){
		$field = jbpAddPro ? $editables.filter("[data-name='post_title']") : $editables.filter("[data-name='_ct_jbp_pro_Tagline']");
		setTimeout(function() { $field.editable('show'); }, 300);
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
				this.$form.find('.editable-submit').button();
				this.$form.find('.editable-cancel').button();
				//}
			}
		});
	};

	//Create a dialog
	window.jbp_create_dialog = function(title, text, options) {
		return $("<div class='dialog' title='" + title + "'>" + text + "</div>").dialog(options);
	}

	//Check for required fields
	window.jbp_required_dialog = function( e, title, text) {
		var $empties = $('.editable-required');

		if( $empties.length > 0) {
			//Do something.
			e.preventDefault();
			e.stopPropagation();

			$empties.each(
			function(){
				text += $(this).data('label') + '<br />';
			});
			console.log(text);
			dlg = jbp_create_dialog(title, text,{
				width: '400',
				resize: 'auto',
				modal: true,
				buttons : { 'OK': function(){ $(this).dialog('close'); } },
				close: function(){ setTimeout( function(){
					$empties.first().editable('show');
					$('html, body').animate({scrollTop: $empties.first().offset().top-50}, 500);
				}, 300); }
			}
			);
			return false;
		}
		return true;
	}

	$.fn.contrastColor = function() {
		return this.each(function() {
			var bg = $(this).css('background-color');
			//use first opaque parent bg if element is transparent
			if(bg == 'transparent' || bg == 'rgba(0, 0, 0, 0)') {
				$(this).parents().each(function(){
					bg = $(this).css('background-color')
					if(bg != 'transparent' && bg != 'rgba(0, 0, 0, 0)') return false;
				});
				//exit if all parents are transparent
				if(bg == 'transparent' || bg == 'rgba(0, 0, 0, 0)') return false;
			}
			//get r,g,b and decide
			var rgb = bg.replace(/^(rgb|rgba)\(/,'').replace(/\)$/,'').replace(/\s/g,'').split(',');
			var yiq = ((rgb[0]*299)+(rgb[1]*587)+(rgb[2]*114))/1000;
			if(yiq >= 128) $(this).removeClass('light-color');
			else $(this).addClass('light-color');
			});
		};

	}(window.jQuery));

