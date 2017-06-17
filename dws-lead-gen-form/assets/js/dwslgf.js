(function($) {
	$(function() {
		$('.dwslgf-form').on( 'submit', function(e) {
			e.preventDefault();
			var submissionFormData = $(this).serialize();
			var form = $(this);
			var formData = 'action=' + dwslgf.action + '&_ajax_nonce=' + dwslgf.nonce + '&' + submissionFormData;
			form.find('.form-msg').remove();
			$.ajax({
				type: 'post',
				url: dwslgf.ajax_url,
				data: formData,
				success: function(data) {
					console.log(data);
					var className;
					if ( false === data.success ) {
						className = 'error';
					} else {
						className = 'success';
						clearForm(form);
					}
					form.append( '<p class="form-msg ' + className + '">' + data.data.msg + '</p>')
				}
			});
		});

		function clearForm( form ) {
			form.find('input, textarea').val('');
		}
	});
})(jQuery);