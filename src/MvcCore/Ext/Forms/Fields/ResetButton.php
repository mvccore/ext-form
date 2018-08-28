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

class ResetButton extends \MvcCore\Ext\Forms\Fields\Button
{
	protected $type = 'reset';

	protected $value = 'Reset';

	protected $validators = [];

	protected $jsClassName = 'MvcCoreForm.Reset';

	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/reset.js';
	
	public function PreDispatch () {
		parent::PreDispatch();
		$this->form->AddJsSupportFile(
			$this->jsSupportingFile, $this->jsClassName, [$this->name]
		);
	}
}
