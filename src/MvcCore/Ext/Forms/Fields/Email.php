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
 * Responsibility - init, predispatch and render `<input>` HTML element with type `email`.
 * Email field has it's own validator to check submitted email/emails format by default.
 */
class		Email
extends		Text
implements	\MvcCore\Ext\Forms\Fields\IMultiple
{
	use \MvcCore\Ext\Forms\Field\Props\Multiple;

	/**
	 * Possible values: `email`
	 * @var string
	 */
	protected $type = 'email';

	/**
	 * Validators: 
	 * - `Email` - to check email format or emails format, if field ha sdefined `multiple` boolean attribute.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['Email'/*, 'MinLength', 'MaxLength', 'Pattern'*/];
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		if ($this->multiple) $this->SetControlAttr('multiple', 'multiple');
		return parent::RenderControl();
	}
}
