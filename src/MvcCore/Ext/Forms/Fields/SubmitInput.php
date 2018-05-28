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

class SubmitInput extends \MvcCore\Ext\Forms\Field
{
	use \MvcCore\Ext\Forms\Field\Attrs\AccessKey;

	protected $type = 'submit';
	
	protected $value = 'Submit';
	
	protected $renderMode = \MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL;
	
	protected $validators = array();
	
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->value) $this->throwNewInvalidArgumentException(
			'No button `value` defined.'
		);
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->translate && $this->value)
			$this->value = $this->form->Translate($this->value);
	}
	
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars(
			array('accessKey',)
		);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, array(
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> $this->value,
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		));
	}
}
