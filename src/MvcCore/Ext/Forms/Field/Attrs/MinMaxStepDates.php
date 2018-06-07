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

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Date` (`.\Time`, `.\DateTime`, `.\Week` ...)
 * Trait contains properties, getters and setters for 
 * protected properties `min`, `max` and `step`.
 */
trait MinMaxStepDates
{
	/**
	 * Minimum value for `Date`, `Time` and `DateTime` field(s) in `string` value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2017-01-01"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "14:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2017-01-01 14:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @var string|NULL
	 */
	protected $min = NULL;

	/**
	 * Maximum value for `Date`, `Time` and `DateTime` field(s) in `string` value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2018-06-24"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "20:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2018-06-24 20:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @var string|NULL
	 */
	protected $max = NULL;

	/**
	 * Step value for `Date`, `Time` and `DateTime` fields, always in `integer`.
	 * For `Date` and `DateTime` fields, step is `int`, number of days.
	 * For `Time` fields, step is `int`, number of seconds.
	 * For `Week` fields, step is `int`, number of weeks...
	 * @see https://www.wufoo.com/html5/date-type/
	 * @var int|NULL
	 */
	protected $step = NULL;

	/**
	 * Get minimum value for `Date`, `Time` and `DateTime` field(s) 
	 * in `string` (already formatted date) value.
	 * - `Date		=> "2017-01-01"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "14:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2017-01-01 14:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @return string|NULL
	 */
	public function GetMin () {
		return $this->min;
	}

	/**
	 * Set minimum value for `Date`, `Time` and `DateTime` field(s) 
	 * in `\DateTimeInterface` or in `int` (UNIX epoch) or in `string` 
	 * (already formatted date) value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2017-01-01"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "14:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2017-01-01 14:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @param \DateTimeInterface|string|int $min
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMin ($min) {
		$this->min = $min;
		return $this;
	}

	/**
	 * Set maximum value for `Date`, `Time` and `DateTime` field(s) 
	 * in `string` (already formatted date) value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2018-06-24"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "20:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2018-06-24 20:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @return string|NULL
	 */
	public function GetMax () {
		return $this->max;
	}

	/**
	 * Set maximum value for `Date`, `Time` and `DateTime` field(s) 
	 * in `\DateTimeInterface` or in `int` (UNIX epoch) or in `string` 
	 * (already formatted date) value.
	 * Example string values for date and time fields:
	 * - `Date		=> "2018-06-24"`		(with `$field->format` = "Y-m-d";`)
	 * - `Time		=> "20:00"`				(with `$field->format` = "H:i";`)
	 * - `DateTime	=> "2018-06-24 20:00"`	(with `$field->format` = "Y-m-d H:i";`)
	 * @see https://www.wufoo.com/html5/date-type/
	 * @param \DateTimeInterface|string|int $max
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMax ($max) {
		$this->max = $max;
		return $this;
	}

	/**
	 * Get step value for `Date`, `Time` and `DateTime` fields, always in `integer`.
	 * For `Date` and `DateTime` fields, step is `int`, number of days.
	 * For `Time` fields, step is `int`, number of seconds.
	 * For `Week` fields, step is `int`, number of weeks...
	 * @see https://www.wufoo.com/html5/date-type/
	 * @return int|NULL
	 */
	public function GetStep () {
		return $this->step;
	}

	/**
	 * Set step value for `Date`, `Time` and `DateTime` fields, always in `integer`.
	 * For `Date` and `DateTime` fields, step is `int`, number of days.
	 * For `Time` fields, step is `int`, number of seconds.
	 * For `Week` fields, step is `int`, number of weeks...
	 * @see https://www.wufoo.com/html5/date-type/
	 * @param int $step
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetStep ($step) {
		$this->step = $step;
		return $this;
	}
}
