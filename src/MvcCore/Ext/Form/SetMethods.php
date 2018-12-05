<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Form;

/**
 * Trait for class `MvcCore\Ext\Form` containing setter methods for configurable properties.
 */
trait SetMethods
{
	/**
	 * Set form id, required to configure.
	 * Used to identify session data, error messages,
	 * CSRF tokens, html form attribute id value and much more.
	 * @requires
	 * @param string $id
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetId ($id) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->id = $id;
		self::$instances[$id] = & $this;
		return $this;
	}

	/**
	 * Set form submitting URL value.
	 * It could be relative or absolute, anything
	 * to complete classic html form attribute `action`.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAction ($url = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->action = $url;
		return $this;
	}

	/**
	 * Set form http submitting method.`POST` by default. 
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @param string $method
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetMethod ($method = \MvcCore\Ext\Forms\IForm::METHOD_POST) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * Set form enctype attribute - how the form values will be encoded 
	 * to send them to the server. Possible values are:
	 * - `application/x-www-form-urlencoded`
	 *   By default, it means all form values will be encoded to 
	 *   `key1=value1&key2=value2...` string.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED`.
	 * - `multipart/form-data`
	 *   Data will not be encoded to URL string form, this value is required,
	 *   when you are using forms that have a file upload control. 
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART`.
	 * - `text/plain`
	 *   Spaces will be converted to `+` symbols, but no other special 
	 *   characters will be encoded.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_PLAINTEXT`.
	 * @param string $enctype
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetEnctype ($enctype = \MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->enctype = $enctype;
		return $this;
	}

	/**
	 * Set form target attribute - where to display the response that is 
	 * received after submitting the form. This is a name of, or keyword for, 
	 * a browsing context (e.g. tab, window, or inline frame). Default value 
	 * is `NULL` to not render any `<form>` element `target` attribute.
	 * The following keywords have special meanings:
	 * - `_self`:		Load the response into the same browsing context as the 
	 *					current one. This value is the default if the attribute 
	 *					is not specified.
	 * - `_blank`:		Load the response into a new unnamed browsing context.
	 * - `_parent`:		Load the response into the parent browsing context of 
	 *					the current one. If there is no parent, this option 
	 *					behaves the same way as `_self`.
	 * - `_top`:		Load the response into the top-level browsing context 
	 *					(i.e. the browsing context that is an ancestor of the 
	 *					current one, and has no parent). If there is no parent, 
	 *					this option behaves the same way as `_self`.
	 * - `iframename`:	The response is displayed in a named `<iframe>`.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetTarget ($target = '_self') {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->target = $target;
		return $this;
	}

	/**
	 * Indicates whether input elements can by default have their values automatically 
	 * completed by the browser. This setting can be overridden by an `autocomplete` 
	 * attribute on an element belonging to the form. Possible values are:
	 * - `'off' | FALSE`:The user must explicitly enter a value into each field for 
	 * 					 every use, or the document provides its own auto-completion 
	 * 					 method; the browser does not automatically complete entries.
	 * - `'on'` | TRUE`: The browser can automatically complete values based on 
	 * 					 values that the user has previously entered in the form.
	 * - `NULL`			 Do not render the attribute.
	 * For most modern browsers setting the autocomplete attribute will not prevent 
	 * a browser's password manager from asking the user if they want to store login 
	 * fields (username and password), if the user permits the storage the browser will
	 * autofill the login the next time the user visits the page. See The autocomplete 
	 * attribute and login fields.
	 * @param bool|string $autoComplete Possible values are `'on' | TRUE | 'off' | FALSE | NULL`.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAutoComplete ($autoComplete = FALSE) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if ($autoComplete === 'on' || $autoComplete === TRUE) {
			$this->autoComplete = TRUE;
		} else if ($autoComplete === 'off' || $autoComplete === FALSE) {
			$this->autoComplete = FALSE;
		} else {
			$this->autoComplete = NULL;
		}
		return $this;
	}

	/**
	 * This Boolean attribute indicates that the form is not to be validated when 
	 * submitted. If this attribute is not specified (and therefore the form is 
	 * validated), this default setting can be overridden by a `formnovalidate` 
	 * attribute on a `<button>` or `<input>` element belonging to the form.
	 * @param bool|NULL $noValidate Only `TRUE` renders the form attribute.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetNoValidate ($noValidate = TRUE) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if ($noValidate === TRUE) {
			$this->noValidate = TRUE;
		} else {
			$this->noValidate = NULL;	
		}
		return $this;
	}

	/**
	 * A list of character encodings that the server accepts. The browser 
	 * uses them in the order in which they are listed. The default value,
	 * the reserved string `'UNKNOWN'`, indicates the same encoding as that 
	 * of the document containing the form element. Any previously configured
	 * accept charset(s) will be replaced by given array. If you want only to
	 * add another charset, use method: `$form->AddAcceptCharset()` instead.
	 * @param \string[] $acceptCharsets 
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAcceptCharsets ($acceptCharsets = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->acceptCharsets = $acceptCharsets;
		return $this;
	}

	/**
	 * Set lang property to complete optional translator language argument automatically.
	 * If you are operating in multi-language project and you want to use
	 * translator in `\MvcCore\Ext\Form`, set this `lang` property to target language code
	 * you want to translate every visible text into target language. Use this property
	 * with `$form->translator`property.
	 * @param string|NULL $lang
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetLang ($lang = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->lang = $lang;
		return $this;
	}

	/**
	 * Set `$form->locale`, usually used to create proper validator for zip codes, currencies etc...
	 * If you are operating in multi-language project and you want to use
	 * in `\MvcCore\Ext\Form` form field validators for locale specific needs,
	 * `$form->locale` property helps you to process validation functionality
	 * with proper validator by locale code.
	 * @param string|NULL $locale
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetLocale ($locale = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->locale = strtoupper($locale);
		return $this;
	}

	/**
	 * Set form HTML element css classes strings.
	 * All previously defined css classes will be removed.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssClasses ($cssClasses) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$cssClassesArr = gettype($cssClasses) == 'array'
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = $cssClassesArr;
		return $this;
	}

	/**
	 * Set form html element additional attributes.
	 * To add any other attribute for html `<form>` element,
	 * set here key/value array, keys will be used as attribute names,
	 * values as attribute values, simple. All previously configured additional
	 * attributes will be replaced by given attributes to this function.
	 * @param array $attributes
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAttributes (array $attributes = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->attributes = $attributes;
		return $this;
	}

	/**
	 * Set form success submit URL string to redirect after, relative or absolute,
	 * to specify, where to redirect user after form has been submitted successfully.
	 * It's required to use `\MvcCore\Ext\Form` like this, if you want to use method
	 * `$form->SubmittedRedirect();`, at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error URL strings.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetSuccessUrl ($url = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->successUrl = $url;
		return $this;
	}

	/**
	 * Set form success submit previous step URL string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `2`.
	 * Which means "previous step" redirection after successful submit. This functionality
	 * to switch result value to `2` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "previous step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetPrevStepUrl ($url = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->nextStepUrl = $url;
		return $this;
	}

	/**
	 * Set form success submit next step URL string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `3`.
	 * Which means "next step" redirection after successful submit. This functionality
	 * to switch result value to `3` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "next step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetNextStepUrl ($url = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->nextStepUrl = $url;
		return $this;
	}

	/**
	 * Set form error submit URL string, relative or absolute, to specify,
	 * where to redirect user after has not been submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error URL strings.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorUrl ($url = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->errorUrl = $url;
		return $this;
	}

	/**
	 * Set form submit result state. Submit could have two basic values (or three values - for next step):
	 * `NULL` - No `Submit()` method has been called yet.
	 * `0`	- Submit has errors. User will be redirected after submit to error url.
	 *		  `\MvcCore\Ext\Form::RESULT_ERRORS`
	 * `1`	- Submit was successful. User will be redirected after submit to success url.
	 *		  `\MvcCore\Ext\Form::RESULT_SUCCESS`
	 * `2`	- Submit was successful. User will be redirected after submit to next step url.
	 *		  `\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`
	 * @param int|NULL $result
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetResult ($result = \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->result = $result;
		return $this;
	}

	/**
	 * Set translator to translate field labels, options, placeholders and error messages.
	 * Translator has to be `callable` (it could be `closure function` or `array`
	 * with `class_name/instance` and `method name` string). First argument
	 * of `callable` has to be a translation key and second argument
	 * has to be language string (`en`, `de` ...) to translate the key into.
	 * Result of `callable` object has to be a string - translated key for called language.
	 * @param callable|NULL $handler
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetTranslator (callable $translator = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if ($translator !== NULL && is_callable($translator))
			$this->translator = $translator;
		return $this;
	}

	/**
	 * Set default switch how to set every form control to be required by default.
	 * If you define directly any control to NOT be required, it will NOT be required.
	 * This is only value used as DEFAULT VALUE for form fields, not to strictly define
	 * required flag value in controls. Default value is `FALSE`.
	 * @param bool $defaultRequired
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetDefaultRequired ($defaultRequired = TRUE) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->defaultRequired = $defaultRequired;
		return $this;
	}

	/**
	 * Set multiple fields values by key/value array.
	 * For each key in `$values` array, this method try to find form field
	 * with the same name. Only data with existing fields by keys are setted into field values.
	 * Values are assigned into fields by keys in case sensitive mode by default.
	 * @param array $values						Key value array with keys as field names and values for fields.
	 * @param bool  $caseInsensitive			If `TRUE`, set up values from `$values` with keys in case insensitive mode.
	 * @param bool  $clearPreviousSessionValues If `TRUE`, clear all previous data records for this form from session.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetValues (array $values = [], $caseInsensitive = FALSE, $clearPreviousSessionValues = FALSE) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		if ($this->dispatchState < 1) $this->Init();
		if ($clearPreviousSessionValues) $this->ClearSession();
		$defaultsKeys = $caseInsensitive
			? ',' . implode(',', array_keys($values)) . ','
			: '' ;
		foreach ($this->fields as $fieldName => & $field) {
			if (isset($values[$fieldName])) {
				$fieldValue = $values[$fieldName];
			} else if ($caseInsensitive) {
				$defaultsKeyPos = stripos($defaultsKeys, ','.$fieldName.',');
				if ($defaultsKeyPos === FALSE) continue;
				$defaultsKey = substr($defaultsKeys, $defaultsKeyPos + 1, strlen($fieldName));
				$fieldValue = $values[$defaultsKey];
			} else {
				continue;
			}
			$fieldValuesIsStr = is_string($fieldValue);
			if (
				$fieldValue !== NULL && (
					!$fieldValuesIsStr || ($fieldValuesIsStr && $fieldValue != '')
				)
			) {
				$field->SetValue($fieldValue);
				$this->values[$fieldName] = $fieldValue;
			}
		}
		return $this;
	}

	/**
	 * Set all form errors. This method is very dangerous, it replace
	 * all previously added form errors with given collection.
	 * If you  want only to add form error, use method:
	 * `$form->AddError($errorMsg, $fieldNames = NULL);` instead.
	 * First param `$errorsCollection` has to be array with arrays.
	 * Every array in collection must have first item as error message
	 * string and second argument (optional) as field name string or
	 * array with field names strings where error happened.
	 * @param array $errorsCollection
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrors ($errorsCollection = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->errors = [];
		foreach ($errorsCollection as $errorMsgAndFieldNames) {
			list ($errorMsg, $fieldNames) = $errorMsgAndFieldNames;
			$this->AddError(
				$errorMsg, is_array($fieldNames) ? $fieldNames : [$fieldNames]
			);
		}
		return $this;
	}

	/**
	 * Set session expiration in seconds. Default value is zero seconds (`0`).
	 * Zero value (`0`) means "until the browser is closed" if there is no more
	 * higher namespace expirations in whole session.
	 * @param $seconds int
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetSessionExpiration ($seconds = 0) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->sessionExpiration = $seconds;
		return $this;
	}

	/**
	 * Set base tab-index value for every field in form, which has defined tab-index value (different from `NULL`).
	 * This value could move tab-index values for each field into higher or lower values by needs, 
	 * where is form currently rendered.
	 * @param $baseTabIndex int
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetBaseTabIndex ($baseTabIndex = 0) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->baseTabIndex = $baseTabIndex;
		return $this;
	}

	/**
	 * Set default control/label rendering mode for each form control/label.
	 * Default values is string `normal`, it means label will be rendered
	 * before control, only label for checkbox and radio button will be
	 * rendered after control.
	 * @param string $defaultFieldsRenderMode
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetDefaultFieldsRenderMode ($defaultFieldsRenderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NORMAL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->defaultFieldsRenderMode = $defaultFieldsRenderMode;
		return $this;
	}

	/**
	 * Set errors rendering mode, by default configured as string: `all-together`.
	 * It means all errors are rendered naturally at form begin together in one HTML `div.errors` element.
	 * If you are using custom template for form - you have to call after form beginning: `echo $this->RenderErrors();`
	 * to get all errors into template.
	 * @param string $errorsRenderMode
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorsRenderMode ($errorsRenderMode = \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_ALL_TOGETHER) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->errorsRenderMode = $errorsRenderMode;
		return $this;
	}

	/**
	 * Set custom form view script relative path without `.phtml` extension.
	 * View script could be `TRUE` to render form by view script name completed
	 * automatically with form id and configured view extension (.phtml) or explicit
	 * relative view script path defined by string. Automatically completed form view
	 * script path and also explicitly defined form view script path by string are
	 * located in directory `/App/Views/Forms` by default. If you want to change this
	 * base directory - use `\MvcCore\Ext\Forms\View::SetFormsDir();` static method.
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetViewScript ($boolOrViewScriptPath = NULL) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

	/**
	 * Set form custom template full class name to create custom view object.
	 * Default value is `\MvcCore\Ext\Forms\View` extended from `\MvcCore\View`.
	 * @param string $viewClass
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetViewClass ($viewClass = '\\MvcCore\\Ext\\Forms\\View') {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->viewClass = $viewClass;
		return $this;
	}

	/**
	 * Set supporting javascript files configuration. This method is dangerous,
	 * It removes all previously, automatically configured javascript support files.
	 * If you want only to add javascript support file, call method:
	 * `$form->AddJsSupportFile($jsRelativePath, $jsClassName, $constructorParams);` instead.
	 * Every record in given `$jsPathsClassNamesAndParams` array has to be defined as array with:
	 *	 `0` - `string` - Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 *	 `1` - `string` - Supporting javascript full class name inside supporting file.
	 *	 `2` - `array`  - Supporting javascript constructor params.
	 * @param array $jsFilesClassesAndConstructorParams
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFiles (array $jsRelPathsClassNamesAndParams = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->jsSupportFiles = [];
		foreach ($jsRelPathsClassNamesAndParams as $jsRelPathClassNameAndParams) {
			list ($jsRelativePath, $jsClassName, $constructorParams) = $jsRelPathClassNameAndParams;
			$this->AddJsSupportFile($jsRelativePath, $jsClassName, $constructorParams);
		}
		return $this;
	}

	/**
	 * Set supporting css files configuration. This method is dangerous,
	 * It removes all previously, automatically configured css support files.
	 * If you want only to add css support file, call method:
	 * `$form->AddCssSupportFile($cssRelativePath);` instead.
	 * Given `$cssRelativePaths` has to be array with supporting css file relative
	 * paths from protected `\MvcCore\Ext\Form::$cssAssetsRootDir`.
	 * @param array $cssRelativePaths
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFiles (array $cssRelativePaths = []) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->cssSupportFiles = [];
		foreach ($cssRelativePaths as $cssRelativePath)
			$this->AddCssSupportFile($cssRelativePath);
		return $this;
	}

	/**
	 * Set javascript support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about external javascript
	 * supporting file. Javascript renderer must add given supporting javascript
	 * file into HTML only once.
	 * @param callable|NULL $jsSupportFilesRenderer
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFilesRenderer (callable $jsSupportFilesRenderer) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->jsSupportFilesRenderer = $jsSupportFilesRenderer;
		return $this;
	}

	/**
	 * Set css support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about external css
	 * supporting file. Css renderer must add given supporting css
	 * file into HTML only once.
	 * @param callable|NULL $cssSupportFilesRenderer
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFilesRenderer (callable $cssSupportFilesRenderer) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->cssSupportFilesRenderer = $cssSupportFilesRenderer;
		return $this;
	}

	/**
	 * This is INTERNAL method for rendering fields. 
	 * Value `TRUE` means `<form>` tag is currently rendered inside, `FALSE` otherwise.
	 * @param bool $formTagRenderingStatus 
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function SetFormTagRenderingStatus ($formTagRenderingStatus = TRUE) {
		/** @var $this \MvcCore\Ext\Forms\IForm */
		$this->formTagRendergingStatus = $formTagRenderingStatus;
		return $this;
	}

	/**
	 * Set MvcCore Form javascript support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom javascript file(s),
	 * you can do it by loading github package `mvccore/form-js` to your custom directory,
	 * you have to create there any other custom javascript support file for any custom field
	 * and change this property value to that javascripts directory. All supporting javascripts
	 * for `\MvcCore\Ext\Form` fields will be loaded now from there.
	 * @param string|NULL $jsSupportFilesRootDir
	 * @return string
	 */
	public static function SetJsSupportFilesRootDir ($jsSupportFilesRootDir) {
		if ($jsSupportFilesRootDir)
			static::$jsSupportFilesRootDir = $jsSupportFilesRootDir;
		return static::$jsSupportFilesRootDir;
	}

	/**
	 * Set MvcCore Form css support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom css file(s),
	 * you can do it by creating an empty directory somewhere, by copying every css file from
	 * library assets directory into it, by creating any other custom css for any custom field
	 * and by change this property value to that directory. All supporting css for `\MvcCore\Ext\Form`
	 * fields will be loaded now from there.
	 * @param string|NULL $cssSupportFilesRootDir
	 * @return string
	 */
	public static function SetCssSupportFilesRootDir ($cssSupportFilesRootDir) {
		if ($cssSupportFilesRootDir)
			static::$cssSupportFilesRootDir = $cssSupportFilesRootDir;
		return static::$cssSupportFilesRootDir;
	}

	/**
	 * Set form validators base namespaces to create validator instance by it's class name.
	 * Validator will be created by class existence in this namespaces order.
	 * This method is dangerous, because it removes all previously configured
	 * validators namespaces. If you only to add another validators namespace,
	 * use method: `\MvcCore\Ext\Form::AddValidatorsNamespaces(...$namespaces);` instead.
	 * @param \string[] $validatorsNamespaces
	 * @return int New validators namespaces count.
	 */
	public static function & SetValidatorsNamespaces (array $validatorsNamespaces = []) {
		static::$validatorsNamespaces = [];
		return static::AddValidatorsNamespaces($validatorsNamespaces);
	}

	/**
	 * Set `autofocus` boolean attribute to target form field by form id and field name.
	 * If there is already defined any previously autofocused field, defined third argument
	 * to not thrown an exception but to solve the duplicity. Third argument possible values:
	 * - `0` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION`)
	 *   Default value, an exception is thrown when there is already defined other autofocused form element.
	 * - `1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW`)
	 *   There will be removed previously defined autofocused element and configured new given one.
	 * - `-1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW`)
	 *   There will be quietly configured another field autofocused. Be careful!!! This is not standard behaviour!
	 * If there is `$formId` and also `$fieldName` with `NULL` value, any previously defined
	 * autofocused form field is changed and `autofocus` boolean attribute is removed.
	 * @param string $formId 
	 * @param string $fieldName 
	 * @param int $duplicateBehaviour 
	 * @throws \RuntimeException 
	 * @return bool
	 */
	public static function SetAutoFocusedFormField ($formId = NULL, $fieldName = NULL, $duplicateBehaviour = \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION) {
		if (self::$autoFocusedFormField) {
			// if any global autofocus record is already defined
			if ($formId === NULL && $fieldName === NULL) {
				// unset old every time form id and field name is both `NULL`
				list ($oldFormId, $oldFieldName) = self::$autoFocusedFormField;
				self::GetById($oldFormId)->GetField($oldFieldName)->SetAutoFocus(FALSE);
				self::$autoFocusedFormField = [];
			} else if ($duplicateBehaviour === \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION) {
				// thrown an runtime exception
				list ($currentFormId, $currentFieldName) = self::$autoFocusedFormField;
				throw new \RuntimeException(
					'Another form field has already defined `autofocus` attribute.'
					. ' Form id: `' . $currentFormId . '`, field name: `' . $currentFieldName . '`.'
				);
			} else if ($duplicateBehaviour === \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW) {
				// quietly set (could be useful to render something in the background)
				self::GetById($formId)->GetField($fieldName)->SetAutoFocus(
					TRUE, \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW
				);
			} else if ($duplicateBehaviour === \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW) {
				// unset previous and set new
				list ($oldFormId, $oldFieldName) = self::$autoFocusedFormField;
				self::GetById($oldFormId)->GetField($oldFieldName)->SetAutoFocus(FALSE);
				self::GetById($formId)->GetField($fieldName)->SetAutoFocus(
					TRUE, \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW
				);
			}
		} else {
			// if no global autofocus record is defined
			self::$autoFocusedFormField = [$formId, $fieldName];
			self::GetById($formId)->GetField($fieldName)->SetAutoFocus(
				TRUE, \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW
			);
		}
		return TRUE;
	}
}
