# MvcCore Extension - Form

[![Latest Stable Version](https://img.shields.io/badge/Stable-v4.3.1-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-form/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.3-brightgreen.svg?style=plastic)

MvcCore extension with base classes to create and render web forms with HTML5 controls, 
to handle and validate submited user data, to manage forms sessions for default values, 
to manage user input errors and to extend and develop custom fields and field groups.

## Installation
```shell
composer require mvccore/ext-form
```

## Features
- creates, renders and submits dynamicly created web forms without needs to specify 
  any static model class for form model (like in classic .NET MVC forms)
- possibility to extend form itself, any field, field group or validator (interfaces, abstract classes)
- automaticly preconfigured HTML5 controls for specific types with predefined validators
- every form (GET/POST) has it's own cross site request forgery (CSRF) hidden 
	input with token name and token value to check if form was submitted by specific 
	user session and not by any foreing atacker javascript
- fields (controls) types:
	- build in:
		- `input:hidden`, `datalist`
	- extended text fields: 
		- `input:text`, `:password`, `:email`, `:search`, `:tel`, `:url`
		- `textarea`
	- extended numeric fields:
		- `input:number`, `:range` (slider) and multiple `input:range`
	- extended selection fields:
		- `select` (multi select)
		- country `select`
		- `input:checkbox`
		- `input:checkbox` group
		- `input:radio` (radio button, switch)
		- `input:color`
	- extended date fields:
		- `input:date`
		- `input:datetime-local`
		- `input:time`
		- `input:week`
		- `input:month`
	- extended button fields: 
		- `input:submit`
		- `input:reset`
		- `button`
		- `button:reset`
		- `button:submit`
		- `input:image`
	- extended file field: 
		- `input:file` with multiple option and validation
	- base field class to extend any control with your custom functionality
- submit validators (each form control has naturaly configured validator(s) by type):
	- build in:
		- required, readOnly, dispabled...
		- safe string (safe characters to display in response - XSS protection)
	- extended text fields:
		- email - to check if email(s) is/are email(s) or not
		- min. and max. text length
		- password - to check password strength by configured rules
		- pattern - PHP preg_match by `pattern` control attribute
		- url (to check if string is url or not)
		- tel (only to clean not allowed chars in phone number)
		- ZIP code (to check international ZIP code form)
	- extended numeric fields:
		- number (min., max. and step)
		- integer (min., max. and step)
		- float (min., max. and step)
		- range (min., max. and step)
	- extended selection fields:
		- checkbox - checked
		- value in options - for selects, country selects, checkbox group and radios
		- min./max. selected options
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
	- `ext-form-field-text`			MvcCore form extension with input field types text, email, password, search, tel, url and textarea field.
	- `ext-form-field-numeric`		MvcCore form extension with input field types number and range.
	- `ext-form-field-selection`	MvcCore form extension with fields select, country select, checkbox(es) and radios.
	- `ext-form-field-date`			MvcCore form extension with input field types date, datetime, time, week and month.
	- `ext-form-field-button`		MvcCore form extension with button fields and input submit fields.
	- `ext-form-field-special`		MvcCore form extension with input type file and color.
	- `ext-form-validator-special`	MvcCore form extension with special text and numeric validators.

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
