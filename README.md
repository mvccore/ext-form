# SimpleForm

[![Latest Stable Version](https://img.shields.io/badge/Stable-v3.1.0-brightgreen.svg?style=plastic)](https://github.com/mvccore/example-helloworld/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://github.com/mvccore/example-helloworld/blob/master/LICENCE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.3-brightgreen.svg?style=plastic)

PHP Library to render web forms with classic user controls and some HTML5 controls, 
to handle and validate submited user data, to manage forms sessions for default values, 
to manage user input errors and to extend and develop custom fields and field groups.

## Installation
```shell
composer require mvccore/simpleform
```

## Features
- creates, renders and submits dynamicly created web forms without needs to specify 
  any static model class for form model (like in stupid classic .NET MVC forms)
- possibility to extend form itself, any field, field group or validator
- automaticly preconfigured controls for specific types with predefined validators
- every form (GET/POST...) has it's own cross site request forgery (CSRF) hidden 
	input with token name and token value to check if form was submitted by specific 
	user session and not by any foreing atacker javascript
- control types:
	- text inputs: text, password, email, hidden
	- number inputs: number, range (slider), multi range
	- selects: select, multi-select, country select
	- checkboxes: single checkbox, checkbox group
	- radio buttons
	- buttons: submit button, reset button, common button, input:submit
	- textarea
	- dates: date, time, datetime
	- no-type input to extend basic input with your custom functionality
- submit validators (each form control has naturaly configured validator(s) by type):
	- required (for all fields by it's configuration)
	- texts:
		- safe string (safe characters for database operations)
		- max. length
		- preg_replace pattern
		- special:
			- email
			- url
			- ZIP code
			- phone
	- numbers:
		- integer
		- number
		- float
		- range (min. & max.)
	- options
		- checkbox - checked
		- select - value in options
		- min/max selected options
	- special
		- EU company ID/VAT ID
- custom js/css assets for any field type
	- possible to render immediately after form HTML body
	- possible to render as external linked file by custom renderer or custom response appender
- error messages - stored in session only for one hook
- loading default values or previous submit values from session
- clearing session after submitting
- declarating error url, success url (next step url for special developer implementations)
- templates rendering automaticly or by custom template
	- naturaly rendered form has each control in empty div
	- rendered custom template shoud have any content and CSRF tokens are creted
	  automaticly by $form->RenderFormBegin();
	- any complex form control shoud have also it's own custom template
	- any form or control template has automaticly asigned properties 
	  from it's local $this context and original $controller->View from 
	  controller passed into form __construct() is asigned into $this->View.
- every visible form shoud be translated by configured translator callable
- form have build-in language property (for translator) and locale for advanced fields and validators
- posibility to extend build-in javascripts by checkout and extend: "[**mvccore/simpleform-custom-js**](https://github.com/mvccore/simpleform-custom-js)"

## Examples
- [**Application Questionnaires (mvccore/app-questionnaires)**](https://github.com/mvccore/app-questionnaires)

## Basic Example

### Form Initialization At 'Contact::Default' Route:
```php
$form = (new SimpleForm($mvcCoreController))
	->SetId('newsletter')
	->SetAction(
		$mvcCoreController->Url('Contact::Submit')
	)
	->SetSuccessUrl(
		$mvcCoreController->Url('Contact::Submitted')
	)
	->SetErrorUrl(
		$mvcCoreController->Url('Contact::Default')
	);
$email = (new SimpleForm_Email)
	->SetName('mail')
	->SetLabel('Your email:')
	->SetRequired();
$submit = (new SimpleForm_SubmitButton)
	->SetName('submit')
	->SetValue('Send');
$form->AddFields($email, $submit);
$mvcCoreController->view->newsletterForm = $form;
```

### Form Rendering In Template At 'Contact::Default' Route:
```php
<body>
	<?php echo $this->newsletterForm; ?>
</body>
```

### Form Submitting At 'Contact::Submit' Route:
```php
// ... form initialization again into var: $form 

// process all configured validators by: $form->Submit();
list ($result, $data, $errors) = $form->Submit();

// if data has been submitted successfuly, 
// store user email somewhere in database:
if ($result == SimpleForm::RESULT_SUCCESS) {
	// store user email somewhere by any custom model class (User):
	(new User())->SetEmail($data['mail'])->Save();
	// clear form session space to not display filled 
	// data by current user to another users
	$form->ClearSession();
}

// redirect user to configured success 
// or error url (by internal $form->Result property):
$form->RedirectAfterSubmit();
```
