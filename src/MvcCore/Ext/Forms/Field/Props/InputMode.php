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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - \MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * @mixin \MvcCore\Ext\Forms\Field
 */
trait InputMode {

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
	 * @var ?string
	 */
	protected $inputMode = NULL;

	/**
	 * A hint to browsers for which virtual keyboard to display. 
	 * This attribute applies when the type attribute is 
	 * `text`, `password`, `email`, or `url`. Possible values:
	 * - `none`    : No virtual keyboard should be displayed.
	 * - `text`    : Text input in the user's locale.
	 * - `decimal` : Fractional numeric input.
	 * - `numeric` : Numeric input.
	 * - `tel`     : Telephone input, including asterisk and 
	 * -             pound key. Prefer `<input type="tel">`.
	 * - `search`  : A virtual keyboard optimized for search input.
	 * - `email`   : Email input. Prefer `<input type="email">`.
	 * - `url`     : URL input. Prefer `<input type="url">`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-inputmode
	 * @return ?string
	 */
	public function GetInputMode () {
		return $this->inputMode;
	}

	/**
	 * A hint to browsers for which virtual keyboard to display. 
	 * This attribute applies when the type attribute is 
	 * `text`, `password`, `email`, or `url`. Possible values:
	 * - `none`    : No virtual keyboard should be displayed.
	 * - `text`    : Text input in the user's locale.
	 * - `decimal` : Fractional numeric input.
	 * - `numeric` : Numeric input.
	 * - `tel`     : Telephone input, including asterisk and 
	 * -             pound key. Prefer `<input type="tel">`.
	 * - `search`  : A virtual keyboard optimized for search input.
	 * - `email`   : Email input. Prefer `<input type="email">`.
	 * - `url`     : URL input. Prefer `<input type="url">`.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-inputmode
	 * @param  ?string $inputMode
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetInputMode ($inputMode) {
		$this->inputMode = $inputMode;
		return $this;
	}

	/**
	 * Automatically set up `inputmode` attribute (if it is still `NULL`) 
	 * by field type in `PreDispatch()` field rendering moment.
	 * @return void
	 */
	protected function preDispatchInputMode () {
		if ($this->inputMode !== NULL) return;
		if ($this->type === 'number') {
			if (
				is_float($this->value) || is_float($this->step) ||
				is_float($this->max) || is_float($this->min)
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
