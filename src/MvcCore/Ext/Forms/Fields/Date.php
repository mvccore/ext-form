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

//require_once('Core/View.php');

class Date extends Core\Field
{
	/**
	 * date/time/datetime field, coud be defined in extended
	 * control classes laso as week, year or day
	 * @see http://www.html5tutorial.info/html5-date.php
	 * @var string
	 */
	protected $type = 'date';
	/**
	 * Valid Datetime format to create PHP Datetime
	 * by DateTime::createFromFormat($field->Format);.
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * @example 'Y-m-d', 'Y/m/d' ...
	 * @var string
	 */
	protected $format = 'Y-m-d';
	/**
	 * Minimum date/time/datetime for current control,
	 * by configured format asigned as string value.
	 * @var string
	 */
	protected $min = null;
	/**
	 * Maximum date/time/datetime for current control,
	 * by configured format asigned as string value.
	 * @var string
	 */
	protected $max = null;
	/**
	 * HTML5 input:date, input:time and input:datetime control
	 * step attribute in seconds.
	 * @see input:date
	 * @var string
	 */
	protected $step = null;
	/**
	 * Any html code containing substring '{control}'
	 * to wrap any code around control itself.
	 * @var string
	 */
	protected $wrapper = '{control}';
	/**
	 * Validators used for submitted value to check format, min, max and dangerous characters
	 * @var string[]|\Closure[]
	 */
	protected $validators = array('Date');
	/**
	 * Set datetime value and hold it formated as string by:
	 * http://php.net/manual/en/datetime.createfromformat.php
	 * @param \DateTime|string $format
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Date
	 */
	public function SetValue ($value) {
		if (gettype($value) == 'string') {
			$this->value = $value;
		} else {
			$this->value = $value->format($this->format);
		}
		return $this;
	}
	/**
	 * Set valid date format for:
	 * http://php.net/manual/en/datetime.createfromformat.php
	 * @param string $format
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Date
	 */
	public function SetFormat ($format) {
		$this->format = $format;
		return $this;
	}
	/**
	 * Set date/time/datetime minimum,
	 * examples:
	 *	- date		(with format 'Y-m-d')		: '2015-11-25'
	 *	- time		(with format 'H:i')			: '11:30'
	 *	- datetime	(with format 'Y-m-d H:i')	: '2015-11-25 11:30'
	 * @param string $min
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Date
	 */
	public function SetMin ($min) {
		$this->min = $min;
		return $this;
	}
	/**
	 * Set date/time/datetime minimum,
	 * examples:
	 *	- date		(with format 'Y-m-d')		: '2017-01-13'
	 *	- time		(with format 'H:i')			: '18:25'
	 *	- datetime	(with format 'Y-m-d H:i')	: '2017-01-13 18:25'
	 * @param string $min
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Date
	 */
	public function SetMax ($max) {
		$this->max = $max;
		return $this;
	}
	/**
	 * Set step in seconds.
	 * @see http://www.wufoo.com/html5/types/4-date.html
	 * @param int $step
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Date
	 */
	public function SetStep ($step) {
		$this->step = $step;
		return $this;
	}
	/**
	 * Set html code wrapper, wrapper has to contain
	 * replacement in form '{control}'. Around this
	 * substring you can wrap any html code you want.
	 * @param string $wrapper
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm\Date
	 */
	public function SetWrapper ($wrapper) {
		$this->wrapper = $wrapper;
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
		$result = \MvcCore\Ext\Forms\View::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $this->value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
		$wrapperReplacement = '{control}';
		$wrapper = mb_strpos($wrapperReplacement, $this->wrapper) !== FALSE 
			? $this->wrapper 
			: $wrapperReplacement;
		return str_replace($wrapperReplacement, $result, $wrapper);
	}
}
