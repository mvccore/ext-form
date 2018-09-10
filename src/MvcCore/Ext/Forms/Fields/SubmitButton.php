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

/**
 * Responsibility: init, predispatch and render `<input type="submit">`
 *				   with default text `Submit` and it's supporting JS code.
 *				   Button has it's custom result state configuration and
 *				   button has no validator.
 */
class SubmitButton 
	extends		\MvcCore\Ext\Forms\Fields\Button 
	implements	\MvcCore\Ext\Forms\Fields\ISubmit
{
	use \MvcCore\Ext\Forms\Field\Props\Submit;
	use \MvcCore\Ext\Forms\Field\Props\FormAttrs;

	/**
	 * Possible values: `submit`.
	 * @var string
	 */
	protected $type = 'submit';
	
	/**
	 * Default visible button text - `Submit`.
	 * This button text is automaticly checked, if there is at least any 
	 * visible text and automaticly translated, if any translator `callable` 
	 * defined in form instance.
	 * @var string
	 */
	protected $value = 'Submit';

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
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
