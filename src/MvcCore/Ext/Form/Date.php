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

namespace MvcCore\Ext\Form;

require_once('Core/Field.php');
//require_once('Core/View.php');

class Date extends Core\Field
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
	 * @var string[]|\Closure[]
	 */
	public $Validators = array('Date');
	/**
	 * Set datetime value and hold it formated as string by:
	 * http://php.net/manual/en/datetime.createfromformat.php
	 * @param \DateTime|string $format
	 * @return \MvcCore\Ext\Form\Date
	 */
	public function SetValue ($value) {
		if (gettype($value) == 'string') {
			$this->Value = $value;
		} else {
			$this->Value = $value->format($this->Format);
		}
		return $this;
	}
	/**
	 * Set valid date format for:
	 * http://php.net/manual/en/datetime.createfromformat.php
	 * @param string $format 
	 * @return \MvcCore\Ext\Form\Date
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
	 * @return \MvcCore\Ext\Form\Date
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
	 * @return \MvcCore\Ext\Form\Date
	 */
	public function SetMax ($max) {
		$this->Max = $max;
		return $this;
	}
	/**
	 * Set step in seconds.
	 * @see http://www.wufoo.com/html5/types/4-date.html
	 * @param int $step 
	 * @return \MvcCore\Ext\Form\Date
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
	 * @return \MvcCore\Ext\Form\Date
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
		include_once('Core/View.php');
		$result = Core\View::Format(static::$Templates->control, array(
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
