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
	 * @var string|NULL
	 */
	protected $action = NULL;

	/**
	 * Form http submitting method.
	 * `POST` by default.
	 * @var string
	 */
	protected $method = \MvcCore\Ext\Forms\IForm::METHOD_POST;

	/**
	 * Form enctype attribute - how the form values
	 * should be encoded when submitting it to the server.
	 * `application/x-www-form-urlencoded` by default, it means
	 * all form values will be encoded to `key1=value1&key2=value2...` string.
	 * @var string
	 */
	protected $enctype = \MvcCore\Ext\Forms\IForm::ENCTYPE_URLENCODED;

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
	 * @var string
	 */
	protected $cssClass = '';

	/**
	 * Form html element additional attributes.
	 * To add any other attribute for HTML `<form>` element,
	 * put here key/value array, keys will be used as attribute names,
	 * values as attribute values, simple.
	 * @var array
	 */
	protected $attributes = array();

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
	 * Form success submit next step url string, relative or absolute, to specify,
	 * where to redirect user after form has been submitted successfully and submit button
	 * will be recognized to switch form result property `$form->result` to value `2`.
	 * Which means "next step" redirection after successfull submit. This functionality
	 * to switch result value to `2` is up to you. This field is designed only for you as empty.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * and you want to go to "next step" by one non-submit button or stay in the same page by
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
	 *       `\MvcCore\Ext\Form::RESULT_ERRORS`
	 * `1` - Submit was successfull. User will be redirected after submit to success url.
	 *       `\MvcCore\Ext\Form::RESULT_SUCCESS`
	 * `2` - Submit was successfull. User will be redirected after submit to next step url.
	 *       `\MvcCore\Ext\Form::RESULT_NEXT_PAGE`
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
	protected $fields = array();

	/**
	 * Form submited values from client. After `$form->Submit()` has been called,
	 * values are cleaned by validators and ready to use if `$form->result` is in success state.
	 * @var array
	 */
	protected $values = array();

	/**
	 * If any configured error happends by executing `$form->Submit()`, it's stored in this array.
	 * Every record in this array is array with first item to be an error message string.
	 * If the error is for specific field name or field names, there is also a second item - array with field names.
	 * Errors array has normal numeric keys.
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Session expiration in seconds. Default value is zero seconds (`0`).
	 * Zero value (`0`) means "until the browser is closed" if there is no more
	 * higher namespace expirations in whole session.
	 * @var int
	 */
	protected $sessionExpiration = 0;

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
	 *	 `0` - `string` - Supporting javascript file relative path from protected `$form->jsAssetsRootDir`.
	 *	 `1` - `string` - Supporting javascript full class name inside supporting file.
	 *	 `2` - `array`  - Supporting javascript constructor params.
	 * @var array
	 */
	protected $jsSupportFiles = array();

	/**
	 * Supporting css files configuration.
	 * Array with supporting css relative paths from
	 * protected `$form->cssAssetsRootDir` to add
	 * into HTML response after form is rendered.
	 * @var \string[]
	 */
	protected $cssSupportFiles = array();

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
	protected $jsSupportFilesRootDir = NULL;

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
	protected $cssSupportFilesRootDir = NULL;
}
