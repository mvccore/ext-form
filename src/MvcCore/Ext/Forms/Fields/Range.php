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

class Range 
	extends		\MvcCore\Ext\Forms\Fields\Number
	implements	\MvcCore\Ext\Forms\Fields\IMultiple
{
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;

	protected $type = 'range';

	protected $validators = ['Range'];

	protected $jsClassName = 'MvcCoreForm.Range';

	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/range.js';

	protected $cssSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/range.css';

	/**
	 * If range has multiple attribute, this function
	 * returns `array` of floats. If select has not multiple
	 * attribute, this function returns `float`.
	 * If there is no value, function returns `NULL`.
	 * @return array|float|NULL
	 */
	public function GetValue () {
		return $this->value;
	}
	
	/**
	 * If range has multiple attribute, set to this function
	 * `array` of floats. If range has not multiple
	 * attribute, set to this function `float`.
	 * If you don't want any pre initialized value, set `NULL`.
	 * @param array|float|NULL $value
	 * @return \MvcCore\Ext\Forms\Fields\Select
	 */
	public function & SetValue ($value) {
		$this->value = $value;
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		$this->form
			->AddJsSupportFile(
				$this->jsSupportingFile, 
				$this->jsClassName, 
				[$this->name . ($this->multiple ? '[]' : '')]
			)
			->AddCssSupportFile($this->cssSupportingFile);
	}

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'pattern',
			'min', 'max', 'step',
			'list',
			'autoComplete',
			'placeHolder',
		]);
		if ($this->multiple) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'multiple="multiple"';
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$valueStr = $this->multiple && gettype($this->value) == 'array' 
			? implode(',', (array) $this->value) 
			: (string) $this->value;
		$valueStr = htmlspecialchars($valueStr, ENT_QUOTES);
		$formViewClass = $this->form->GetViewClass();
		$result = $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name . ($this->multiple ? '[]' : ''),
			'type'		=> $this->type,
			'value'		=> $valueStr . '" data-value="' . $valueStr,
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
		return $this->renderControlWrapper($result);
	}
}
