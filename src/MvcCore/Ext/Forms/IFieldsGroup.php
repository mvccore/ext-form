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

namespace MvcCore\Ext\Forms;

interface IFieldsGroup {

	/**
	 * Create new form control group instance.
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public static function CreateInstance ($cfg = []);

	/**
	 * Get css class or classes for group label as array of strings.
	 * @return \string[]
	 */
	public function & GetGroupLabelCssClasses ();

	/**
	 * Set css class or classes for group label,
	 * as array of strings or string with classes
	 * separated by space. Any previously defined 
	 * group css classes will be replaced.
	 * @param string|\string[] $groupLabelCssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetGroupLabelCssClasses ($groupLabelCssClasses);

	/**
	 * Add css class or classes for group label as array of 
	 * strings or string with classes separated by space.
	 * @param string|\string[] $groupLabelCssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddGroupLabelCssClasses ($groupLabelCssClasses);

	/**
	 * Get any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * @return array
	 */
	public function & GetGroupLabelAttrs ();

	/**
	 * Set any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * Any previously defined attributes will be replaced.
	 * @param array $groupLabelAttrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetGroupLabelAttrs ($groupLabelAttrs = []);

	/**
	 * Add any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * All additional attributes will be completed as array merge
	 * with previous values and new values.
	 * @param array $groupLabelAttrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddGroupLabelAttr ($groupLabelAttrs = []);

	/**
	 * Get form group controls value, in most cases it's an array of strings.
	 * For extended class `RadioGroup` - the type is only a `string` or `NULL`.
	 * @return \string[]|string|NULL
	 */
	public function GetValue ();

	/**
	 * Set form group controls value, in most cases - it could be an array with types,
	 * which are possible simply to convert into array of strings. `NULL` value is then 
	 * an empty array. For extended class `RadioGroup` - the value type is only a `string` 
	 * or `NULL`.
	 * @param \float[]|\int[]|\string[]|float|int|string|NULL $value
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetValue ($value);

	/**
	 * Field group is always marked as multiple value control. This function 
	 * always return `TRUE` for field group instance.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return bool
	 */
	public function GetMultiple ();

	/**
	 * Field group is always marked as multiple value control. This function 
	 * does nothing, because multiple option has to be `TRUE` for field group instance all time.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetMultiple ($multiple = TRUE);

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form by `$form->AddField();` method. 
	 * Do not use this method even if you don't develop any form field group.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Check if there are any options for current controls group.
	 * @param \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\FieldsGroup
	 */
	public function SetForm (\MvcCore\Ext\IForm $form);

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * Set up field properties before rendering process.
	 * - Set up field render mode.
	 * - Set up translation boolean.
	 * - Translate label property if any.
	 * - Translate all option texts if necessary.
	 * @return void
	 */
	public function PreDispatch ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field naturally by render mode.
	 * Field should be rendered with label beside, label around
	 * or without label by local field configuration. Also there
	 * could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderNaturally ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render field naturally by configured property `$field->renderMode` if any 
	 * or by default render mode without any label. Field should be rendered with 
	 * label beside, label around or without label by local field configuration. 
	 * Also there could be rendered specific field errors before or after field
	 * if field form is configured in that way.
	 * @return string
	 */
	public function RenderControlInsideLabel ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render all sub-controls by multiple calls of `$field->RenderControlItem();`.
	 * @return string
	 */
	public function RenderControl ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render label tag only without control or specific errors.
	 * @return string
	 */
	public function RenderLabel ();
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\FieldsGroup` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render sub-controls with each sub-control label tag
	 * and without group label or without group specific errors.
	 * @param string $key
	 * @param string|array $option
	 * @return string
	 */
	public function RenderControlItem ($key, $option);
}
