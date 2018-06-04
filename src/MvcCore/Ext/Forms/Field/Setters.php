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

trait Setters
{
	/**
	 * Set form field HTML id attribute, completed from form name and field name.
	 * This value is completed automaticly, but you can customize it.
	 * @param string $id
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetId ($id = NULL) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Set form field specific name, used to identify submited value.
	 * This value is reguired for all form fields.
	 * @requires
	 * @param string $name
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetName ($name = NULL) {
		$this->name = $name;
		return $this;
	}

	/**
	 * Set form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param string $type
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetType ($type = NULL) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Set form field value. It could be string or array, in or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param string|array|int|float|NULL $value
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValue ($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * Set control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automaticly
	 * before rendering process. Default value is `NULL`.
	 * @param string $label
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabel ($label = NULL) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Set label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param string $labelSide
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabelSide ($labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT) {
		$this->labelSide = $labelSide;
		return $this;
	}

	/**
	 * Set rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->SetLabelSide('left' | 'right');`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL` - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL` - `<input />`
	 * @param string $renderMode
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRenderMode ($renderMode = \MvcCore\Ext\Forms\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
		$this->renderMode = $renderMode;
		return $this;
	}

	/**
	 * Set form field attribute required, determinating
	 * if field will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automaticly considered required by default form configuration.
	 * @param bool $required
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRequired ($required = TRUE) {
		$this->required = $required;
		return $this;
	}

	/**
	 * Set form field attribute `readonly`, determinating if field value will be 
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
	 * @param bool $readonly
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetReadOnly ($readOnly = TRUE) {
		$this->readOnly = $readOnly;
		return $this;
	}

	/**
	 * Set form field attribute `disabled`, determinating if field value will be 
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
	 * @param bool $readonly
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetDisabled ($disabled) {
		$this->disabled = $disabled;
		return $this;
	}

	/**
	 * Set form field HTML element css classes strings.
	 * All previously defined css classes will be removed.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCssClasses ($cssClasses) {
		$cssClassesArr = gettype($cssClasses) == 'array'
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = $cssClassesArr;
		return $this;
	}

	/**
	 * Add css classes strings for HTML element attribute `class`.
	 * Given css classes will be added after previously defined css classes.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddCssClasses ($cssClasses) {
		$cssClassesArr = gettype($cssClasses) == 'array'
			? $cssClasses
			: explode(' ', (string) $cssClasses);
		$this->cssClasses = array_merge($this->cssClasses, $cssClassesArr);
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetControlAttrs (array $attrs = []) {
		$this->controlAttrs = & $attrs;
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetControlAttr ($name, $value) {
		$this->controlAttrs[$name] = $value;
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddControlAttrs (array $attrs = []) {
		$this->controlAttrs = array_merge($this->controlAttrs, $attrs);
		return $this;
	}

	/**
	 * Set collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or
	 * `class` attributes, those attributes have it's own 
	 * configurable properties by setter methods 
	 * or by constructor config array. Label `class` 
	 * attribute has always the same css classes as 
	 * it's field automaticly. Default value is an empty 
	 * array to not render any additional attributes.
	 * All previously defined additional label attributes 
	 * will be replaced by given array.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabelAttrs (array $attrs = []) {
		$this->labelAttrs = & $attrs;
		return $this;
	}

	/**
	 * Set `<label>` HTML element additional attribute 
	 * by name and with it's value. Do not use system 
	 * attributes as: `id`,`for` or `class`, those 
	 * attributes have it's own configurable properties 
	 * by setter methods or by constructor config array. 
	 * Label `class` attribute has always the same css 
	 * classes as it's field automaticly. 
	 * Given additional label attribute will be directly
	 * set into additional attributes array and any 
	 * previous attribute with the same name will be overwritten.
	 * @param string $name
	 * @param mixed $value
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetLabelAttr ($name, $value) {
		$this->labelAttrs[$name] = $value;
		return $this;
	}

	/**
	 * Add collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or
	 * `class` attributes, those attributes have it's own 
	 * configurable properties by setter methods 
	 * or by constructor config array. Label `class` 
	 * attribute has always the same css classes as 
	 * it's field automaticly. 
	 * All given additional label attributes 
	 * will be merged with previously defined attributes.
	 * @param array $attrs
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddLabelAttrs (array $attrs = []) {
		$this->labelAttrs = array_merge($this->labelAttrs, $attrs);
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValidators (array $validatorsNamesOrInstances = []) {
		$this->validators = [];
		return call_user_func_array([$this, 'AddValidators'], $validatorsNamesOrInstances);
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddValidators (/* ...$validatorsNamesOrInstances */) {
		$validatorsNamesOrInstances = func_get_args();
		foreach ($validatorsNamesOrInstances as $validatorNameOrInstance) {
			$instanceType = FALSE;
			if (is_string($validatorNameOrInstance)) {
				$validatorClassName = $validatorNameOrInstance;
			} else if ($validatorNameOrInstance instanceof \MvcCore\Ext\Forms\IValidator) {
				$instanceType = TRUE;
				$validatorClassName = get_class($validatorNameOrInstance);
			} else  {
				return $this->throwNewInvalidArgumentException(
				'Unknown validator type given: `' . $validatorNameOrInstance 
					. '`, type: `' . gettype($validatorNameOrInstance) . '`.'
				);
			}
			$slashPos = strrpos($validatorClassName, '\\');
			$validatorName = $slashPos !== FALSE 
				? substr($validatorClassName, $slashPos + 1)
				: $validatorClassName;
			$this->validators[$validatorName] = $validatorNameOrInstance;
			if ($instanceType) $validatorNameOrInstance->SetField($this);
		}
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & RemoveValidator ($validatorNameOrInstance) {
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
		if (isset($this->validators[$validatorName]))
			unset($this->validators[$validatorName]);
		return $this;
	}

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
	 * To render field naturaly, set `FALSE`, empty string or `NULL` (`NULL` is default).
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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetViewScript ($boolOrViewScriptPath = NULL) {
		$this->viewScript = $boolOrViewScriptPath;
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetJsClassName ($jsClassName) {
		$this->jsClassName = $jsClassName;
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetJsSupportingFile ($jsSupportingFilePath) {
		$this->jsSupportingFile = $jsSupportingFilePath;
		return $this;
	}

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
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & SetCssSupportingFile ($cssSupportingFilePath) {
		$this->cssSupportingFile = $cssSupportingFilePath;
		return $this;
	}

	/**
	 * Add field error message text to render it in rendering process.
	 * This method is only for rendering purposes, not to add errors
	 * into session. It's always called internaly from `\MvcCore\Ext\Form`
	 * in render preparing process. To add form error properly, 
	 * use `$field->form->AddError($errorMsg, $fieldNames);` method instead.
	 * @param string $errorMsg
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IField
	 */
	public function & AddError ($errorMsg) {
		$this->errors[] = $errorMsg;
		return $this;
	}

	/**
	 * Set fields (and labels) default templates 
	 * for natural (not customized) field rendering.
	 * @param array|\stdClass $templates 
	 * @return array
	 */
	public static function SetTemplates ($templates = []) {
		return static::$templates = (array) $templates;
	}
}
