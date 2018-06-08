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

trait AutoComplete
{
	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autocomplete
	 * @var string|NULL
	 */
	protected $autoComplete = NULL;

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autocomplete
	 * @return string|NULL
	 */
	public function GetAutoComplete () {
		return $this->autoComplete;
	}

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-autocomplete
	 * @param string $autoComplete 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetAutoComplete ($autoComplete) {
		$this->autoComplete = $autoComplete;
		return $this;
	}
}
