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

trait Wrapper
{
	/**
	 * Html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any html code you want.
	 * Defaul wrapper values is: `{control}`.
	 * @var string
	 */
	public $wrapper = '{control}';

	/**
	 * Get html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any html code you want.
	 * Defaul wrapper values is: `{control}`.
	 * @return string
	 */
	public function GetWrapper () {
		return $this->wrapper;
	}

	/**
	 * Set html code wrapper, wrapper has to contain
	 * replacement in string form: `{control}`. Around this
	 * substring you can wrap any html code you want.
	 * @param string $wrapper Defaul wrapper values is: `{control}`.
	 * @return \MvcCore\Ext\Forms\IField
	 */
	public function & SetWrapper ($wrapper) {
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
