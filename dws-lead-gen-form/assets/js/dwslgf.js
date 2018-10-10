( function ( $ ) {
	$( function () {
		$( '.dwslgf-form' ).on( 'submit', function ( e ) {
			e.preventDefault();
			var submissionFormData = $( this ).serialize();
			var form = $( this );
			var formData = 'action=' + dwslgf.action + '&_ajax_nonce=' + dwslgf.nonce + '&' + submissionFormData;
			var formBtn = $( '#dwslgf-submit' );
			form.find( '.form-msg' ).remove();
			formBtn.attr( 'disabled', true );
			$.ajax( {
				type: 'post',
				url: dwslgf.ajax_url,
				data: formData,
				success: function ( data ) {
					var className;
					if ( false === data.success ) {
						className = 'error';
					} else {
						className = 'success';
						clearForm( form );
					}
					form.append( '<p class="form-msg ' + className + '">' + data.data.msg + '</p>' );
					formBtn.attr( 'disabled', false );
				}
			} );
		} );

		function clearForm( form ) {
			form.find( 'input, textarea' ).val( '' );
		}
	} );
} )( jQuery );