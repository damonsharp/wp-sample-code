# Lead Generation Shortcode POC
WordPress plugin proof of concept that will provide a customizable shortcode for use on a page, post, or text widget. This shortcode outputs a lead generation form which the user can submit via AJAX and will sanitize and save the data within a "Customer" custom post type in the WordPress admin.

##### Highlights
- OOP/Composer architecture.
- Integration of third party meta field plugin (Fieldmanager);
- Custom Post Type and custom meta fields usage for Customers.
- Form submission via AJAX and storage within a custom post type.
- Functionality to give admins the ability to utilize the shortcode inside text widget areas.

### Shortcode usage
Place the following shortcode within post, page, or text widget content...

[dws_lead_gen_form]

### Shortcode Attributes
The following attributes can be used to control the shortcode's output...

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