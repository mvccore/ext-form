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

class File 
	extends		\MvcCore\Ext\Forms\Field
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
				\MvcCore\Ext\Forms\Fields\ILabel,
				\MvcCore\Ext\Forms\Fields\IMultiple,
				\MvcCore\Ext\Forms\Fields\IFiles
{
	use \MvcCore\Ext\Forms\Field\Attrs\VisibleField;
	use \MvcCore\Ext\Forms\Field\Attrs\Label;
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Files;
	use \MvcCore\Ext\Forms\Field\Attrs\Wrapper;

	protected $type = 'field';

	protected $validators = ['Files'];

	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if ($this->accept === NULL) $this->throwNewInvalidArgumentException(
			'No `accept` property defined.'
		);
		if ($form->GetEnctype() !== \MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART) 
			$this->throwNewInvalidArgumentException(
				'Form needs to define `enctype` attribute as `' 
				. \MvcCore\Ext\Forms\IForm::ENCTYPE_MULTIPART . '`.'
			);
		return $this;
	}

	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
	}

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'accept',
			'capture',
		]);
		if ($this->multiple) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'multiple="multiple"';
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		$result = $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name . ($this->multiple ? '[]' : ''),
			'type'		=> $this->type,
			'value'		=> "",
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
		return $this->renderControlWrapper($result);
	}
}
