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

trait InputMode
{
	protected static $fieldTypesAndInputModes = [
		'text'		=> 'text',
		'password'	=> 'text',
		// decided by value | min | max | step
		//'decimal'	=> 'decimal',
		//'numeric'	=> 'numeric',
		'tel'		=> 'tel',
		'search'	=> 'search',
		'email'		=> 'email',
		'url'		=> 'url'
	];

	/**
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-inputmode
	 * @var string|NULL
	 */
	protected $inputMode = NULL;

	/**
	 * A hint to browsers for which virtual keyboard to display. 
	 * This attribute applies when the the type attribute is 
	 * `text`, `password`, `email`, or `url`. Possible values:
	 * 	`none`		: No virtual keyboard should be displayed.
	 * 	`text`		: Text input in the user's locale.
	 * 	`decimal`	: Fractional numeric input.
	 * 	`numeric`	: Numeric input.
	 * 	`tel`		: Telephone input, including asterisk and 
	 * 				  pound key. Prefer `<input type="tel">`.
	 * 	`search`	: A virtual keyboard optimized for search input.
	 * 	`email`		: Email input. Prefer `<input type="email">`.
	 * 	`url`		: URL input. Prefer `<input type="url">`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-inputmode
	 * @return string|NULL
	 */
	public function & GetInputMode () {
		return $this->inputMode;
	}

	/**
	 * A hint to browsers for which virtual keyboard to display. 
	 * This attribute applies when the the type attribute is 
	 * `text`, `password`, `email`, or `url`. Possible values:
	 * 	`none`		: No virtual keyboard should be displayed.
	 * 	`text`		: Text input in the user's locale.
	 * 	`decimal`	: Fractional numeric input.
	 * 	`numeric`	: Numeric input.
	 * 	`tel`		: Telephone input, including asterisk and 
	 * 				  pound key. Prefer `<input type="tel">`.
	 * 	`search`	: A virtual keyboard optimized for search input.
	 * 	`email`		: Email input. Prefer `<input type="email">`.
	 * 	`url`		: URL input. Prefer `<input type="url">`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-inputmode
	 * @param string|NULL $inputMode
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetInputMode ($inputMode) {
		$this->inputMode = $inputMode;
		return $this;
	}

	/**
	 * Automaticly set up `inputmode` attribute (if it is still `NULL`) by field type.
	 * @return void
	 */
	protected function setFormInputMode () {
		if ($this->inputMode !== NULL) return;
		if ($this->type === 'number') {
			if (
				(is_numeric($this->value) && floor($this->value) !== $this->value) ||
				(is_numeric($this->step) && floor($this->step) !== $this->step) ||
				(is_numeric($this->min) && floor($this->min) !== $this->min) ||
				(is_numeric($this->max) && floor($this->max) !== $this->max)
			) {
				$this->inputMode = 'decimal';
			} else {
				$this->inputMode = 'numeric';
			}
		} else if (isset(static::$fieldTypesAndInputModes[$this->type])) {
			$this->inputMode = static::$fieldTypesAndInputModes[$this->type];
		}
	}
}
