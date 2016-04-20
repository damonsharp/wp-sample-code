// Process the contact form via ajax
var MBB = MBB || {};

( function( $ ) {

    MBB.Contact = {

        init: function() {
            this.processContactForm();
        },

        processContactForm: function() {
            var self = this;
            $( '.contact-form' ).on( 'submit', function( e ) {
                e.preventDefault();
                var $contactForm = $( this ),
                    $formBtn = $( '#contact-submit' ),
                    $formMsg = $( '.form-msg' ),
                    $formErrorMsg = $( '.error-msg' ),
                    $formSuccessMsg = $( '.success-msg' ),
                    $formAction  = $contactForm.attr( 'action' ),
                    $formData    = $contactForm.serialize() + '&action=' + mbbAjax.action;
                // Remove form messages
                $formMsg.addClass( 'hidden' );
                // Disable form submit
                $formBtn.attr( 'disable', true );
                // Remove errors class
                $( '.error' ).removeClass( 'error' );
                $.ajax( {
                    type: "post",
                    dataType: "json",
                    url: $formAction,
                    data: $formData,
                    success: function( response ) {
                        $formBtn.attr( 'disable', false );
                        if ( 'errors' === response.status && response.payload ) {
                            $formErrorMsg.removeClass( 'hidden' );
                            var errors = response.payload,
                                name;
                            for ( name in errors ) {
                                $( '#' + name ).parent( 'p' ).addClass( 'error' );
                            }
                        }
                        if ( 'success' === response.status ){
                            $formSuccessMsg.removeClass( 'hidden' );
                            self.clearForm( $contactForm );
                        }
                    }
                } );
            } );
        },

        clearForm: function( $form ) {
            $form.find( 'input[type=text], input[type=email], textarea' ).val( '' );
        }

    }

    MBB.Contact.init();

} )( jQuery );