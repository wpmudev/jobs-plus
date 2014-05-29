jQuery(function($) {

	function populate_checkboxes() {
		if($('#roles').length)
		{
			$('#ajax-loader').show();
			// clear checked fields
			$('#capabilities input').attr( 'checked', false );
			// set data
			var data = {
				action: 'jbp_get_caps',
				role: $('#roles option:selected').val(),
				post_type: $('#post_type').val()
			};
			// make the post request and process the response
			$.post(ajaxurl, data, function(response) {
				$('#ajax-loader').hide();
				$.each(response, function(index) {
					if ( index !== null ) {
						$('input[name="capabilities[' + index + ']"]').attr( 'checked', true );
					}
				});
			});
		}
	}

	populate_checkboxes();

	$('#roles').change(populate_checkboxes);

	$('#save_roles').click(function() {
		$('#ajax-loader').show();
		var data = $(this).closest('form').serializeArray();
		$.post(ajaxurl, data, function(data) {
			$('#ajax-loader').hide();
		});
		return false;
	});
	
});
