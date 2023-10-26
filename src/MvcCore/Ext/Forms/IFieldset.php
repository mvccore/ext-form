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
	 * Sort direct children fields and fieldsets, not recursively.
	 * By natural order and also by `fieldOrder` property on each 
	 * child (field or fieldset) if any.
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SortChildren ();

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * fieldset is naturally rendered. It sets up fieldset for rendering process.
	 * Do not use this method even if you don't develop any form fieldset.
	 * - method translate legend and title if necessary.
	 * @internal
	 * @template
	 * @return void
	 */
	public function PreDispatch ();
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after fieldset
	 * is added into form instance by `$form->AddFieldset();` method. Do not 
	 * use this method even if you don't develop any form fieldset.
	 * - method sets up form instance into fieldset
	 * - method sets up translation booleans
	 * @internal
	 * @template
	 * @param  \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetForm (\MvcCore\Ext\IForm $form);
	
	
	/***************************************************************************
	 *                        Rendering Fieldset trait                         *
	 **************************************************************************/
	
	/**
	 * Render fieldset with it's legend, possible errors 
	 * and contained controls, labels and another fieldsets.
	 * @return string
	 */
	public function __toString ();

	/**
	 * Render fieldset with it's legend, possible errors 
	 * and contained controls, labels and another fieldsets.
	 * @return string
	 */
	public function Render ();

	/**
	 * Render fieldset `<legend>` element with allowed HTML tags.
	 * @return string
	 */
	public function RenderLegend ();
	
	/**
	 * Render fieldset errors for current fielset 
	 * level and fielset children controls.
	 * @return string
	 */
	public function RenderErrorsAndContent ();

	
	/***************************************************************************
	 *                      GettersSetters Fieldset trait                      *
	 **************************************************************************/

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

	/**
	 * Get form content rendering mode (only inside fieldset), configuration how errors, 
	 * labels, constrols  and submit buttons will be rendered - with or without 
	 * any structural HTML elements like `<div>` or `<table>` elements.
	 * Default value is to render form content with `<div>` elements structure.
	 * This value could be uset to change form rendering mode only inside fieldset,
	 * not in whole form. If this `value` is not configured, it's used form settings.
	 * @return int|NULL
	 */
	public function GetFormRenderMode ();

	/**
	 * Set form content rendering mode (only inside fieldset), configuration how errors, 
	 * labels, constrols and submit buttons will be rendered - with or without 
	 * any structural HTML elements like `<div>` or `<table>` elements.
	 * Default value is to render form content with `<div>` elements structure.
	 * This value could be uset to change form rendering mode only inside fieldset,
	 * not in whole form. If this `value` is not configured, it's used form settings.
	 * @param  int $formRenderMode
	 * @return \MvcCore\Ext\Form
	 */
	public function SetFormRenderMode ($formRenderMode = \MvcCore\Ext\IForm::FORM_RENDER_MODE_DIV_STRUCTURE);
	
	/**
	 * Return fields and fieldset controls content tree structure 
	 * for rendering. Sorted by default. Array keys are field or 
	 * fieldset names, values are fields instances or fieldset instances.
	 * @param  bool $sorted
	 * @return \MvcCore\Ext\Forms\Field[]|\MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetChildren ($sorted = TRUE);
	
	/**
	 * Return form instance if fieldset has been added into form or `NULL` otherwise.
	 * @return \MvcCore\Ext\Form|NULL
	 */
	public function GetForm ();
	
	
	/***************************************************************************
	 *                       FieldMethods Fieldset trait                       *
	 **************************************************************************/

	/**
	 * Replace all previously configured fields with given fields array. 
	 * Method have infinite params with new field instances. This 
	 * method is powerful - it removes previously added fields into this
	 * fieldset only and adds new given fields. If you want 
	 * only to add another field(s) into this fieldset, use methods:
	 *  - `$fieldset->AddField($field);`
	 *  - `$fieldset->AddFields($field1, $field2, $field3...);`
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFields ($fields);
	
	/**
	 * Replace previously configured field with new given field.
	 * This method is powerful - it removes any previously added field
	 * inside this fieldset only and adds new given field. If you want 
	 * only to add another field(s) into this fieldset, use methods:
	 *  - `$fieldset->AddField($field);`
	 *  - `$fieldset->AddFields($field1, $field2, $field3...);`
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @param  string|NULL              $fieldName
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field, $fieldName = NULL);

	/**
	 * Add multiple fully configured form field instances into fieldset,
	 * method have infinite params with new field instances.
	 * After this fieldset is added into form instance, all fields 
	 * inside this fieldset (and also inside any nested fieldsets) are 
	 * automatically registered into form, so it's not necessary 
	 * to call `$form->AddFields($fields)` on form instance again.
	 * @param  \MvcCore\Ext\Forms\Field[] $fields,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFields ($fields);
	
	/**
	 * Add fully configured form field instance into fieldset.
	 * After this fieldset is added into form instance, all fields 
	 * inside this fieldset (and also inside any nested fieldsets) are 
	 * automatically registered into form, so it's not necessary 
	 * to call `$form->AddField($field)` on form instance again.
	 * @param  \MvcCore\Ext\Forms\Field $field
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddField (\MvcCore\Ext\Forms\IField $field);

	/**
	 * Return `TRUE` if given field instance or given
	 * field name exists inside this fieldset, `FALSE` otherwise.
	 * Method doesn't return `TRUE` if field exists in any nested 
	 * fieldset inside this fieldset, only if field is direct child.
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasField ($fieldOrFieldName);

	/**
	 * Remove configured field instance by given instance or given 
	 * field name from this fieldset. If fieldset already containes 
	 * form instance, then the field is also unregistered from form.
	 * If field is not found by it's name, no error thrown. 
	 * Method doens't remove field from any nested fieldset, to 
	 * remove field from nested fildset, use the same method on 
	 * nested fieldset or call `$form->RemoveField($field)`.
	 * @param  \MvcCore\Ext\Forms\Field|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveField ($fieldOrFieldName);
	
	/**
	 * Get field controls added only into this fieldset. Method doesn't
	 * return fields in nested fieldsets. If you need fieldset fields 
	 * structure in rendering order, use method `$fieldset->GetChildren()` 
	 * instead.
	 * @return \MvcCore\Ext\Forms\Field[]
	 */
	public function & GetFields ();
	
	/**
	 * Return fieldset field instance by field name if it exists, 
	 * or return `NULL` if field not found. Method returns only field 
	 * instance existing inside this fieldset level, not in any nested 
	 * fieldset or not inside whole form. 
	 * @param  string $fieldName
	 * @return \MvcCore\Ext\Forms\Field|NULL
	 */
	public function GetField ($fieldName);

	
	/***************************************************************************
	 *                     FieldsetMethods Fieldset trait                      *
	 **************************************************************************/

	/**
	 * Get parent fieldset instance if any or `NULL`.
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetParentFieldset ();
	
	/**
	 * Set parent fieldset instance. If any previous parent fieldset
	 * instance already exists, an exception is thrown. This method 
	 * is used mostly internally.
	 * @internal
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetParentFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset = NULL);

	/**
	 * Replace all previously configured fieldsets with given fieldsets array. 
	 * Method have infinite params with new fieldset instances. This 
	 * method is powerful - it removes all previously added fieldsets inside
	 * this fieldset and if form instance in this fieldset already exists, 
	 * it removes all fieldsets also from form. Than method adds new given fieldsets. 
	 * If you want only to add another fieldset(s) into form, use methods:
	 *  - `$form->AddFieldset($fieldset);`
	 *  - `$form->AddFieldsets($fieldset1, $fieldset2, $fieldset3...);`
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldsets ($fieldsets);
	
	/**
	 * Replace previously configured fieldset with new given fieldset.
	 * This method is powerful - it removes previously added fieldset
	 * directly inside this fieldset and if form instance in this fieldset 
	 * already exists, it removes fieldset also from form. Than method
	 * adds new given fieldset. If you want only to add another fieldset(s) 
	 * into form, use methods:
	 *  - `$form->AddFieldset($fieldset);`
	 *  - `$form->AddFieldsets($fieldset1, $fieldset2, $fieldset3...);`
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @param  string|NULL                 $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset, $fieldsetName = NULL);

	/**
	 * Add multiple fully configured form field instances into fieldset,
	 * method have infinite params with new field instances.
	 * After this fieldset is added into form instance, all nested fieldsets 
	 * inside this fieldset (and also inside any nested fieldsets) are 
	 * automatically registered into form, so it's not necessary 
	 * to call `$form->AddFieldsets($fieldsets)` on form instance again.
	 * @param  \MvcCore\Ext\Forms\Fieldset[] $fieldsets,...
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFieldsets ($fieldsets);
	
	/**
	 * Add fully configured fieldset instance into fieldset.
	 * After this fieldset is added into form instance, all nested fieldsets 
	 * inside this fieldset (and also inside any nested fieldsets) are 
	 * automatically registered into form, so it's not necessary 
	 * to call `$form->AddFieldset($fieldset)` on form instance again.
	 * @param  \MvcCore\Ext\Forms\Fieldset $fieldset
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function AddFieldset (\MvcCore\Ext\Forms\IFieldset $fieldset);

	/**
	 * Return `TRUE` if given fieldset instance or given
	 * fieldset name exists insinde this fieldset, `FALSE` otherwise.
	 * Method doesn't return `TRUE` if fieldset exists in any nested 
	 * fieldset inside this fieldset, only if fieldset is direct child.
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return bool
	 */
	public function HasFieldset ($fieldsetOrFieldsetName);

	/**
	 * Remove configured fieldset instance by given instance or given 
	 * fieldset name from this fieldset. If fieldset already containes 
	 * form instance, then the fieldset is also unregistered from form.
	 * If fieldset is not found by it's name, no error thrown. 
	 * Method doens't remove fieldset from any nested fieldset, to 
	 * remove fieldset from nested fildset, use the same method on 
	 * nested fieldset or call `$form->RemoveFieldset($fieldset)`.
	 * @param  \MvcCore\Ext\Forms\Fieldset|string $fieldOrFieldName
	 * @return \MvcCore\Ext\Form
	 */
	public function RemoveFieldset ($fieldsetOrFieldsetName);
	
	/**
	 * Get children fieldsets added only into this fieldset. Method doesn't
	 * return fieldsets in nested fieldsets. If you need fieldset inside 
	 * fieldsets structure in rendering order, use method 
	 * `$fieldset->GetChildren()` instead.
	 * @return \MvcCore\Ext\Forms\Fieldset[]
	 */
	public function GetFieldsets ();
	
	/**
	 * Return children fieldset instance by field name if it exists, 
	 * or return `NULL` if fieldset not found. Method returns only fieldset 
	 * instance existing inside this fieldset level, not in any nested 
	 * fieldset or not inside whole form.  
	 * @param  string $fieldsetName
	 * @return \MvcCore\Ext\Forms\Fieldset|NULL
	 */
	public function GetFieldset ($fieldsetName);

	/**
	 * Get fieldset template for natural rendering (not customized with `*.phtml` view).
	 * Default value: `<fieldset name={name}{attrs}>{legend}{content}</fieldset>`.
	 * @return string
	 */
	public function GetTemplate ();

	/**
	 * Set fieldset template for natural rendering (not customized with `*.phtml` view).
	 * Default value: `<fieldset name={name}{attrs}>{legend}{content}</fieldset>`.
	 * @param  string $template Template HTML code with prepared replacements.
	 * @return \MvcCore\Ext\Forms\Fieldset
	 */
	public function SetTemplate ($template);
	
}