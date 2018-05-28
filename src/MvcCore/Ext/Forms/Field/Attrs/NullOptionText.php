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

trait NullOptionText
{
	/**
	 * @var string|NULL
	 */
	protected $nullOptionText = NULL;

	/**
	 * @param string $nullOptionText 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function & SetNullOptionText ($nullOptionText) {
		$this->nullOptionText = $nullOptionText;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetNullOptionText () {
		return $this->nullOptionText;
	}
}
