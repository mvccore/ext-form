<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: init, pre-dispatch and render `<input>` HTML element 
 *				   with type `hidden`. `Hidden` field has it's own validator 
 *				   `SafeString` to clean string from base ASCII chars and 
 *				   some control chars by default. But validator `SafeString` 
 *				   doesn't prevent SQL injects and more.
 */
class	Hidden 
extends	\MvcCore\Ext\Forms\Field {

	use \MvcCore\Ext\Forms\Field\Props\AutoComplete;

	/**
	 * Possible values: `hidden`.
	 * @var string
	 */
	protected $type = 'hidden';

	/**
	 * Validators: 
	 * - `SafeString` - remove from submitted value base ASCII characters from 0 to 31 included 
	 *					(first column) and escape special characters: `& " ' < > | = \ %`.
	 *					This validator is not prevent SQL inject attacks!
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['SafeString'];

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'autoComplete',
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
