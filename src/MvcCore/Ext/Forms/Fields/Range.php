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

class Range extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxStepNumber;
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Wrapper;

	protected $type = 'range';

	protected $validators = array('RangeField');

	protected $jsClassName = 'MvcCoreForm.Range';

	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/range.js';

	protected $cssSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/range.css';

	public function PreDispatch () {
		parent::PreDispatch();
		$this->form
			->AddJsSupportFile($this->jsSupportingFile, $this->jsClassName, array($this->name))
			->AddCssSupportFile($this->cssSupportingFile);
	}
	public function RenderControl () {
		if ($this->multiple) 
			$this->multiple = 'multiple';
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('min', 'max', 'step', 'multiple')
		);
		$valueStr = $this->multiple && gettype($this->value) == 'array' 
			? implode(',', (array) $this->value) 
			: (string) $this->value;
		$result = \MvcCore\Ext\Forms\View::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $valueStr . '" data-value="' . $valueStr,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
		return $this->renderControlWrapper($result);
	}
}
