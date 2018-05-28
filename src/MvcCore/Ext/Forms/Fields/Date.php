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
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxStep;
	use \MvcCore\Ext\Forms\Field\Attrs\Wrapper;

	/**
	 * @see http://www.html5tutorial.info/html5-date.php
	 * @var string
	 */
	protected $type = 'date';

	/**
	 * String format mask to format given values in `Intl` extension `\DateTimeInterface` type
	 * or string format mask to format given values in `integer` type by PHP `date()` function.
	 * Example: `"Y-m-d" | "Y/m/d"`
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * @see http://php.net/manual/en/function.date.php
	 * @var string
	 */
	protected $format = 'Y-m-d';
	
	
	/**
	 * Validators used for submitted value to check format, min, max and dangerous characters
	 * @var string[]|\Closure[]
	 */
	protected $validators = array('Date');

	/**
	 * Get formated by configured `$field->format` property as string to render.
	 * @return string
	 */
	public function GetValue () {
		return $this->value;
	}
	
	/**
	 * 
	 * Set value as `\Datetime`, int (UNIX timestamp) or formated string value 
	 * and use it internaly as formated string.
	 * For given `\Datetime` instance, format `$value` by 
	 * `Intl` extension function `date_format()`,
	 * for given `integer`, format `$value` by PHP function `date()`.
	 * http://php.net/manual/en/datetime.createfromformat.php
	 * @param \DateTimeInterface|int|string $value
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValue ($value) {
		if ($value instanceof \DateTimeInterface) {
			$this->value = \date_format($value, $this->format);
		} else if (is_int($value)) {
			$this->value = \date($this->format, $value);
		} else {
			$this->value = $value;
		}
		return $this;
	}

	/**
	 * Get string format mask to format given values in `Intl` extension `\DateTimeInterface` type
	 * or string format mask to format given values in `integer` type by PHP `date()` function.
	 * Example: `"Y-m-d" | "Y/m/d"`
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * @see http://php.net/manual/en/function.date.php
	 * @return string
	 */
	public function GetFormat () {
		return $this->format;
	}

	/**
	 * Set string format mask to format given values in `Intl` extension `\DateTimeInterface` type
	 * or string format mask to format given values in `integer` type by PHP `date()` function.
	 * Example: `$field->SetFormat("Y-m-d") | $field->SetFormat("Y/m/d");`
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * @see http://php.net/manual/en/function.date.php
	 * @param string $format
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetFormat ($format) {
		$this->format = $format;
		return $this;
	}
	
	/**
	 * Render control element, without label or possible error messages, only the element.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('min', 'max', 'step')
		);
		$formViewClass = $this->form->GetViewClass();
		$result = $formViewClass::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $this->value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
		return $this->renderControlWrapper($result);
	}
}
