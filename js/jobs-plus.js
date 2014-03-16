/**
* @package Jobs Board
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

//Global variables
window.jbpNewPro = false;
window.jbpNewJob = false;
window.jbpPopupEnabled = false;
window.canEditPro = false;
window.canEditJob = false;
window.magnificPopupAttach
window.jbpPopup;

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

	magnificPopupAttach = function ( enable ){
		$('.portfolio').magnificPopup({
			delegate: 'a:first-child' // child items selector, by clicking on it popup will open
			,type: 'image'
			,gallery: { enabled: true }
			,disableOn: function(){ return enable;}
		});
	}

	jbpPopup = function(){
		jbpPopupEnabled = !jbpPopupEnabled;
		if(jbpPopupEnabled){
			$('.show-on-edit').hide();
			$('.hide-on-edit').show();
			$('.editable').editable('disable');
		} else {
			$('.show-on-edit').show();
			$('.hide-on-edit').hide();
			$('.editable').editable('enable');
		}
		magnificPopupAttach(jbpPopupEnabled);
	}

});