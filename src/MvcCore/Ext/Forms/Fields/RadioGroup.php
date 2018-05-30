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

class RadioGroup 
	extends \MvcCore\Ext\Forms\Field 
	implements \MvcCore\Ext\Forms\Fields\IOptions
{
	protected $type = 'radio';

	protected $value = '';

	protected $validators = array('ValueInOptions');
	
	public function GetMultiple () {
		return FALSE;
	}

	public function __construct(array $cfg = array()) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
}
