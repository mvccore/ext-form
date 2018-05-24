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

require_once(__DIR__.'/Core/Field.php');
//require_once(__DIR__.'/Core/View.php');

class Range extends Core\Field
{
	public $Type = 'range';
	public $Min = null;
	public $Max = null;
	public $Step = null;
	public $Multiple = FALSE;
	public $Wrapper = '{control}';
	public $Validators = array('RangeField');
	public $JsClass = 'MvcCoreForm.Range';
	public $Js = \MvcCore\Ext\Forms\IForm::FORM_DIR_REPLACEMENT . '/fields/range.js';
	public $Css = \MvcCore\Ext\Forms\IForm::FORM_DIR_REPLACEMENT . '/fields/range.css';

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
	public function SetMultiple ($multiple) {
		$this->Multiple = $multiple;
		return $this;
	}
	public function SetWrapper ($wrapper) {
		$this->Wrapper = $wrapper;
		return $this;
	}
	public function SetUp () {
		parent::SetUp();
		$this->Form->AddJs($this->Js, $this->JsClass, array($this->Name));
		$this->Form->AddCss($this->Css);
	}
	public function RenderControl () {
		if ($this->Multiple) $this->Multiple = 'multiple';
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('Min', 'Max', 'Step','Multiple')
		);
		$this->Multiple = $this->Multiple ? TRUE : FALSE ;
		$valueStr = $this->Multiple && gettype($this->Value) == 'array' ? implode(',', $this->Value) : (string)$this->Value;
		include_once('Core/View.php');
		$result = Core\View::Format(static::$Templates->control, array(
			'id'		=> $this->Id,
			'name'		=> $this->Name,
			'type'		=> $this->Type,
			'value'		=> $valueStr . '" data-value="' . $valueStr,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
		$wrapperReplacement = '{control}';
		$wrapper = mb_strpos($wrapperReplacement, $this->Wrapper) !== FALSE ? $this->Wrapper : $wrapperReplacement;
		return str_replace($wrapperReplacement, $result, $wrapper);
	}
}
