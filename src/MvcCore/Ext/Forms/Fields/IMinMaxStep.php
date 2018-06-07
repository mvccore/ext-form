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

interface IMinMaxStep
{
    /**
	 * Get minimum value for `Number` field(s) in `float` or in `integer`.
	 * Get minimum value for `Date`, `Time` and `DateTime` field(s) in `string` value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2017-01-01"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "14:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2017-01-01 14:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @return float|NULL
	 */
	public function GetMin ();

	/**
	 * Set minimum value for `Number` field(s) in `float` or in `integer`.
	 * Set minimum value for `Date`, `Time` and `DateTime` field(s) in `string` value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2017-01-01"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "14:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2017-01-01 14:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @param float|int|string|NULL $min
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMin ($min);

	/**
	 * Get maximum value for `Number` field(s) in `float` or in `integer`.
	 * Get maximum value for `Date`, `Time` and `DateTime` field(s) in `string` value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2018-06-24"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "20:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2018-06-24 20:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @return float|NULL
	 */
	public function GetMax ();

	/**
	 * Set maximum value for `Number` field(s) in `float` or in `integer`.
	 * Set maximum value for `Date`, `Time` and `DateTime` field(s) in `string` value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2018-06-24"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "20:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2018-06-24 20:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @param float|int|string|NULL $max
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMax ($max);

	/**
	 * Get step value for `Number`, `Date`, `Time` and `DateTime` fields, always in `integer`.
	 * For `Number` fields, step is `float` or `int`.
	 * For `Date` and `DateTime` fields, step is `int`, number of days.
	 * For `Time` fields, step is `int`, number of seconds.
	 * For `Week` fields, step is `int`, number of weeks...
	 * @see https://www.wufoo.com/html5/date-type/
	 * @return float|NULL
	 */
	public function GetStep ();
	/**
	 * Set step value for `Number`, `Date`, `Time` and `DateTime` fields, always in `integer`.
	 * For `Number` fields, step is `float` or `int`.
	 * For `Date` and `DateTime` fields, step is `int`, number of days.
	 * For `Time` fields, step is `int`, number of seconds.
	 * For `Week` fields, step is `int`, number of weeks...
	 * @see https://www.wufoo.com/html5/date-type/
	 * @param float|int|string|NULL $step
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetStep ($step);
}
