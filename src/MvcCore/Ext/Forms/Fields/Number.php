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

require_once('Core/Field.php');
//require_once('Core/View.php');

class Number extends Core\Field
{
	public $Type = 'number';
	public $Size = null;
	public $Min = null;
	public $Max = null;
	public $Step = null;
	public $Pattern = null;
	public $Wrapper = '{control}';
	public $Validators = array('NumberField');
	public function SetSize ($size) {
		$this->Size = $size;
		return $this;
	}
	public function SetMin ($min) {
		$this->Min = $min;
		return $this;
	}
	public function SetMax ($max) {
		$this->Max = $max;
		return $this;
	}
	public function SetStep ($step) {
		$this->Step = $step;
		return $this;
	}
	public function SetPattern ($pattern) {
		$this->Pattern = $pattern;
		return $this;
	}
	public function SetWrapper ($wrapper) {
		$this->Wrapper = $wrapper;
		return $this;
	}
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Size', 'Min', 'Max', 'Step', 'Pattern')
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
