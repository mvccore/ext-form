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
 * - `\MvcCore\Ext\Forms\Fields\Date`
 *    - `\MvcCore\Ext\Forms\Fields\DateTime`
 *    - `\MvcCore\Ext\Forms\Fields\Month`
 *    - `\MvcCore\Ext\Forms\Fields\Time`
 *    - `\MvcCore\Ext\Forms\Fields\Week`
 * - `\MvcCore\Ext\Forms\Fields\File`
 * - `\MvcCore\Ext\Forms\Fields\Number`
 *    - `\MvcCore\Ext\Forms\Fields\Range`
 */
trait Wrapper {

	/**
	 * Html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any HTML code you want.
	 * Default wrapper values is: `{control}`.
	 * @var string
	 */
	public $wrapper = '{control}';

	/**
	 * Get html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any HTML code you want.
	 * Default wrapper values is: `{control}`.
	 * @return string
	 */
	public function GetWrapper () {
		return $this->wrapper;
	}

	/**
	 * Set html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any HTML code you want.
	 * @param string $wrapper Default wrapper values is: `{control}`.
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetWrapper ($wrapper) {
		/** @var $this \MvcCore\Ext\Forms\Field */
		$this->wrapper = $wrapper;
		return $this;
	}

	/**
	 * Wrap around rendered control HTML core
	 * any configured content, if wrapper property contains 
	 * substring for wrapping: `{control}`.
	 * Return rendered and wrapper HTML code.
	 * @param string $renderedCode 
	 * @return string
	 */
	protected function renderControlWrapper ($renderedCode) {
		$wrapperReplacement = '{control}';
		$wrapper = mb_strpos($wrapperReplacement, $this->wrapper) !== FALSE 
			? $this->wrapper 
			: $wrapperReplacement;
		return str_replace($wrapperReplacement, $renderedCode, $wrapper);
	}
}
