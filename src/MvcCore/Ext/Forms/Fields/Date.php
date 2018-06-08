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

class Date extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\Format;
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxStepDates;
	use \MvcCore\Ext\Forms\Field\Attrs\Wrapper;

	/**
	 * @see http://www.html5tutorial.info/html5-date.php
	 * @var string
	 */
	protected $type = 'date';

	/**
	 * Value is used as `\DateTimeInterface`,
	 * but it could be set into field as formated `string`
	 * by `$this->format` or as `int` (Unix epoch).
	 * @var \DateTimeInterface|NULL
	 */
	protected $value = NULL;
	
	/**
	 * String format mask to format given values in `\DateTimeInterface` type for PHP `date_format()` function or 
	 * string format mask to format given values in `integer` type by PHP `date()` function.
	 * Example: `"Y-m-d"`
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * @see http://php.net/manual/en/function.date.php
	 * @var string
	 */
	protected $format = 'Y-m-d';

	/**
	 * Validators used for submitted value to check format, min., max. and dangerous characters.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['Date'];

	/**
	 * Get value as `\DateTimeInterface`.
	 * @see http://php.net/manual/en/class.datetime.php
	 * @param bool $getFormatedString Get value as formated string by `$this->format`.
	 * @return \DateTimeInterface|string|NULL
	 */
	public function GetValue ($getFormatedString = FALSE) {
		return $getFormatedString
			? $this->value->format($this->format)
			: $this->value;
	}
	
	/**
	 * Set value as `\DateTimeInterface` or `int` (UNIX timestamp) or 
	 * formatted `string` value by `date()` by `$this->format` 
	 * and use it internally as `\DateTimeInterface`.
	 * @see http://php.net/manual/en/class.datetime.php
	 * @param \DateTimeInterface|int|string $value
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValue ($value) {
		$this->value = $this->createDateTimeFromInput($value, TRUE);
		return $this;
	}

	/**
	 * Render control element, without label or possible error messages, only the element.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			['min', 'max', 'step']
		);
		$formViewClass = $this->form->GetViewClass();
		$result = $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $this->value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
		return $this->renderControlWrapper($result);
	}
}
