# Lead Generation Shortcode Test

This is a WordPress plugin proof of concept showing how to add a lead generation form, with submission via ajax, through a shortcode interface and storage within a "Customer" custom post type. Certain aspects of the shortcode can be customized via custom attributes as shown below...

### Shortcode usage

- [dws_lead_gen_form]

### Shortcode Attributes
The following attributes can be used to control the form's output...

- name_label: change the name field label
- name_maxlength: change the name field maxlength
- phone_label: change the phone field label
- phone_maxlength: change the phone field maxlength
- email_label: change the email field label
- email_maxlength: change the email field maxlength
- budget_label: change the budget field label
- budget_maxlength: change the budget field maxlength
- message_label: change the message field label
- message_maxlength: change the message field maxlength
- message_rows: change the message field rows
- message_cols: change the message field columns
- submit_label: change the submit button label

### Examples:

[dws_lead_gen_form submit_label="Submit this form"]

[dws_lead_gen_form phone_label="Mobile Phone" message_label="What do you want to tell us?"]