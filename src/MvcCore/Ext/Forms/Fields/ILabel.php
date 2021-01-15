<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: define getters and setters for field label 
 *				   properties: `label`, `labelSide` and `labelAttrs`.
 * Interface for classes:
 * - `\MvcCore\Ext\Forms\Field`
 *    - `\MvcCore\Ext\Forms\Field\Rendering`
 * - `\MvcCore\Ext\Forms\Fields\Color`
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\File`
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\ResetInput`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
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
interface ILabel {

	/**
	 * Get control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automatically
	 * before rendering process. Default value is `NULL`.
	 * @return string|NULL
	 */
	public function GetLabel ();

	/**
	 * Set control label visible text.
	 * If field form has configured any translator,
	 * translation will be processed automatically
	 * before rendering process. Default value is `NULL`.
	 * @param string|NULL  $label
	 * @param boolean|NULL $translateLabel
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabel ($label, $translateLabel = NULL);

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
	public function GetLabelSide ();

	/**
	 * Set label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param string $labelSide
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabelSide ($labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT);

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
	public function GetRenderMode ();

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
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetRenderMode ($renderMode = \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND);

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
	public function & GetLabelAttrs ();

	/**
	 * Get `<label>` HTML element additional attribute 
	 * by name and with it's value. Do not use system 
	 * attributes as: `id`,`for` or `class`, those 
	 * attributes has it's own configurable properties 
	 * with it's own getters. Label `class` attribute 
	 * has always the same css classes as it's field automatically. 
	 * If attribute doesn't exist, `NULL` is returned.
	 * @param string $name
	 * @return mixed
	 */
	public function GetLabelAttr ($name = 'data-*');

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
	 * @param array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabelAttrs (array $attrs = []);

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
	 * @param string $name
	 * @param mixed $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetLabelAttr ($name, $value);

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
	 * @param array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddLabelAttrs (array $attrs = []);
}
