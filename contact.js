/*
Contact form submission for monarchbaseball.com
 */
var MBB = MBB || {};

document.addEventListener('DOMContentLoaded', () => {

	MBB.Contact = {

		init: function() {
			const contactForm = document.querySelector('.contact-form');

			contactForm.addEventListener('submit', (e) => {
				e.preventDefault();
				const formData = new FormData(contactForm);
				const formBtn = contactForm.querySelector('#contact-submit');
				const formErrorMsg = contactForm.querySelector('.error-msg');
				const formSuccessMsg = contactForm.querySelector('.success-msg');

				// Clear the form messages
				this.removeFormMessages(contactForm);

				// Disable form submit until processed
				this.disableFormBtn(formBtn);

				// Submit the ajax request
				fetch(mbbContact.ajaxUrl, {
					method: 'post',
					body: formData,
				}).then((response) => {
					return response.json();
				}).then((response) => {
					this.enableFormBtn(formBtn);
					if ('errors' === response.status && response.payload) {
						formErrorMsg.classList.remove('hidden');
						for (let name in response.payload) {
							document.querySelector(`#${name}`).parentNode.classList.add('error');
						}
					}
					if ('success' === response.status) {
						formSuccessMsg.classList.remove('hidden');
						this.clearFormInputs(contactForm);
					}
				});
			});
		},

		removeFormMessages: (contactForm) => {
			// Clear success and error messages
			const formMsgs = contactForm.querySelectorAll('.form-msg');
			[...formMsgs].forEach((el) => {
				el.classList.add('hidden');
			});

			// Clear form field error formatting
			const formFieldErrors = contactForm.querySelectorAll('.error');
			[...formFieldErrors].forEach((el) => {
				el.classList.remove('error');
			});
		},

		clearFormInputs: (contactForm) => {
			const contactFormFields = contactForm.querySelectorAll('input[type=text], input[type=email], textarea');
			[...contactFormFields].forEach((el) => {
				el.value = '';
			});
		},

		disableFormBtn: (formBtn) => {
			formBtn.setAttribute('disabled', 'disabled');
			formBtn.classList.add('disabled');
		},

		enableFormBtn: (formBtn) => {
			formBtn.removeAttribute('disabled');
			formBtn.classList.remove('disabled');
		}

	}

	MBB.Contact.init();

});
