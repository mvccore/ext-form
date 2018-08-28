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

class Email extends Text
{
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;

	protected $type = 'email';

	protected $validators = ['Email'/*, 'MinLength', 'MaxLength', 'Pattern'*/];
	
	public function RenderControl () {
		if ($this->multiple) $this->SetControlAttr('multiple', 'multiple');
		return parent::RenderControl();
	}
}
