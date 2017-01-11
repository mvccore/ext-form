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

class SimpleForm_Number extends SimpleForm_Core_Field
{
	public $Type = 'number';
	public $Min = null;
	public $Max = null;
	public $Step = null;
	public $Pattern = null;
	public $Wrapper = '{control}';
	public $Validators = array('NumberField');
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
			array('Min', 'Max', 'Step', 'Pattern')
		);
		$result = SimpleForm_Core_View::Format(static::$templates->control, array(
			'id'		=> $this->Id, 
			'name'		=> $this->Name, 
			'type'		=> $this->Type,
			'value'		=> $this->Value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '', 
		));
		return str_replace('{control}', $result, $this->Wrapper);
	}
}
