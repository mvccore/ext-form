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

interface IField
{
	/**
	 * Render label HTML element on the left side from field HTML element.
	 */
	const LABEL_SIDE_LEFT = 'left';

	/**
	 * Render label HTML element on the right side from field HTML element.
	 */
	const LABEL_SIDE_RIGHT = 'right';
	
	/**
	 * Constants used internally and mostly
	 * in field autofocus setter to define additional
	 * behaviour for possible duplicate field focus.
	 */
	const	AUTOFOCUS_DUPLICITY_EXCEPTION = 0,
			AUTOFOCUS_DUPLICITY_UNSET_OLD_SET_NEW = 1,
			AUTOFOCUS_DUPLICITY_QUIETLY_SET_NEW = -1;

	
	/***************************************************************************
	 *                              Base Field class                           *
	 **************************************************************************/

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form instance by `$form->AddField();` method. Do not 
	 * use this method even if you don't develop any form field.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Set up translate boolean property.
	 * @param \MvcCore\Ext\Forms\IForm $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form);

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * - Set up field render mode if not defined.
	 * - Translate label text if necessary.
	 * @return void
	 */
	public function PreDispatch ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` 
	 * in submit processing. Do not use this method even if you 
	 * don't develop form library or any form field.
	 * 
	 * Submit field value - process raw request value with all
	 * configured validators and add errors into form if necessary.
	 * Then return safe processed value by all from validators or `NULL`.
	 * 
	 * @param array $rawRequestParams Raw request params from MvcCore 
	 *								  request object based on raw app 
	 *								  input, `$_GET` or `$_POST`.
	 * @return string|int|array|NULL
	 */
	public function Submit (array & $rawRequestParams = []);

	/**
	 * Default implementation for any extended field class to get field specific
	 * data for validator purposes. If you want to extend any field, you could 
	 * implement this method better and faster. It's only necessary in your 
	 * implementation to return array with keys to be field specific properties 
	 * in camel case and values to be field properties values, which validator 
	 * requires.
	 * @param array $fieldPropsDefaultValidValues
	 * @return array
	 */
	public function & GetValidatorData ($fieldPropsDefaultValidValues = []);

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field` 
	 * in submit processing. Do not use this method even if you 
	 * don't develop any form field or field validator.
	 * 
	 * Add form error with given error message containing 
	 * possible replacements for array values. 
	 * 
	 * If there is necessary to translate form elements 
	 * (form has configured property `translator` as `callable`)
	 * than given error message is translated first before replacing.
	 * 
	 * Before error message processing for replacements,
	 * there is automatically assigned into first position into `$errorMsgArgs`
	 * array (translated) field label or field name and than 
	 * error message is processed for replacements.
	 * 
	 * If there is given some custom `$replacingCallable` param,
	 * error message is processed for replacements by custom `$replacingCallable`.
	 * 
	 * If there is not given any custom `$replacingCallable` param,
	 * error message is processed for replacements by static `Format()`
	 * method by configured form view class.
	 * 
	 * @param string $errorMsg 
	 * @param array $errorMsgArgs 
	 * @param callable $replacingCallable 
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function AddValidationError (
		$errorMsg = '', array 
		$errorMsgArgs = [], 
		callable $replacingCallable = NULL
	);
	

	/***************************************************************************
	 *                             Getters Field trait                         *
	 **************************************************************************/
	
	/**
	 * Get form field HTML id attribute, completed from form name and field name.
	 * This value is completed automatically, but you can customize it.
	 * @return string|NULL
	 */
	public function GetId ();

	/**
	 * Get form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @return string|NULL
	 */
	public function GetName ();

	/**
	 * Get form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @return string|NULL
	 */
	public function GetType ();

	/**
	 * Get form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @return string|array|int|float|NULL
	 */
	public function GetValue ();

	/**
	 * Get form field HTML element css classes strings as array.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @return \string[]
	 */
	public function & GetCssClasses ();

	/**
	 * Get collection with field HTML element 
	 * additional attributes by array keys/values.
	 * There are no system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable 
	 * properties with it's own getters.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`
	 * Default value is an empty array to not 
	 * render any additional attributes.
	 * @return array
	 */
	public function & GetControlAttrs ();

	/**
	 * Get field HTML element additional attribute 
	 * by attribute name and value.
	 * There are no system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable 
	 * properties with it's own getters.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`
	 * If attribute doesn't exist, `NULL` is returned.
	 * @param string $name
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*');

	/**
	 * Get list of predefined validator classes ending names or validator instances.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every validator class (ending name) or validator instance has to 
	 * implement interface `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * @return \string[]|\MvcCore\Ext\Forms\IValidator[]
	 */
	public function & GetValidators ();

	/**
	 * Get `TRUE`, if field has configured in it's validators array
	 * given validator class ending name or validator instance.
	 * @param string|\MvcCore\Ext\Forms\IValidator $validatorNameOrInstance
	 * @return bool
	 */
	public function & HasValidator ($validatorNameOrInstance);

	/**
	 * Get boolean `TRUE` or string with template relative path 
	 * without `.phtml` or `.php` extension to render 
	 * field by any custom template. 
	 * 
	 * If `TRUE`, path to template is always completed by configured 
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);`
	 * value, which is `/App/Views/Forms/Fields` by default.
	 * 
	 * If returned any string with relative path, path is always relative from configured
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);` value, which is again 
	 * `/App/Views/Forms/Fields` by default.
	 * 
	 * `FALSE` or `NULL` (`NULL` is default) is returned to render field naturally.
	 * 
	 * Example:
	 * ```
	 * // Render field template prepared in:
	 * // '/App/Views/Forms/Fields/my-specials/my-field-type.phtml':
	 * 
	 * \MvcCore\Ext\Forms\View::GetFieldsDir(); // returned by default: 'Forms/Fields'
	 * $field->GetViewScript(); // returned 'my-specials/my-field-type'
	 * 
	 * // Or the same by:
	 * \MvcCore\Ext\Forms\View::GetFieldsDir(); // returned 'Forms/Fields/my-specials'
	 * $field->GetType(); // returned 'my-field-type'
	 * $field->GetViewScript(); // returned TRUE
	 * ```
	 * @return bool|string|NULL
	 */
	public function GetViewScript ();

	/**
	 * Get supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->SetJsSupportingFile(...)` to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @return string|NULL
	 */
	public function GetJsClassName ();

	/**
	 * Get field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @return string|NULL
	 */
	public function GetJsSupportingFile ();

	/**
	 * Get field supporting css file relative path.
	 * If you want to use any custom supporting css file 
	 * for any additional purposes for your custom field, you need to 
	 * define path to your css file relatively from configured 
	 * `\MvcCore\Ext\Form::SetCssSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting css file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetCssSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting css files into response by your 
	 * own and also you can run your helper css also by your own. Is up to you.
	 * `NULL` by default.
	 * @return string|NULL
	 */
	public function GetCssSupportingFile ();

	/**
	 * Get fields (and labels) default templates 
	 * for natural (not customized) field rendering.
	 * @return array
	 */
	public static function & GetTemplates ();


	/***************************************************************************
	 *                             Setters Field trait                         *
	 **************************************************************************/
	
	/**
	 * Set form field HTML id attribute, completed from form name and field name.
	 * This value is completed automatically, but you can customize it.
	 * @param string $id
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetId ($id = NULL);

	/**
	 * Set form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @requires
	 * @param string $name
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetName ($name = NULL);

	/**
	 * Set form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param string $type
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetType ($type = NULL);

	/**
	 * Set form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param string|array|int|float|NULL $value
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetValue ($value);

	/**
	 * Set form field HTML element css classes strings.
	 * All previously defined css classes will be removed.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetCssClasses ($cssClasses);

	/**
	 * Add css classes strings for HTML element attribute `class`.
	 * Given css classes will be added after previously defined css classes.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & AddCssClasses ($cssClasses);

	/**
	 * Set collection with field HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable properties
	 * by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`
	 * Default value is an empty array to not 
	 * render any additional attributes.
	 * All previously defined additional field attributes 
	 * will be replaced by given array.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetControlAttrs (array $attrs = []);

	/**
	 * Set field HTML element additional attribute 
	 * by attribute name and value.
	 * Do not use system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable properties
	 * by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`
	 * Given additional field attribute will be directly
	 * set into additional attributes array and any 
	 * previous attribute with the same name will be overwritten.
	 * @param string $name
	 * @param mixed $value
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetControlAttr ($name, $value);

	/**
	 * Add (and merge) collection with field HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, 
	 * `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes have it's own configurable properties
	 * by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, 
	 * <button>, <select>, <textarea> ...`.
	 * All given additional field attributes 
	 * will be merged with previously defined attributes.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & AddControlAttrs (array $attrs = []);

	/**
	 * Set list of predefined validator classes ending names or validator instances.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every given validator class (ending name) or given validator instance has to 
	 * implement interface `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * @param \string[]|\MvcCore\Ext\Forms\IValidator[] $validatorsNamesOrInstances
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetValidators (array $validatorsNamesOrInstances = []);

	/**
	 * Add list of predefined validator classes ending names or validator instances.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every given validator class (ending name) or given validator instance has to 
	 * implement interface  `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base  abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * @param \string[]|\MvcCore\Ext\Forms\IValidator[] $validatorsNamesOrInstances,...
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & AddValidators (/* ...$validatorsNamesOrInstances */);

	/**
	 * Remove predefined validator by given class ending name or by given validator instance.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every given validator class (ending name) or given validator instance has to 
	 * implement interface  `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base  abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * @param \string[]|\MvcCore\Ext\Forms\IValidator[] $validatorNameOrInstance,...
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & RemoveValidator ($validatorNameOrInstance);

	/**
	 * Set boolean `TRUE` or string with template relative path 
	 * without `.phtml` or `.php` extension, if you want to render 
	 * field by any custom template. 
	 * 
	 * If `TRUE` given, path to template
	 * is completed by configured `\MvcCore\Ext\Forms\view::SetFieldsDir(...);`
	 * value, which is `/App/Views/Forms/Fields` by default.
	 * 
	 * If any string with relative path given, path must be relative from configured
	 * `\MvcCore\Ext\Forms\view::SetFieldsDir(...);` value, which is again 
	 * `/App/Views/Forms/Fields` by default.
	 * 
	 * To render field naturally, set `FALSE`, empty string or `NULL` (`NULL` is default).
	 * 
	 * Example:
	 * ```
	 * // To render field template prepared in:
	 * // '/App/Views/Forms/Fields/my-specials/my-field-type.phtml':
	 * 
	 * \MvcCore\Ext\Forms\View::SetFieldsDir('Forms/Fields'); // by default
	 * $field->SetViewScript('my-specials/my-field-type');
	 * 
	 * // Or you can do the same by:
	 * \MvcCore\Ext\Forms\View::SetFieldsDir('Forms/Fields/my-specials');
	 * $field->SetType('my-field-type');
	 * ```
	 * @param bool|string|NULL $boolOrViewScriptPath
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetViewScript ($boolOrViewScriptPath = NULL);

	/**
	 * Set supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->SetJsSupportingFile(...)` to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @param string $jsClass
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetJsClassName ($jsClassName);

	/**
	 * Set field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @param string $jsFullFile
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetJsSupportingFile ($jsSupportingFilePath);

	/**
	 * Set field supporting css file relative path.
	 * If you want to use any custom supporting css file 
	 * for any additional purposes for your custom field, you need to 
	 * define path to your css file relatively from configured 
	 * `\MvcCore\Ext\Form::SetCssSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting css file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetCssSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting css files into response by your 
	 * own and also you can run your helper css also by your own. Is up to you.
	 * `NULL` by default.
	 * @param string $cssFullFile
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetCssSupportingFile ($cssSupportingFilePath);

	/**
	 * Add field error message text to render it in rendering process.
	 * This method is only for rendering purposes, not to add errors
	 * into session. It's always called internally from `\MvcCore\Ext\Form`
	 * in render preparing process. To add form error properly, 
	 * use `$field->form->AddError($errorMsg, $fieldNames);` method instead.
	 * @param string $errorMsg
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & AddError ($errorMsg);

	/**
	 * Set field (or label) default template for natural
	 * (not customized with `*.phtml` view) field rendering.
	 * @param string $templateName Template name in array `static::$templates`.
	 * @param string $templateCode Template HTML code with prepared replacements.
	 * @return string Newly configured template value.
	 */
	public static function SetTemplate ($templateName = 'control',  $templateCode = '');

	/**
	 * Set fields (and labels) default templates for natural
	 * (not customized with `*.phtml` view) field rendering.
	 * @param array|\stdClass $templates 
	 * @return array
	 */
	public static function SetTemplates ($templates = []);


	/***************************************************************************
	 *                            Rendering Field trait                        *
	 **************************************************************************/

	/**
	 * Render field in full mode (with configured label), naturally or by custom template.
	 * @return string
	 */
	public function Render ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Renders field by configured custom template property `$field->viewScript`.
	 * This method creates `$view = new \MvcCore\Ext\Form\Core\View();`,
	 * sets all local context variables into view instance and renders 
	 * configured view instance into result string.
	 * @return string
	 */
	public function RenderTemplate ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field naturally by configured property `$field->renderMode` if any 
	 * or by default render mode without any label. Field should be rendered with 
	 * label beside, label around or without label by local field configuration. 
	 * Also there could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field control and label by local configuration in left or in right side,
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderLabelAndControl ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field control inside label by local configuration, render field
	 * errors beside if form is configured to render specific errors beside controls.
	 * @return string
	 */
	public function RenderControlInsideLabel ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field specific errors only without control or label.
	 * @return string
	 */
	public function RenderErrors ();
}
