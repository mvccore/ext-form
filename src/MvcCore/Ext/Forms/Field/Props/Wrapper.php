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
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\File`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 * - `\MvcCore\Ext\Forms\Fields\Color`
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 *    - `\MvcCore\Ext\Forms\Fields\LocalizationSelect`
 * - `\MvcCore\Ext\Forms\Fields\Text`
 *    - `\MvcCore\Ext\Forms\Fields\Email`
 *    - `\MvcCore\Ext\Forms\Fields\Password`
 *    - `\MvcCore\Ext\Forms\Fields\Search`
 *    - `\MvcCore\Ext\Forms\Fields\Tel`
 *    - `\MvcCore\Ext\Forms\Fields\Url`
 * - `\MvcCore\Ext\Forms\Fields\Textarea`
 * - `MvcCore\Ext\Forms\Fields\RadioGroup`
 * - `MvcCore\Ext\Forms\Fields\CheckboxGroup`
 * @mixin \MvcCore\Ext\Forms\Field
 */
trait Wrapper {

	/**
	 * Html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any HTML code you want.
	 * Default wrapper values is: `'{control}'`.
	 * @var string
	 */
	public $wrapper = NULL;

	/**
	 * Get html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any HTML code you want.
	 * Default wrapper values is: `'{control}'`.
	 * @return string
	 */
	public function GetWrapper () {
		return $this->wrapper;
	}

	/**
	 * Set html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any HTML code you want.
	 * @param  string $wrapper Default wrapper values is: `'{control}'`.
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetWrapper ($wrapper) {
		$this->wrapper = $wrapper;
		return $this;
	}

	/**
	 * Wrap around rendered control HTML core
	 * any configured content, if wrapper property contains 
	 * substring for wrapping: `'{control}'`.
	 * Return rendered and wrapper HTML code.
	 * @param  string $renderedCode 
	 * @return string
	 */
	protected function renderControlWrapper ($renderedCode) {
		if ($this->wrapper === NULL)
			return $renderedCode;
		$wrapperReplacement = '{control}';
		$wrapper = mb_strpos($this->wrapper, $wrapperReplacement) !== FALSE 
			? $this->wrapper 
			: $wrapperReplacement;
		return str_replace($wrapperReplacement, $renderedCode, $wrapper);
	}
}
