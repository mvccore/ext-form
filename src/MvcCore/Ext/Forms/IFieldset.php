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

interface IFieldset {
	
	const ALLOWED_LEGEND_ELEMENTS = '<abbr><b><bdo><br><canvas><cite><code><data><dfn><em><h1><h2><h3><h4><h5><h6><i><img><kbd><mark><math><meter><output><picture><progress><q><ruby><samp><small><span><strong><sub><sup><svg><time><var><wbr>';

	/**
	 * Get form fieldset specific name, used to identify 
	 * fieldset between each other and between fields.
	 * This value is required and it's used mostly internally.
	 * @return string
	 */
	public function GetName ();

	/**
	 * Set form fieldset specific name, used to identify 
	 * fieldset between each other and between fields.
	 * This value is required and it's used mostly internally.
	 * @requires
	 * @param  string $name
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetName ($name);

	/**
	 * Get fixed field order number, `NULL` by default.
	 * @return int|NULL 
	 */
	public function GetFieldOrder ();

	/**
	 * Set fixed field order number, `NULL` by default.
	 * @param  int $fieldOrder
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldOrder ($fieldOrder);

	/**
	 * Get form fieldset `<legend>` tag content, it 
	 * could contains HTML code, default `NULL`.
	 * Allowed HTML tags are container in constant:
	 * `\MvcCore\Ext\Forms\IFielset::ALLOWED_LEGEND_ELEMENTS`.
	 * @return string|NULL
	 */
	public function GetLegend ();

	/**
	 * Set form fieldset `<legend>` tag content, it 
	 * could contains HTML code, default `NULL`.
	 * Allowed HTML tags are container in constant:
	 * `\MvcCore\Ext\Forms\IFielset::ALLOWED_LEGEND_ELEMENTS`.
	 * @param  string|NULL $legend 
	 * @param  bool|NULL   $translateLegend
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetLegend ($legend, $translateLegend = NULL);

	/**
	 * Get form fieldset `disabled` boolean attribute, 
	 * default `FALSE`. Browsers render all fields 
	 * disabled in `<fieldset>` with disabled attribute.
	 * Disabled fieldset also automatically disables 
	 * all fields inside this fieldset for submitting.
	 * @return bool
	 */
	public function GetDisabled ();

	/**
	 * Set form fieldset `disabled` boolean attribute, 
	 * default `FALSE`. Browsers render all fields 
	 * disabled in `<fieldset>` with disabled attribute.
	 * Disabled fieldset also automatically disables 
	 * all fields inside this fieldset for submitting.
	 * @param  bool $disabled
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetDisabled ($disabled);
	

	/**
	 * Get form field HTML element css classes strings as array.
	 * Default value is an empty array, but there is always rendered
	 * HTML `class` attribute with fieldset name as css class.
	 * @return \string[]
	 */
	public function & GetCssClasses ();

	
	/**
	 * Set form field HTML element css classes strings.
	 * All previously defined css classes will be removed.
	 * Default value is an empty array, but there is always rendered
	 * HTML `class` attribute with fieldset name as css class.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param  string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetCssClasses ($cssClasses);
	
	/**
	 * Add css classes strings for HTML element attribute `class`.
	 * Given css classes will be added after previously defined css classes.
	 * Default value is an empty array, but there is always rendered
	 * HTML `class` attribute with fieldset name as css class.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param  string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddCssClasses ($cssClasses);

	/**
	 * Get field title, global HTML attribute, optional.
	 * @return string|NULL
	 */
	public function GetTitle ();
	
	/**
	 * Set field title, global HTML attribute, optional.
	 * @param  string|NULL $title
	 * @param  bool|NULL   $translateTitle
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetTitle ($title, $translateTitle = NULL);

	/**
	 * Get collection with fieldset HTML element additional 
	 * attributes by array keys/values. Do not use system 
	 * attributes as: `name`, `disabled`, `class` or `title` ...
	 * Those attributes has it's own configurable properties 
	 * by setter methods or by constructor config array. 
	 * Default value is an empty array to not  render 
	 * any additional attributes.
	 * @return array
	 */
	public function & GetControlAttrs ();

	/**
	 * Get fieldset HTML element additional attribute 
	 * by attribute name and value.
	 * There are no system attributes as: `name`, 
	 * `disabled`, `class` or `title` ...
	 * Those attributes have it's own configurable 
	 * properties with it's own getters.
	 * If attribute doesn't exist, `NULL` is returned.
	 * @param  string $name
	 * @return mixed
	 */
	public function GetControlAttr ($name = 'data-*');

	/**
	 * Set collection with fieldset HTML element additional 
	 * attributes by array keys/values. Do not use system 
	 * attributes as: `name`, `disabled`, `class` or `title` ...
	 * Those attributes has it's own configurable properties 
	 * by setter methods or by constructor config array. 
	 * Default value is an empty array to not  render 
	 * any additional attributes.
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetControlAttrs (array $attrs = []);

	/**
	 * Set fieldset HTML element additional attribute 
	 * by attribute name and value.
	 * Do not use system attributes as: `name`, 
	 * `disabled`, `class` or `title` ...
	 * Those attributes have it's own configurable properties
	 * by setter methods or by constructor config array.
	 * Given additional fieldset attribute will be directly
	 * set into additional attributes array and any 
	 * previous attribute with the same name will be overwritten.
	 * @param  string $name
	 * @param  mixed  $value
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetControlAttr ($name, $value);

	/**
	 * Add fieldset HTML element additional attribute 
	 * by attribute name and value.
	 * Do not use system attributes as: `name`, 
	 * `disabled`, `class` or `title` ...
	 * Those attributes have it's own configurable properties
	 * by setter methods or by constructor config array.
	 * All given additional fieldset attributes 
	 * will be merged with previously defined attributes.
	 * @param  array $attrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddControlAttrs (array $attrs = []);
	
	
	/***************************************************************************
	 *                       FieldMethods Fieldset trait                       *
	 **************************************************************************/

	/**
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFields ($fields);
	
	/**
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @param  string|NULL              $fieldName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field, $fieldName = NULL);

	/**
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFields ($fields);
	
	/**
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddField (\MvcCore\Ext\Forms\IField $field);

	/**
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasField ($fieldOrFieldName);

	/**
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveField ($fieldOrFieldName);
	
	/**
	 * 
	 * @return \MvcCore\Ext\Forms\Field[]
	 */
	public function & GetFields ();
	
	/**
	 * 
	 * @param  string $fieldName
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetField ($fieldName);


	/**
	 * 
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetParentFieldset ();
	
	/**
	 * 
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetParentFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset = NULL);

	/**
	 * @return \MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetFieldsets ();
	
	/**
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldsets ($fieldsets);
	
	/**
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFieldsets ($fieldsets);
	
	/**
	 * 
	 * @param  string $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetFieldset ($fieldsetName);
	
	/**
	 * 
	 * @param  string                      $fieldsetName
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldset ($fieldsetName, \MvcCore\Ext\Forms\IFieldset $fieldset);

	/**
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset);

	/**
	 * 
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasFieldset ($fieldsetOrFieldsetName);

	/**
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveFieldset ($fieldsetOrFieldsetName);
	
	/**
	 * Sort direct children fields and fieldsets, not recursively.
	 * By natural order and also by `fieldOrder` property on each child if any.
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SortChildren ();

	/**
	 * Return fieldset controls content tree structure for rendering. Sorted by default.
	 * Array keys are field or fieldset names, values are fields instances or fieldset instances.
	 * @param  bool $sorted
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren ($sorted = TRUE);
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * fieldset is naturally rendered. It sets up fieldset for rendering process.
	 * Do not use this method even if you don't develop any form fieldset.
	 * - 
	 * - 
	 * @internal
	 * @template
	 * @return void
	 */
	public function PreDispatch ();
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after fieldset
	 * is added into form instance by `$form->AddFieldset();` method. Do not 
	 * use this method even if you don't develop any form fieldset.
	 * -
	 * -
	 * -
	 * @internal
	 * @template
	 * @param  \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetForm (\MvcCore\Ext\IForm $form);

	/**
	 * @return \MvcCore\Ext\Form
	 */
	public function GetForm ();
			
	/**
	 * Render fieldset with it's legend and contained 
	 * controls, labels and another fieldsets.
	 * @return string
	 */
	public function Render ();

	/**
	 * @return string
	 */
	public function RenderLegend ();
	
	/**
	 * @return string
	 */
	public function RenderContent ();

}