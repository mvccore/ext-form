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

class SubmitButton 
	extends		\MvcCore\Ext\Forms\Fields\Button 
	implements	\MvcCore\Ext\Forms\Fields\ISubmit
{
	use \MvcCore\Ext\Forms\Field\Props\Submit;
	use \MvcCore\Ext\Forms\Field\Props\FormAttrs;

	protected $type = 'submit';
	
	/**
	 * Default visible button text - `Submit`.
	 * @var string
	 */
	protected $value = 'Submit';

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'formAction', 'formEnctype', 'formMethod', 'formNoValidate', 'formTarget'
		]);
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars($this->value, ENT_QUOTES),
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}
}
