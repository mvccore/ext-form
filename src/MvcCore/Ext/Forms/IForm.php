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

namespace MvcCore\Ext\Forms;

interface IForm
{
	/**
	 * MvcCore Extension - Form - version:
	 * Comparation by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.0.0-alpha';


	/**
	 * Form http submitting method (`GET`).
	 */
	const METHOD_GET		= 'GET';

	/**
	 * Form http submitting method (`POST`).
	 */
	const METHOD_POST		= 'POST';


	/**
	 * Form enctype attribute value `application/x-www-form-urlencoded`,
	 * By submitting - all form values will be encoded
	 * to `key1=value1&key2=value2&...` string.
	 * This enctype type is used for all `\MvcCore\Ext\Form` instances by default.
	 */
	const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';

	/**
	 * Form enctype attribute value `multipart/form-data`,
	 * By submitting - data will not be encoded to url string form.
	 * This value is required when you are using forms that have a file upload control.
	 */
	const ENCTYPE_MULTIPART  = 'multipart/form-data';

	/**
	 * Form enctype attribute value `application/x-www-form-urlencoded`,
	 * By submitting - spaces will be converted to `+` symbols,
	 * but no other special characters will be encoded.
	 */
	const ENCTYPE_PLAINTEXT  = 'text/plain';


	/**
	 * Html id attributes delimiter,
	 * used for form controls to complete
	 * it's ids as `<form-id>_<control-name>`.
	 */
	const HTML_IDS_DELIMITER = '_';


	/**
	 * Form submit result state (`0` - error happend).
	 * Submit was not successful, there was an error or errors.
	 */
	const RESULT_ERRORS		= 0;

	/**
	 * Form submit result state (`1` - everything ok).
	 * Submit was successful, no error happend.
	 * User could be redirected to success url.
	 */
	const RESULT_SUCCESS	= 1;

	/**
	 * Form submit result state (`2` - everything ok, redirect user to previous step url).
	 * Submit was successful, no error happend and one of submitting
	 * button is control to indicate that user could be redirected
	 * to previous step url in multiple forms wizzard (typicly eshop ordering).
	 */
	const RESULT_PREV_PAGE	= 2;

	/**
	 * Form submit result state (`3` - everything ok, redirect user to next step url).
	 * Submit was successful, no error happend and one of submitting
	 * button is control to indicate that user could be redirected
	 * to next step url in multiple forms wizzard (typicly eshop ordering).
	 */
	const RESULT_NEXT_PAGE	= 3;


	/**
	 * Control/labels rendering mode (`normal`).
	 * Label will be rendered before control,
	 * only checkbox and radio button label
	 * will be rendered after control.
	 */
	const FIELD_RENDER_MODE_NORMAL			= 'normal';

	/**
	 * Control/labels rendering mode (`no-label`).
	 * No label will be rendered with control.
	 */
	const FIELD_RENDER_MODE_NO_LABEL		= 'no-label';

	/**
	 * Control/labels rendering mode (`label-around`).
	 * Label will be rendered around control.
	 */
	const FIELD_RENDER_MODE_LABEL_AROUND	= 'label-around';


	/**
	 * Control errors rendering mode (`all-together`).
	 * All errors are rendered naturaly at form begin together in one html div element.
	 * If you are using custom template for form - you have to call after form beginning
	 * `$form->RenderErrors();` to get all errors into template. This value is used as
	 * default for all `\MvcCore\Ext\Form` instances.
	 */
	const ERROR_RENDER_MODE_ALL_TOGETHER		= 'all-together';

	/**
	 * Control errors rendering mode (`before-each-control`).
	 * If there will be any error, it will be rendered as single span.errors
	 * before current form control with single or multiple span.error elements
	 * inside, by errors count for current form control. It will be rendered in
	 * natural form rendering mode without template but also in custom form rendering mode
	 * with template if you call anytime in template `$field->RenderLabelAndControl();`
	 * If you will use in custom form rendering mod with template method `$field->RenderControl();`,
	 * there will be not rendered any error spans before control, you have to use `$field->RenderErrors();`
	 * to get errors for each control.
	 */
	const ERROR_RENDER_MODE_BEFORE_EACH_CONTROL	= 'before-each-control';

	/**
	 * Control errors rendering mode (`after-each-control`).
	 * If there will be any error, it will be rendered as single span.errors
	 * after current form control with single or multiple span.error elements
	 * inside, by errors count for current form control. It will be rendered in
	 * natural form rendering mode without template but also in custom form rendering mode
	 * with template if you call anytime in template `$field->RenderLabelAndControl();`
	 * If you will use in custom form rendering mode with template method `$field->RenderControl();`,
	 * there will be rendered no error spans before control, you have to use `$field->RenderErrors();`
	 * to get errors for each control.
	 */
	const ERROR_RENDER_MODE_AFTER_EACH_CONTROL	= 'after-each-control';

	/**
	 * MvcCore Form extension library directory replacement string.
	 */
	const FORM_ASSETS_DIR_REPLACEMENT = '__MVCCORE_FORM_ASSETS_DIR__';


	/***************************************************************************
	 *                               Base Form class                           *
	 **************************************************************************/

	/**
	 * Initialize the form, check if form is initialized or not and do it only once.
	 * Check if any form id exists and exists only once and initialize translation 
	 * boolean for better field initializations. This is template method. To define 
	 * any fields in custom `\MvcCore\Ext\Form` extended class, do it in custom 
	 * extended `Init()` method and call `parent::Init();` as first line inside 
	 * your extended `Init()` method.
	 * @throws \RuntimeException No form id property defined or Form id `...` already defined.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function Init ();

	/**
	 * Prepare form and it's fields for rendering.
	 * 
	 * This function is called automaticly by rendering process if necessary.
	 * But if you need to operate with fields in your controller before rendering
	 * with real session values and initialized session errors, you can call this
	 * method anytime to prepare form for rendering and operate with anything inside.
	 * 
	 * - Process all defined fields and call `$field->PreDispatch();`
	 *   to prepare all fields for rendering process.
	 * - Load any possible error from session and set up
	 *   errors into fields and into form object to render them properly.
	 * - Load any possible previously submitted and/or stored values
	 *   from session and set up form fields with them.
	 * - Set initialized state to 2, which means - prepared, pre-dispatched for rendering.
	 * 
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function PreDispatch ();

	/**
	 * Translate given string with configured translator and configured language code.
	 * @param string $translationKey 
	 * @return string
	 */
	public function Translate ($translationKey);


	/***************************************************************************
	 *                            GetMethods Form trait                        *
	 **************************************************************************/

	/**
	 * Get form id, required to configure.
	 * Used to identify session data, error messages,
	 * CSRF tokens, html form attribute id value and much more.
	 * @param string $id
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function GetId ();

	/**
	 * Get form submitting url value.
	 * It could be relative or absolute, anything
	 * to complete classic html form attribute `action`.
	 * @return string|NULL
	 */
	public function GetAction ();

	/**
	 * Get form http submitting method. `POST` by default. 
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @return string
	 */
	public function GetMethod ();

	/**
	 * Get form enctype attribute - how the form values will be encoded 
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
	 * @return string
	 */
	public function GetEnctype ();

	/**
	 * Get form target attribute - where to display the response that is 
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
	 * @return string|NULL
	 */
	public function GetTarget ();

	/**
	 * Indicates whether input elements can by default have their values automatically 
	 * completed by the browser. This setting can be overridden by an `autocomplete` 
	 * attribute on an element belonging to the form. Possible values are:
	 * - `FALSE` (`'off'`):	The user must explicitly enter a value into each field for 
	 * 						every use, or the document provides its own auto-completion 
	 * 						method; the browser does not automatically complete entries.
	 * - `TRUE` (`'on'`):	The browser can automatically complete values based on 
	 * 						values that the user has previously entered in the form.
	 * - `NULL`				Do not render the attribute.
	 * For most modern browsers setting the autocomplete attribute will not prevent 
	 * a browser's password manager from asking the user if they want to store login 
	 * fields (username and password), if the user permits the storage the browser will
	 * autofill the login the next time the user visits the page. See The autocomplete 
	 * attribute and login fields.
	 * @return bool|NULL
	 */
	public function GetAutoComplete ();

	/**
	 * This Boolean attribute indicates that the form is not to be validated when 
	 * submitted. If this attribute is not specified (and therefore the form is 
	 * validated), this default setting can be overridden by a `formnovalidate` 
	 * attribute on a `<button>` or `<input>` element belonging to the form.
	 * Only `TRUE` renders the form attribute.
	 * @return bool|NULL
	 */
	public function GetNoValidate ();

	/**
	 * A list of character encodings that the server accepts. The browser 
	 * uses them in the order in which they are listed. The default value,
	 * the reserved string `'UNKNOWN'`, indicates the same encoding as that 
	 * of the document containing the form element.
	 * @return \string[]
	 */
	public function GetAcceptCharsets ();

	/**
	 * Get lang property to complete optional translator language argument automaticly.
	 * If you are operating in multilanguage project and you want to use
	 * translator in `\MvcCore\Ext\Form`, this `lang` property with target language code
	 * serves to translate every visible text into target lang. Use this property
	 * with `$form->translator` property.
	 * @return string|NULL
	 */
	public function GetLang ();

	/**
	 * Get `$form->locale`, uppercase locale code or `NULL`, usualy used to create
	 * proper validator for zip codes, currencies etc...
	 * If you are operating in multilanguage project and you want to use
	 * in `\MvcCore\Ext\Form` form field validators for locale specific needs,
	 * `$form->locale` property helps you to process validation functionality
	 * with proper validator by locale code.
	 * @return string|NULL
	 */
	public function GetLocale ();

	/**
	 * Get form field HTML element css classes strings as array.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @return string
	 */
	public function & GetCssClasses ();

	/**
	 * Get form html element additional attributes.
	 * To add any other attribute for html `<form>` element,
	 * set here key/value array, keys will be used as attribute names,
	 * values as attribute values, simple. All previously configured additional
	 * attributes will be replaced by given attributes to this function.
	 * @return array
	 */
	public function & GetAttributes ();

	/**
	 * Get form success submit url string to redirect after, relative or absolute,
	 * to specify, where to redirect user after form has been submitted successfully.
	 * It's required to use `\MvcCore\Ext\Form` like this, if you want to use method
	 * `$form->SubmittedRedirect();`, at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @return string|NULL
	 */
	public function GetSuccessUrl ();

	/**
	 * Get form success submit prev step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `2`.
	 * Which means "previous step" redirection after successfull submit. This functionality
	 * to switch result value to `2` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "previous step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @return string|NULL
	 */
	public function GetPrevStepUrl ();

	/**
	 * Get form success submit next step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `3`.
	 * Which means "next step" redirection after successfull submit. This functionality
	 * to switch result value to `3` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "next step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @return string|NULL
	 */
	public function GetNextStepUrl ();

	/**
	 * Get form error submit url string, relative or absolute, to specify,
	 * where to redirect user after has not been submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @return string|NULL
	 */
	public function GetErrorUrl ();

	/**
	 * Get form submit result state. Submit could have two basic values (or three values - for next step):
	 * `NULL` - No `Submit()` method has been called yet. Call `$form->Submit();` before.
	 * `0`	- Submit has errors. User will be redirected after submit to error url.
	 *		  `\MvcCore\Ext\Form::RESULT_ERRORS`
	 * `1`	- Submit was successfull. User will be redirected after submit to success url.
	 *		  `\MvcCore\Ext\Form::RESULT_SUCCESS`
	 * `2`	- Submit was successfull. User will be redirected after submit to next step url.
	 *		  `\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`
	 * @return int|NULL
	 */
	public function GetResult ();

	/**
	 * Get translator to translate field labels, options, placeholders and error messages.
	 * Translator is `callable` (it could be `closure function` or `array`
	 * with `classname/instance` and `method name` string). First argument
	 * of `callable` is a translation key and second argument
	 * is language string (`en`, `de` ...) to translate the key into.
	 * Result of `callable` object is a string - translated key for called language.
	 * @return callable|NULL
	 */
	public function GetTranslator ();

	/**
	 * Get internal flag to quickly know if form fields will be translated or not.
	 * Automaticly completed to `TRUE` if `$form->translator` is not `NULL` and also if
	 * `$form->translator` is `callable`. `FALSE` otherwise. Default value is `FALSE`.
	 * @return bool
	 */
	public function GetTranslate ();

	/**
	 * Get default switch how to set every form control to be required by default.
	 * If you define directly any control to NOT be required, it will NOT be required.
	 * This is only value used as DEFAULT VALUE for form fiels, not to strictly define
	 * required flag value in controls. Default value is `FALSE`.
	 * @return bool
	 */
	public function GetDefaultRequired ();

	/**
	 * Get multiple fields values as key/value array.
	 * @return array
	 */
	public function & GetValues ();

	/**
	 * Get all form errors. Returned collection is array with arrays.
	 * Every array in collection have first item as error message
	 * string and second argument (optional) as field name string or
	 * array with field names strings, where error happend.
	 * @return array
	 */
	public function & GetErrors ();

	/**
	 * Get session expiration in seconds. Default value is zero seconds (`0`).
	 * Zero value (`0`) means "until the browser is closed" if there is no more
	 * higher namespace expirations in whole session.
	 * @return int
	 */
	public function GetSessionExpiration ();

	/**
	 * Get base tabindex value for every field in form, which has defined tabindex value (different from `NULL`).
	 * This value could move tabindex values for each field into higher or lower values by needs, 
	 * where is form currently rendered.
	 * @return int
	 */
	public function GetBaseTabIndex ();

	/**
	 * This method is INTERNAL, used by fields in predispatch rendering moment.
	 * This method returns next automatic tabindex value for field.
	 * @return int
	 */
	public function GetFieldNextAutoTabIndex ();

	/**
	 * Get default control/label rendering mode for each form control/label.
	 * Default values is string `normal`, it means label will be rendered
	 * before control, only label for checkbox and radio button will be
	 * rendered after control.
	 * @return string
	 */
	public function GetDefaultFieldsRenderMode ();

	/**
	 * Get errors rendering mode, by default configured as string: `all-together`.
	 * It means all errors are rendered naturaly at form begin together in one HTML `div.errors` element.
	 * If you are using custom template for form - you have to call after form beginning: `echo $this->RenderErrors();`
	 * to get all errors into template.
	 * @return string
	 */
	public function GetErrorsRenderMode ();

	/**
	 * Get custom form view script relative path without `.phtml` extension.
	 * View script could be `TRUE`/`FALSE` to render or not form by view script name
	 * completed automaticly with form id and configured view extension (.phtml) or explicit
	 * relative view script path defined by string. Automaticly completed form view
	 * script path and also explicitly defined form view script path by string are
	 * located in directory `/App/Views/Forms` by default. If you want to change this
	 * base directory - use `\MvcCore\Ext\Forms\View::SetFormsDir();` static method.
	 * @return string|bool|NULL
	 */
	public function GetViewScript ();

	/**
	 * Get form custom template full class name to create custom view object.
	 * Default value is `\MvcCore\Ext\Forms\View` extended from `\MvcCore\View`.
	 * @return string
	 */
	public function GetViewClass ();

	/**
	 * Get supporting javascript files configuration.
	 * Every record in returned array is an array with:
	 *	 `0` - `string` - Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 *	 `1` - `string` - Supporting javascript full class name inside supporting file.
	 *	 `2` - `array`  - Supporting javascript constructor params.
	 * @return array
	 */
	public function & GetJsSupportFiles ();

	/**
	 * Get supporting css files configuration, an array with supporting
	 * css file relative paths from protected `\MvcCore\Ext\Form::$cssAssetsRootDir`.
	 * @return array
	 */
	public function & GetCssSupportFiles ();

	/**
	 * Get javascript support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal javascript
	 * supporting file. Javascript renderer must add given supporting javascript
	 * file into HTML only once.
	 * @return callable|NULL
	 */
	public function GetJsSupportFilesRenderer ();

	/**
	 * Get css support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal css
	 * supporting file. Css renderer must add given supporting css
	 * file into HTML only once.
	 * @return callable|NULL
	 */
	public function GetCssSupportFilesRenderer ();

	/**
	 * This is INTERNAL method for rendering fields. 
	 * Value `TRUE` means `<form>` tag is currently rendered inside, `FALSE` otherwise.
	 * @return bool
	 */
	public function GetFormTagRenderingStatus ();

	/**
	 * Get MvcCore Form javascript support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom javascript file(s),
	 * you can do it by loading github package `mvccore/form-js` to your custom directory,
	 * you have to create there any other custom javascript support file for any custom field
	 * and change this property value to that javascripts directory. All supporting javascripts
	 * for `\MvcCore\Ext\Form` fields will be loaded now from there.
	 * @return string|NULL
	 */
	public static function GetJsSupportFilesRootDir ();

	/**
	 * Get MvcCore Form css support files root directory.
	 * After `\MvcCore\Ext\Form` instance is created, this value is completed to library internal
	 * assets directory. If you want to create any custom field with custom css file(s),
	 * you can do it by creating an empty directory somewhere, by copying every css file from
	 * library assets directory into it, by creating any other custom css for any custom field
	 * and by change this property value to that directory. All supporting css for `\MvcCore\Ext\Form`
	 * fields will be loaded now from there.
	 * @return string|NULL
	 */
	public static function GetCssSupportFilesRootDir ();

	/**
	 * Get form validators base namespaces to create validator instance by it's class name.
	 * Validator will be created by class existence in this namespaces order.
	 * @return \string[]
	 */
	public static function GetValidatorsNamespaces ();

	/**
	 * Get PHP data limit as integer value by given ini variable name.
	 * @param string $iniVarName 
	 * @return int|NULL
	 */
	public static function GetPhpIniSizeLimit ($iniVarName);

	/**
	 * Get form instance by form id string.
	 * If no form instance found, thrown an `RuntimeException`.
	 * @param string $formId 
	 * @throws \RuntimeException 
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public static function & GetById ($formId);

	/**
	 * Get form field instance with defined `autofocus` boolean attribute.
	 * If there is no field in any form with this attribute, return `NULL`.
	 * @param string $formId 
	 * @param string $fieldName 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public static function & GetAutoFocusedFormField ($formId, $fieldName);


	/***************************************************************************
	 *                            SetMethods Form trait                        *
	 **************************************************************************/

	/**
	 * Set form id, required to configure.
	 * Used to identify session data, error messages,
	 * CSRF tokens, html form attribute id value and much more.
	 * @requires
	 * @param string $id
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetId ($id);

	/**
	 * Set form submitting url value.
	 * It could be relative or absolute, anything
	 * to complete classic html form attribute `action`.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetAction ($url = NULL);

	/**
	 * Set form http submitting method.`POST` by default. 
	 * Use `GET` only if form data contains only ASCII characters.
	 * Possible values: `'POST' | 'GET'`
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_POST`
	 * - `\MvcCore\Ext\Forms\IForm::METHOD_GET`
	 * @param string $method
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetMethod ($method = \MvcCore\Ext\Forms\IForm::METHOD_POST);

	/**
	 * Set form enctype attribute - how the form values will be encoded 
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
	 * @param string $enctype
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetEnctype ($enctype = \MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED);

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
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetTarget ($target = '_self');

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
	 * @param bool|string $autoComplete Posible values are `'on' | TRUE | 'off' | FALSE | NULL`.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetAutoComplete ($autoComplete = FALSE);

	/**
	 * This Boolean attribute indicates that the form is not to be validated when 
	 * submitted. If this attribute is not specified (and therefore the form is 
	 * validated), this default setting can be overridden by a `formnovalidate` 
	 * attribute on a `<button>` or `<input>` element belonging to the form.
	 * @param bool|NULL $noValidate Only `TRUE` renders the form attribute.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetNoValidate ($noValidate = TRUE);

	/**
	 * A list of character encodings that the server accepts. The browser 
	 * uses them in the order in which they are listed. The default value,
	 * the reserved string `'UNKNOWN'`, indicates the same encoding as that 
	 * of the document containing the form element. Any previously configured
	 * accept charsets will be replaced by given array. If you want only to
	 * add another charset, use method: `$form->AddAcceptCharset()` instead.
	 * @param \string[] $acceptCharsets 
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetAcceptCharsets ($acceptCharsets = []);

	/**
	 * Set lang property to complete optional translator language argument automaticly.
	 * If you are operating in multilanguage project and you want to use
	 * translator in `\MvcCore\Ext\Form`, set this `lang` property to target language code
	 * you want to translate every visible text into target language. Use this property
	 * with `$form->translator`property.
	 * @param string|NULL $lang
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetLang ($lang = NULL);

	/**
	 * Set `$form->locale`, usualy used to create proper validator for zip codes, currencies etc...
	 * If you are operating in multilanguage project and you want to use
	 * in `\MvcCore\Ext\Form` form field validators for locale specific needs,
	 * `$form->locale` property helps you to process validation functionality
	 * with proper validator by locale code.
	 * @param string|NULL $locale
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetLocale ($locale = NULL);

	/**
	 * Set form HTML element css classes strings.
	 * All previously defined css classes will be removed.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssClasses ($cssClasses);

	/**
	 * Set form html element additional attributes.
	 * To add any other attribute for html `<form>` element,
	 * set here key/value array, keys will be used as attribute names,
	 * values as attribute values, simple. All previously configured additional
	 * attributes will be replaced by given attributes to this function.
	 * @param array $attributes
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetAttributes (array $attributes = []);

	/**
	 * Set form success submit url string to redirect after, relative or absolute,
	 * to specify, where to redirect user after form has been submitted successfully.
	 * It's required to use `\MvcCore\Ext\Form` like this, if you want to use method
	 * `$form->SubmittedRedirect();`, at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetSuccessUrl ($url = NULL);

	/**
	 * Set form success submit previous step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `2`.
	 * Which means "previous step" redirection after successfull submit. This functionality
	 * to switch result value to `2` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "previous step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetPrevStepUrl ($url = NULL);

	/**
	 * Set form success submit next step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized as submit type to switch form result property `$form->result` to value `3`.
	 * Which means "next step" redirection after successfull submit. This functionality
	 * to switch result value to `3` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "next step" by one submit button or stay in the same page by
	 * another submit button, this is very good and comfortable pattern.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetNextStepUrl ($url = NULL);

	/**
	 * Set form error submit url string, relative or absolute, to specify,
	 * where to redirect user after has not been submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @param string|NULL $url
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorUrl ($url = NULL);

	/**
	 * Set form submit result state. Submit could have two basic values (or three values - for next step):
	 * `NULL` - No `Submit()` method has been called yet.
	 * `0`	- Submit has errors. User will be redirected after submit to error url.
	 *		  `\MvcCore\Ext\Form::RESULT_ERRORS`
	 * `1`	- Submit was successfull. User will be redirected after submit to success url.
	 *		  `\MvcCore\Ext\Form::RESULT_SUCCESS`
	 * `2`	- Submit was successfull. User will be redirected after submit to next step url.
	 *		  `\MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE`
	 * @param int|NULL $result
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetResult ($result = \MvcCore\Ext\Forms\IForm::RESULT_SUCCESS);

	/**
	 * Set translator to translate field labels, options, placeholders and error messages.
	 * Translator has to be `callable` (it could be `closure function` or `array`
	 * with `classname/instance` and `method name` string). First argument
	 * of `callable` has to be a translation key and second argument
	 * has to be language string (`en`, `de` ...) to translate the key into.
	 * Result of `callable` object has to be a string - translated key for called language.
	 * @param callable|NULL $handler
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetTranslator (callable $translator = NULL);

	/**
	 * Set default switch how to set every form control to be required by default.
	 * If you define directly any control to NOT be required, it will NOT be required.
	 * This is only value used as DEFAULT VALUE for form fiels, not to strictly define
	 * required flag value in controls. Default value is `FALSE`.
	 * @param bool $defaultRequired
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetDefaultRequired ($defaultRequired = TRUE);

	/**
	 * Set multiple fields values by key/value array.
	 * For each key in `$values` array, this method try to find form field
	 * with the same name. Only data with existing fields by keys are setted into field values.
	 * Values are assigned into fields by keys in case sensitive mode by default.
	 * @param array $values						Key value array with keys as field names and values for fields.
	 * @param bool  $caseInsensitive			If `TRUE`, set up values from `$values` with keys in case insensive mode.
	 * @param bool  $clearPreviousSessionValues If `TRUE`, clear all previous data records for this form from session.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetValues (array $values = [], $caseInsensitive = FALSE, $clearPreviousSessionValues = FALSE);

	/**
	 * Set all form errors. This method is very dangerous, it replace
	 * all previously added form errors with given collection.
	 * If you  want only to add form error, use method:
	 * `$form->AddError($errorMsg, $fieldNames = NULL);` instead.
	 * First param `$errorsCollection` has to be array with arrays.
	 * Every array in collection must have first item as error message
	 * string and second argument (optional) as field name string or
	 * array with field names strings where error happend.
	 * @param array $errorsCollection
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrors ($errorsCollection = []);

	/**
	 * Set session expiration in seconds. Default value is zero seconds (`0`).
	 * Zero value (`0`) means "until the browser is closed" if there is no more
	 * higher namespace expirations in whole session.
	 * @param $seconds int
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetSessionExpiration ($seconds = 0);

	/**
	 * Set base tabindex value for every field in form, which has defined tabindex value (different from `NULL`).
	 * This value could move tabindex values for each field into higher or lower values by needs, 
	 * where is form currently rendered.
	 * @param $baseTabIndex int
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetBaseTabIndex ($baseTabIndex = 0);

	/**
	 * Set default control/label rendering mode for each form control/label.
	 * Default values is string `normal`, it means label will be rendered
	 * before control, only label for checkbox and radio button will be
	 * rendered after control.
	 * @param string $defaultFieldsRenderMode
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetDefaultFieldsRenderMode ($defaultFieldsRenderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_NORMAL);

	/**
	 * Set errors rendering mode, by default configured as string: `all-together`.
	 * It means all errors are rendered naturaly at form begin together in one HTML `div.errors` element.
	 * If you are using custom template for form - you have to call after form beginning: `echo $this->RenderErrors();`
	 * to get all errors into template.
	 * @param string $errorsRenderMode
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorsRenderMode ($errorsRenderMode = \MvcCore\Ext\Forms\IForm::ERROR_RENDER_MODE_ALL_TOGETHER);

	/**
	 * Set custom form view script relative path without `.phtml` extension.
	 * View script could be `TRUE` to render form by view script name completed
	 * automaticly with form id and configured view extension (.phtml) or explicit
	 * relative view script path defined by string. Automaticly completed form view
	 * script path and also explicitly defined form view script path by string are
	 * located in directory `/App/Views/Forms` by default. If you want to change this
	 * base directory - use `\MvcCore\Ext\Forms\View::SetFormsDir();` static method.
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetViewScript ($boolOrViewScriptPath = NULL);

	/**
	 * Set form custom template full class name to create custom view object.
	 * Default value is `\MvcCore\Ext\Forms\View` extended from `\MvcCore\View`.
	 * @param string $viewClass
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetViewClass ($viewClass = '\\MvcCore\\Ext\\Forms\\View');

	/**
	 * Set supporting javascript files configuration. This method is dangerous,
	 * It removes all previously, automaticly configured javascript support files.
	 * If you want only to add javascript support file, call method:
	 * `$form->AddJsSupportFile($jsRelativePath, $jsClassName, $constructorParams);` instead.
	 * Every record in given `$jsPathsClassNamesAndParams` array has to be defined as array with:
	 *	 `0` - `string` - Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 *	 `1` - `string` - Supporting javascript full class name inside supporting file.
	 *	 `2` - `array`  - Supporting javascript constructor params.
	 * @param array $jsFilesClassesAndConstructorParams
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFiles (array $jsRelPathsClassNamesAndParams = []);

	/**
	 * Set supporting css files configuration. This method is dangerous,
	 * It removes all previously, automaticly configured css support files.
	 * If you want only to add css support file, call method:
	 * `$form->AddCssSupportFile($cssRelativePath);` instead.
	 * Given `$cssRelativePaths` has to be array with supporting css file relative
	 * paths from protected `\MvcCore\Ext\Form::$cssAssetsRootDir`.
	 * @param array $cssRelativePaths
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFiles (array $cssRelativePaths = []);

	/**
	 * Set javascript support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal javascript
	 * supporting file. Javascript renderer must add given supporting javascript
	 * file into HTML only once.
	 * @param callable|NULL $jsSupportFilesRenderer
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetJsSupportFilesRenderer (callable $jsSupportFilesRenderer);

	/**
	 * Set css support files external renderer. Given callable has
	 * to accept first argument to be `\SplFileInfo` about extenal css
	 * supporting file. Css renderer must add given supporting css
	 * file into HTML only once.
	 * @param callable|NULL $cssSupportFilesRenderer
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssSupportFilesRenderer (callable $cssSupportFilesRenderer);

	/**
	 * This is INTERNAL method for rendering fields. 
	 * Value `TRUE` means `<form>` tag is currently rendered inside, `FALSE` otherwise.
	 * @param bool $formTagRenderingStatus 
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function SetFormTagRenderingStatus ($formTagRenderingStatus = TRUE);

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
	public static function SetJsSupportFilesRootDir ($jsSupportFilesRootDir);

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
	public static function SetCssSupportFilesRootDir ($cssSupportFilesRootDir);

	/**
	 * Set form validators base namespaces to create validator instance by it's class name.
	 * Validator will be created by class existence in this namespaces order.
	 * This method is dangerous, because it removes all previously configured
	 * validators namespaces. If you only to add another validators namespace,
	 * use method: `\MvcCore\Ext\Form::AddValidatorsNamespaces(...$namespaces);` instead.
	 * @param \string[] $validatorsNamespaces
	 * @return int New validators namespaces count.
	 */
	public static function & SetValidatorsNamespaces (array $validatorsNamespaces = []);

	/**
	 * Set `autofocus` boolean attribute to target form field by form id and field name.
	 * If there is already defined any previously autofocused field, defined third argument
	 * to not thrown an exception but to solve the duplicity. Third argument possible values:
	 * - `0` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION`)
	 *   Default value, an exception is thrown when there is already defined other autofocused form element.
	 * - `1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW`)
	 *   There will be removed previously defined autofocused element and configured new given one.
	 * - `-1` (`\MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW`)
	 *   There will be quietly configured another field autofocused. Be carefull!!! This is not standard behaviour!
	 * If there is `$formId` and also `$fieldName` with `NULL` value, any previously defined
	 * autofocused form field is changed and `autofocus` boolean attribute is removed.
	 * @param string $formId 
	 * @param string $fieldName 
	 * @param int $duplicateBehaviour 
	 * @throws \RuntimeException 
	 * @return bool
	 */
	public static function SetAutoFocusedFormField (
		$formId = NULL, 
		$fieldName = NULL, 
		$duplicateBehaviour = \MvcCore\Ext\Forms\IField::AUTOFOCUS_DUPLICITY_EXCEPTION
	);
	

	/***************************************************************************
	 *                            AddMethods Form trait                        *
	 **************************************************************************/

	/**
	 * Add into list of character encodings that the server accepts. The 
	 * browser uses them in the order in which they are listed. The default 
	 * value,the reserved string `'UNKNOWN'`, indicates the same encoding 
	 * as that of the document containing the form element.
	 * @param string $charset 
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function AddAcceptCharset ($charset);

	/**
	 * Add css classes strings for HTML element attribute `class`.
	 * Given css classes will be added after previously defined css classes.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & AddCssClasses ($cssClasses);

	/**
	 * Add form submit error and switch form result to zero - to error state.
	 * @param string $errorMsg Any error message, translated if necessary. All html tags from error message will be removed automaticly.
	 * @param string|array|NULL $fieldNames Optional, field name string or array with field names where error happend.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function AddError ($errorMsg, $fieldNames = NULL);

	/**
	 * Add supporting javascript file.
	 * @param string $jsRelativePath	Supporting javascript file relative path from protected `\MvcCore\Ext\Form::$jsAssetsRootDir`.
	 * @param string $jsClassName		Supporting javascript full class name inside supporting file.
	 * @param array  $constructorParams	Supporting javascript constructor params.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & AddJsSupportFile (
		$jsRelativePath = '/fields/custom-type.js', 
		$jsClassName = 'MvcCoreForm.FieldType', 
		$constructorParams = []
	);

	/**
	 * Add supporting css file.
	 * @param string $cssRelativePath Supporting css file relative path from protected `\MvcCore\Ext\Form::$cssAssetsRootDir`.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & AddCssSupportFile ($cssRelativePath = '/fields/custom-type.css');

	/**
	 * Add CSRF (Cross Site Request Forgery) error handler.
	 * If CSRF submit comparation fails, it's automaticly processed
	 * queue with this handlers, you can put here for example handler
	 * to deauthenticate your user or anything else to more secure your application.
	 * Params in `callable` should be two with following types:
	 *	- `\MvcCore\Ext\Form`	- Form instance where error happend.
	 *	- `\MvcCore\Request`	- Current request object.
	 *	- `string`				- Translated error meessage string.
	 * Example:
	 * `\MvcCore\Ext\Form::AddCsrfErrorHandler(function($form, $request, $errorMsg) {
	 *		// ... anything you want to do, for example to sign out user.
	 * });`
	 * @param callable $handler
	 * @param int|NULL $priorityIndex
	 * @return int New CSRF error handlers count.
	 */
	public static function AddCsrfErrorHandler (callable $handler, $priorityIndex = NULL);

	/**
	 * Add form validators base namespaces to create validator instance by it's class name.
	 * Validator will be created by class existence in this namespaces order.
	 * Validators namespaces array configured by default: `array('\\MvcCore\\Ext\\Forms\\Validators\\');`.
	 * @param \string[] $validatorsNamespaces,...
	 * @return int New validators namespaces count.
	 */
	public static function AddValidatorsNamespaces (/* ...$validatorsNamespaces */);


	/***************************************************************************
	 *                           FieldMethods Form trait                       *
	 **************************************************************************/

	/**
	 * Get all form field controls.
	 * After adding any field into form instance by `$form->AddField()` method
	 * field is added under it's name into this array with all another form fields 
	 * except CSRF `input:hidden`s. Fields are rendered by order in this array.
	 * @return \MvcCore\Ext\Forms\IField[]
	 */
	public function & GetFields();

	/**
	 * Replace all previously configured fields with given fully configured fields array.
	 * This method is dangerous - it will remove all previously added form fiels
	 * and add given fields. If you want only to add another field(s) into form,
	 * use functions:
	 * - `$form->AddField($field);`
	 * - `$form->AddFields($field1, $field2, $field3...);`
	 * @param \MvcCore\Ext\Forms\IField[] $fields Array with `\MvcCore\Ext\Forms\IField` instances to set into form.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SetFields ($fields = []);

	/**
	 * Add multiple fully configured form field instances,
	 * function have infinite params with new field instances.
	 * @param \MvcCore\Ext\Forms\IField[] $fields,... Any `\MvcCore\Ext\Forms\IField` fully configured instance to add into form.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & AddFields (/* ...$fields */);

	/**
	 * Add fully configured form field instance.
	 * @param \MvcCore\Ext\Forms\IField $field
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & AddField (\MvcCore\Ext\Forms\IField $field);

	/**
	 * If `TRUE` if given field instance or given
	 * field name exists in form, `FALSE` otherwise.
	 * @param \MvcCore\Ext\Forms\IField|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasField ($fieldOrFieldName = NULL);

	/**
	 * Remove configured form field instance by given instance or given field name.
	 * If field is not found by it's name, no error happend.
	 * @param \MvcCore\Ext\Forms\IField|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & RemoveField ($fieldOrFieldName = NULL);

	/**
	 * Return form field instance by form field name if it exists, else return null;
	 * @param string $fieldName
	 * @return \MvcCore\Ext\Forms\IField|NULL
	 */
	public function & GetField ($fieldName = '');

	/**
	 * Return form field instances by given field type string.
	 * If no field(s) found, it's returned empty array.
	 * Result array is keyed by field names.
	 * @param string $fieldType
	 * @return \MvcCore\Ext\Forms\IField[]|array
	 */
	public function & GetFieldsByType ($fieldType = '');

	/**
	 * Return first catched form field instance by given field type string.
	 * If no field found, `NULL` is returned.
	 * @param string $fieldType
	 * @return \MvcCore\Ext\Forms\IField|NULL
	 */
	public function & GetFirstFieldByType ($fieldType = '');

	/**
	 * Return form field instances by field class name
	 * compared by `is_a($field, $fieldClassName)` check.
	 * If no field(s) found, it's returned empty array.
	 * Result array is keyed by field names.
	 * @param string $fieldClassName Full php class name or full interface name.
	 * @param bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\IField[]|array
	 */
	public function & GetFieldsByPhpClass ($fieldClassName = '', $directTypesOnly = FALSE);

	/**
	 * Return first catched form field instance by field class name
	 * compared by `is_a($field, $fieldClassName)` check.
	 * If no field found, it's returned `NULL`.
	 * @param string $fieldClassName Full php class name or full interface name.
	 * @param bool   $directTypesOnly Get only instances created directly from called type, no instances extended from given class name.
	 * @return \MvcCore\Ext\Forms\IField|NULL
	 */
	public function & GetFirstFieldByPhpClass ($fieldClassName = '', $directTypesOnly = FALSE);


	/***************************************************************************
	 *                             Session Form trait                          *
	 **************************************************************************/

	/**
	 * Clear form values to empty array and clear form values in form session namespace,
	 * clear form errors to empty array and clear form errors in form session namespace and
	 * clear form CSRF tokens clear CRSF tokens in form session namespace.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & ClearSession ();

	/**
	 * Store form values, form errors and form CSRF tokens
	 * in it's own form session namespace.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SaveSession ();


	/***************************************************************************
	 *                            Rendering Form trait                         *
	 **************************************************************************/

	/**
	 * Rendering process alias for `\MvcCore\Ext\Form::Render();`.
	 * @return string
	 */
	public function __toString ();

	/**
	 * Render whole `<form>` with all content into HTML string to display it.
	 * - If form is not initialized, there is automaticly
	 *   called `$form->Init();` method.
	 * - If form is not pre-dispatched for rendering, there is
	 *   automaticly called `$form->Predispatch();` method.
	 * - Create new form view instance and set up the view with local
	 *   context variables.
	 * - Render form naturaly or by custom template.
	 * - Clean session errors, because errors shoud be rendered
	 *   only once, only when it's used and it's now - in this rendering process.
	 * @return string
	 */
	public function Render ($controllerDashedName = '', $actionDashedName = '');

	/**
	 * Render form inner content, all field controls, content inside `<form>` tag, 
	 * without form errors. Go through all `$form->fields` and call `$field->Render();` 
	 * on every field instance and put field render result into an empty `<div>` 
	 * element. Render each field in full possible way - naturaly by label 
	 * configuration with possible errors configured beside or with custom field template.
	 * @return string
	 */
	public function RenderContent ();

	/**
	 * Render form errors to display them inside `<form>` element.
	 * If form is configured to render all errors together at form beginning,
	 * this function completes all form errors into `div.errors` with `div.error` elements
	 * inside containing each single errors message.
	 * @return string
	 */
	public function RenderErrors ();

	/**
	 * Render form begin - opening `<form>` tag and automaticly
	 * prepared hidden input with CSRF (Cross Site Request Forgery) tokens.
	 * @return string
	 */
	public function RenderBegin ();

	/**
	 * Render form end - closing `</form>` tag and supporting javascript and css files
	 * only if there is necessary to add any supporting javascript or css files by
	 * form configuration and if form is not using external JS/CSS renderer(s).
	 * @return string
	 */
	public function RenderEnd ();

	/**
	 * Render all supporting CSS files directly
	 * as `<style>` tag content inside HTML template
	 * placed directly after `</form>` end tag or
	 * render all supporting CSS files by configured external
	 * CSS files renderer to add only links to HTML response `<head>`
	 * section, linked to external CSS source files.
	 * @return string
	 */
	public function RenderSupportingCss ();

	/**
	 * Render all supporting JS files directly
	 * as `<script>` tag content inside HTML template
	 * placed directly after `</form>` end tag or
	 * render all supporting JS files by configured external
	 * JS files renderer to add only links to HTML response `<head>`
	 * section, linked to external JS source files.
	 * Anyway there is always created at least one `<script>` tag 
	 * placed directly after `</form>` end tag with supporting javascripts
	 * initializations - `new MvcCoreForm(/*javascript*\/);` - by rendered form fieds 
	 * options, names, counts, values etc...
	 * @return string
	 */
	public function RenderSupportingJs ();


	/***************************************************************************
	 *                            Submitting Form trait                        *
	 **************************************************************************/

	/**
	 * Process standard low level submit process.
	 * If no params passed as first argument, all params from object
	 * `\MvcCore\Application::GetInstance()->GetRequest()` are used.
	 * - If fields are not initialized - initialize them by calling `$form->Init();`.
	 * - Check max. post size by php configuration if form is posted.
	 * - Check cross site request forgery tokens with session tokens.
	 * - Process all field values and their validators and call `$form->AddError()` where necessary.
	 *	 `AddError()` method automaticly switch `$form->Result` property to zero - `0`, it means error submit result.
	 * Return array with form result, safe values from validators and errors array.
	 * @param array $rawRequestParams optional
	 * @return array Array to list: `array($form->Result, $form->Data, $form->Errors);`
	 */
	public function Submit (array & $rawRequestParams = []);

	/**
	 * Try to set up form submit result state into any special positive
	 * value by presented submit button name in `$rawRequestParams` array
	 * if there is any special submit result value configured by button names
	 * in `$form->customResultStates` array. If no special button submit result 
	 * value configured, submit result state is set to `1` by default.
	 * @param array $rawRequestParams 
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function SubmitSetStartResultState (array & $rawRequestParams = []);

	/**
	 * Validate max. posted size in POST request body by `Content-Length` HTTP header.
	 * If there is no `Content-Length` request header, add error.
	 * If `Content-Length` value is bigger than `post_max_size` from PHP ini, add form error.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function & SubmitValidateMaxPostSizeIfNecessary ();
	
	/**
	 * Go throught all fields, which are not `button:submit` or `input:submit` types
	 * and call on every `$field->Submit()` method to process all configured field validators.
	 * If method `$field->Submit()` returns anything else than `NULL`, that value is automaticly
	 * assigned under field name into form result values and into form field value.
	 * @param array $rawRequestParams
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function SubmitAllFields (array & $rawRequestParams = []);


	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/prev/next
	 * step url address into final place and store everything into session.
	 * You can also to redirect form after submit by yourself.
	 * @return void
	 */
	public function SubmittedRedirect ();

	/**
	 * Get cached validator instance by name. If validator instance doesn't exist
	 * in `$this->validators` array, create new validator instance, cache it and return it.
	 * @param string $validatorName 
	 * @return \MvcCore\Ext\Forms\IValidator
	 */
	public function & GetValidator ($validatorName);

	/**
	 * Get error message string from internal protected static property
	 * `\MvcCore\Ext\Form::$defaultErrorMessages` by given integer index.
	 * @param int $index
	 * @return string
	 */
	public function GetDefaultErrorMsg ($index);

	/***************************************************************************
	 *                               Csrf Form trait                           *
	 **************************************************************************/

	/**
	 * Call all CSRF (Cross Site Request Forgery) error handlers in static queue.
	 * @param \MvcCore\Ext\Forms\IForm $form Form instance where CSRF error happend.
	 * @param string $errorMsg Translated error message abouzt CSRF invalid tokens.
	 * @return void
	 */
	public static function ProcessCsrfErrorHandlersQueue (\MvcCore\Ext\Forms\IForm & $form, $errorMsg);

	/**
	 * Return current CSRF (Cross Site Request Forgery) hidden
	 * input name and it's value as `\stdClass`with  keys `name` and `value`.
	 * @return \stdClass
	 */
	public function GetCsrf ();

	/**
	 * Check CSRF (Cross Site Request Forgery) sended tokens from user with session tokens.
	 * If tokens are diferent, add form error and process CSRF error handlers queue.
	 * If there is any exception catched in CSRF error handlers queue, it's logged
	 * by configured core debug class with `CRITICAL` flag.
	 * @param array $rawRequestParams Raw request params given into `Submit()` method or all `\Mvccore\Request` params.
	 * @return \MvcCore\Ext\Forms\IForm
	 */
	public function SubmitCsrfTokens (array & $rawRequestParams = []);

	/**
	 * Create new fresh CSRF (Cross Site Request Forgery) tokens,
	 * store them in current form session namespace and return them.
	 * @return \string[]
	 */
	public function SetUpCsrf ();
}
