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
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Validators\Pattern`
 */
trait Pattern
{
	/**
	 * RegExp match pattern for HTML attribute `pattern` and
	 * RegExp match pattern for build in `Pattern` validator
	 * added automatically after field is added into form, if any 
	 * RegExp pattern value defined.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-pattern
	 * @var string|NULL
	 */
	protected $pattern = NULL;

	/**
	 * Get RegExp match pattern for HTML attribute `pattern`
	 * and RegExp match pattern for build in `Pattern` validator
	 * added automatically after field is added into form, if any 
	 * RegExp pattern value defined.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-pattern
	 * @return string RegExp pattern without border characters for javascript and PHP.
	 */
	public function GetPattern () {
		return $this->pattern;
	}

	/**
	 * Set RegExp match pattern for HTML attribute `pattern`
	 * and RegExp match pattern for build in `Pattern` validator
	 * added automatically after field is added into form, if any 
	 * RegExp pattern value defined.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-pattern
	 * @param string $pattern RegExp pattern without border characters for javascript and PHP.
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetPattern ($pattern) {
		$this->pattern = $pattern;
		return $this;
	}

	/**
	 * Check after field is added into form, if field 
	 * has defined any value for pattern property and if it does,
	 * add automaticaly build in pattern validator.
	 * @return void
	 */
	protected function setFormPattern () {
		if ($this->pattern && !isset($this->validators['Pattern']))
			$this->validators['Pattern'] = 'Pattern';
	}
}
