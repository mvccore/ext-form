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

namespace MvcCore\Ext\Forms\Field;

trait Getters
{
	/**
	 * Get form field HTML id attribute, completed from form name and field name.
	 * This value is completed automaticly, but you can customize it.
	 * @return string|NULL
	 */
	public function GetId () {
		return $this->id;
	}

	/**
	 * Get form field specific name, used to identify submited value.
	 * This value is reguired for all form fields.
	 * @return string|NULL
	 */
	public function GetName () {
		return $this->name;
	}

	/**
	 * Get form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @return string|NULL
	 */
	public function GetType () {
		return $this->type;
	}

	/**
	 * Get form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @return string|array|int|float|NULL
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * Get control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automaticly
	 * before rendering process. Default value is `NULL`.
	 * @return string|NULL
	 */
	public function GetLabel () {
		return $this->label;
	}

	/**
	 * Get label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @return string
	 */
	public function GetLabelSide () {
		return $this->labelSide;
	}

	/**
	 * Get rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->SetLabelSide('left' | 'right');`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL` - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL` - `<input />`
	 * @return string
	 */
	public function GetRenderMode () {
		return $this->renderMode;
	}

	/**
	 * Get form field attribute required, determinating
	 * if field will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automaticly considered as required by default form configuration.
	 * But this method return only value stored inside this field instance.
	 * @return bool|NULL
	 */
	public function GetRequired () {
		return $this->required;
	}

	/**
	 * Get form field attribute `readonly`, determinating if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sended under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @return bool|NULL
	 */
	public function GetReadOnly () {
		return $this->readOnly;
	}

	/**
	 * Get form field attribute `disabled`, determinating if field value will be 
	 * possible to change by user and if user will be graphicly informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sended under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @return bool|NULL
	 */
	public function GetDisabled () {
		return $this->disabled;
	}

	/**
	 * Get form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @return \string[]
	 */
	public function & GetCssClasses () {
		return $this->cssClasses;
	}

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
	public function & GetControlAttrs () {
		return $this->controlAttrs;
	}

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
	public function GetControlAttr ($name = 'data-*') {
		return isset($this->controlAttrs[$name])
			? $this->controlAttrs[$name]
			: NULL;
	}

	/**
	 * Get collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * There are no system attributes as: `id`,`for` or
	 * `class` attributes, those attributes have it's own 
	 * configurable properties with it's own getters. 
	 * Label `class` attribute has always the same css 
	 * classes as it's field automaticly. 
	 * Default value is an empty array to not render 
	 * any additional attributes.
	 * @return array
	 */
	public function & GetLabelAttrs () {
		return $this->labelAttrs;
	}

	/**
	 * Get `<label>` HTML element additional attribute 
	 * by name and with it's value. Do not use system 
	 * attributes as: `id`,`for` or `class`, those 
	 * attributes has it's own configurable properties 
	 * with it's own getters. Label `class` attribute 
	 * has always the same css classes as it's field automaticly. 
	 * If attribute doesn't exist, `NULL` is returned.
	 * @param string $name
	 * @return mixed
	 */
	public function GetLabelAttr ($name = 'data-*') {
		return isset($this->labelAttrs[$name])
			? $this->labelAttrs[$name]
			: NULL;
	}

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
	public function & GetValidators () {
		return $this->validators;
	}

	/**
	 * Get `TRUE`, if field has configured in it's validators array
	 * given validator class ending name or validator instance.
	 * @param string|\MvcCore\Ext\Forms\IValidator $validatorNameOrInstance
	 * @return bool
	 */
	public function & HasValidator ($validatorNameOrInstance) {
		if (is_string($validatorNameOrInstance)) {
			$validatorClassName = $validatorNameOrInstance;
		} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
			$validatorClassName = get_class($validatorNameOrInstance);
		} else {
			return $this->throwNewInvalidArgumentException(
				'Unknown validator type given: `' . $validatorNameOrInstance 
				. '`, type: `' . gettype($validatorNameOrInstance) . '`.'
			);
		}
		$slashPos = strrpos($validatorClassName, '\\');
		$validatorName = $slashPos !== FALSE 
			? substr($validatorClassName, $slashPos + 1)
			: $validatorClassName;
		return isset($this->validators[$validatorName]);
	}

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
	 * `FALSE` or `NULL` (`NULL` is default) is returned to render field naturaly.
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
	public function GetViewScript () {
		return $this->viewScript;
	}

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
	public function GetJsClassName () {
		return $this->jsClassName;
	}

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
	public function GetJsSupportingFile () {
		return $this->jsSupportingFile;
	}

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
	public function GetCssSupportingFile () {
		return $this->cssSupportingFile;
	}

	/**
	 * Get fields (and labels) default templates 
	 * for natural (not customized) field rendering.
	 * @return array
	 */
	public static function & GetTemplates () {
		return (array) static::$templates;
	}
}
