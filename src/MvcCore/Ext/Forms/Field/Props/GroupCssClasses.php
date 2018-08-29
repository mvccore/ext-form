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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 */
trait GroupLabelCssClasses
{
	/**
	 * Css class(es) for group label as array of strings.
	 * @var \string[]
	 */
	protected $groupLabelCssClasses = [];

	/**
	 * Get css class(es) for group label as array of strings.
	 * @return \string[]
	 */
	public function & GetGroupLabelCssClasses () {
		return $this->groupLabelCssClasses;
	}

	/**
	 * Set css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space. Any previously defined 
	 * group css classes will be replaced.
	 * @param string|\string[] $groupLabelCssClasses
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetGroupLabelCssClasses ($groupLabelCssClasses) {
		if (gettype($groupLabelCssClasses) == 'array') {
			$this->groupLabelCssClasses = $groupLabelCssClasses;
		} else {
			$this->groupLabelCssClasses = explode(' ', (string) $groupLabelCssClasses);
		}
		return $this;
	}

	/**
	 * Add css class(es) for group label as array of 
	 * strings or string with classes separated by space.
	 * @param string|\string[] $groupLabelCssClasses
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function AddGroupLabelCssClasses ($groupLabelCssClasses) {
		if (gettype($groupLabelCssClasses) == 'array') {
			$groupCssClasses = $groupLabelCssClasses;
		} else {
			$groupCssClasses = explode(' ', (string) $groupLabelCssClasses);
		}
		$this->groupLabelCssClasses = array_merge($this->groupLabelCssClasses, $groupCssClasses);
		return $this;
	}
}
