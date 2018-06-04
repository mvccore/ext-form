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

trait GroupCssClasses
{
	/**
	 * Css class for group label.
	 * @var string[]
	 */
	protected $groupCssClasses = [];

	/**
	 * Get css class(es) for group label as array of strings.
	 * @return \string[]
	 */
	public function & GetGroupCssClass () {
		return $this->groupCssClasses;
	}

	/**
	 * Set css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space.
	 * Any previously defined group css classes will be replaced.
	 * @var string|string[]
	 */
	public function & SetGroupCssClasses ($cssClasses) {
		if (gettype($cssClasses) == 'array') {
			$this->groupCssClasses = $cssClasses;
		} else {
			$this->groupCssClasses = explode(' ', (string) $cssClasses);
		}
		return $this;
	}

	/**
	 * Add css class(es) for group label,
	 * as array of strings or string with classes
	 * separated by space.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function AddGroupCssClass ($cssClasses) {
		if (gettype($cssClasses) == 'array') {
			$groupCssClasses = $cssClasses;
		} else {
			$groupCssClasses = explode(' ', (string) $cssClasses);
		}
		$this->groupCssClasses = array_merge($this->groupCssClasses, $groupCssClasses);
		return $this;
	}
}
