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

trait CustomResultState
{
	/**
	 * `TRUE` to consider this button for "next step submit result state".
	 * `FALSE` or `NULL` otherwise.
	 * @var int|NULL
	 */
	protected $customResultState = NULL;

	public function & SetCustomResultState ($customResultState = \MvcCore\Ext\Forms\IForm::RESULT_NEXT_PAGE) {
		$this->customResultState = $customResultState;
		return $this;
	}

	public function GetCustomResultState () {
		return $this->customResultState;
	}
}
