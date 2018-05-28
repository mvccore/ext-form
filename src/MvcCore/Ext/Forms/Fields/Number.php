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

class Number extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\Size;
	use \MvcCore\Ext\Forms\Field\Attrs\MinMaxStep;
	use \MvcCore\Ext\Forms\Field\Attrs\Pattern;
	use \MvcCore\Ext\Forms\Field\Attrs\Wrapper;

	protected $type = 'number';
	
	protected $validators = array('NumberField');

	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		$this->checkValidatorsPattern();
		return $this;
	}
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('size', 'min', 'max', 'step', 'pattern')
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
