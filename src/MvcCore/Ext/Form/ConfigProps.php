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

trait ConfigProps
{
	/**
	 * Form id, required to configure.
	 * Used to identify session data, error messages,
	 * CSRF tokens, html form attribute id value and much more.
	 * @requires
	 * @var string|NULL
	 */
	protected $id = NULL;

	/**
	 * Form submitting url value.
	 * Should be relative or absolute, anything
	 * to complete classic html form attribute action.
	 * @requires
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-action
	 * @var string|NULL
	 */
	protected $action = NULL;

	/**
	 * Form http submitting method. `POST` by default. 
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-method
	 * @var string
	 */
	protected $method = \MvcCore\Ext\Forms\IForm::METHOD_POST;

	/**
	 * Form enctype attribute - how the form values will be encoded 
	 * to send them to the server. Possible values are:
	 * - `application/x-www-form-urlencoded`
	 *   By default, it means all form values will be encoded to 
	 *   `key1=value1&key2=value2...` string.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED`.
	 * - `multipart/form-data`
	 *   Data will not be encoded to url string form, this value is required,
	 *   when you are using forms that have a file upload control. 
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART`.
	 * - `text/plain`
	 *   Spaces will be converted to `+` symbols, but no other special 
	 *   characters will be encoded.
	 *   Constant: `\MvcCore\Ext\Forms\IForm::ENCTYPE_PLAINTEXT`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-enctype
	 * @var string
	 */
	protected $enctype = \MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED;

	/**
	 * Form target attribute - where to display the response that is 
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
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-target
	 * @var string|NULL
	 */
	protected $target = NULL;
	
	/**
	 * Indicates whether input elements can by default have their values automatically 
	 * completed by the browser. This setting can be overridden by an `autocomplete` 
	 * attribute on an element belonging to the form. Possible values are:
	 * - `'off' | FALSE:The user must explicitly enter a value into each field for 
	 * 					every use, or the document provides its own auto-completion 
	 * 					method; the browser does not automatically complete entries.
	 * - `'on'` | TRUE: The browser can automatically complete values based on 
	 * 					values that the user has previously entered in the form.
	 * - `NULL`			Do not render the attribute.
	 * For most modern browsers setting the autocomplete attribute will not prevent 
	 * a browser's password manager from asking the user if they want to store login 
	 * fields (username and password), if the user permits the storage the browser will
	 * autofill the login the next time the user visits the page. See The autocomplete 
	 * attribute and login fields.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-autocomplete
	 * @var string|NULL
	 */
	protected $autoComplete = NULL;

	/**
	 * This Boolean attribute indicates that the form is not to be validated when 
	 * submitted. If this attribute is not specified (and therefore the form is 
	 * validated), this default setting can be overridden by a `formnovalidate` 
	 * attribute on a `<button>` or `<input>` element belonging to the form.
	 * Only `TRUE` renders the form attribute.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-novalidate
	 * @var bool|NULL
	 */
	protected $noValidate = NULL;
	
	/**
	 * A list of character encodings that the server accepts. The browser 
	 * uses them in the order in which they are listed. The default value,
	 * the reserved string `'UNKNOWN'`, indicates the same encoding as that 
	 * of the document containing the form element.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form#attr-accept-charset
	 * @var \string[]
	 */
	protected $acceptCharsets = [];

	/**
	 * Property to complete optional translator language argument automaticly.
	 * If you are operating in multilanguage project and you want to use
	 * translator in `\MvcCore\Ext\Form`, set this `lang` property to desired language code
	 * you want to translate every visible text into it. Use this property
	 * with `$form->translator` and `$form->translate` properties.
	 * @var string|NULL
	 */
	protected $lang = NULL;

	/**
	 * Field to create proper validator for zip codes, currencies etc...
	 * If you are operating in multilanguage project and you want to use
	 * form field validators for locale specific needs in `\MvcCore\Ext\Form`,
	 * set `$form->locale` property to desired international locale code
	 * you want to use proper validator functionality.
	 * @var string|NULL
	 */
	protected $locale = NULL;

	/**
	 * Form html element css class attribute value.
	 * To specify more css classes - add more strings separated by space.
	 * Value is used for standard css class attribute for HTML `<form>` tag.
	 * @var \string[]
	 */
	protected $cssClasses = [];

	/**
	 * Form html element additional attributes.
	 * To add any other attribute for HTML `<form>` element,
	 * put here key/value array, keys will be used as attribute names,
	 * values as attribute values, simple.
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Form success submit url string to redirect after, relative or absolute,
	 * to specify, where to redirect user after form has been submitted successfully.
	 * It's required to use `\MvcCore\Ext\Form` like this, if you want to use method
	 * `$form->SubmittedRedirect();`, at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @var string|NULL
	 */
	protected $successUrl = NULL;
	
	/**
	 * Form success submit previous step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized to switch form result property `$form->result` to value `2`.
	 * Which means "previous step" redirection after successfull submit. This functionality
	 * to switch result value to `2` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "previous step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @var string|NULL
	 */
	protected $prevStepUrl = NULL;

	/**
	 * Form success submit next step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized to switch form result property `$form->result` to value `3`.
	 * Which means "next step" redirection after successfull submit. This functionality
	 * to switch result value to `3` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "next step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @var string|NULL
	 */
	protected $nextStepUrl = NULL;

	/**
	 * Form error submit url string, relative or absolute, to specify,
	 * where to redirect user after has not been submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @var string|NULL
	 */
	protected $errorUrl = NULL;

	/**
	 * Form submit result state. Submit could have two basic values (or three values for next step):
	 * `0` - Submit has errors. User will be redirected after submit to error url.
	 *	   `\MvcCore\Ext\Form::RESULT_ERRORS`
	 * `1` - Submit was successfull. User will be redirected after submit to success url.
	 *	   `\MvcCore\Ext\Form::RESULT_SUCCESS`
	 * `2` - Submit was successfull. User will be redirected after submit to next step url.
	 *	   `\MvcCore\Ext\Form::RESULT_NEXT_PAGE`
	 * @var int|NULL
	 */
	protected $result = NULL;

	/**
	 * Translator to translate field labels, options, placeholders and error messages.
	 * Translator has to be `callable` (it could be `closure function` or `array`
	 * with `classname/instance` and `method name` string). First argument
	 * of `callable` has to be a translation key and second argument
	 * has to be language string (`en`, `de` ...) to translate the key into.
	 * Result of `callable` object has to be a string - translated key to called language.
	 * @var callable|NULL
	 */
	protected $translator = NULL;

	/**
	 * Default switch how to set every form control to be required by default.
	 * If you define directly any control to NOT be required, it will NOT be required.
	 * This is only value used as DEFAULT VALUE for form fiels, not to strictly define
	 * required flag value in controls. Default value is `FALSE`.
	 * @var bool
	 */
	protected $defaultRequired = FALSE;

	/**
	 * All form field controls.
	 * After adding any field into form instance by `$form->AddField()` method
	 * field is added under it's name into this array with all another form fields 
	 * except CSRF `input:hidden`s. Fields are rendered by order in this array.
	 * @var \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\IField[]
	 */
	protected $fields = [];

	/**
	 * Form submited values from client. After `$form->Submit()` has been called,
	 * values are cleaned by validators and ready to use if `$form->result` is in success state.
	 * @var array
	 */
	protected $values = [];

	/**
	 * If any configured error happends by executing `$form->Submit()`, it's stored in this array.
	 * Every record in this array is array with first item to be an error message string.
	 * If the error is for specific field name or field names, there is also a second item - array with field names.
	 * Errors array has normal numeric keys.
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Session expiration in seconds. Default value is zero seconds (`0`).
	 * Zero value (`0`) means "until the browser is closed" if there is no more
	 * higher namespace expirations in whole session.
	 * @var int
	 */
	protected $sessionExpiration = 0;

	/**
	 * Base tabindex value for every field in form, which has defined tabindex value (different from `NULL`).
	 * This value could move tabindex values for each field into higher or lower values by needs, 
	 * where is form currently rendered.
	 * @var int|NULL
	 */
	protected $baseTabIndex = 0;

	/**
	 * Default control/label rendering mode for each form control/label.
	 * Default values is string `normal`, it means label will be rendered
	 * before control, only label for checkbox and radio button will be
	 * rendered after control.
	 * @var string
	 */
	protected $defaultFieldsRenderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NORMAL;

	/**
	 * Errors rendering mode, by default configured as string: `all-together`,
	 * It means all errors are rendered naturaly at form begin together in one HTML `div.errors` element.
	 * If you are using custom template for form - you have to call after form beginning: `$this->RenderErrors();`
	 * to get all errors into template.
	 * @var string
	 */
	protected $errorsRenderMode = \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_ALL_TOGETHER;

	/**
	 * Form custom template relative path without `.phtml` or `.php` extension.
	 * It's `NULL` by default, which means there will be used no template and form
	 * will be rendered naturaly, all fiels one by one without any breaking line html element.
	 * If there is any path defined, it has to be defined relatively from directory
	 * `/App/Views/Scripts` to desired template.
	 * @var string|NULL
	 */
	protected $viewScript = NULL;

	/**
	 * Form custom template full class name to create custom view object.
	 * Default value is `\MvcCore\Ext\Forms\View` extended from `\MvcCore\View`.
	 * @var string|NULL
	 */
	protected $viewClass = '\\MvcCore\\Ext\\Forms\\View';

	/**
	 * Supporting javascript files configuration.
	 * Every record in `$jsSupportFiles` array has to be defined as array with:
	 *	 `0` - `string` - Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 *	 `1` - `string` - Supporting javascript full class name inside supporting file.
	 *	 `2` - `array`  - Supporting javascript constructor params.
	 * @var array
	 */
	protected $jsSupportFiles = [];

	/**
	 * Supporting css files configuration.
	 * Array with supporting css relative paths from
	 * protected `\MvcCore\Ext\Form::$cssAssetsRootDir` to add
	 * into HTML response after form is rendered.
	 * @var \string[]
	 */
	protected $cssSupportFiles = [];

	/**
	 * Javascript support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal javascript
	 * supporting file. Javascript renderer must add given supporting javascript
	 * file into HTML only once.
	 * @var callable|NULL
	 */
	protected $jsSupportFilesRenderer = NULL;

	/**
	 * Css support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal css
	 * supporting file. Css renderer must add given supporting css
	 * file into HTML only once.
	 * @var callable|NULL
	 */
	protected $cssSupportFilesRenderer = NULL;

	/**
	 * MvcCore Form javascript support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom javascript file(s),
	 * you can do it by loading github package `mvccore/form-js` to your custom directory,
	 * you have to create there any other custom javascript support file for any custom field
	 * and change this property value to that javascripts directory. All supporting javascripts
	 * for `\MvcCore\Ext\Form` fields will be loaded now from there.
	 * @var string
	 */
	protected static $jsSupportFilesRootDir = NULL;

	/**
	 * MvcCore Form css support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom css file(s),
	 * you can do it by creating an empty directory somewhere, by copying every css file from
	 * library assets directory into it, by creating any other custom css for any custom field
	 * and by change this property value to that directory. All supporting css for `\MvcCore\Ext\Form`
	 * fields will be loaded now from there.
	 * @var string
	 */
	protected static $cssSupportFilesRootDir = NULL;

	/**
	 * Form validators base namespaces to create validator instance by it's class name.
	 * Validator will be created by class existence in this namespaces order.
	 * @var \string[]
	 */
	protected static $validatorsNamespaces = [
		'\\MvcCore\\Ext\\Forms\\Validators\\'
	];

	/**
	 * Form field with `autofocus` boolean attribute defined. Form field 
	 * with `autofocus` atribute defined could be only one in whole rendered document.
	 * @var \string[]
	 */
	protected static $autoFocusedFormField = [];
}
