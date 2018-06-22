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

namespace MvcCore\Ext\Forms\Fields;

interface IChecked
{
	/**
	 * Set `TRUE` to rendered field as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automaticly resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @param bool $checked 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetChecked ($checked = TRUE);

	/**
	 * Get `TRUE` if field is rendered as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automaticly resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @return bool|NULL
	 */
	public function GetChecked ();

	/**
	 * Return `TRUE` for any `array`, `object`, `resource` or `unknown type`,
	 * `TRUE` for `boolean` `TRUE`, for `string` not equal to `no`, 
	 * for `integer` not equal to `0` and `TRUE` for `float` not equal to `0.0`.
	 * @param mixed $value 
	 * @return bool
	 */
	public static function GetCheckedByValue ($value);
}
