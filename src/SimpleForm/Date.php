<?php

/**
 * SimpleForm
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/simpleform)
 * @license		https://mvccore.github.io/docs/simpleform/3.0.0/LICENCE.md
 */

require_once('Core/Field.php');
require_once('Core/View.php');

class SimpleForm_Date extends SimpleForm_Core_Field
{
	/**
	 * date/time/datetime field, coud be defined in extended
	 * control classes laso as week, year or day
	 * @see http://www.html5tutorial.info/html5-date.php
	 * @var string
	 */
	public $Type = 'date';
	/**
	 * Valid Datetime format to create PHP Datetime 
	 * by DateTime::createFromFormat($field->Format);.
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * @example 'Y-m-d', 'Y/m/d' ...
	 * @var string
	 */
	public $Format = 'Y-m-d';
	/**
	 * Minimum date/time/datetime for current control,
	 * by configured format asigned as string value.
	 * @var string
	 */
	public $Min = null;
	/**
	 * Maximum date/time/datetime for current control,
	 * by configured format asigned as string value.
	 * @var string
	 */
	public $Max = null;
	/**
	 * HTML5 input:date, input:time and input:datetime control
	 * step attribute in seconds.
	 * @see input:date
	 * @var string
	 */
	public $Step = null;
	/**
	 * Any html code containing substring '{control}'
	 * to wrap any code around control itself.
	 * @var string
	 */
	public $Wrapper = '{control}';
	/**
	 * Validators used for submitted value to check format, min, max and dangerous characters
	 * @var string[]|Closure[]
	 */
	public $Validators = array('Date');
	/**
	 * Set valid date format for:
	 * http://php.net/manual/en/datetime.createfromformat.php
	 * @param string $format 
	 * @return SimpleForm_Date
	 */
	public function SetFormat ($format) {
		$this->Format = $format;
		return $this;
	}
	/**
	 * Set date/time/datetime minimum,
	 * examples:
	 *	- date		(with format 'Y-m-d')		: '2015-11-25'
	 *	- time		(with format 'H:i')			: '11:30'
	 *	- datetime	(with format 'Y-m-d H:i')	: '2015-11-25 11:30'
	 * @param string $min 
	 * @return SimpleForm_Date
	 */
	public function SetMin ($min) {
		$this->Min = $min;
		return $this;
	}
	/**
	 * Set date/time/datetime minimum,
	 * examples:
	 *	- date		(with format 'Y-m-d')		: '2017-01-13'
	 *	- time		(with format 'H:i')			: '18:25'
	 *	- datetime	(with format 'Y-m-d H:i')	: '2017-01-13 18:25'
	 * @param string $min
	 * @return SimpleForm_Date
	 */
	public function SetMax ($max) {
		$this->Max = $max;
		return $this;
	}
	/**
	 * Set step in seconds.
	 * @see http://www.wufoo.com/html5/types/4-date.html
	 * @param int $step 
	 * @return SimpleForm_Date
	 */
	public function SetStep ($step) {
		$this->Step = $step;
		return $this;
	}
	/**
	 * Set html code wrapper, wrapper has to contain
	 * replacement in form '{control}'. Around this 
	 * substring you can wrap any html code you want.
	 * @param string $wrapper 
	 * @return SimpleForm_Date
	 */
	public function SetWrapper ($wrapper) {
		$this->Wrapper = $wrapper;
		return $this;
	}
	/**
	 * Render control element, without label or possible error messages, only the element.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Min', 'Max', 'Step')
		);
		$result = SimpleForm_Core_View::Format(static::$templates->control, array(
			'id'		=> $this->Id,
			'name'		=> $this->Name,
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
		$wrapperReplacement = '{control}';
		$wrapper = mb_strpos($wrapperReplacement, $this->Wrapper) !== FALSE ? $this->Wrapper : $wrapperReplacement;
		return str_replace($wrapperReplacement, $result, $wrapper);
	}
}
