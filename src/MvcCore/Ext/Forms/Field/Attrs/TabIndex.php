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

namespace MvcCore\Ext\Forms\Field\Attrs;

/**
 * Trait for classes:
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
trait TabIndex
{
	/**
	 * An integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. You can set `auto` string value to get next form tabindex value automaticly. 
	 * Tabindex for every field in form is better to index from value `1` or automaticly and 
	 * moved to specific higher value by place, where is form currently rendered by form 
	 * instance method `$form->SetBaseTabIndex()` to move tabindex for each field into 
	 * final values. Tabindex can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @var int|string|NULL
	 */
	protected $tabIndex = NULL;

	/**
	 * Get an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. You can set `auto` string value to get next form tabindex value automaticly. 
	 * Tabindex for every field in form is better to index from value `1` or automaticly and 
	 * moved to specific higher value by place, where is form currently rendered by form 
	 * instance method `$form->SetBaseTabIndex()` to move tabindex for each field into 
	 * final values. Tabindex can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @return int|string|NULL
	 */
	public function GetTabIndex () {
		return $this->tabIndex;
	}

	/**
	 * Set an integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. You can set `auto` string value to get next form tabindex value automaticly. 
	 * Tabindex for every field in form is better to index from value `1` or automaticly and 
	 * moved to specific higher value by place, where is form currently rendered by form 
	 * instance method `$form->SetBaseTabIndex()` to move tabindex for each field into 
	 * final values. Tabindex can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tabindex. If several elements share the same tabindex, 
	 *   their relative order follows their relative positions in the document.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes#attr-tabindex
	 * @param int|string $tabIndex 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetTabIndex ($tabIndex = 'auto') {
		if ($tabIndex === 'auto' || is_int($tabIndex)) {
			$this->tabIndex = $tabIndex;
		} else {
			$this->throwNewInvalidArgumentException(
				'Tabindex property is possible to configure only with `auto` string or integer. `'.$tabIndex.'` value given.'
			);
		}
		return $this;
	}

	/**
	 * Check after field is added into form, if field 
	 * has defined any value for pattern property and if it does,
	 * add automaticaly build in pattern validator.
	 * @return void
	 */
	protected function preDispatchTabIndex () {
		if ($this->tabIndex === 'auto') 
			$this->tabIndex = $this->form->GetFieldNextAutoTabIndex();
	}
}
