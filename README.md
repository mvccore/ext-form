# MvcCore Extension - Form

[![Latest Stable Version](https://img.shields.io/badge/Stable-v4.3.1-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-form/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.3-brightgreen.svg?style=plastic)

MvcCore extension to render web forms with classic user controls and some HTML5 controls, 
to handle and validate submited user data, to manage forms sessions for default values, 
to manage user input errors and to extend and develop custom fields and field groups.

## Installation
```shell
composer require mvccore/ext-form
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
	  automaticly by `$form->RenderFormBegin();`
	- any complex form control shoud have also it's own custom template
	- any form or control template has automaticly asigned properties 
	  from it's local `$this` context and original `$controller->View` from 
	  controller passed into form `__construct();` is asigned into `$this->View`.
- every visible form shoud be translated by configured translator callable
- form have build-in language property (for translator) and locale for advanced fields and validators
- posibility to extend build-in javascripts by checkout and extend: "[**mvccore/ext-form-js**](https://github.com/mvccore/ext-form-js)"

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

### Form Extensible Packages Map

- `ext-form-all`					MvcCore extension to render web forms, handle submits, managing fields, sessions and errors, extension with all form packages.
	- `ext-form`					MvcCore form extension with base classes.
		MvcCore - Extension - Form - form extension with base classes to create and render web forms with HTML5 controls, to handle and validate submited user data, to manage forms sessions for default values, to manage user input errors and to extend and develop custom fields and field groups.
	- `ext-form-field-text`			MvcCore form extension with input field types text, email, password, search, tel, url and textarea field.
		MvcCore - Extension - Form - Field - Text - form field types - input:text, input:email, input:password, input:search, input:tel, input:url and textarea.
	- `ext-form-field-numeric`		MvcCore form extension with input field types number and range.
		MvcCore - Extension - Form - Field - Numeric - form field types - input:number and input:range.
	- `ext-form-field-selection`	MvcCore form extension with fields select, country select, checkbox(es) and radios.
		MvcCore - Extension - Form - Field - Selection - form field types - select, country select, checkbox, radio button and checkboxes group.
	- `ext-form-field-date`			MvcCore form extension with input field types date, datetime, time, week and month.
		MvcCore - Extension - Form - Field - Date - form field types - input:date, input:datetime-local, input:time, input:week and input:month.
	- `ext-form-field-button`		MvcCore form extension with button fields and input submit fields.
		MvcCore - Extension - Form - Field - Button - form field types - button:submit, button:reset, input:submit, input:reset and image.
	- `ext-form-field-special`		MvcCore form extension with input type file and color.
		MvcCore - Extension - Form - Field - Special - form field types - input:file to upload files and input:color.
	- `ext-form-validator-special`	MvcCore form extension with special text and numeric validators.
		MvcCore - Extension - Form - Validator - Special - form special text and numeric validators - company ID (EU), company VAT ID (EU), credit card, hexadecimal number, IBAN bank account number, IP address and ZIP code.

	
- `ext-form`
	+ Form
		- AddMethods.php
		- Assets.php
		- ConfigProps.php
		- Csrf.php
		- FieldMethods.php
		- GetMethods.php
		- InternalProps.php
		- Rendering.php
		- Session.php
		- SetMethods.php
		- Submitting.php
	+ Forms
		+ assets
			+ fields
				- checkbox-group.js
				- range.css
				- range.js
				- reset.js
			- mvccore-form.js
		+ Field
			+ Props
				- AccessKey.php
				- AutoComplete.php
				- AutoFocus.php
				- DataList.php
				- Disabled.php
				- GroupCssClasses.php
				- GroupLabelAttrs.php
				- InputMode.php
				- Label.php
				- Multiple.php
				- Options.php
				- PlaceHolder.php
				- ReadOnly.php
				- Required.php
				- Size.php
				- TabIndex.php
				- VisibleField.php
				- Wrapper.php
			- Getters.php
			- Props.php
			- Rendering.php
			- Setters.php
		+ Fields
			- IDataList.php
			- IMultiple.php
			- ILabel.php
			- IOptions.php
			- ISubmit.php
			- IVisibleField.php
			- DataList.php
			- Hidden.php
		+ Validators
			- SafeString.php
		- Field.php
		- FieldsGroup.php
		- IError.php
		- IField.php
		- IFieldsGroup.php
		- IForm.php
		- IValidator.php
		- IView.php
		- Validator.php
		- View.php
	- Form.php

- `ext-form-field-text`
	+ Forms
		+ Field
			+ Props
				- MinMaxLength.php
				- Pattern.php
				- RowsColsWrap.php
				- SpellCheck.php
		+ Fields
			- IMinMaxLength.php
			- IPattern.php
			- Email.php
			- Password.php
			- Search.php
			- Tel.php
			- Text.php
			- Textarea.php
			- Url.php
	+ Forms
		+ Validators
			- Email.php
			- MinMaxLength.php
			- Password.php
			- Pattern.php
			- Tel.php
			- Url.php
			
- `ext-form-field-numeric`
	+ Forms
		+ Field
			+ Props
				- MinMaxStepNumbers.php
		+ Fields
			- IMinMaxStepNumbers.php
			- INumber.php
			- Number.php
			- Range.php
		+ Validators
			- Float.php
			- Integer.php
			- Number.php
			- Range.php
			
- `ext-form-field-selection`
	+ Forms
		+ Field
			+ Props
				- Checked.php
				- MinMaxOptions.php
				- NullOptionText.php
		+ Fields
			- IMinMaxOptions
			- IChecked.php.php
			- CountrySelect.php
			- Select.php
			- Checkbox.php
			- CheckboxGroup.php
			- RadioGroup.php
		+ Validators
			- MinMaxOptions.php
			- ValueInOptions.php
			
- `ext-form-field-dates`
	+ Forms
		+ Field
			+ Props
				- Format.php
				- MinMaxStepDates.php
		+ Fields
			- IFormat.php
			- IMinMaxStepDates.php
			- Date.php
			- DateTime.php
			- Month.php
			- Time.php
			- Week.php
		+ Validators
			- Date.php
			- DateTime.php
			- Month.php
			- Time.php
			- Week.php
		
- `ext-form-field-button`
	+ Forms
		+ Field
			+ Props
				- FormAttrs.php
				- Submit.php
				- WidthHeight.php
		+ Fields
			- Button.php
			- ResetButton.php
			- ResetInput.php
			- SubmitButton.php
			- SubmitInput.php
			- Image.php
		
- `ext-form-field-special`
	+ Forms
		+ Field
			+ Props
				- Files.php
		+ Fields
			- IFiles.php
			- Color.php
			- File.php
		+ Validators
			- Color.php
			- Files.php
		
- `ext-form-validator-special`
	+ Forms
		+ Validators
			- CompanyIdEu.php
			- CompanyVatIdEu.php
			- CreditCard.php
			- Hex.php
			- Iban.php
			- Ip.php
			- ZipCode.php