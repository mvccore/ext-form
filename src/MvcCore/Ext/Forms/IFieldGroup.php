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

namespace MvcCore\Ext\Forms;

interface IFieldGroup
{
	/**
	 * Get css class(es) for group label as array of strings.
	 * @return \string[]
	 */
	public function & GetGroupCssClass ();

	/**
	 * Set css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space.
	 * Any previously defined group css classes will be replaced.
	 * @var string|string[]
	 */
	public function & SetGroupCssClasses ($cssClasses);

	/**
	 * Add css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function AddGroupCssClass ($cssClasses);

	/**
	 * Get any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * @var \string[]
	 */
	public function & GetGroupLabelAttrs ();

	/**
	 * Set any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * Any previously defined attributes will be replaced.
	 * @param $groupLabelAttrs string[]
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetGroupLabelAttrs ($groupLabelAttrs = []);

	/**
	 * Add any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * All additional attributes will be completed as array merge
	 * with previous values and new values.
	 * @var string[]
	 */
	public function AddGroupLabelAttr ($attr = []);
}
