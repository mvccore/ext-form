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

namespace MvcCore\Ext\Forms\Field;

trait Getters
{
	public function GetName () {
		return $this->name;
	}

	/**
	 * Get control value, should be string or array, by field type implementation.
	 * @return string|array
	 */
	public function GetValue () {
		return $this->value;
	}

	public function GetReadOnly () {
		return $this->readOnly;
	}

	public function GetDisabled () {
		return $this->disabled;
	}

	public static function & GetTemplates () {
		return (array) static::$templates;
	}
}
