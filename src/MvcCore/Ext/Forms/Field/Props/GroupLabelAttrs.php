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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\FieldsGroup`
 *    - `\MvcCore\Ext\Forms\CheckboxGroup`
 *    - `\MvcCore\Ext\Forms\RadioGroup`
 */
trait GroupLabelAttrs {

	/**
	 * Any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * @var array
	 */
	protected $groupLabelAttrs = [];

	/**
	 * Get any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * @return array
	 */
	public function & GetGroupLabelAttrs () {
		return $this->groupLabelAttrs;
	}

	/**
	 * Set any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * Any previously defined attributes will be replaced.
	 * @param array $groupLabelAttrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetGroupLabelAttrs ($groupLabelAttrs = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->groupLabelAttrs = $groupLabelAttrs;
		return $this;
	}

	/**
	 * Add any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * All additional attributes will be completed as array merge
	 * with previous values and new values.
	 * @param array $groupLabelAttrs
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddGroupLabelAttr ($groupLabelAttrs = []) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->groupLabelAttrs = array_merge($this->groupLabelAttrs, $groupLabelAttrs);
		return $this;
	}
}
