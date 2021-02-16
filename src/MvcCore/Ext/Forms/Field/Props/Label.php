<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for all visible fields, for classes:
 * - `\MvcCore\Ext\Forms\Fields\Button`
 *    - `\MvcCore\Ext\Forms\Fields\SubmitButton`
 *    - `\MvcCore\Ext\Forms\Fields\ResetButton`
 * - `\MvcCore\Ext\Forms\Fields\Color`
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\File`
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * - `\MvcCore\Ext\Forms\Fields\Image`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\ResetInput`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\Fields\SubmitInput`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 */
trait Label {

	/**
	 * Control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automatically
	 * before rendering process. Default value is `NULL`.
	 * @var string
	 */
	protected $label = NULL;

	/**
	 * Boolean to translate label text, `TRUE` by default.
	 * @var boolean
	 */
	protected $translateLabel = TRUE;
	
	/**
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @var string|NULL
	 */
	protected $labelSide = NULL; // `NULL | right | left`
	
	/**
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL`       - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL`     - `<input />`
	 * @var int|NULL
	 */
	protected $renderMode = NULL;
	
	/**
	 * Collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or
	 * `class`, those attributes has it's own 
	 * configurable properties by setter methods 
	 * or by constructor config array. Label `class` 
	 * attribute has always the same css classes as 
	 * it's field automatically. Default value is an empty 
	 * array to not render any additional attributes.
	 * @var array
	 */
	protected $labelAttrs = [];

	/**
	 * Get control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automatically
	 * before rendering process. Default value is `NULL`.
	 * @return string|NULL
	 */
	public function GetLabel () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		return $this->label;
	}

	/**
	 * Set control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automatically
	 * before rendering process. Default value is `NULL`.
	 * @param  string|NULL  $label
	 * @param  boolean|NULL $translateLabel
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabel ($label, $translateLabel = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		$this->label = $label;
		if ($translateLabel !== NULL)
			$this->translateLabel = $translateLabel;
		return $this;
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
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		return $this->labelSide;
	}

	/**
	 * Set label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param  string $labelSide
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabelSide ($labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		$this->labelSide = $labelSide;
		return $this;
	}

	/**
	 * Get rendering mode flag how to render field and it's label.
	 * Default value is normal render mode (`0`) to render label and field, label 
	 * first or field first by another property `$field->SetLabelSide('left' | 'right');`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL` - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL` - `<input />`
	 * @return int|NULL
	 */
	public function GetRenderMode () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		return $this->renderMode;
	}

	/**
	 * Set rendering mode flag how to render field and it's label.
	 * Default value is normal render mode (`0`) to render label and field, label 
	 * first or field first by another property `$field->SetLabelSide('left' | 'right');`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL` - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL` - `<input />`
	 * @param  int $renderMode
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetRenderMode ($renderMode = \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		$this->renderMode = $renderMode;
		return $this;
	}

	/**
	 * Get collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * There are no system attributes as: `id`,`for` or
	 * `class` attributes, those attributes have it's own 
	 * configurable properties with it's own getters. 
	 * Label `class` attribute has always the same css 
	 * classes as it's field automatically. 
	 * Default value is an empty array to not render 
	 * any additional attributes.
	 * @return array
	 */
	public function & GetLabelAttrs () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		return $this->labelAttrs;
	}

	/**
	 * Get `<label>` HTML element additional attribute 
	 * by name and with it's value. Do not use system 
	 * attributes as: `id`,`for` or `class`, those 
	 * attributes has it's own configurable properties 
	 * with it's own getters. Label `class` attribute 
	 * has always the same css classes as it's field automatically. 
	 * If attribute doesn't exist, `NULL` is returned.
	 * @param  string $name
	 * @return mixed
	 */
	public function GetLabelAttr ($name = 'data-*') {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		return isset($this->labelAttrs[$name])
			? $this->labelAttrs[$name]
			: NULL;
	}

	/**
	 * Set collection with `<label>` HTML element 
	 * additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or
	 * `class` attributes, those attributes have it's own 
	 * configurable properties by setter methods 
	 * or by constructor config array. Label `class` 
	 * attribute has always the same css classes as 
	 * it's field automatically. Default value is an empty 
	 * array to not render any additional attributes.
	 * All previously defined additional label attributes 
	 * will be replaced by given array.
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabelAttrs (array $attrs = []) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		$this->labelAttrs = $attrs;
		return $this;
	}

	/**
	 * Set `<label>` HTML element additional attribute 
	 * by name and with it's value. Do not use system 
	 * attributes as: `id`,`for` or `class`, those 
	 * attributes have it's own configurable properties 
	 * by setter methods or by constructor config array. 
	 * Label `class` attribute has always the same css 
	 * classes as it's field automatically. 
	 * Given additional label attribute will be directly
	 * set into additional attributes array and any 
	 * previous attribute with the same name will be overwritten.
	 * @param  string $name
	 * @param  mixed  $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabelAttr ($name, $value) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
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
	 * it's field automatically. 
	 * All given additional label attributes 
	 * will be merged with previously defined attributes.
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddLabelAttrs (array $attrs = []) {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\Label */
		$this->labelAttrs = array_merge($this->labelAttrs, $attrs);
		return $this;
	}
}
