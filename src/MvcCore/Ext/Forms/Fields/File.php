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
	implements	\MvcCore\Ext\Forms\Fields\IAccessKey, 
				\MvcCore\Ext\Forms\Fields\ITabIndex,
				\MvcCore\Ext\Forms\Fields\IMultiple
{
	use \MvcCore\Ext\Forms\Field\Attrs\AccessKey;
	use \MvcCore\Ext\Forms\Field\Attrs\TabIndex;
	use \MvcCore\Ext\Forms\Field\Attrs\Multiple;
	use \MvcCore\Ext\Forms\Field\Attrs\Accept;

	protected $type = 'field';

	protected $validators = ['Files'];

	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'accessKey', 
			'tabIndex',
			'multiple',
			'accept',
		]);
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'type'		=> $this->type,
			'value'		=> htmlspecialchars($this->value, ENT_QUOTES),
			'attrs'		=> $attrsStr ? " $attrsStr" : '',
		]);
	}
}
