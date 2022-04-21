# MvcCore - Extension - Form

[![Latest Stable Version](https://img.shields.io/badge/Stable-v5.1.33-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-form/releases)
[![License](https://img.shields.io/badge/License-BSD%203-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.4-brightgreen.svg?style=plastic)

<br />

**THIS IS NOT PACKAGE WITH ALL FORM FIELD CLASSES!  
IF YOU JUST WANT TO CREATE A FORM VERY QUICKLY,  
USE PACKAGE [`mvccore/ext-form-all`](https://github.com/mvccore/ext-form-all) INSTEAD WITH ALL FORM FIELDS AND VALIDATORS!**

<br />

MvcCore extension with base form and field classes to create and render web forms with 
HTML5 controls, to handle and validate submited user data, to manage forms sessions 
for default values, to manage user input errors and to extend and develop custom fields 
and field groups.

This package can not exist alone without any form field extension(s) like: `mvccore/ext-form-field-*`
to have possiblity to create specific field(s) in youf form instance. If you want to use all 
fields and validators, use extension [`mvccore/ext-form-all`](https://github.com/mvccore/ext-form-all) instead of `mvccore/ext-form`.
This extension is only part of whole thing to not have too much field classes code in small aplications.

## Installation
```shell
composer require mvccore/ext-form
```

Than you need install any form field extension bellow like: `mvccore/ext-form-field-*`
```shell
composer require mvccore/ext-form-field-text
```

### Form Extensible Packages Map

- [`mvccore/ext-form-all`](https://github.com/mvccore/ext-form-all)&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;									- Main huge extension with all subextensions to render web forms, handle  
						&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;		submits, managing fields, sessions and errors, extension with all form packages.  
- `mvccore/ext-form`	&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&thinsp;&thinsp;&thinsp;												- Form extension with only base form and field classes.  
- [`mvccore/ext-form-field-text`](https://github.com/mvccore/ext-form-field-text)			&emsp;&emsp;&emsp;&nbsp;&thinsp;					- Fields extension with input field types text, email, password, search, tel, url
						&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;		and textarea. 
- [`mvccore/ext-form-field-numeric`](https://github.com/mvccore/ext-form-field-numeric)		&emsp;&emsp;										- Fields extension with input field types number and range.  
- [`mvccore/ext-form-field-selection`](https://github.com/mvccore/ext-form-field-selection)	&emsp;												- Fields extension with fields select, country select, checkbox(es), radios and color.  
- [`mvccore/ext-form-field-date`](https://github.com/mvccore/ext-form-field-date)			&emsp;&emsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			- Fields extension with input field types date, datetime, time, week and month.  
- [`mvccore/ext-form-field-button`](https://github.com/mvccore/ext-form-field-button)		&emsp;&emsp;&thinsp;&thinsp;						- Fields extension with button fields and input submit fields.  
- [`mvccore/ext-form-field-file`](https://github.com/mvccore/ext-form-field-file)			&emsp;&emsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			- Fields extension with input type file(s) with upload validation.  
- [`mvccore/ext-form-validator-special`](https://github.com/mvccore/ext-form-validator-special)&thinsp;&thinsp;									- Validators only extension with special text and numeric validators.


## Main Features
- create dynamic forms with variable assigned fields
- All HTML5 fields and atributes
- build in validator in every field by it's type
- automatic/customizable CSRF and XSS protection
- managing error messagess in session
- rendefing forms by default or by it's template
- rendefing any custom or build-in field by default or by it's template
- very extensible form class and field classes
- very extensible supporting javascripts for any fields
- translations, session data management
- different/custom result states for multiple submit buttons

### Fields
- build in fields in `mvccore/ext-form`:
	- base `Field` and `FieldsGroup` clases to extend any control with your custom functionality
	- `input:hidden`
	- `datalist`
- extended text fields in [`mvccore/ext-form-field-text`](https://github.com/mvccore/ext-form-field-text): 
	- `input:text`, `:password`, `:email`, `:search`, `:tel`, `:url`
	- `textarea`
- extended numeric fields in [`mvccore/ext-form-field-numeric`](https://github.com/mvccore/ext-form-field-numeric):
	- `input:number`, `:range` (slider) and multiple `input:range`
- extended selection fields in [`mvccore/ext-form-field-selection`](https://github.com/mvccore/ext-form-field-selection):
	- `select` (multi select)
	- country `select`
	- `input:checkbox`
	- `input:checkbox` group
	- `input:radio` (radio button, switch)
	- `input:color`
- extended date fields in [`mvccore/ext-form-field-date`](https://github.com/mvccore/ext-form-field-date):
	- `input:date`
	- `input:datetime-local`
	- `input:time`
	- `input:week`
	- `input:month`
- extended button fields in [`mvccore/ext-form-field-button`](https://github.com/mvccore/ext-form-field-button): 
	- `input:button`
	- `input:reset`
	- `input:submit`
	- `input:image`
	- `button:button`
	- `button:reset`
	- `button:submit`
- extended file field in [`mvccore/ext-form-field-file`](https://github.com/mvccore/ext-form-field-file): 
	- `input:file` with multiple option and validation

### Validators
Each form control has always naturaly configured validator(s) by type.
- build in validators in every field and in `mvccore/ext-form`:
	- required, readOnly, disabled...
	- safe string (keep characters to safely display submitted value in response - XSS protection)
- extended text fields in [`mvccore/ext-form-field-text`](https://github.com/mvccore/ext-form-field-text):
	- email - to check if email(s) is/are valid form or not
	- min. and max. text length
	- password - to check password strength by configured rules
	- pattern - PHP preg_match by `pattern` control attribute
	- url (to check if string is url or not)
	- tel (only to clean not allowed chars in phone number)
	- ZIP code (to check international ZIP code form)
- extended numeric fields in [`mvccore/ext-form-field-numeric`](https://github.com/mvccore/ext-form-field-numeric):
	- number (integer or float, min., max. and step)
	- range (min., max. and step)
- extended selection fields in [`mvccore/ext-form-field-selection`](https://github.com/mvccore/ext-form-field-selection):
	- checkbox - checked
	- value in options (check if submitted value exists in options or not, for selects, country selects, checkbox group and radios)
	- min./max. options selected
	- hexadecimal non-transparent color
- extended date fields in [`mvccore/ext-form-field-date`](https://github.com/mvccore/ext-form-field-date):
	- date - if date has correct format and check min. max. and step
	- datetime, time, week and month validators are extended from date validator
- extended file field in [`mvccore/ext-form-field-file`](https://github.com/mvccore/ext-form-field-file)
	- files validator to check everything possible in uploaded file(s) (by magic bytes and more)
- extended special validators in [`mvccore/ext-form-validator-special`](https://github.com/mvccore/ext-form-validator-special)
	- EU company ID/VAT ID
	- credit card (only checking number checksum)
	- iban bank account number (only checking number checksum)
	- hexadecimal number
	- IP address (IPv4 and IPV6 format checking)
	- international ZIP code format checking
	
### All About Features
- creates, renders and submits dynamicly created web forms without needs to specify 
  any static model class for form model (like in classic .NET MVC forms)
- implemented all HTML5 form attributes, all HTML5 fields and their HTML5 and older atributes
- every field has it's build-in default validator and it's possible to define any other or all field validators by you
- automatic/customizable CSRF and XSS protection
	- every form (GET/POST) has it's own cross site request forgery (CSRF) hidden 
  	  input with token name and token value to check if form was submitted by specific 
  	  user session and not by any foreing atacker javascript code
	- possiblity to manage CSRF protection by your own
	- all field where is possible to pass dangerous characters for XSS attack are protected by validator
  	  for XSS safe string by default. It's possible to remove this validator and implement it's own protection
- error messages - stored in session only for one form submit, rendered automaticly
- templates rendering automaticly or by custom template
	- naturaly rendered form has each control in empty div
	- rendered custom template shoud have any content and CSRF tokens are creted
	  automaticly by `$form->RenderFormBegin();`
	- any complex form control could have also it's own custom template
	- any form or control template has automaticly asigned properties 
	  from it's local `$this` context and original `$controller->View` from 
	  controller passed into form `__construct();` is asigned into `$this->View`.
- possibility to extend form itself, any field, field group or validator, you can use build-in interfaces, 
  abstract classes and traits
- custom js/css assets for any field type
	- possible to render immediately after form HTML body (by default)
	- possible to render as external linked file by custom renderer or custom response appending script/solution
	- posibility to extend build-in javascripts by checkout and extend:
	  "[**mvccore/ext-form-js**](https://github.com/mvccore/ext-form-js)"
- translations, session data management
	- every visible form shoud be translated by configured translator callable into form instance
	- loading default values or previous submit values from session
	- possible to optionally clear session after submitting
- different/custom result states for multiple submit buttons
	- declarating error url, success url (previous and next step url for special developer implementations)
	- possibility to define for any submit button custom form result state to recognize what to do next
- form have build-in language property (for translator) and locale property for advanced fields and validators

## Examples
- [**Example - CD Collection (mvccore/example-cdcol)**](https://github.com/mvccore/example-cdcol)
- [**Application - Questionnaires (mvccore/app-questionnaires)**](https://github.com/mvccore/app-questionnaires)

## Basic Example

### Form Initialization At 'Contact:Default' Route:
```php
$form = (new \MvcCore\Ext\Form($mvcCoreController))
	->SetId('newsletter')
	->SetAction(
		$mvcCoreController->Url('Contact:Submit')
	)
	->SetSuccessUrl(
		$mvcCoreController->Url('Contact:Submitted')
	)
	->SetErrorUrl(
		$mvcCoreController->Url('Contact:Default')
	);
$email = (new \MvcCore\Ext\Form\Email)
	->SetName('mail')
	->SetLabel('Your email:')
	->SetRequired();
$submit = (new \MvcCore\Ext\Form\SubmitButton)
	->SetName('submit')
	->SetValue('Send');
$form->AddFields($email, $submit);
$mvcCoreController->view->newsletterForm = $form;
```

### Form Rendering In Template At 'Contact:Default' Route:
```php
<body>
	<?php echo $this->newsletterForm; ?>
</body>
```

### Form Submitting At 'Contact:Submit' Route:
```php
// ... form initialization again into var: $form 

// process all configured validators by: $form->Submit();
list($result, $data, $errors) = $form->Submit();

// if data has been submitted successfuly, 
// store user email somewhere in database:
if ($result == \MvcCore\Ext\Form::RESULT_SUCCESS) {
	// store user email somewhere by any custom model class (User):
	(new User())->SetEmail($data['mail'])->Save();
	// clear form session space to not display filled 
	// data by current user to another users
	$form->ClearSession();
}

// redirect user to configured success 
// or error url (by internal $form->Result property):
$form->SubmittedRedirect();
```