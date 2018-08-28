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

class Button 
	extends		\MvcCore\Ext\Forms\Field
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField
{
	use \MvcCore\Ext\Forms\Field\Attrs\VisibleField;

	protected $type = 'button'; // submit | reset | button

	/**
	 * Default visible button text - `OK`.
	 * @var string
	 */
	protected $value = 'OK';

	public static $templates = [
		'control'	=> '<button id="{id}" name="{name}" type="{type}"{attrs}>{value}</button>',
	];

	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
	
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
		$this->preDispatchTabIndex();
	}
}
